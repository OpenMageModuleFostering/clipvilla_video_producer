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
class ClipVilla_MorI_Helper_Path extends Mage_Core_Helper_Abstract
{

    const ENCODING = '.mp4';

    /**
     * returns Logo that got uploaded in the Module Configuration
     *
     * @param string $file
     *
     * @return string
     */
    public function getLogoFile($file = '')
    {
        return Mage::getBaseDir() . DS . 'media' . DS . 'clipvilla' . DS . $file;
    }

    /**
     * returns directory for product images
     *
     * @param string $file
     *
     * @return string
     */
    public function getProductImageDir($file = '')
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $file;
    }

    /**
     * @param $productId
     *
     * @return string
     */
    public function getVideoFile($productId)
    {
        return Mage::getBaseDir() . DS . Mage::getStoreConfig('clipvilla/configuration/video_folder') . DS . $productId
        . self::ENCODING;
    }

    /**
     * returns URL for Video download
     *
     * @param $productId
     *
     * @return string
     */
    public function getVideoDownloadPath($productId)
    {
        $myFile = $this->getMoriResultFile($productId, self::COMPLETE);
        $lines = file($myFile);
        return trim($lines[1]);
    }

    /**
     * returns the mapping XML for the Rendering Templates
     *
     * @return string
     */
    public function getMappingXml()
    {
        return $mapping = simplexml_load_file(Mage::getModuleDir('etc', 'ClipVilla_MorI') . DS . 'mapping.xml');
    }

    /**
     * returns File + Path of the Video-Sitemap
     *
     * @return string
     */
    public function getSitemapVideo()
    {
        return Mage::getBaseDir() . DS . 'media' . DS . 'clipvilla' . DS . 'sitemap_video.xml';
    }
}