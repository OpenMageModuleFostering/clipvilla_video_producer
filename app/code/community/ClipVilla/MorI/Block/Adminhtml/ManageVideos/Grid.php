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
class ClipVilla_MorI_Block_Adminhtml_ManageVideos_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('manageVideosId');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

	/**
     * return the collection for the manage videos grid
     * 
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('clipvilla_mori/videos')->getCollection();

        $collection->getSelect()->joinLeft(array('stack' => 'clipvilla_stack'),
            'main_table.video_id = stack.video_id', array(
                 'stack_status' => 'status',
            )
        );

        $collection->addFieldToFilter(
            array('main_table.status', 'stack.status'),
            array(
                 array('neq' => 0), // main_table.status != 0
                 array('notnull' => true) // stack.status IS NOT NULL
            )
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

	/**
     * adds columns to the grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => Mage::helper('clipvilla_mori')->__('Product ID'),
            'align'  =>'right',
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'product_id'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('clipvilla_mori')->__('Video Name'),
            'width'  => '900px',
            'index'  => 'name'
        ));

        $this->addColumn('video_template', array(
            'header'   => Mage::helper('clipvilla_mori')->__('Video Template'),
            'type'     => 'options',
            'options'  => $this->getTemplateNameOptions(),
            'renderer' => 'ClipVilla_MorI_Block_Adminhtml_ManageVideos_Renderer_TemplateName',
            'index'    => 'render_project_id'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('clipvilla_mori')->__('Created At'),
            'width'  => '180px',
            'align'  => 'center',
            'type'   => 'datetime',
            'index'  => 'creation_date'
        ));

        $this->addColumn('status', array(
            'header'   => Mage::helper('clipvilla_mori')->__('Status'),
            'type'     => 'options',
            'width'    => '150px',
            'align'    => 'center',
            'options'  => $this->getStatusOptions(),
            'renderer' => 'ClipVilla_MorI_Block_Adminhtml_ManageVideos_Renderer_Status',
            'index'    => 'main_table.status'
        ));

        $this->addColumn('video_popup', array(
            'header'   => Mage::helper('sales')->__('Show Video'),
            'width'    => '5%',
            'align'    => 'center',
            'renderer' => 'ClipVilla_MorI_Block_Adminhtml_ManageVideos_Renderer_Popup',
            'filter'   => false,
            'sortable' => false,
            'index'    => 'video_id'
        ));

        return parent::_prepareColumns();
    }

	/**
     * adds the massaction to delete Videos
     * 
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('video_id');
        $this->getMassactionBlock()->setFormFieldName('video_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_manageVideos', array(
            'label'   => Mage::helper('clipvilla_mori')->__('Delete'),
            'url'     => Mage::getModel('adminhtml/url')->getUrl('*/manageVideos/massDeleteAll'),
            'confirm' => Mage::helper('clipvilla_mori')->__('Are you sure?')
        ));
        return $this;
    }

	/**
     * returns the name to all templates from the mapping.xml
     *
     * @return array
     */
    public function getTemplateNameOptions()
    {
        $templateNames = array();
        $mapping = Mage::helper('clipvilla_mori/path')->getMappingXml();
        foreach ($mapping->mapping as $template) {
            $templateNames[(String)$template->renderProjectId] = (String)$template->templateName;
        }

        return $templateNames;
    }

	/**
     * returns the Status of the Video
     *
     * @return array
     */
    public function getStatusOptions()
    {
        $status = array(
            ClipVilla_MorI_Model_Videos::STATUS_EXISTS => 'Video exists',
            ClipVilla_MorI_Model_Videos::STATUS_EXISTS_NOT => 'Video in queue',
            ClipVilla_MorI_Model_Videos::STATUS_ERROR => 'Error',
            ClipVilla_MorI_Model_Videos::STATUS_LICENSE => 'Invalid Key',
            ClipVilla_MorI_Model_Videos::STATUS_CAPACITY => 'Capacity exceeded'
        );

        return $status;
    }
			

}