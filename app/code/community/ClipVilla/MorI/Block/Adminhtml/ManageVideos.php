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
class ClipVilla_MorI_Block_Adminhtml_ManageVideos extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_manageVideos';
        $this->_blockGroup = 'clipvilla_mori';
        $this->_headerText = Mage::helper('clipvilla_mori')->__('Manage Videos');

        $this->_addButtonLabel = Mage::helper('clipvilla_mori')->__('Download Video Sitemap');
        parent::__construct();
    }

}