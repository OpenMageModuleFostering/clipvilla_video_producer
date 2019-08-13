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
class ClipVilla_MorI_Model_Observer_MassAction
{

    /**
     * Adds an mass-action to product grid for video rendering
     *
     * @param $observer
     */
    public function addRenderAll($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'catalog_product'
        ) {
            $block->addItem(
                'newmodule', array(
                    'label' => 'Render Video',
                    'url'   => Mage::getModel('adminhtml/url')->getUrl(
                        '*/video/massSaveAll', array('_current' => true)
                    )
                )
            );
        }
    }

}