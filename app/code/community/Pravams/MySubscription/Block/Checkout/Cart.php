<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Block_Checkout_Cart extends Mage_Checkout_Block_Cart
{
    protected function _afterToHtml($html) 
    {
        $msCheckout = Mage::getSingleton('mysubscription/cart')->getCheckoutSession();
        if(!$msCheckout->getMsquoteId()){            
            return $html;
        }

        $items = $this->getItems();

        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems()){
            $msBlock = $this->getLayout()
                ->createBlock('mysubscription/checkout_mscart','mysubscription.cart')
                ->setTemplate('mysubscription/checkout/cart.phtml');
            $this->addItemHtml($msBlock);
            $msBlockHtml = $msBlock->toHtml();
            /* check if cart is empty */
            if(empty($items)){
                $html = $msBlockHtml;
            }else{
                $html = $msBlockHtml.$html;
            }
        }

        return $html;
    }

    /*
     * Add subscription item Html to the cart
     * */
    public function addItemHtml($parentBlock){
        $msitemBlock = $this->getLayout()
            ->createBlock('mysubscription/checkout_mscart','mysubscription.cart.item')
            ->setTemplate('mysubscription/checkout/cart/item.phtml');
        $parentBlock->setChild('mysubscription.cart.item', $msitemBlock);
        return $parentBlock;
    }
}
?>
