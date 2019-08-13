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
class ClipVilla_MorI_Model_Observer_ProductEditForm
{

    /**
     * adds the active template name on the product edit page video producer tab
     *
     * @param Varien_Event_Observer $observer
     */
    public function addTemplateName(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('clipvilla_mori');

        $_product = Mage::registry('current_product');
        $templateId = $helper->getTemplateIdFromCategory($_product->getCategoryIds());
        if ($templateId == '') {
            $templateId = Mage::getStoreConfig('clipvilla/custom_data/default_template');
        }

        $productTemplateId = $_product->getClipvillaTemplate();
        if (empty($productTemplateId)) {
            $templateName = $helper->getTemplateName($templateId);
        } else {
            $templateName = $helper->getTemplateName($productTemplateId);
        }

        $form = $observer->getEvent()->getForm();
        foreach ($form->getElements() as $element) {
            // ID is different in each attribute set
            if ($element->getLegend() == 'ClipVilla Video Producer') {
                $element->setLegend('Active ClipVilla - Template: ' . $templateName);
            }
        }
    }

	/**
     * adds the render Video and Delete Video Buttons to the product edit page
     */
    public function addCustomButtons()
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {
            $productId = Mage::app()->getRequest()->getParam('id');
            $existingVideo = Mage::getModel('clipvilla_mori/videos')->load($productId, 'product_id');

			$this->addButton(
				'Render Video', 'If the Product has not been saved, rendering will not start!',
				'save_button', 'render', 'save'
			);

            if ($existingVideo->getStatus() == ClipVilla_MorI_Model_Videos::STATUS_EXISTS) {
                $this->addButton('Delete Video', 'Are you sure?', 'delete_button', 'delete', 'delete');
            }
        }
    }

    /**
     * adds custom Button to Product Edit Page
     *
     */
    public function addButton($label, $message, $origButtonName, $action, $cssClass)
    {
        if (Mage::getStoreConfig('clipvilla/configuration/activate')) {

            $layout = Mage::app()->getLayout();
            $productEditBlock = $layout->getBlock('product_edit');
            $origButton = $productEditBlock->getChild($origButtonName);

            // New Custom Button
            $newButton = $layout->createBlock('adminhtml/widget_button');
            $newButton->setLabel(Mage::helper('clipvilla_mori')->__($label));
            $newButton->setClass($cssClass);
            if ($cssClass == 'delete') {
                // prompts Javascript Message
                $newButton->setOnClick(
                    'confirmSetLocation(\'' . Mage::helper('clipvilla_mori')->__($message) . '\', \''
                    . $this->getButtonUrl($action) . '\')'
                );
            } else {
                // simple link
                $newButton->setOnClick('setLocation(\'' . $this->getButtonUrl($action) . '\')');
            }

            $container = $layout->createBlock('core/text_list', 'button_container.' . $action);
            $container->append($newButton);
            $container->append($origButton);
            $productEditBlock->setChild($origButtonName, $container);
        }
    }

    /**
     * Retrieve the URL for button click
     *
     * @return string
     */
    public function getButtonUrl($action)
    {
        return Mage::getModel('adminhtml/url')->getUrl(
            '*/video/' . $action, array(
                '_current'   => true,
                'back'       => 'edit',
                'tab'        => '{{tab_id}}',
                'active_tab' => null
            )
        );
    }

}