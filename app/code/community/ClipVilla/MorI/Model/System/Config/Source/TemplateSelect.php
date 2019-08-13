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
class ClipVilla_MorI_Model_System_Config_Source_TemplateSelect
{

	/**
     * returns the options for the template select in module configuration
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
        foreach ($mapping->mapping as $map) {
            $options[] = array(
                'value' => $map->renderProjectId,
                'label' => $map->templateName
            );
        }

        return $options;
    }

}