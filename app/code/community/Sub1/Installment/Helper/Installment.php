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
 * Installment helper
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
class Sub1_Installment_Helper_Installment extends Mage_Core_Helper_Abstract{
	/**
	 * check if breadcrumbs can be used
	 * @access public
	 * @return bool
	 * @author Ultimate Module Creator
	 */
	public function getUseBreadcrumbs(){
		return Mage::getStoreConfigFlag('installment/installment/breadcrumbs');
	}
}