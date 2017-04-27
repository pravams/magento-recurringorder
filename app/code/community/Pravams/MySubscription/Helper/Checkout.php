<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Helper_Checkout extends Mage_Checkout_Helper_Data
{
    /*
     * Check is allowed Guest Checkout
     * Use config settings and observer
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     * */
    public function isAllowedGuestCheckout(Mage_Sales_Model_Quote $quote, $store = null) {

        $guestCheckout = parent::isAllowedGuestCheckout($quote, $store);

        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems()){
            return false;
        }

        return $guestCheckout;
    }

    /*
     * Check if multishipping checkout is available.
     * There should be a valid quote in checkout session. If not, only the config value will be returned
     * @return bool
     * */
    public function isMultishippingCheckoutAvailable(){
        $multishippingFlag = parent::isMultishippingCheckoutAvailable();
        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems()){
            return false;
        }
        return $multishippingFlag;
    }
}