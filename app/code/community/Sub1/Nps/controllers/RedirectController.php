<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


require_once(Mage::getBaseDir('lib') . '/Sub1/psp_client.php');

/**
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sub1_Nps_RedirectController extends Mage_Core_Controller_Front_Action
{
    /**
     * When a customer chooses Google Checkout on Checkout/Payment page
     *
     */
    public function redirectAction()
    {

        /**
         * obtengo la ultima orden
         */
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());

        /**
         * creo un bloque para la vista
         */
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','nps',array('template' => 'nps/redirect.phtml'));

        $pspSession = Mage::getSingleton('core/session')->getPspSession();
        if(!isset($pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]) || !count($pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]) || empty($pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()])) {

            /**
             * primero pido el transaction id a psp
             */
            switch( Mage::getModel('nps/nps')->getConfigData('payment_action_nps') )
            {
              case 'authorize_capture': $method_name = 'PayOnLine_3p'; break;
              case 'authorize': $method_name = 'Authorize_3p'; break;
            }

            $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
            $psp_NumPayments = Mage::getSingleton('core/session')->getInstallment();

            $psp_parameters = array(
                'psp_Version'                  => '1',
                'psp_MerchantId'               => Mage::getModel('nps/nps')->getConfigData('merchant_id'),
                'psp_TxSource'                 => 'WEB',
                'psp_MerchTxRef'               => strtoupper(uniqid($order->getIncrementId().'.', true)),
                'psp_PosDateTime'              => date('Y-m-d H:i:s'),
                'psp_MerchOrderId'             => $order->getIncrementId(),
                // 'psp_Amount'                   => Mage::getModel('nps/nps')->calculatePspAmount($order->getGrandTotal(), $order->getPayment()->getData('cc_type'), $psp_NumPayments),
                'psp_Amount'                   => Mage::getModel('nps/nps')->toCents($order->getGrandTotal()),
                'psp_NumPayments'              => $psp_NumPayments,
                'psp_Currency'                 => substr(Mage::getModel('nps/nps')->formatCurrencyToPspCurrency( Mage::app()->getStore()->getCurrentCurrencyCode() ),0,3),
                'psp_CustomerMail'             => $order->getCustomerEmail() ? substr($order->getCustomerEmail(),0,255) : 'nomail@magento.com',
                'psp_MerchantMail'             => substr(Mage::getModel('nps/nps')->getConfigData('merchant_email'),0,255),
                'psp_Product'                  => Mage::getModel('nps/nps')->formatCCTypeToPspProductId($order->getPayment()->getData('cc_type')),
                'psp_Country'                  => substr(Mage::getModel('nps/nps')->formatCountryToPspCountry(Mage::getStoreConfig('general/country/default')),0,3),
                'psp_ReturnURL'                => Mage::getUrl('nps/redirect/response'),
                'psp_FrmLanguage'              => 'es_AR',
                'psp_FrmBackButtonURL'         => Mage::getUrl('checkout/cart'), // entra en loop infinito con esto no se porque...
                // 'psp_FrmBackButtonURL'         => Mage::getUrl('nps/redirect/response'),
                'psp_PurchaseDescription'      => substr('ORDER-'.$order->getIncrementId(),0,255),
                // 'psp_ForceProcessingMethod'    => "1",
            );

            $psp_parameters['psp_MerchantAdditionalDetails'] = array(
                'ShoppingCartInfo' => 'Magento '.Mage::getVersion(),
                'ShoppingCartPluginInfo' => 'Magento NPS Plugin '.(string)Mage::getConfig()->getNode('modules/Sub1_Nps/version'),
            );

            /**
             * Customer Data (if there is a customer)
             */
            if($order->getCustomerId()) {
              $AccountPreviousActivity = "0";
              $orderCollection = Mage::getModel('sales/order')->getCollection();
              $orderCollection->getSelect()->where('customer_id = '.$order->getCustomerId());
              foreach($orderCollection as $item) {
                if($item->getPayment()->getMethodInstance()->getTitle() == 'NPS (Net Payment Service)') {
                  $counter++;
                }
                if($counter > 1) {
                  $AccountPreviousActivity = "1";
                  break;
                }
              }

              $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
              $psp_parameters['psp_CustomerAdditionalDetails'] = array(
                'EmailAddress'=>$order->getCustomerEmail() ? substr($order->getCustomerEmail(),0,255) : 'nomail@magento.com',
                //'AlternativeEmailAddress'=>'2', // aparentemente no disponible
                'IPAddress'=>substr((string)$_SERVER['REMOTE_ADDR'],0,45),
                'AccountID'=>substr($order->getCustomerId(),0,128),
                'AccountCreatedAt'=>date('Y-m-d',strtotime($customer->getData('created_at'))),
                'AccountPreviousActivity'=>$AccountPreviousActivity,
                // 'AccountHasCredentials'=>'1', // nose lo que es
                // 'DeviceType'=>'2', // no disponible
                // 'DeviceFingerPrint'=>'2', // no disponible
                // 'BrowserLanguage'=>'ES', // no disponible
                'HttpUserAgent'=>substr(Mage::helper('core/http')->getHttpUserAgent(),0,255),
              );
            }

            if(is_object($order->getBillingAddress())) {
              $matches = array();
              $input_string = $order->getBillingAddress()->getStreet(1);
              if(preg_match('/(?P<address>[^d]+) (?P<number>\d+.?)/', $input_string, $matches)){
                  $street = $matches['address'];
                  $number = $matches['number'];
              } else { // no number found, it is only address
                  $street = $input_string;
                  $number = null;
              }

              if($order->getBillingAddress()->getFirstname()
                  && ($street && $number)
                  && $order->getBillingAddress()->getCity()
                  && $order->getBillingAddress()->getCountryModel()->getIso3Code()
                  && $order->getBillingAddress()->getPostcode()) {
                $psp_parameters['psp_BillingDetails'] = array(
                  'Person'=>array(
                      'FirstName'=>substr($order->getBillingAddress()->getFirstname(),0,50),
                      'LastName'=>substr($order->getBillingAddress()->getLastname(),0,30),
                      'MiddleName'=>substr($order->getBillingAddress()->getMiddlename(),0,30),
                      //'PhoneNumber1'=>'4123-1234', // no disponible
                      //'PhoneNumber2'=>'4123-1234', // no disponible
                      //'Gender'=>'M', // no disponible
                      //'DateOfBirth'=> '1987-01-01', // no disponible
                      //'Nationality'=>'ARG', // no disponible
                      //'IDNumber'=>'32123123', // no disponible
                      //'IDType'=>'1', // no disponible
                  ),
                  'Address'=>array(
                      'Street'=>substr($street,0,50),
                      'HouseNumber'=>substr($number,0,15),
                      // 'AdditionalInfo'=>'3', // no disponible
                      'City'=>substr($order->getBillingAddress()->getCity(),0,40),
                      'StateProvince'=>substr($order->getBillingAddress()->getRegion(),0,40),
                      'Country'=>substr($order->getBillingAddress()->getCountryModel()->getIso3Code(),0,3),
                      'ZipCode'=>substr($order->getBillingAddress()->getPostcode(),0,10),
                  ),
                );

              }
            }

            if(is_object($order->getShippingAddress())) {
              $matches = array();
              $input_string = $order->getShippingAddress()->getStreet(1);
              if(preg_match('/(?P<address>[^d]+) (?P<number>\d+.?)/', $input_string, $matches)){
                  $street = $matches['address'];
                  $number = $matches['number'];
              } else { // no number found, it is only address
                  $street = $input_string;
                  $number = null;
              }

              $shippingMethod = $order->getShippingMethod(true);
              if($order->getShippingAddress()->getData('firstname')
                   && ($street && $number)
                   && $order->getShippingAddress()->getData("city")
                   && $order->getShippingAddress()->getCountryModel()->getIso3Code()
                   && $order->getShippingAddress()->getData("postcode") ) {

                $psp_parameters['psp_ShippingDetails'] = array(
                  // 'ShippingIDNumber'=>$order->getShipmentsCollection()->getFirstItem()->getIncrementId(), // no disponible
                  'Method'=>substr(Mage::getModel('nps/nps')->formatShippingMethod(null),0,2), // required // ??????????
                  'Carrier'=>substr(Mage::getModel('nps/nps')->formatShippingCarrier(null),0,3), // no disponible
                  // 'DeliveryDate'=>(string)date('Y-m-d',strtotime($shippingMethod->getCreatedAtTimestamp())), // no disponible
                  'FreightAmount'=> (int)$order->getShippingAmount() > 0 ? substr((string)Mage::getModel('nps/nps')->toCents($order->getShippingAmount()),0,12) : null,
                  // 'GiftMessage'=>'4', // no disponible
                  // 'GiftWrapping'=>'4', // no disponible
                  'PrimaryRecipient'=>array( // required
                    'FirstName'=>substr($order->getShippingAddress()->getData('firstname'),0,50), // required
                    'LastName'=>substr($order->getShippingAddress()->getData('lastname'),0,30),
                    'MiddleName'=>substr($order->getShippingAddress()->getData('middlename'),0,30),
                    // 'PhoneNumber1'=>'4', // no disponible
                    // 'PhoneNumber2'=>'4', // no disponible
                    // 'Gender'=>'M', // no disponible
                    // 'DateOfBirth'=>'1987-01-01', // no disponible
                    // 'Nationality'=>'ARG', // no disponible
                    // 'IDNumber'=>'4', // no disponible
                    // 'IDType'=>'4', // no disponible
                  ),
                  'Address'=>array( // required
                    'Street'=>substr($street,0,50),
                    'HouseNumber'=>substr($number,0,15),
                    // 'AdditionalInfo'=>'3', // no disponible
                    'City'=>substr($order->getShippingAddress()->getData("city"),0,40),
                    'StateProvince'=>substr($order->getShippingAddress()->getRegion(),0,40),
                    'Country'=>substr($order->getShippingAddress()->getCountryModel()->getIso3Code(),0,3),
                    'ZipCode'=>substr($order->getShippingAddress()->getData("postcode"),0,10),
                  ),

                );
              }
            }

            foreach($order->getAllVisibleItems() as $item) {
              $psp_parameters['psp_OrderDetails']['OrderItems'][] = array(
                'Quantity'=>(string)intval($item->getQtyOrdered()),
                'UnitPrice'=>Mage::getModel('nps/nps')->toCents($item->getPrice()),
                'Description'=>substr($item->getName(),0,127),
                // 'Type'=>$item->getType(), // no disponible
                'SkuCode'=>substr($item->getSku(),0,48),
                // 'ManufacturerPartNumber'=>'1', // no disponible
                // 'Risk'=>'H' // ?????????
              );
            }

            $response = Mage::getModel('nps/nps')->sendToNPS($method_name, $psp_parameters);


            if(is_array($response) && count($response) && @$response['psp_TransactionId']) {
              $pspSession = is_array(Mage::getSingleton('core/session')->getPspSession()) ? Mage::getSingleton('core/session')->getPspSession() : array();
              $pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()] = $response;
              Mage::getSingleton('core/session')->setPspSession($pspSession);

              foreach($response as $k => $v) {
                $block->setData($k, $v);
              }
              Mage::getModel('nps/nps')->directLinkTransact($order,@$response['psp_TransactionId'], $response, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, $method_name, $closed = 0);
            }else {
              $this->cancelAction();
              Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
            }
        }else {
          $block->setData('psp_FrontPSP_URL', $pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]['psp_FrontPSP_URL']);
          $block->setData('psp_Session3p', $pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]['psp_Session3p']);
          $block->setData('psp_TransactionId', $pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]['psp_TransactionId']);
          $block->setData('psp_MerchantId', $pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]['psp_MerchantId']);
          $block->setData('psp_MerchTxRef', $pspSession['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]['psp_MerchTxRef']);
        }

        /**
         * rendereo el form y autosubmiteo a psp
         */
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }



    public function directLinkTransactionReformat($response) {
      foreach($response as $indexA => $valueA) {
        if(is_array($valueA)) {
          foreach($valueA as $indexB => $valueB) {
            if(is_array($valueB)) {
              foreach($valueB as $indexC => $valueC) {
                if( is_array($valueC) ) {
                  foreach($valueC as $indexD => $valueD) {
                    if(is_array($valueD)) {

                    }else {
                      $response[$indexA.'.'.$indexB.'.'.$indexC.'.'.$indexD] = $valueD;
                    }
                  }
                }else {
                  $response[$indexA.'.'.$indexB.'.'.$indexC] = $valueC;
                }
              }
            }else {
              $response[$indexA.'.'.$indexB] = $valueB;
            }
          }
          unset($response[$indexA]);
        }
      }
      return $response;
    }




    // The response action is triggered when your gateway sends back a response after processing the customer's payment
    public function responseAction($retries=0) {
        if($this->getRequest()->isPost()) {

            if(is_array(@$_SESSION['queried_psp_tx_ids'])
                    && in_array($_POST['psp_TransactionId'],$_SESSION['queried_psp_tx_ids'])) {
              Mage_Core_Controller_Varien_Action::_redirect('');
              return;
            }

            /**
             * comentado 2015-06-01
             * unset($_SESSION['psp_transaction'.Mage::getSingleton('checkout/session')->getLastRealOrderId()]);
             */


            // SimpleQueryTx
            $psp_parameters_query = array(
                'psp_Version'         => '1',
                'psp_MerchantId'      => Mage::getModel('nps/nps')->getConfigData('merchant_id'),
                'psp_QueryCriteria'   => 'T',
                'psp_QueryCriteriaId' => $_POST['psp_TransactionId'],
                'psp_PosDateTime'     => date('Y-m-d H:i:s')
            );

            $response = Mage::getModel('nps/nps')->sendToNPS('SimpleQueryTx', $psp_parameters_query);

            if($response === FALSE && $retries <= 5) {
              sleep(2);
              $this->responseAction($retries+1);
            }

            /**
             * Se hace asi porque se requiere el psp_MerchOrderId
             */
            if(is_array($response) && isset($response['psp_Transaction']) )
            {
                $_SESSION['queried_psp_tx_ids'][] = $_POST['psp_TransactionId'];

                if($response['psp_Transaction']['psp_ResponseCod'] == 0) {

                    // Payment was successful, so update the order's state, send order email and move to the success page
                    $order = Mage::getModel('sales/order');
                    $order->loadByIncrementId($response['psp_Transaction']['psp_MerchOrderId']);

                    Mage::getModel('nps/nps')->directLinkTransact($order,@$response['psp_Transaction']['psp_TransactionId'], $this->directLinkTransactionReformat($response), Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, 'SimpleQueryTx', $closed = 0);

                    $order->addStatusHistoryComment('Gateway has authorized the payment.');
                    $order->sendNewOrderEmail();
                    $order->setEmailSent(true);
                    $order->save();

                    Mage::getSingleton('checkout/session')->unsQuoteId();

                    if(Mage::getModel('nps/nps')->getConfigData('payment_action_nps') == 'authorize_capture') {

                      try {
                        if(!$order->canInvoice()) {
                          Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
                        }
                        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                        if(!$invoice->getTotalQty()) {
                          Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
                        }
                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                        $invoice->register();
                        $transactionSave = Mage::getModel('core/resource_transaction')
                                ->addObject($invoice)
                                ->addObject($invoice->getOrder());
                        $transactionSave->save();
                      }catch (Mage_Core_Exception $e) {

                      }

                    }

                    Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
                }else {
                    $this->cancelAction($response);
                    Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
                }
            }else {
                $order = new Mage_Sales_Model_Order();
                $order->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
                $order->hold()->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, 'Gateway has not response, order require manual revision.');
                $order->save();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
            }

        }else {
            Mage_Core_Controller_Varien_Action::_redirect('');
        }
    }


    // The cancel action is triggered when an order is to be cancelled
    public function cancelAction($response) {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.');
                if(is_array($response) && count($response)) {

                  Mage::getModel('nps/nps')->directLinkTransact($order,@$response['psp_Transaction']['psp_TransactionId'], $this->directLinkTransactionReformat($response), Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);

                  if($response['psp_Transaction']['psp_TransactionId']) {
                    $order->getPayment()->setCcTransId($response['psp_Transaction']['psp_TransactionId']);
                    $order->getPayment()->setTransactionId(@$response['psp_Transaction']['psp_TransactionId']);
                    $order->getPayment()->setIsTransactionClosed(0);
                    $order->getPayment()->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,$response);
                  }
                }
                $order->save();
            }
        }
    }

    /**
     * JSON retrieve installments
     */
    public function query_installmentAction() {
      if(Mage::getModel('nps/nps')->getConfigData('enable_installment')) {
        /*
        $cc_type = 'VI';
        $country = 'AR';
        $currency = 'ARS';
        */

        /**
         * producto viene siempre por parametro
         */
        $cc_type = mysql_escape_string($_REQUEST['cc_type']);


        /**
         * country lo saco de la secion del store
         */
        $country = Mage::getStoreConfig('general/country/default');


        /**
         * currency puede venir por parametro, si no viene uso el del store
         */
        $currency = isset($_REQUEST['currency']) ? mysql_escape_string($_REQUEST['currency']) : Mage::app()->getStore()->getBaseCurrencyCode();


        /**
         * se filtra por product(cc_type)+country+currency
         */
        $table = Mage::getSingleton('core/resource')->getTableName('installment/installment');
        $table2 = Mage::getSingleton('core/resource')->getTableName('installment/installment_store');
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $query = 'SELECT ii.* FROM '.$table.' ii
            INNER JOIN '.$table2.' iis ON ii.entity_id = iis.installment_id
            WHERE ii.cc_type = \''.$cc_type.'\'
            AND ii.country = \''.$country.'\' AND ii.currency = \''.$currency.'\'
            AND iis.store_id = '.Mage::app()->getStore()->getId().'
            ORDER BY ii.qty';
        $installments = $readConnection->fetchAll($query);

        echo json_encode($installments);
      }
    }



}
