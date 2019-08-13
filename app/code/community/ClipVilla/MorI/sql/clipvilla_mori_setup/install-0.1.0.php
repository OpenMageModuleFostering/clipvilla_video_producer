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
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

// Product Attribute Group
$setup->addAttributeGroup('catalog_product', 'Default', 'ClipVilla Video Producer', 1000);

// Product Attributes
$templateData = array(
    'attribute_set' => 'Default',
    'group'         => 'ClipVilla Video Producer',
    'label'         => 'Video Template',
    'visible'       => true,
    'type'          => 'int',
    'input'         => 'select',
    'source'        => 'clipvilla_mori/system_attribute_source_provider',
    'system'        => true,
    'required'      => false,
    'user_defined'  => true,
);
$setup->addAttribute('catalog_product', 'clipvilla_template', $templateData);


$nameData = array(
    'attribute_set' => 'Default',
    'group'         => 'ClipVilla Video Producer',
    'label'         => 'Product Name',
    'visible'       => true,
    'type'          => Varien_Db_Ddl_Table::TYPE_TEXT,
    'input'         => 'text',
    'system'        => true,
    'required'      => false,
    'user_defined'  => true,
);
$setup->addAttribute('catalog_product', 'clipvilla_name', $nameData);

$uspDataOne = array(
    'attribute_set' => 'Default',
    'group'         => 'ClipVilla Video Producer',
    'label'         => 'Video Text 1',
    'visible'       => true,
    'type'          => Varien_Db_Ddl_Table::TYPE_TEXT,
    'input'         => 'text',
    'system'        => true,
    'required'      => false,
    'user_defined'  => true,
);
$setup->addAttribute('catalog_product', 'clipvilla_usp_one', $uspDataOne);

$uspDataTwo = array(
    'attribute_set' => 'Default',
    'group'         => 'ClipVilla Video Producer',
    'label'         => 'Video Text 2',
    'visible'       => true,
    'type'          => Varien_Db_Ddl_Table::TYPE_TEXT,
    'input'         => 'text',
    'system'        => true,
    'required'      => false,
    'user_defined'  => true,
);
$setup->addAttribute('catalog_product', 'clipvilla_usp_two', $uspDataTwo);

$uspDataThree = array(
    'attribute_set' => 'Default',
    'group'         => 'ClipVilla Video Producer',
    'label'         => 'Video Text 3',
    'visible'       => true,
    'type'          => Varien_Db_Ddl_Table::TYPE_TEXT,
    'input'         => 'text',
    'system'        => true,
    'required'      => false,
    'user_defined'  => true,
);
$setup->addAttribute('catalog_product', 'clipvilla_usp_three', $uspDataThree);

// Template select in category-page
$template = array (
    'group'             => 'General Information',
    'type'              => 'int',
    'backend'           => '',
    'frontend_input'    => '',
    'frontend'          => '',
    'label'             => 'Video Template',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'clipvilla_mori/system_attribute_source_provider',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'          => false,
    'default'           => '',
);
$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'clipvilla_template', $template);

$installer->endSetup();