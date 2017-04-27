<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Helper_Profile extends Mage_Core_Helper_Abstract
{
    /*
     * Logic to get the first time subscription and subscription with todays
     * date as the next order date
     * */
    public function getQuoteProfilesToOrder(){
        $todaysDate = date('Y-m-d');
        $quoteIds = array();
        $allowedMethods = $this->getValidPaymentMethods();

        $profile = Mage::getModel('mysubscription/profile')->getCollection();
        $profile->getSelect()->join(
            array('ms_payment' => $profile->getTable('mysubscription/ms_payment')),
            'main_table.msquote_id = ms_payment.msquote_id '
        );
        $profile->getSelect()->where('main_table.next_order_time is null');
        $profile->getSelect()->orWhere('main_table.next_order_time = ?', $todaysDate);
        $profile->getSelect()->where('ms_payment.method IN (?)', $allowedMethods);

        foreach($profile as $_aprofile){
            $quoteIds[] = $_aprofile->getMsquoteId();
        }
        if(sizeof($quoteIds) > 0){
            $msquote = Mage::getModel('mysubscription/msquote')->getCollection()
                ->addFieldToFilter('entity_id', $quoteIds);
            return $msquote;
        }else{
            return false;
        }
    }

    /*
     * Get all the valid payment methods allowed by this extension
     * */
    public function getValidPaymentMethods(){
        $paymentMethods = Mage::getStoreConfig('mysubscription/ms_payment', Mage::app()->getStore()->getStoreId());
        $methods = array();
        foreach($paymentMethods as $key => $value){
            $methods[] = $key;
        }
        return $methods;
    }
}