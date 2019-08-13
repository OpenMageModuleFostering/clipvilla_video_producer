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
class ClipVilla_MorI_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{

    protected $_matchedEntities = array(
            'test_entity' => array(
                Mage_Index_Model_Event::TYPE_SAVE
            )
        );

    protected $_registered = false;
    protected $_processed = false;

    /**
     * returns Indexer Name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('core')->__('Render Videos');
    }

    /**
     * returns Indexer Description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('core')->__('Generate Videos for all visible products');
    }

    /**
     * registers event
     *
     * @param Mage_Index_Model_Event $event
     *
     * @return $this
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $this->_registered = true;
        return $this;
    }

    /**
     * renders video for visibile product on save
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if (!$this->_processed && Mage::getStoreConfig('clipvilla/configuration/activate')) {
            $this->_processed = true;
            $product = $event->getDataObject();

            $video = Mage::getModel('clipvilla_mori/videos')->load($product->getId(), 'product_id');
            $stack = Mage::getModel('clipvilla_mori/stack')->load($video->getVideoId(), 'video_id');

            if ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
                if ($video->getStatus() == 0 && $product->getVisibility() != 1) {
                    $stackData = $stack->getData();
                    if (empty($stackData) && $video->getImageOne() !== '') {
                        $stack = Mage::getModel('clipvilla_mori/stack');
                        $stack->setVideoId($video->getVideoId());
                        $stack->setPriority(0);
                        $stack->setStatus(ClipVilla_MorI_Model_Stack::STATUS_QUEUE);
                        $stack->save();
                    }
                }
            }
        }
        $this->_processed = false;
    }

	/**
     * returns if the event matches
     * 
     * @param Mage_Index_Model_Event $event
     *
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        return Mage_Catalog_Model_Product::ENTITY == $event->getEntity();
    }

    /**
     * renders Videos for all visible products
     */
    public function reindexAll()
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
            Mage::getModel('clipvilla_mori/videos')->saveForAllProducts();
        }
    }
}