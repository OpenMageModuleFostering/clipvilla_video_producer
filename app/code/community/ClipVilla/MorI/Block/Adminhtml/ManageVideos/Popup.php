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
class ClipVilla_MorI_Block_Adminhtml_ManageVideos_Popup extends Mage_Adminhtml_Block_Template
{

	/**
     * returns the video File for the manage videos page in adimhtml
     *
     * @return string
     */
    public function getVideo()
    {
        $videoId = $this->getRequest()->getParam('video_id');
        $productId = Mage::getModel('clipvilla_mori/videos')->load($videoId)->getProductId();

        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $video = Mage::getStoreConfig('clipvilla/configuration/video_folder').DS.$productId.'.mp4';
        return $baseUrl.$video;
    }

}
