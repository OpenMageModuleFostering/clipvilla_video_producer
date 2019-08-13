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
class ClipVilla_MorI_Block_Videos extends Mage_Core_Block_Template
{

    private $productId;
    private $video;
    private $videoFile;

    protected function _construct()
    {
        $this->productId = Mage::registry('current_product')->getId();

        $this->video = Mage::getModel('clipvilla_mori/videos')
            ->load($this->productId, 'product_id');

        $this->videoFile = Mage::getStoreConfig('clipvilla/configuration/video_folder')
            . DS . $this->productId . '.mp4';
    }

    /**
     * returns VideoFile
     *
     * @return string
     */
    public function getVideoFile()
    {
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        return $baseUrl.$this->videoFile;
    }

    /**
     * Checks if Video can be display in frontend
     *
     * @return bool
     */
    public function videoExists()
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
            if ($this->video->getStatus()) {
                if (file_exists($this->videoFile)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * return the Creation Date of the Video
     *
     * @return mixed
     */
    public function getCreationDate()
    {
        $date = Mage::getModel('core/date')->timestamp(strtotime($this->video->getCreationDate()));
        $date = date('Y-m-d', $date);
        return $date;
    }

    /**
     * returns the first Image used in the Video
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $ImageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
            .'media' . DS . 'catalog' . DS . 'product' . $this->video->getImageOne();
    }

    /**
     * return the Meta Title, if its empty the product Name is returned
     *
     * @return mixed
     */
    public function getMetaTitle()
    {
        $metaTitle = Mage::registry('current_product')->getMetaTitle();
        if (empty($metaTitle)) {
            $metaTitle = Mage::registry('current_product')->getName();
        }
        return $metaTitle;
    }

    /**
     * return the Meta Title, if its empty the product Name is returned
     *
     * @return mixed
     */
    public function getMetaDescription()
    {
        $metaDescription = Mage::registry('current_product')->getMetaDescription();
        if (empty($metaDescription)) {
            $metaDescription = Mage::registry('current_product')->getShortDescription();
        }
        return $metaDescription;
    }

    /**
     * returns the Video duration
     *
     * @return mixed
     */
    public function getVideoDuration()
    {
        return Mage::helper('clipvilla_mori')->getVideoDuration($this->video->getRenderProjectId());
    }

}