<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Profile_View extends Mage_Core_Block_Template
{
    protected $_profile;

    protected $_msquote;

    protected function _construct(){
        parent::_construct();
        $this->_profile = $this->getRequest()->getParam('profile_id');
    }

    /*
     * Get the Subscription Profile
     * @return Pravams_MySubscription_Model_Profile
     * */
    public function getProfile(){
        $profileId = $this->_profile;
        $profile = Mage::getModel('mysubscription/profile')->load($profileId);
        return $profile;
    }

    /*
     * Get the Quote for the Subscription
     * @return Pravams_MySubscription_Model_Msquote
     * */
    public function getMsquote(){
        $profileId = $this->_profile;
        $msQuote = Mage::getModel('mysubscription/profile')->getMsquote($profileId);
        $this->_msquote = $msQuote;
        return $msQuote;
    }

    /*
     * Get the quote shipping address
     * @return Pravams_MySubscription_Model_Address
     * */
    public function getShippingAddress(){
        $msQuote = $this->_msquote;
        return Mage::getModel('mysubscription/address')->getShippingAddress($msQuote);
    }

    /*
     * Get the quote billing address
     * @return Pravams_MySubscription_Model_Address
     * */
    public function getBillingAddress(){
        $msQuote = $this->_msquote;
        return Mage::getModel('mysubscription/address')->getBillingAddress($msQuote);
    }

    /*
     * Get the payment method
     * @return Pravams_MySubscription_Model_Payment
     * */
    public function getPaymentMethod(){
        $msQuote = $this->_msquote;
        return Mage::getModel('mysubscription/payment')->getPaymentMethod($msQuote);
    }
}