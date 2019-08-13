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
class ClipVilla_MorI_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{

    private $moduleActive;

    protected function _construct()
    {
        $this->moduleActive = Mage::getStoreConfig('clipvilla/configuration/activate');
    }

	/**
     * returns the number of entries in the stack table, which is the rendering queue
     * 
     * @return bool
     */
    public function getStackCount()
    {
        if ($this->moduleActive) {
            return Mage::getResourceModel('clipvilla_mori/stack_collection')->getSize();
        } else {
            return false;
        }
    }

	
	/**
     * returns the last renered Video
     * 
     * @return array|bool
     */
    public function getLastRendered()
    {
        if ($this->moduleActive) {
            $lastRendered = Mage::getModel('clipvilla_mori/videos')->getCollection()
                ->addFieldToFilter('status', array('eq' => ClipVilla_MorI_Model_Videos::STATUS_EXISTS))
                ->setOrder('creation_date', 'DESC')
                ->getFirstItem();

            return array('name' => $lastRendered->getName(), 'id' => $lastRendered->getProductId());
        } else {
            return false;
        }
    }

}
