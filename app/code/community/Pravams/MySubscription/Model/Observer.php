<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Observer
{
    public function afterOrderCreated($observer){
        Mage::helper('mysubscription')->clearSubscriptionCart();
    }

    /*
     * add the billing, shipping and customer information to the Msquote
     * loaded for placing order
     * */
    protected function _prepareCustomerQuote($_msquote){
        /*
         * add the customer to the Msquote
         * */
        $customer = Mage::getModel('customer/customer')->load($_msquote->getCustomerId());
        $_msquote->setCustomer($customer);

        /*
         * Retrieve the billing address from Msquote
         * */
        $billing = $_msquote->getBillingAddress();
//        $customer->addAddress($billing);

        /*
         * Retrieve the shipping address from Msquote
         * */
        $shipping = $_msquote->getShippingAddress();
//        $customer->addAddress($shipping);


        /*
         * Retrieve the payment method from Msquote
         * */
        $payment = $_msquote->getPaymentMethod();

        return $_msquote;
    }

    public function placeOrder(){
        /*
         * place an order from the quote saved based on the subscription frequency
         * */
        $msquotes = Mage::helper('mysubscription/profile')->getQuoteProfilesToOrder();
        if($msquotes){
            foreach($msquotes as $_msquote){
                $_msquote = $this->_prepareCustomerQuote($_msquote);
                $service = Mage::getModel('mysubscription/service_msquote', $_msquote);
                $service->submitOrder();
            }
        }
    }
}
?>
