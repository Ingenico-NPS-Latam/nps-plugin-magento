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
 * Installment admin edit block
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Block_Adminhtml_Installment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	/**
	 * constuctor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->_blockGroup = 'installment';
		$this->_controller = 'adminhtml_installment';
		$this->_updateButton('save', 'label', Mage::helper('installment')->__('Save Installment'));
		$this->_updateButton('delete', 'label', Mage::helper('installment')->__('Delete Installment'));
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('installment')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	/**
	 * get the edit form header
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getHeaderText(){
		if( Mage::registry('installment_data') && Mage::registry('installment_data')->getId() ) {
			return Mage::helper('installment')->__("Edit Installment '%s'", $this->htmlEscape(Mage::registry('installment_data')->getRate()));
		} 
		else {
			return Mage::helper('installment')->__('Add Installment');
		}
	}
}