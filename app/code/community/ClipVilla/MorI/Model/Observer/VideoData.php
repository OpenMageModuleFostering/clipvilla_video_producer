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
class ClipVilla_MorI_Model_Observer_VideoData
{

    /**
     * saves Video Parameters in clipvilla_videos table
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveValues(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
			$product = $observer->getProduct();
			$helper = Mage::helper('clipvilla_mori');
			$existingVideo = Mage::getModel('clipvilla_mori/videos')->getCollection()
				->addFieldToFilter('product_id', $product->getId())
				->getFirstItem();

			if ($existingVideo->getProductId()) {
				$video = $existingVideo;
			} else {
				$video = Mage::getModel('clipvilla_mori/videos');
			}

			// sets RenderProjectId
			$templateId = $product->getClipvillaTemplate();
			if ($templateId == '') {
				$templateId = $helper->getTemplateIdFromCategory($product->getCategoryIds());
				if ($templateId == '') {
					$templateId = Mage::getStoreConfig('clipvilla/custom_data/default_template');
				}
			}

			$this->setProductUsps($product);
			$images = $product->getData('media_gallery');
			$images = $images['images'];
			$images = $helper->getImageFiles($images);


			if ($video->needUpdate($product, $images) || $video->getProductId() == '') {
				$mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
				$video->setProductId($product->getId());
				$video->setRenderProjectId($templateId);

				foreach ($mapping as $map) {
					if ($map->renderProjectId == $video->getRenderProjectId()) {
						$video->setImages($images, $map, $product);
						foreach ($map->textDZs as $textDz) {
							foreach ($textDz as $param) {
								$videoMethod = 'set' . $param->name;
								$productMethod = 'getClipvilla' . $param->name;
								$video->$videoMethod($product->$productMethod());
							}
						}
					}
				}
				$video->setStatus(ClipVilla_MorI_Model_Videos::STATUS_EXISTS_NOT);
				$existingVideo->setCreationDate(null);
				$video->save();
			}
			$this->unsetProductUsps($product);
			Mage::unregister('usps');
        }
    }

    /**
     * only used to check the Product USPs in needUpdate
     *
     * @param $product
     */
    protected function setProductUsps($product)
    {
        $helper = Mage::helper('clipvilla_mori');
        $usps = Mage::registry('usps');
        $i = 1;

        if ($usps != '') {
            foreach ($usps as $usp) {
                $method = 'setClipvillaUsp' . $helper->numberConverter($i);
				if ($usp == '') {
					$usp = Mage::getStoreConfig('clipvilla/custom_data/usp_' . strtolower($helper->numberConverter($i)));
				}
                $product->$method($usp);
                $i++;
            }
        }
    }

	/**
     * unsets the product usps if they are the same with the usps in config
     *
     * @param $product
     */
    protected function unsetProductUsps($product)
    {	
        $helper = Mage::helper('clipvilla_mori');
        $usps = Mage::registry('usps');
        $i = 1;
		
        if ($usps != '') {
            foreach ($usps as $usp) {
                $method = 'setClipvillaUsp' . $helper->numberConverter($i);
				if ($usp == Mage::getStoreConfig('clipvilla/custom_data/usp_' . strtolower($helper->numberConverter($i)))) {
					$product->$method('');
				}
                $i++;
            }
        }
    }

    /*
     * Deletes table entries and the corresponding video on product-delete
     *
     */
    public function deleteAll(Varien_Event_Observer $observer)
    {
        $path = Mage::helper('clipvilla_mori/path');
        $product = $observer->getProduct();
        $video = Mage::getModel('clipvilla_mori/videos')->load($product->getId(), 'product_id');
        $stack = Mage::getModel('clipvilla_mori/stack')->load($video->getVideoId(), 'video_id');
        $file = $path->getVideoFile($product->getId());
        if (file_exists($file)) {
            unlink($file);
        }
        $video->delete();
        $stack->delete();

        // sets index "update required" to no -> Column is displayed Green in Backend
        $index = Mage::getSingleton('index/indexer')
            ->getProcessByCode('clipvilla_indexer');
        $indexEvent = Mage::getModel('index/event')->getCollection()
            ->addFieldToSelect('event_id')
            ->addFieldToFilter('entity_pk', $product->getId())
            ->addFieldToFilter('type', 'delete')
            ->getFirstItem();
        $indexProcessEvent = Mage::getResourceModel('index/process');
        $indexProcessEvent->updateEventStatus(
            $index->getId(), $indexEvent->getId(), Mage_Index_Model_Process::EVENT_STATUS_DONE
        );
    }

}