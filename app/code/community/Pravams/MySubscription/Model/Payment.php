<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Payment extends Mage_Core_Model_Abstract
{
    protected function _construct() {
        $this->_init('mysubscription/payment');
    }
    
    /*
     * @var Pravams_MySubscription_Model_Msquote
     * @return Pravams_MySubscription_Model_Payment
     */
    public function getPaymentMethod(Pravams_MySubscription_Model_Msquote $msquote){
        $msQuoteId = $msquote->getId();
        $mspayment = Mage::getModel('mysubscription/payment')->getCollection()
            ->addFieldToFilter('msquote_id', $msQuoteId)
            ->getFirstItem();
        return $mspayment;
    }
}
?>
