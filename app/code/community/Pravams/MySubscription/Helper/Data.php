<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAddToSubscribeUrl($_product){
        $productId = $_product->getId();
        return Mage::getUrl('mysubscription/cart/add',array('product'=> $productId));
    }

    /*
     * Find if the cart having items which are only for subscription
     * @return bool
     * */
    public function isCartOnlySubscription(){
        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->getId()){
            /*
             * Check if the regular cart has any items in it
             * */
            if(Mage::getSingleton('checkout/session')->getQuote()->getItemsCount() > 0){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }

    }

    /*
     * Find if the cart has any subscription items
     * */
    public function hasSubscriptionItems(){
//        return Mage::getSingleton('mysubscription/cart')->getMsquote()->getId();
        return Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems();
    }

    /*
     * Clear the Subscription cart after it is placed during checkout
     * */
    public function clearSubscriptionCart(){
        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->getId()){
            $msSession = Mage::getSingleton('mysubscription/cart')
                ->getCheckoutSession();
            /*
             * update the Msquote table that subscription order is active
             */
            $msquote = Mage::getModel('mysubscription/msquote')->load($msSession->getMsquoteId());

            $msquote->setMsorderState(Pravams_MySubscription_Model_Msquote::ACTIVE)
                ->save();

            /*
             * Remove the msquote id from the session
             */
            $msSession->unsetAll();
        }
    }

    /*
     * Formatting the price to display in template
     * */
    public function formatPrice($price){
        $formattedPrice = Mage::getModel('sales/order')->formatPrice($price);
        return $formattedPrice;
    }

    /*
     * Formatting the qty to display in template
     * */
    public function formatQty($qty){
        $formattedQty = number_format($qty);
        return $formattedQty;
    }

    /*
     * Get Cart Items to display in template
     * */
    public function getMscartItems(){
        $msQuote = Mage::getSingleton('mysubscription/cart')
            ->getMsquote();
        $msItemsCollection =  $msQuote->getItemsCollection();
        foreach($msItemsCollection as $_msitem){
            if($_msitem->getParentItemId() === null){
                $msitem = Mage::getModel('mysubscription/msquote_item')->load($_msitem->getItemId());
                $msItems[] = $msitem;
            }
        }
        return $msItems;
    }

    /*
     * Retrieve all Subscription frequency time
     * @return array
     * */
    public function getMsFrequency(){
        $options = Mage::getStoreConfig('mysubscription/frequency/interval', Mage::app()->getStore()->getStoreId());
        $frequency = explode(',', $options);
        $allFreq = array();
        foreach($frequency as $_frequency){
            if($_frequency > 0){
                switch($_frequency){
                    case 1: $allFreq['1'] = "Daily";
                        break;
                    case 7: $allFreq['7'] = "Weekly";
                        break;
                    case 30: $allFreq['30'] = "Monthly";
                        break;
                    default:
                        $key = "Every ".$_frequency." Days";
                        $allFreq[$_frequency] = "Every ".$_frequency." Days";
                        break;
                }
            }
        }
        return $allFreq;
    }

    /*
     * Get the label for subscription frequency
     * */
    public function getMsFrequencyLabel($frequency){
        switch($frequency){
            case 1: $label = "Daily";
                break;
            case 7: $label = "Weekly";
                break;
            case 30: $label = "Monthly";
                break;
            default:
                $label = "Every ".$frequency." Days";
                break;
        }
        return $label;
    }
}
?>