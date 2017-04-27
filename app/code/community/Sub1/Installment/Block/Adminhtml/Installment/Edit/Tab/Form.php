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
 * Installment edit form tab
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Block_Adminhtml_Installment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return Installment_Installment_Block_Adminhtml_Installment_Edit_Tab_Form
	 * @author Ultimate Module Creator
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('installment_');
		$form->setFieldNameSuffix('installment');
		$this->setForm($form);
		$fieldset = $form->addFieldset('installment_form', array('legend'=>Mage::helper('installment')->__('Installment')));

                $fieldset->addField('cc_type', 'select', array(
                        'name'  => 'cc_type',
                        'label' => Mage::helper('installment')->__('cc_type'),
                        'title' => Mage::helper('installment')->__('cc_type'),
                        'required' => true,
                        // 'note'	=> $this->__('cc_type'),
                        'values' => Mage::getSingleton('payment/config')->getCcTypes()));
                
		$fieldset->addField('qty', 'text', array(
			'label' => Mage::helper('installment')->__('qty'),
			'name'  => 'qty',
			// 'note'	=> $this->__('qty'),
			'required'  => true,
			'class' => 'required-entry',

		));

		$fieldset->addField('rate', 'text', array(
			'label' => Mage::helper('installment')->__('rate'),
			'name'  => 'rate',
			// 'note'	=> $this->__('rate'),
			'required'  => true,
			'class' => 'required-entry',

		));
		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('installment')->__('Status'),
			'name'  => 'status',
			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('installment')->__('Enabled'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('installment')->__('Disabled'),
				),
			),
		));
                
                
		$fieldset->addField('country', 'select', array(
			'label' => Mage::helper('installment')->__('country'),
			'name'  => 'country',
			// 'note'	=> $this->__('country'),
			'required' => true,
			'values'=> Mage::getResourceModel('directory/country_collection')->load()->toOptionArray(),
		));

		$fieldset->addField('currency', 'select', array(
			'label' => Mage::helper('installment')->__('currency'),
			'name'  => 'currency',
			// 'note'	=> $this->__('currency'),
			'required'  => true,
			'values' => Mage::app()->getLocale()->getOptionCurrencies(),
		));                
                
                
		if (Mage::app()->isSingleStoreMode()){
			$fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_installment')->setStoreId(Mage::app()->getStore(true)->getId());
		}
		if (Mage::getSingleton('adminhtml/session')->getInstallmentData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getInstallmentData());
			Mage::getSingleton('adminhtml/session')->setInstallmentData(null);
		}
		elseif (Mage::registry('current_installment')){
			$form->setValues(Mage::registry('current_installment')->getData());
		}
		return parent::_prepareForm();
	}
}