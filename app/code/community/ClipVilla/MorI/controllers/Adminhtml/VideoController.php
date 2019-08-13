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
class ClipVilla_MorI_Adminhtml_VideoController extends Mage_Adminhtml_Controller_Action
{

    /**
     * adds entry to rendering stack with priority 1
	 * video data needs to exists to add an entry to the rendering stack
     * redirect to Product Edit
     *
     */
    public function renderAction()
    {
        $productId = $this->getRequest()->getParam('id');
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {

            $existingVideo = Mage::getModel('clipvilla_mori/videos')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->getFirstItem();

            // Checks if the product has not excluded images
            if ($existingVideo->getImageOne() !== '') {
                $existingVideoData = $existingVideo->getData();

                // Checks if the product needs to be saved
                if (empty($existingVideoData)) {
                    $this->_getSession()->addError($this->__('The Product needs to be saved before rendering!'));
                } else {
                    // add product to rendering stack with priority 1
                    Mage::getModel('clipvilla_mori/stack')->prioritizeVideo($existingVideo->getVideoId());
                    $existingVideo->setCreationDate(null)
                        ->setStatus(0)
                        ->save();
                    $this->_getSession()->addSuccess($this->__('Added to rendering queue'));
                }
            } else {
                $this->_getSession()->addError(
                    $this->__('The Product needs at least one not excluded Image to render a video.')
                );
            }

        }

        // redirect to product edit page
        $this->_redirect(
            '*/catalog_product/edit', array(
                                           'id'       => $productId,
                                           '_current' => true
                                      )
        );
    }

	/**
     * deletes the videos and the video data in clipvilla_videos/ clipvilla_stack tables
     */
    public function deleteAction()
    {
        $productId = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $video = Mage::getModel('clipvilla_mori/videos')->load($productId, 'product_id');
        $stack = Mage::getModel('clipvilla_mori/stack')->load($video->getVideoId(), 'video_id');
        $file = Mage::helper('clipvilla_mori/path')->getVideoFile($product->getId());
        if (file_exists($file)) {
            unlink($file);
        }
        $video->delete();
        $stack->delete();

        $this->_getSession()->addSuccess($this->__('The video has been deleted.'));

        // redirect to product edit page
        $this->_redirect(
            '*/catalog_product/edit', array(
                                           'id'       => $productId,
                                           '_current' => true
                                      )
        );
    }

	/**
     * adds the product data in the clipvilla_videos/ clipvilla_stack tables
     * only on visibly products and products that meet the requirements (has Images etc.)
     */
    public function massSaveAllAction()
    {
        $params = $this->getRequest()->getParams();
        $productIds = $params['product'];
        Mage::getModel('clipvilla_mori/videos')->saveForAllProducts($productIds);
        $this->_getSession()->addSuccess($this->__('Products have been added to rendering queue.'));

        $this->_redirect('*/catalog_product/index');
    }

}