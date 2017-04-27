<?php
/**
 * Pravams MySubscription Module
 * Extending the quote class to make checkout happen with only subscription items
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Model_Quote extends Mage_Sales_Model_Quote
{
    /*
     * Item visibility overridden
     * to enable checkout for subscription items only
     * @return bool
     * */
    public function hasItems(){
        $itemsCount = sizeof($this->getAllItems());
        if( $itemsCount > 0 ){
            return $itemsCount;
        }else{
            $subscriptionItems = Mage::getSingleton('mysubscription/cart')->getMsquote()->getItemsCollection();
            if(sizeof($subscriptionItems) > 0){
                return true;
            }else{
                return false;
            }
        }
    }
}