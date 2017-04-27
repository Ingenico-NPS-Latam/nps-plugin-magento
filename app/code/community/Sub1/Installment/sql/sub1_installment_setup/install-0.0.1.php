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
 * Installment module install script
 *
 * @category	Sub1
 * @package		Sub1_Installment
 * @author Ultimate Module Creator
 */
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('installment/installment'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Installment ID')
	->addColumn('cc_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'cc_type')

	->addColumn('qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
		'unsigned'  => true,
		), 'qty')

	->addColumn('rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
		'nullable'  => false,
		), 'rate')

	->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		), 'Status')
        
	->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'country')
        
	->addColumn('currency', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'currency')

	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Installment Creation Time')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Installment Modification Time')
	->setComment('Installment Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
	->newTable($this->getTable('installment/installment_store'))
	->addColumn('installment_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
		), 'Installment ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Store ID')
	->addIndex($this->getIdxName('installment/installment_store', array('store_id')), array('store_id'))
	->addForeignKey($this->getFkName('installment/installment_store', 'installment_id', 'installment/installment', 'entity_id'), 'installment_id', $this->getTable('installment/installment'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('installment/installment_store', 'store_id', 'core/store', 'store_id'), 'store_id', $this->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Installments To Store Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();