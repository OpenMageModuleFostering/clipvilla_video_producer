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

if ($installer->getConnection()->isTableExists('clipvilla_stack') != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('clipvilla_mori/stack'))
        ->addColumn(
            'stack_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), 'Id'
        )
        ->addColumn(
            'video_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), ' Video ID'
        )
        ->addColumn(
            'priority', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Priority'
        )
        ->addColumn(
            'status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Video Status'
        )
        ->addColumn(
            'render_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Render Id'
        );
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();