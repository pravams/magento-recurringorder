<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Model_Convert_Msquote extends Varien_Object
{
    /*
     * Convert quote model to order model
     *
     * @param Pravams_MySubscription_Model_Msquote
     * @return Mage_Sales_Model_Order
     * */
    public function toOrder(Pravams_MySubscription_Model_Msquote $msquote, $order=null){
        if(!($order instanceof Mage_Sales_Model_Order)){
            $order = Mage::getModel('sales/order');
        }

        $order->setStoreId($msquote->getStoreId())
                ->setCustomer($msquote->getCustomer());

        Mage::helper('core')->copyFieldset('sales_convert_quote', 'to_order', $msquote, $order);
        return $order;
    }

    /*
     * Convert quote address model to order
     *
     * @param Mage_Customer_Model_Address
     * @return Mage_Sales_Model_Order
     * */
    public function addressToOrder(Pravams_MySubscription_Model_Address $address, $order = null){
        if(!($order instanceof Mage_Sales_Model_Order)){
            $order = $this->toOrder($address->getMsquote());
        }

        Mage::helper('core')->copyFieldSet('sales_convert_quote_address', 'to_order', $address, $order);
        return $order;
    }

    /*
     * Convert msquote Address to order address
     *
     * @param Pravams_MySubscription_Model_Address
     * @return Mage_Sales_Model_Order_Address
     * */
    public function addressToOrderAddress(Pravams_MySubscription_Model_Address $address){
        $orderAddress = Mage::getModel('sales/order_address')
            ->setStoreId($address->getMsquote()->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        if($address->getAddressType() == Mage_Customer_Model_Address::TYPE_SHIPPING){
            $orderAddress->setShippingMethod($address->getShippingMethod());
            $orderAddress->setShippingDescription($address->getShippingDescription());
        }

        Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_order_address', $address, $orderAddress);
        return $orderAddress;
    }

    /*
     * Convert msquote payment to order payment
     * @param Pravams_MySubscription_Model_Payment
     * @return Mage_Sales_Model_Order_Payment
     * */
    public function paymentToOrderPayment(Pravams_MySubscription_Model_Payment $payment, $msquote){
        $orderPayment = Mage::getModel('sales/order_payment')
                ->setStoreId($msquote->getStoreId())
                ->setCustomerPaymentId($payment->getPaymentId());
        Mage::helper('core')->copyFieldset('sales_convert_quote_payment', 'to_order_payment', $payment, $orderPayment);

        return $orderPayment;
    }

    /*
     * Convert msquote item to order item
     * @param Pravams_MySubscription_Model_Msquote_Item
     * @return Mage_Sales_Model_Order_Item
     * */
    public function itemToOrderItem(Pravams_MySubscription_Model_Msquote_Item $item){
        $orderItem = Mage::getModel('sales/order_item')
                ->setStoreId($item->getStoreId())
                ->setProductId($item->getProductId())
                ->setQuoteItemId($item->getId())
                ->setQuoteParentItemId($item->getParentItemId())
                ->setProductType($item->getProductType())
                ->setProduct($item->getProduct())
                ;

        if($item->getProductType() != 'bundle') {
            $item->setDescription();
        }

        $options = $item->getProductOrderOptions();
        if (!$options) {
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
//            $options = Mage::getModel('mysubscription/msquote_item_option')->getCollection()->getOptionsByItem($item);
        }

        if($item->getProductType() == 'configurable') {
            $options = Mage::helper('mysubscription/product_configuration')->getProductOptions($item);
        }

        $orderItem->setProductOptions($options);

        Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item', $item, $orderItem);

        $orderItem->setOriginalPrice($item->getPrice())
            ->setBasePrice($item->getBasePrice())
            ->setPrice($item->getPrice());

        if($item->getParentItem()){
            $orderItem->setQtyOrdered($orderItem->getQtyOrdered()*$item->getParentItem()->getQty());
        }

        Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
            array('order_item'=>$orderItem, 'item'=>$item)
        );

        return $orderItem;
    }
}