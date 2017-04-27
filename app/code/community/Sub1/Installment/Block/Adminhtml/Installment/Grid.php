<?php
/**
 * Sub1_Installment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Sub1
 * @package		Sub1_Installment
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Installment admin grid block
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Block_Adminhtml_Installment_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('installmentGrid');
		$this->setDefaultSort('cc_type');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 * @return Sub1_Installment_Block_Adminhtml_Installment_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('installment/installment')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 * @return Sub1_Installment_Block_Adminhtml_Installment_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareColumns(){
                /*
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('installment')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));
                 * 
                 */
		$this->addColumn('cc_type', array(
			'header'=> Mage::helper('installment')->__('cc_type'),
			'index' => 'cc_type',
			'type' => 'options',
                        'options' => Mage::getSingleton('payment/config')->getCcTypes(),

		));
		$this->addColumn('qty', array(
			'header'=> Mage::helper('installment')->__('qty'),
			'index' => 'qty',
			'type'	 	=> 'text',

		));
		$this->addColumn('rate', array(
			'header'=> Mage::helper('installment')->__('rate'),
			'index' => 'rate',
			'type'	 	=> 'text',

		));
		$this->addColumn('status', array(
			'header'	=> Mage::helper('installment')->__('Status'),
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> array(
				'1' => Mage::helper('installment')->__('Enabled'),
				'0' => Mage::helper('installment')->__('Disabled'),
			)
		));
                
		$this->addColumn('country', array(
			'header'	=> Mage::helper('installment')->__('Country'),
			'index'		=> 'country',
			'type'		=> 'text',
		));
                
		$this->addColumn('currency', array(
			'header'	=> Mage::helper('installment')->__('Currency'),
			'index'		=> 'currency',
			'type'		=> 'text',
		));                
                
                
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'=> Mage::helper('installment')->__('Store Views'),
				'index' => 'store_id',
				'type'  => 'store',
				'store_all' => true,
				'store_view'=> true,
				'sortable'  => false,
				'filter_condition_callback'=> array($this, '_filterStoreCondition'),
			));
		}
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('installment')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('installment')->__('Updated at'),
			'index' 	=> 'updated_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('installment')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('installment')->__('Edit'),
						'url'   => array('base'=> '*/*/edit'),
						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('installment')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('installment')->__('Excel'));
		$this->addExportType('*/*/exportXml', Mage::helper('installment')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 * @return Sub1_Installment_Block_Adminhtml_Installment_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('installment');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('installment')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('installment')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('installment')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'status' => array(
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('installment')->__('Status'),
						'values' => array(
								'1' => Mage::helper('installment')->__('Enabled'),
								'0' => Mage::helper('installment')->__('Disabled'),
						)
				)
			)
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 * @param Sub1_Installment_Model_Installment
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	/**
	 * get the grid url
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	/**
	 * after collection load
	 * @access protected
	 * @return Sub1_Installment_Block_Adminhtml_Installment_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	/**
	 * filter store column
	 * @access protected
	 * @param Sub1_Installment_Model_Resource_Installment_Collection $collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Sub1_Installment_Block_Adminhtml_Installment_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
        	return;
		}
		$collection->addStoreFilter($value);
		return $this;
    }
}