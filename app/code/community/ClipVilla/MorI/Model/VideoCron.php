<?php
/**
*ClipVilla Video Producer
*
*The ClipVilla Video Producer's code is created by ClipVilla GmbH. 
*If the code is completely or partially changed by a third party all guarantee and warranty claims against ClipVilla GmbH will be invalidated.
*
* NOTICE OF LICENSE
*
* This source file is subject to the GNU General Public License (GPLv3)
* It is available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/quick-guide-gplv3.html.en
*/
class ClipVilla_MorI_Model_VideoCron extends Mage_Core_Model_Abstract
{

    /**
     * Generate a Video-Sitemap every Night
     * The previous Version gets overriden
     */
    public function generateVideoSitemap()
    {
        Mage::getModel('clipvilla_mori/sitemap_video')->createSitemap();
    }


    /**
     * Generate a Video with every neccesary step:
     * 1. Starts rendering
     * 2. Checks rendering Status
     * 3. Video download
     */
    public function generateVideo()
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
            $path = Mage::helper('clipvilla_mori/path');

            // Return stack entity that has already been sent to rendering
            $stack = Mage::getModel('clipvilla_mori/stack')
                ->load(ClipVilla_MorI_Model_Stack::STATUS_RENDERING, 'status');

            // If for no Entity the rendering process has been started, returns first entity sorted by priority
            $isRendering = $stack->getData();
            if (empty($isRendering)) {
                $stack = Mage::getModel('clipvilla_mori/stack')->getCollection()
                    ->setOrder('priority', 'DESC')
                    ->getFirstItem();
            }

            // Returns corresponding Video Data to the stack entity
            $existingVideo = Mage::getModel('clipvilla_mori/videos')->load($stack->getVideoId(), 'video_id');
            $existingVideoData = $existingVideo->getData();

            if (!empty($existingVideoData)) {
                $file = Mage::getModel('clipvilla_mori/file');
                $mori = Mage::getModel('clipvilla_mori/executer_mori');
                $productId = $existingVideo->getProductId();
                $shopVideoFile = $path->getVideoFile($productId);

                // resets Entry after 30 status requests if it gets Stuck
                $renderingCount = Mage::getStoreConfig('clipvilla/rendering/reset');
                if ($renderingCount == 30) {
                    $stack->setStatus(ClipVilla_MorI_Model_Stack::STATUS_QUEUE);
                    Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);
                }

                // 1. Starts Rendering
                if ($stack->getStatus() == ClipVilla_MorI_Model_Stack::STATUS_QUEUE) {
                    try {
                        $soapVar = $existingVideo->getSoapVar($existingVideo->getRenderProjectId());
                        $renderId = $mori->startRendering($soapVar);

                        // Rendering Error (API returns error)
                        if (!is_int($renderId)) {
                            $errorMessage = substr($renderId, 0, 450);
                            Mage::log($errorMessage, null, 'clipvilla.log', true);
                            Mage::getModel('clipvilla_mori/mail')->sendErrorMail($productId, $errorMessage);
                            if ($renderId == 'License key invalid') {
                                $existingVideo->setStatus(3)->save();
                            } elseif('License key capacity exceeded') {
								$existingVideo->setStatus(4)->save();
							} else {
                                $existingVideo->setStatus(2)->save();
                            }
                            $stack->delete();
                            Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);

                            return;
                        }

                        $stack->setRenderId($renderId);
                        $stack->setStatus(ClipVilla_MorI_Model_Stack::STATUS_RENDERING);
                        $stack->save();

                        // Sonstige Fehler
                    } catch (Exception $e) {
                        $errorMessage = 'Rendering Failed';
                        Mage::log($e, null, 'clipvilla.log', true);
                        Mage::getModel('clipvilla_mori/mail')->sendErrorMail($productId, $errorMessage);
                        $existingVideo->setStatus(2)->save();
                        $stack->delete();
                        Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);

                        return;
                    }
                    return;
                }

                // 2. Checks the Status of the rendering process
                $renderingStatus = '';
                if ($stack->getStatus() == ClipVilla_MorI_Model_Stack::STATUS_RENDERING) {
                    try {
                        $renderingCount++;
                        Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', $renderingCount);
                        $response = $mori->getStatus($stack->getRenderId());
                        $renderingStatus = $response->status;
                        if ($renderingStatus == 'Job Complete') {
                            $downloadUrl = $response->movieUrl;
                        }

                        // Fehler bei der Statusabfrage
                    } catch (Exception $e) {
                        $errorMessage = 'Failed to get Status';
                        Mage::log($e, null, 'clipvilla.log', true);
                        Mage::getModel('clipvilla_mori/mail')->sendErrorMail($productId, $errorMessage);
                        $existingVideo->setStatus(2)->save();
                        $stack->delete();
                        Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);

                        return;
                    }
                }

                //3. video download
                if ($renderingStatus == 'Job Complete') {
                    try {
                        $stack->setStatus(ClipVilla_MorI_Model_Stack::STATUS_DOWNLOAD);
                        $stack->save();

                        $errorMessage = $file->downloadMovieFile($downloadUrl, $shopVideoFile);
                        if (!empty($errorMessage)) {
                            Mage::log(substr($errorMessage, 0, 450), null, 'clipvilla.log', true);
                            Mage::getModel('clipvilla_mori/mail')->sendErrorMail($productId, $errorMessage);
                            $existingVideo->setStatus(2)->save();
                            $stack->delete();
                            Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);

                            return;
                        }
                    } catch (Exception $e) {
                        $errorMessage = 'Videodownload Failed.';
                        Mage::getModel('clipvilla_mori/mail')->sendErrorMail($productId, $errorMessage);
                        $existingVideo->setStatus(2)->save();
                        $stack->delete();
                        Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);

                        return;
                    }
                    Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);
                    $existingVideo->setStatus(ClipVilla_MorI_Model_Videos::STATUS_EXISTS);
                    $existingVideo->setCreationDate(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
                    $existingVideo->save();

                    //4. garbage collection
                    $stack->delete();

                }
            }
        }
    }
}