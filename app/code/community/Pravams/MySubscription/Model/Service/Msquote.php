<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*
 * Msquote submit service model
 * */
class Pravams_MySubscription_Model_Service_Msquote
{
    /*
     * Msquote Object
     * @var Pravams_MySubscription_Model_Msquote
     * */
    protected $_msquote;

    /*
     * Msquote Convert Object
     * @var Pravams_MySubscription_Model_Convert_Msquote
     * */
    protected $_convertor;

    /*
     * Order that may be created during msquote submission
     * @var Mage_Sales_Model_Order
     * */
    protected $_order = null;

    /*
     * Class Constructor
     *
     * @param Pravams_MySubscription_Model_Msquote
     * */
    public function __construct(Pravams_MySubscription_Model_Msquote $msquote){
        $this->_msquote = $msquote;
        $this->_convertor = Mage::getModel('mysubscription/convert_msquote');
    }

    /*
     * Get assigned Msquote Object
     * */
    public function getMsquote(){
        return $this->_msquote;
    }

    /*
     * Submit the Msquote. Quote submit process will create the order based on msquote data
     * @return Mage_Sales_Model_Order
     * */
    public function submitOrder(){
        $this->_validate();
        $msquote = $this->_msquote;
        $isVirtual = $msquote->getData('is_virtual');

        $transaction = Mage::getModel('core/resource_transaction');
        if($msquote->getCustomerId()){
            $transaction->addObject($msquote->getCustomer());
        }

        if($isVirtual){
            $order = $this->_convertor->addressToOrder($msquote->getMsBillingAddress());
        }else{
            $order = $this->_convertor->addressToOrder($msquote->getMsShippingAddress());
        }

        $order->setBillingAddress($this->_convertor->addressToOrderAddress($msquote->getMsBillingAddress()));
        $order->getBillingAddress()->setCustomerAddress($msquote->getBillingAddress());

        if(!$isVirtual){
            $order->setShippingAddress($this->_convertor->addressToOrderAddress($msquote->getMsShippingAddress()));
            $order->getShippingAddress()->setCustomerAddress($msquote->getShippingAddress());
        }

        $order->setPayment($this->_convertor->paymentToOrderPayment($msquote->getPayment(), $msquote));

        $itemsCollect = Mage::getModel('mysubscription/msquote_item')
            ->getCollection()
            ->addFieldToFilter('msquote_id', $msquote->getId());

        foreach($itemsCollect as $_item){
            $item = Mage::getModel('mysubscription/msquote_item')->load($_item->getItemId());
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if($item->getParentItemId()){
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItemId()));
            }
            $order->addItem($orderItem);
        }

        $transaction->addObject($order);
        $transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        try {
            $transaction->save();
        } catch(Exception $e){

            // reset order ID's on exception, because order not saved
            $order->setId(null);
            /* @var $item Mage_Sales_Model_Order_Item */
            foreach($order->getItemsCollection as $item){
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            throw $e;
        }

        $this->_order = $order;
        $this->afterPlaceOrder();

        return $order;
    }

    public function afterPlaceOrder(){
        $msquote = $this->_msquote;
        $order = $this->_order;
        /* Save the order data in mysubscription table */
        $msorder = Mage::getModel('mysubscription/msorder')
            ->setOrderId($order->getId())
            ->setMsquoteId($msquote->getId());
        $msorder->save();

        /* Update the subscription Profile */
        $profile = Mage::getModel('mysubscription/profile')->getMsProfile($msquote);
        $startDate = $profile->getTime();
        $todaysDate = date('Y-m-d 00:00:00');
        $interval = $profile->getFrequencyVal();
        $nextOrderDate = date('Y-m-d 00:00:00', strtotime($todaysDate.' + '.$interval.' day'));
        $profile->setNextOrderTime($nextOrderDate);
        $profile->save();
        return;
    }

    /*
     * Validating msquote data before converting it into order
     * */
    protected function _validate(){
        $isVirtual = $this->_msquote->getData('is_virtual');

        if(!$isVirtual){
            $shippingAddress = $this->_msquote->getShippingAddress();
            $shippingAddressValidation = $shippingAddress->validate();
            if($shippingAddressValidation !=  true){
                Mage::throwException(
                    Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $shippingAddressValidation))
                );
            }

            $msShippingAddress = $this->_msquote->getMsShippingAddress();
            $method = $msShippingAddress->getShippingMethod();
            $rate = $this->_msquote->getShippingRateByCode($method);
            if(!$method || !$rate){
                Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
            }
        }

        $billingAddress = $this->_msquote->getBillingAddress();
        $billingAddressValidation = $billingAddress->validate();
        if($billingAddressValidation != true){
            Mage::throwException(
                Mage::helper('sales')->__('Please check billing address information %s', implode(' ', $billingAddressValidation))
            );
        }
    }
}