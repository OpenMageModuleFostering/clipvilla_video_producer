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
class ClipVilla_MorI_Adminhtml_ManageVideosController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("catalog/manageVideos")->_addBreadcrumb(Mage::helper("adminhtml")->__("Manage Videos"),Mage::helper("adminhtml")->__("Manage Videos"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("Catalog"));
        $this->_title($this->__("Manage Videos"));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Deletes Videos
     */
    public function massDeleteAllAction()
    {
        $params = $this->getRequest()->getParams();

        foreach ($params['video_ids'] as $videoId) {
            $video = Mage::getModel('clipvilla_mori/videos')->load($videoId);
            $productId = $video->getProductId();
            $video->delete();
            Mage::getModel('clipvilla_mori/stack')->load($videoId, 'video_id')->delete();
            $file = Mage::helper('clipvilla_mori/path')->getVideoFile($productId);
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->_getSession()->addSuccess($this->__('Delete was successful.'));
        $this->_redirect('*/manageVideos/index');
    }

    /**
     * newAction = Download/create Video Sitemap - Button
     *
     */
    public function newAction()
    {
        $sitemap = Mage::getModel('clipvilla_mori/sitemap_video')->createSitemap();
        $fileName = 'sitemap_video.xml';
        try {
            $file = file_get_contents($sitemap);
            $this->_prepareDownloadResponse($fileName, $file);
        } catch (Exception $e) {
            Mage::log($e, null, 'clipvilla.log', true);
            $this->_getSession()->addSuccess($this->__('File could not be downloaded.'));
        }
    }

    public function popupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
