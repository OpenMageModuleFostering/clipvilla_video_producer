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
class ClipVilla_MorI_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Returns the Corresponding Word to a number
     *
     * @param $count
     *
     * @return string
     */
    public function numberConverter($count)
    {
        switch ($count) {
            case 1:
                return 'One';
                break;
            case 2:
                return 'Two';
                break;
            case 3:
                return 'Three';
                break;
            case 4:
                return 'Four';
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * returns array with all product images
     *
     *
     * @return array
     */
    public function getImageFiles($images)
    {
        if ($images != '') {
            $imagesFiles = array();
            foreach ($images as $image) {
                if (!$image['disabled']) {
                    if (!array_key_exists('removed', $image)) {
                        $imagesFiles[] = $image['file'];
                    } elseif (!$image['removed']) {
                        $imagesFiles[] = $image['file'];
                    }
                }
            }
            return $imagesFiles;
        }
    }

    /**
     * returns Templatename from mapping.xml
     *
     * @param $templateId
     *
     * @return mixed
     */
    public function getTemplateName($templateId)
    {
        $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
        foreach ($mapping->mapping as $template) {
            if ($template->renderProjectId == $templateId) {
                return $template->templateName;
            }
        }
    }

    /**
     * returns Video duration from mapping.xml
     *
     * @param $templateId
     *
     * @return mixed
     */
    public function getVideoDuration($templateId)
    {
        $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
        foreach ($mapping->mapping as $template) {
            if ($template->renderProjectId == $templateId) {
                return $template->duration;
            }
        }
    }

	/**
     * returns all template ids from all categories
     *
     * @param $catIds
     *
     * @return string
     */
    public function getTemplateIdFromCategory($catIds)
    {
        if(empty($catIds)){
            return '';
        }
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('clipvilla_template')
            ->addAttributeToFilter('entity_id', $catIds)
            ->addIsActiveFilter();

        foreach ($categories as $cat) {
            $id = $cat->getClipvillaTemplate();
            if ($id != '') {
                return $id;
            }
        }
        return '';
    }


}