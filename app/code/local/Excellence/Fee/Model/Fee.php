<?php
class Excellence_Fee_Model_Fee extends Varien_Object{
    const FEE_AMOUNT = 10;

    public static function getFee($grandTotal){
        // return self::FEE_AMOUNT;

        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());      

        if( isset($_REQUEST['payment']) ) {
          $installment = $_REQUEST['payment']['installment'];  
          $cc_type = $_REQUEST['payment']['cc_type'];  
        }elseif($order->getPayment()) {
          $installment = $order->getPayment()->getAdditionalInformation('installment');
          $cc_type = $order->getPayment()->getData('cc_type');
        }else {
          return 0;
        }
        
        if( $order->getPayment() ) {
          $order->getPayment()->setAdditionalInformation('installment', $installment);
          $order->getPayment()->save();
          $order->save();
        }
            
        $amount_in_cents = number_format(round($grandTotal, 2),2,'','');
        $amount_with_cc_fee = Mage::getModel('nps/nps')->calculatePspAmount($grandTotal, $cc_type, $installment);
        $amount_cc_fee = $amount_with_cc_fee - $amount_in_cents;

        $fee = number_format( ($amount_cc_fee/100),2);        
        
        return $fee;
    }
    public static function canApply($address){
        if(Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethod() == 'nps') {
            return true;
        }
        return false;
            //put here your business logic to check if fee should be applied or not
            //if($address->getAddressType() == 'billing'){
            //return true;
            //}
    }
}