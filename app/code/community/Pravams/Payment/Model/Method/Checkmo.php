<?php
/**
 * Pravams Payment Module
 *
 * @category    Pravams
 * @package     Pravams_Payment
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_Payment_Model_Method_Checkmo extends Mage_Payment_Model_Method_Checkmo
{
    /*
     * This is the override of the payment model class to enable it
     * for MySubscription module
     *
     * @param Mage_Sales_Model_Quote $quote
     * @patam int|null $checkBitMask
     * @return bool
     * */
    public function isApplicableToQuote($quote, $checksBitMask){
        if ($checksBitMask & self::CHECK_USE_FOR_COUNTRY) {
            if (!$this->canUseForCountry($quote->getBillingAddress()->getCountry())) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_FOR_CURRENCY) {
            if (!$this->canUseForCurrency($quote->getStore()->getBaseCurrencyCode())) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_CHECKOUT) {
            if (!$this->canUseCheckout()) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_FOR_MULTISHIPPING) {
            if (!$this->canUseForMultishipping()) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_USE_INTERNAL) {
            if (!$this->canUseInternal()) {
                return false;
            }
        }

        if ($checksBitMask & self::CHECK_ORDER_TOTAL_MIN_MAX) {
            $total = $quote->getBaseGrandTotal();
            $minTotal = $this->getConfigData('min_order_total');
            $maxTotal = $this->getConfigData('max_order_total');
            if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_RECURRING_PROFILES) {
            if (!$this->canManageRecurringProfiles() && $quote->hasRecurringItems()) {
                return false;
            }
        }
        if ($checksBitMask & self::CHECK_ZERO_TOTAL) {
            $msQuote = Mage::getSingleton('mysubscription/cart')->getMsquote();
            if(Mage::helper('mysubscription')->hasSubscriptionItems() && Mage::helper('pravams_payment')->isCheckEnabled()){
                $total = $msQuote->getGrandTotal();
            }else{
                $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
            }

            if ($total < 0.0001 && $this->getCode() != 'free'
                && !($this->canManageRecurringProfiles() && $quote->hasRecurringItems())
            ) {
                return false;
            }
        }
        return true;
    }
}