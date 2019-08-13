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
class ClipVilla_MorI_Model_System_Attribute_Source_Provider extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options = null;

	/**
     * returns the options for template select in category
     *
     * @param bool $withEmpty
     *
     * @return array|null
     */
    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
            foreach ($mapping->mapping as $map) {
                $this->_options[] = array(
                    'value' => $map->renderProjectId,
                    'label' => $map->templateName
                );
            }
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array('value' => '', 'label' => ''));
        }
        return $options;
    }

	/**
     * returns the label for the options (template id -> template name)
     *
     * @param int|string $value
     *
     * @return bool|string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned' => false,
            'default'  => null,
            'extra'    => null
        );

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type'] = 'int(10)';
            $column['is_null'] = true;
        } else {
            $column['type'] = Varien_Db_Ddl_Table::TYPE_SMALLINT;
            $column['length'] = 10;
            $column['nullable'] = true;
            $column['comment'] = $attributeCode . ' column';
        }

        return array($attributeCode => $column);
    }

    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}