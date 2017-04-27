<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Shippingrate extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('mysubscription/shippingrate');
    }
    
    /*
     * @param Pravams_MySubscription_Model_Address $shipping
     * @return Pravams_MySubscription_Model_Shippingrate $msShippingMethod
     */
    public function getShippingRate(Pravams_MySubscription_Model_Address $shipping)
    {
        $msShippingMethod = Mage::getModel('mysubscription/shippingrate')->getCollection()
                            ->addFieldToFilter('address_id', $shipping->getAddressId())
                            ->getFirstItem();
        return $msShippingMethod;
    }
}
?>
