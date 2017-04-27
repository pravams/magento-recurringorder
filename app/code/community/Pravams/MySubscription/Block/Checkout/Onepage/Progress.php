<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{
    public function getMsProfile(){
        $msQuoute = Mage::getModel('mysubscription/cart')->getMsquote();
        $profile = Mage::getModel('mysubscription/profile')->getMsProfile($msQuoute);
        return $profile;
    }
}