<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /*
     * Msquote instance
     * 
     * @var null|Pravams_MySubscription_Model_Msquote
     */
    protected $_msquote;
    
    /*
     * Customer instance
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer;
    
    /* 
     * Class constructor to intialize the mysubscription session 
     */
    public function _construct() {
        $this->init('mysubscription');
    }
    
    /*
     * Unset all data associated with the object
     */
    public function unsetAll()
    {
        parent::unsetAll();
        $this->_msquote = null;        
    }
    
    /*
     * Get mysubscription quote instance by current session 
     * 
     * @return Pravams_MySubscription_Model_Msquote
     */
    public function getMsquote($loadQuoteId = null)
    {
        if($this->_msquote === null){
            /* @var $msquote Pravams_MySubscription_Model_Msquote */
            $msquote = Mage::getModel('mysubscription/msquote')->setStoreId(Mage::app()->getStore()->getId());
            if($loadQuoteId){
                $msquote->load($loadQuoteId);
            }else{
                $msquote->load($this->getMsquoteId());
            }
        }else{
            $msquote = $this->_msquote;
        }
        
        $customerSession = Mage::getSingleton('customer/session');
//        if($this->getMsquoteId()){
            if($customerSession->isLoggedIn() || $this->_customer){
                $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                $msquote->setCustomer($customer);
            }
//        }

        $msquote->setStore(Mage::app()->getStore());
        $this->_msquote = $msquote;
        
        return $this->_msquote;
    }
    
    protected function _getMsquoteIdKey()
    {
        return 'msquote_id_' . Mage::app()->getStore()->getWebsiteId();
    }
    
    public function setMsquoteId($msquoteId)
    {
        $this->setData($this->_getMsquoteIdKey(), $msquoteId);
    }
    
    public function getMsquoteId()
    {
        return $this->getData($this->_getMsquoteIdKey());
    }
}
?>
