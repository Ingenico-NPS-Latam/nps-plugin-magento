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
 * Installment admin edit tabs
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Block_Adminhtml_Installment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('installment_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('installment')->__('Installment'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Sub1_Installment_Block_Adminhtml_Installment_Edit_Tabs
	 * @author Ultimate Module Creator
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_installment', array(
			'label'		=> Mage::helper('installment')->__('Installment'),
			'title'		=> Mage::helper('installment')->__('Installment'),
			'content' 	=> $this->getLayout()->createBlock('installment/adminhtml_installment_edit_tab_form')->toHtml(),
		));
		if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_installment', array(
				'label'		=> Mage::helper('installment')->__('Store views'),
				'title'		=> Mage::helper('installment')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('installment/adminhtml_installment_edit_tab_stores')->toHtml(),
			));
		}
		return parent::_beforeToHtml();
	}
}