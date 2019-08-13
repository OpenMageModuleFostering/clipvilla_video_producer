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

// Marks Indexer as Ready on Module Installation
Mage::getSingleton('index/indexer')
    ->getProcessByCode('clipvilla_indexer')
    ->changeStatus(Mage_Index_Model_Process::STATUS_PENDING);

// Opens in the ClipVilla Information Group in Magento Backend on default for all admin users
$adminUsers = Mage::getModel('admin/user')->getCollection()->load();
foreach ($adminUsers as $adminUser) {
    $extra = $adminUser->getExtra();
    if (!is_array($extra)) {
        $extra = array();
    }
    if (!isset($extra['configState'])) {
        $extra['configState'] = array();
    }
    $extra['configState']['clipvilla_info'] = 1;
    $adminUser->saveExtra($extra);
}

// generates reset counter for stuck entries in the rendering queue
Mage::getModel('core/config')->saveConfig('clipvilla/rendering/reset', 0);
