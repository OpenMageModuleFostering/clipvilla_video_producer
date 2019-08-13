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
class ClipVilla_MorI_Model_Videos extends Mage_Core_Model_Abstract
{

    const STATUS_EXISTS_NOT = 0;
    const STATUS_EXISTS     = 1;
    const STATUS_ERROR      = 2;
    const STATUS_LICENSE    = 3;
    const STATUS_CAPACITY    = 4;
    const USPONE    = 'setUspOne';
    const USPTWO    = 'setUspTwo';
    const USPTHREE  = 'setUspThree';
    const NAME      = 'setName';
    public $defaultTemplateId;

    protected function _construct()
    {
        $this->defaultTemplateId = Mage::getStoreConfig('clipvilla/custom_data/default_template');
        $this->_init('clipvilla_mori/videos');
    }

    /**
     * Checks if video relevant data has been changed
     *
     * @param $product
     * @param $images
     *
     * @return bool
     */
    public function needUpdate($product, $images)
    {
        $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
        foreach ($mapping as $map) {
            if ($map->renderProjectId == $this->getRenderProjectId()) {
                foreach ($map->textDZs as $textDz) {
                    foreach ($textDz as $param) {
                        $videoMethod = 'get' . (String)$param->name;
                        $productMethod = 'getClipvilla' . (String)$param->name;
                        if ($product->$productMethod() != $this->$videoMethod()) {
                            return true;
                        }
                    }
                }

                $imgCount = 0;
                foreach ($map->imageDZs as $imgDz) {
                    foreach ($imgDz as $param) {
                        $videoMethod = 'get' . (String)$param->name;
                        if ($images[$imgCount] != $this->$videoMethod()) {
                            return true;
                        }
                        $imgCount++;
                        if ($imgCount === count($images)) {
                            $imgCount = 0;
                        }
                    }
                }
            }
        }
		
		// sets RenderProjectId
		$templateId = $product->getClipvillaTemplate();
		if ($templateId == '') {
			$templateId = Mage::helper('clipvilla_mori')->getTemplateIdFromCategory($product->getCategoryIds());
			if ($templateId == '') {
				$templateId = Mage::getStoreConfig('clipvilla/custom_data/default_template');
			}
		}
		
        if ($templateId != $this->getRenderProjectId()) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the Product Attribute has been set and uses it if its set
     * if Product Attribute is not set the configuration Value is used
     *
     * @param $configAttribute
     * @param $productAttribute
     * @param $method
     */
    public function setVideoAttribute($configAttribute, $productAttribute, $method)
    {
        if (empty($productAttribute)) {
            $this->$method($configAttribute);
        } else {
            $this->$method($productAttribute);
        }
    }

    /**
     * Sets Images for Video
     *
     * @param $video
     *
     * @return mixed
     */
    public function setImages($images, $mapping, $product)
    {
        try {
            $imgCount = 0;
            foreach ($mapping->imageDZs as $imgDz) {
                foreach ($imgDz as $param) {
                    $method = 'set' . (String)$param->name;
                    if (empty($images)) {
                        $this->$method('');
                    } else {
                        $this->$method($images[$imgCount]);
                        $imgCount++;
                        if ($imgCount === count($images)) {
                            $imgCount = 0;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return;
        }
        return $this;
    }

    /**
     * returns multidimensional Text-Arrays corresponding to mapping
     *
     * @param $mapping
     *
     * @return array
     */
    public function getTextDZs($mapping)
    {
        $text = array();
        foreach ($mapping as $map) {
            if ($map->renderProjectId == $this->getRenderProjectId()) {
                foreach ($map->textDZs as $textDz) {
                    foreach ($textDz as $param) {
                        $method = 'get' . (String)$param->name;
                        $text[] = array(
                            'text'         => $this->$method(),
                            'dropZoneName' => 'dz' . (String)$param->dz
                        );
                    }
                }
            }
        }
        return $text;
    }

    /**
     * returns multidimensional Img-Arrays corresponding to mapping
     *
     * @param $mapping
     * @param $path
     *
     * @return array
     */
    public function getImgDZs($mapping)
    {
        $path = Mage::helper('clipvilla_mori/path');
        $img = array();
        foreach ($mapping as $map) {
            if ($map->renderProjectId == $this->getRenderProjectId()) {
                foreach ($map->imageDZs as $imgDz) {
                    foreach ($imgDz as $param) {
                        $method = 'get' . (String)$param->name;
                        $imgFile = $this->$method();
                        $img[] = array(
                            'dropZoneName'    => 'dz' . (String)$param->dz,
                            'renderImageType' => 'OTHER',
                            'fileName'        => basename($imgFile),
                            'imageData'       => file_get_contents($path->getProductImageDir($imgFile))
                        );
                    }
                }
            }
        }
        return $img;
    }


    /**
     * Builds array for SOAP-Request
     *
     * @param $renderProjectId
     *
     * @return array
     */
    public function getSoapVar($renderProjectId)
    {
        $path = Mage::helper('clipvilla_mori/path');
        $mapping = $path->getMappingXml();
        $this->setShopName(Mage::getStoreConfig('clipvilla/custom_data/shop'));

        $videoData = array(
            'RenderRequestMtom' => array(
                'renderProjectId' => $renderProjectId,
                'licenseKey' => Mage::getStoreConfig('clipvilla/configuration/license_key'),

                'renderTextList'  => array(
                    'renderText' => $this->getTextDZs($mapping)
                ),
                'renderImageList' => array(
                    'renderImageMtom' => $this->getImgDZs($mapping)
                )
            )
        );

        return $videoData;
    }

    /**
     * Saves Video Data for all Products (must have at least 1 Image and be visible)
     *
     * @param string $productIds
     */
    public function saveForAllProducts($productIds = array())
    {
        $helper = Mage::helper('clipvilla_mori');
        try {

            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('visibility', array('neq' => 1));

            if (!empty($productIds)) {
                $products->addFieldToFilter('entity_id', $productIds);
            }

            $products->joinTable('clipvilla_mori/videos', 'product_id=entity_id', array(
                'videos_video_id' => 'video_id'
                ), null, 'left'
            )
            ->joinTable('clipvilla_mori/stack', 'video_id=videos_video_id', array(
                     'stack_id'     => 'stack_id',
                     'stack_status' => 'status'
                ), null, 'left'
            );

            $configUspOne = Mage::getStoreConfig('clipvilla/custom_data/usp_one');
            $configUspTwo = Mage::getStoreConfig('clipvilla/custom_data/usp_two');
            $configUspThree = Mage::getStoreConfig('clipvilla/custom_data/usp_three');
            $stack = Mage::getModel('clipvilla_mori/stack');

            foreach ($products as $product) {
                $product->getResource()->getAttribute('media_gallery')
                    ->getBackend()
                    ->afterLoad($product);
                $images = $product->getData('media_gallery');
                $images = $images['images'];
                $images = $helper->getImageFiles($images);

                if (!empty($images) && $product->getStackStatus() == ClipVilla_MorI_Model_Stack::STATUS_QUEUE) {
                    $productVideoId = $product->getVideosVideoId();
                    if ($productVideoId !== null) {
                        $this->setVideoId($productVideoId);
                    }
                    $this->setProductId($product->getEntityId());
                    $this->setStatus(self::STATUS_EXISTS_NOT);
                    $this->setCreationDate(null);

                    $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
                    $this->setProductId($product->getId());

                    // sets RenderProjectId
					$templateId = $product->getClipvillaTemplate();
                    if ($templateId == '') {
						$templateId = $helper->getTemplateIdFromCategory($product->getCategoryIds());
						if ($templateId == '') {
							$templateId = Mage::getStoreConfig('clipvilla/custom_data/default_template');
						}
                    }
					
                    $this->setRenderProjectId($templateId);
                    $this->setVideoAttribute(
                        $product->getName(), $product->getClipvillaName(),
                        self::NAME
                    );
                    $this->setVideoAttribute(
                        $configUspOne, $product->getClipvillaUspOne(),
                        self::USPONE
                    );
                    $this->setVideoAttribute(
                        $configUspTwo, $product->getClipvillaUspTwo(),
                        self::USPTWO
                    );
                    $this->setVideoAttribute(
                        $configUspThree, $product->getClipvillaUspThree(),
                        self::USPTHREE
                    );

                    foreach ($mapping as $map) {
                        if ($map->renderProjectId == $this->getRenderProjectId()) {
                            $this->setImages($images, $map, $product);
                        }
                    }

                    $this->save();

                    if ($product->getStackId() == null && $this->getImageOne() !== '') {
                        $stack->setVideoId($this->getVideoId());
                        $stack->setPriority(0);
                        $stack->setStatus(ClipVilla_MorI_Model_Stack::STATUS_QUEUE);
                        $stack->save();
                    }

                    $this->unsetData();
                    $stack->unsetData();

                }
            }

        } catch (Exception $e) {
            Mage::log($e, null, 'clipvilla.log', true);
        }
    }

}