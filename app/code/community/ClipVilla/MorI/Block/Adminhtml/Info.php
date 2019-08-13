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
class ClipVilla_MorI_Block_Adminhtml_Info extends Mage_Core_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{

	/**
	 * returns an phtml with the extension Information
	 *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setTemplate('clipvilla' . DS . 'information.phtml');
        return $this->_toHtml();
    }

	/**
	 * returns the logo file for the information tab in module configuration
	 *
	 *@ return string
	 */
    public function getLogo()
    {
        return Mage::getBaseUrl('media') . 'clipvilla' . DS . 'videos' . DS . 'clipvillalogo.png';
    }
}