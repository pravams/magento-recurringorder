<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Address extends Mage_Core_Model_Abstract
{
    /*
     * Msquote Object
     *
     * @var Pravams_MySubscription_Model_Msquote
     * */
    protected $_msquote = null;

    /*
     * Class constructor
     * */
    protected function _construct() 
    {
        $this->_init('mysubscription/address');
    }
    
    /*
     * @param Pravams_MySubscription_Model_Msquote $msQuote
     * @return Pravams_MySubscription_Model_Address
     */
    public function getBillingAddress(Pravams_MySubscription_Model_Msquote $msQuote){
        $msQuoteId = $msQuote->getId();
        $billing = Mage::getModel('mysubscription/address')->getCollection()
                    ->addFieldToFilter('msquote_id', $msQuoteId)
                    ->addFieldToFilter('address_type', Mage_Customer_Model_Address_Abstract::TYPE_BILLING)
                    ->getFirstItem();
        return $billing;
    }
    
    /*
     * @param Pravams_MySubscription_Model_Msquote $msQuote
     * @return Pravams_MySubscription_Model_Address
     */
    public function getShippingAddress(Pravams_MySubscription_Model_Msquote $msQuote){
        $msQuoteId = $msQuote->getId();
        $shipping = Mage::getModel('mysubscription/address')->getCollection()
                    ->addFieldToFilter('msquote_id', $msQuoteId)
                    ->addFieldToFilter('address_type', Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING)
                    ->getFirstItem();
        return $shipping;
    }

    /*
     * Retrieve Msquote Object
     * @return Pravams_MySubscription_Model_Msquote
     * */
    public function getMsquote(){
        return $this->_msquote;
    }

    /*
     * Declare Address Msquote model object
     *
     * @param Pravams_MySubscription_Model_Msquote
     * @return Pravams_MySubscrption_Model_Address
     * */
    public function setMsquote(Pravams_MySubscription_Model_Msquote $msquote){
        $this->_msquote = $msquote;
        return $this;
    }
}
?>
