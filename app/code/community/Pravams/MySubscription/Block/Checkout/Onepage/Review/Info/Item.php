<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Checkout_Onepage_Review_Info_Item extends Mage_Core_Block_Template
{
    /*
     * Retrieve the msquote object from the session
     * @return Pravams_MySubscription_Model_Msquote
     * */
    public function getMsquote(){
        $msQuote = Mage::getSingleton('mysubscription/cart')
            ->getMsquote();
        return $msQuote;
    }
}