<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    protected function _afterSave() {
        parent::_afterSave();
        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->getId()){
            $this->saveMsQuoteSteps();
        }
        return $this;
    }
    
    /*
     * Save the billing address, shipping address, shipping method 
     * and payment method as part of the Mysubscription Quote
     * 
     */
    public function saveMsQuoteSteps(){
        
        /*
         * Use case when both cart and subscription items are there in the cart
         */
        
        /*
         * get address data from checkout session
         */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        $msQuote = Mage::getSingleton('mysubscription/cart')->getMsquote();
        $msQuoteId = $msQuote->getId();
        
        /* set billing address */
        $billing = Mage::getModel('mysubscription/address')->getBillingAddress($msQuote);
        $billingAddress = $quote->getBillingAddress();
        $msQuote->setMsAddress($billing, $billingAddress, Mage_Customer_Model_Address_Abstract::TYPE_BILLING);

        if(!$msQuote->getIsVirtual()){
            /* set shipping address */
            $shipping = Mage::getModel('mysubscription/address')->getShippingAddress($msQuote);
            $shippingAddress = $quote->getShippingAddress();
            $msQuote->setMsAddress($shipping, $shippingAddress, Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING);

            /* set shipping method */
            $msShippingMethod = Mage::getModel('mysubscription/shippingrate')->getShippingRate($shipping);
            $msQuote->setMsShippingMethod($msShippingMethod, $shippingAddress);
        }
        
        /* set the payment details */
        $mspayment = Mage::getModel('mysubscription/payment')->getPaymentMethod($msQuote);
        $payment = $quote->getPayment();
        $msQuote->setPayment($mspayment, $payment);
        
        /*
         * update the row totals with tax amount, customer, currency
         */
        $globalCurrencyCode  = Mage::app()->getBaseCurrencyCode();

        $msUpdateQuote = Mage::getModel('mysubscription/msquote')->load($msQuote->getId());
        $msUpdateQuote->updateRowTotals();
        $msUpdateQuote->setCustomer($customer);
        $msUpdateQuote->setGlobalCurrencyCode($globalCurrencyCode);
        $msUpdateQuote->save();
        
        return;
    }
}
?>
