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
class ClipVilla_MorI_Block_Adminhtml_ManageVideos_Renderer_Popup
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * makes the "view" clickable and opens the new page in an Pop-Up
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $video_id = $row->getData($this->getColumn()->getIndex());
        $url = Mage::helper('adminhtml')->getUrl('*/manageVideos/popup', array('video_id' => $video_id));
        $name = $row->getData('name');
        $name = preg_replace("/'/", "\\'", $name);
        $name = preg_replace("/\"/", "&quot", $name);

        if ($row->getData('status') == 1) {
            return '<div style ="color:#ea7601;text-decoration:underline;cursor:pointer;" onclick="openPopup(\''.$url.'\', \''.$name.'\')">View</div>';
        }
    }

}