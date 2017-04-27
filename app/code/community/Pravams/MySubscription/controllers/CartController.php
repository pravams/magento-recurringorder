<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_CartController extends Mage_Core_Controller_Front_Action
{
    protected function _getCart(){
        return Mage::getSingleton('mysubscription/cart');
    }

    public function addAction(){
        /* create a singleton class for the mysubscription cart */
        $cart = Mage::getSingleton('mysubscription/cart');
        /* add the rows in db for the quote and items */
        $storeId = Mage::app()->getStore()->getId();
        $params = $this->getRequest()->getParams();
        
        $productId = $params['product'];
        $product = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($productId);        
        
        $cart->addProduct($product, $params);
        
        $cart->save();

        $this->_redirect('checkout/cart');
        return;
    }

    /*
     * Save the MySubscription Details on Checkout page
     * */
    public function saveProfileAction(){
        $subscriptionInfo = $this->getRequest()->getParams();
        $msQuoute = Mage::getSingleton('mysubscription/session')->getMsquote();
        
        $msProfile = Mage::getModel('mysubscription/profile')->getMsProfile($msQuoute);
        $frequencyLabel = Mage::helper('mysubscription')->getMsFrequencyLabel($subscriptionInfo['subscription_frequency']);

        $msProfile->setName($subscriptionInfo['subscription_name'])
                ->setTime(date('Y-m-d 00:00:00', strtotime($subscriptionInfo['subscription_time'])))
                ->setFrequency($frequencyLabel)
                ->setFrequencyVal($subscriptionInfo['subscription_frequency'])
                ->setStoreId(Mage::app()->getStore()->getId())
                ->setMsquoteId($msQuoute->getId());
        
        $msQuoute->setProfile($msProfile);

        $checkoutBlock = Mage::app()->getLayout()->createBlock('mysubscription/checkout_onepage')->getCheckout();
        $checkoutBlock->setStepData('mysubscription', 'allow', true)
                      ->setStepData('mysubscription', 'complete', true)
                      ->setStepData('billing', 'allow', true);
        
        return;
    }

    /*
     * Update the MySubscription Cart
     * */
    public function updateAction(){
        $mscartData = $this->getRequest()->getParam('mscart');
        if(is_array($mscartData)){
            $filter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => Mage::app()->getLocale()->getLocaleCode())
            );
            foreach($mscartData as $index => $data){
                if(isset($data['qty'])){
                    $mscartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                }
            }
        }

        $msCart = $this->_getCart();
        $msCart->updateItems($mscartData);

        $this->_redirect('checkout/cart/index');
    }

    /*
     * Delete Items from the MySubscription Cart
     * */
    public function deleteAction(){
        $cartItem = $this->getRequest()->getParam('id');
        $msCart = $this->_getCart();
        $msCart->removeItem($cartItem);
        $this->_redirect('checkout/cart/index');
    }

    public function testCronAction(){
        Mage::getModel('mysubscription/observer')->placeOrder();
    }
}
?>
