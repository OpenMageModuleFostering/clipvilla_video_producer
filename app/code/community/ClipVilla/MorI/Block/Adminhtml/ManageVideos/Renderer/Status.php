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
class ClipVilla_MorI_Block_Adminhtml_ManageVideos_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * changes the video Status (int) to something readable
     * adds an colored background depending on the status
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('clipvilla_mori/path');
        $value = $row->getStatus();
        $stackStatus = $row->getStackStatus();

        switch ($value) {
            case ClipVilla_MorI_Model_Videos::STATUS_EXISTS_NOT:

                switch ($stackStatus) {
                    case ClipVilla_MorI_Model_Stack::STATUS_QUEUE:
                        $value = $helper->__('Video in queue');
                        $backgroundColor = '#FF8000';
                        break;
                    case ClipVilla_MorI_Model_Stack::STATUS_DOWNLOAD:
                        $value = $helper->__('Downloading video');
                        $backgroundColor = '#D7DF01';
                        break;
                    case ClipVilla_MorI_Model_Stack::STATUS_RENDERING:
                        $value = $helper->__('Video rendering');
                        $backgroundColor = '#D7DF01';
                        break;
                }

                break;
            case ClipVilla_MorI_Model_Videos::STATUS_EXISTS:
                $backgroundColor = '#04B431';
                $value = $helper->__('Video Exists');
                break;
            case ClipVilla_MorI_Model_Videos::STATUS_ERROR:
                $backgroundColor = '#DF0101';
                $value = $helper->__('Error');
                break;
            case ClipVilla_MorI_Model_Videos::STATUS_LICENSE:
                $backgroundColor = '#DF0101';
                $value = $helper->__('Invalid Key');
                break;
			case ClipVilla_MorI_Model_Videos::STATUS_CAPACITY:
                $backgroundColor = '#DF0101';
                $value = $helper->__('Capacity exceeded');
                break;
        }

        return '<div style="color:#FFF;font-weight:bold;background:' . $backgroundColor . ';border-radius:8px;width:100%">' . $value . '</div>';
    }
}
