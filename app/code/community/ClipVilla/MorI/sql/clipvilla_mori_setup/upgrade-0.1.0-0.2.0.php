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
$installer->startSetup();

if ($installer->getConnection()->isTableExists('clipvilla_videos') != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('clipvilla_mori/videos'))
        ->addColumn(
            'video_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), 'Id'
        )
        ->addColumn(
            'product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Product ID'
        )
        ->addColumn(
            'render_project_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Product ID'
        )
        ->addColumn(
            'name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Render Project ID'
        )
        ->addColumn(
            'usp_one', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'USP One'
        )
        ->addColumn(
            'usp_two', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'USP two'
        )
        ->addColumn(
            'usp_three', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'USP Three'
        )
        ->addColumn(
            'image_one', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Product Image'
        )
        ->addColumn(
            'image_two', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Product Image'
        )
        ->addColumn(
            'image_three', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Product Image'
        )
        ->addColumn(
            'image_four', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Product Image'
        )
        ->addColumn(
            'creation_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                'nullable' => true,
            ), 'Video Creation Date'
        )
        ->addColumn(
            'status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Video Status'
        );

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();