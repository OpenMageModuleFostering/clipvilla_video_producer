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
class ClipVilla_MorI_Model_Observer_DefaultValues
{

    private $_product;

    /**
     * sets default values for product on product edit page
     *
     * @param Varien_Event_Observer $observer
     */
    public function setDefaultValues(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
            $this->_product = $observer->getProduct();

            $values = array(
                'name'     => array(
                    'productValue' => $this->_product->getClipvillaName(),
                    'configValue'  => $this->_product->getName(),
                    'setMethod'    => 'setClipvillaName'
                ),
                'uspOne'   => array(
                    'productValue' => $this->_product->getClipvillaUspOne(),
                    'configValue'  => Mage::getStoreConfig('clipvilla/custom_data/usp_one'),
                    'setMethod'    => 'setClipvillaUspOne'
                ),
                'uspTwo'   => array(
                    'productValue' => $this->_product->getClipvillaUspTwo(),
                    'configValue'  => Mage::getStoreConfig('clipvilla/custom_data/usp_two'),
                    'setMethod'    => 'setClipvillaUspTwo'
                ),
                'uspThree' => array(
                    'productValue' => $this->_product->getClipvillaUspThree(),
                    'configValue'  => Mage::getStoreConfig('clipvilla/custom_data/usp_three'),
                    'setMethod'    => 'setClipvillaUspThree'
                )
            );

            foreach ($values as $value) {
                if ($value['productValue'] == '') {
                    $this->_product->$value['setMethod']($value['configValue']);
                }
            }
        }
    }

    /**
     * sets the Attributes that have been left empty
     *
     * @param Varien_Event_Observer $observer
     */
    public function setEmptyAttributes(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        if ($product->getClipvillaName() == '') {
            $product->setClipvillaName($product->getName());
        }

    }

    /**
     * Compares Product-USP with Config-USP and deletes Product-USP if the values are equal
     *
     * @param Varien_Event_Observer $observer
     */
    public function deleteValues(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
            $helper = Mage::helper('clipvilla_mori');
            $this->_product = $observer->getProduct();
            $usps = array();
            for ($i = 1; $i !== 4; $i++) {
                $getMethod = 'getClipvillaUsp' . $helper->numberConverter($i);
                $usps[] = $this->_product->$getMethod();

                if ($this->_product->$getMethod() == Mage::getStoreConfig('clipvilla/custom_data/usp_' . strtolower($helper->numberConverter($i)))) {
					$setMethod = 'setClipvillaUsp' . $helper->numberConverter($i);
                    $this->_product->$setMethod('');
                }
            }
            Mage::register('usps', $usps);
        }
    }
}