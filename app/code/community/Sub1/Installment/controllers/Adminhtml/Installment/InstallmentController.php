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
 * Installment admin controller
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Adminhtml_Installment_InstallmentController extends Sub1_Installment_Controller_Adminhtml_Installment{
	/**
	 * init the installment
	 * @access protected
	 * @return Sub1_Installment_Model_Installment
	 */
	protected function _initInstallment(){
		$installmentId  = (int) $this->getRequest()->getParam('id');
		$installment	= Mage::getModel('installment/installment');
		if ($installmentId) {
			$installment->load($installmentId);
		}
		Mage::register('current_installment', $installment);
		return $installment;
	}
 	/**
	 * default action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('installment')->__('Installment'))
			 ->_title(Mage::helper('installment')->__('Installments'));
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit installment - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function editAction() {
		$installmentId	= $this->getRequest()->getParam('id');
		$installment  	= $this->_initInstallment();
		if ($installmentId && !$installment->getId()) {
			$this->_getSession()->addError(Mage::helper('installment')->__('This installment no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$installment->setData($data);
		}
		Mage::register('installment_data', $installment);
		$this->loadLayout();
		$this->_title(Mage::helper('installment')->__('Installment'))
			 ->_title(Mage::helper('installment')->__('Installments'));
		if ($installment->getId()){
			$this->_title($installment->getRate());
		}
		else{
			$this->_title(Mage::helper('installment')->__('Add installment'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new installment action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save installment - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('installment')) {
			try {
				$installment = $this->_initInstallment();
				$installment->addData($data);
				$installment->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('installment')->__('Installment was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $installment->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('There was a problem saving the installment.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('Unable to find installment to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete installment - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$installment = Mage::getModel('installment/installment');
				$installment->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('installment')->__('Installment was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('There was an error deleteing installment.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('Could not find installment to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete installment - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massDeleteAction() {
		$installmentIds = $this->getRequest()->getParam('installment');
		if(!is_array($installmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('Please select installments to delete.'));
		}
		else {
			try {
				foreach ($installmentIds as $installmentId) {
					$installment = Mage::getModel('installment/installment');
					$installment->setId($installmentId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('installment')->__('Total of %d installments were successfully deleted.', count($installmentIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('There was an error deleteing installments.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * mass status change - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massStatusAction(){
		$installmentIds = $this->getRequest()->getParam('installment');
		if(!is_array($installmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('Please select installments.'));
		} 
		else {
			try {
				foreach ($installmentIds as $installmentId) {
				$installment = Mage::getSingleton('installment/installment')->load($installmentId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d installments were successfully updated.', count($installmentIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('installment')->__('There was an error updating installments.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * export as csv - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportCsvAction(){
		$fileName   = 'installment.csv';
		$content	= $this->getLayout()->createBlock('installment/adminhtml_installment_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportExcelAction(){
		$fileName   = 'installment.xls';
		$content	= $this->getLayout()->createBlock('installment/adminhtml_installment_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportXmlAction(){
		$fileName   = 'installment.xml';
		$content	= $this->getLayout()->createBlock('installment/adminhtml_installment_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}