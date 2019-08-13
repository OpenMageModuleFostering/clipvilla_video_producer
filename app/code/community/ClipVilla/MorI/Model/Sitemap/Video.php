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
class ClipVilla_MorI_Model_Sitemap_Video extends Mage_Core_Model_Abstract
{
    const XMLNS       = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    const XMLNS_VIDEO = 'http://www.google.com/schemas/sitemap-video/1.1';

    private $path;
    private $filePath;

    public function _construct()
    {
        $this->path = Mage::helper('clipvilla_mori/path');
        $this->filePath = $this->path->getSitemapVideo();
    }
	
    /**
     * generates an xml-video-sitemap
     *
     * @return mixed
     */
    public function createSitemap()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('meta_title')
            ->addAttributeToSelect('meta_description')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('short_description');
        $collection->getSelect()->joinLeft(
            array('video' => "clipvilla_videos"),
            "e.entity_id = video.product_id",
            array('video.status', 'video.creation_date', 'image_one', 'render_project_id')
        );
        $collection->getSelect()->where('video.status = 1');

        #echo $collection->getSelect()->__toString();die();

        $xml = new SimpleXMLElement('<urlset xmlns="' . self::XMLNS . '" xmlns:video="' . self::XMLNS_VIDEO . '" />');
        foreach ($collection as $item) {
            Mage::app()->setCurrentStore(1);
            $xml = $this->addVideo($xml, $item);
        }

        try {
            $xml->asXML($this->filePath); // save
        } catch (Exception $e) {
            Mage::log($e, null, 'clipvilla.log', true);
            return;
        }
        return $this->filePath;
    }

    /**
     * adds an video entry to the video-sitemap
     *
     * @param $xml
     * @param $item
     *
     * @return mixed
     */
    public function addVideo($xml, $item)
    {
        $duration = Mage::helper('clipvilla_mori')->getVideoDuration($item->getRenderProjectId());

        $date = Mage::getModel('core/date')->timestamp(strtotime($item->getCreationDate()));
        $date = date('Y-m-d', $date);

        $metaDescription = $item->getMetaDescription();
        if (empty($metaDescription)) {
            $metaDescription = $item->getShortDescription();
        }
        $metaTitle = $item->getMetaTitle();
        if (empty($metaTitle)) {
            $metaTitle = $item->getName();
        }
        $ImageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media' . DS . 'catalog' . DS . 'product' . $item->getImageOne();
        $videoUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media' . DS . 'clipvilla' . DS . 'videos' . DS . $item->getId() . '.mp4';

        $urlNode = $xml->addChild('url');
        $urlNode->addChild('loc', $item->getProductUrl());
        // required Params
        $videoNode = $urlNode->addChild('video','', self::XMLNS_VIDEO);
        $videoNode->addChild('thumbnail_loc', $ImageUrl, self::XMLNS_VIDEO);
        $videoNode->addChild('title', $metaTitle, self::XMLNS_VIDEO);
        $videoNode->addChild('description', $metaDescription, self::XMLNS_VIDEO);
        $videoNode->addChild('content_loc', $videoUrl, self::XMLNS_VIDEO);
        // optional Params
        $videoNode->addChild('duration', $duration, self::XMLNS_VIDEO);
        $videoNode->addChild('publication_date', $date, self::XMLNS_VIDEO);
        $videoNode->addChild('family_friendly', 'yes', self::XMLNS_VIDEO);

        return $xml;
    }

}