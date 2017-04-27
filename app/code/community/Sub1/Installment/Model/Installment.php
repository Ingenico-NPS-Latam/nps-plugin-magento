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
 * Installment model
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Model_Installment extends Mage_Core_Model_Abstract{
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY= 'installment_installment';
	const CACHE_TAG = 'installment_installment';
	/**
	 * Prefix of model events names
	 * @var string
	 */
	protected $_eventPrefix = 'installment_installment';
	
	/**
	 * Parameter name in event
	 * @var string
	 */
	protected $_eventObject = 'installment';
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('installment/installment');
	}
	/**
	 * before save installment
	 * @access protected
	 * @return Sub1_Installment_Model_Installment
	 * @author Ultimate Module Creator
	 */
	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
			$this->setCreatedAt($now);
		}
		$this->setUpdatedAt($now);
		return $this;
	}
	/**
	 * save installment relation
	 * @access public
	 * @return Sub1_Installment_Model_Installment
	 * @author Ultimate Module Creator
	 */
	protected function _afterSave() {
		return parent::_afterSave();
	}
}