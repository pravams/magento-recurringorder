<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Checkout_Onepage_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info
{
    /*
     * Modify the review section is cart has only subscription items
     * */
    protected function _beforeToHtml(){
        if(Mage::helper('mysubscription')->isCartOnlySubscription()){
            $this->setTemplate('mysubscription/checkout/onepage/ms_review.phtml');
            $this->addItemHtml($this);
            $this->addTotalHtml($this);
            $this->addButtonHtml($this);
        }
    }

    /*
     * Modify the review section if the cart has both subscription and regular items
     * */
    public function _afterToHtml($html){
        if(Mage::helper('mysubscription')->isCartOnlySubscription()){
            return $html;
        }

        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems()){
            $msreviewBlock = $this->getLayout()
                ->createBlock('mysubscription/checkout_onepage_review_info_item','mysubscription.checkout.msreview')
                ->setTemplate('mysubscription/checkout/onepage/ms_review.phtml');

            $this->addItemHtml($msreviewBlock);
            $this->addTotalHtml($msreviewBlock);
            $this->addMessageHtml($msreviewBlock);

            $newHtml = $html.$msreviewBlock->toHtml();
        }else{
            $newHtml = $html;
        }

        return $newHtml;
    }

    /*
     * Add subscription item Html to the review section
     * */
    public function addItemHtml($parentBlock){
        $msitemBlock = $this->getLayout()
            ->createBlock('mysubscription/checkout_onepage_review_info_item','mysubscription.checkout.review.item')
            ->setTemplate('mysubscription/checkout/onepage/review/item.phtml');
        $parentBlock->setChild('mysubscription.checkout.review.item', $msitemBlock);
        return $this;
    }

    /*
     * Add totals html to the review section
     * */
    public function addTotalHtml($parentBlock){
        $msTotals = $this->getLayout()
            ->createBlock('mysubscription/checkout_onepage_review_info_item', 'mysubscription.checkout.review.total')
            ->setTemplate('mysubscription/checkout/onepage/review/total.phtml');
        $parentBlock->setChild('mysubscription.checkout.review.total', $msTotals);
        return $this;
    }

    /*
     * Add the place subscription order button
     * */
    public function addButtonHtml($parentBlock){
        $msButton = $this->getLayout()
            ->createBlock('mysubscription/checkout_onepage_review_info_item', 'mysubscription.checkout.review.button')
            ->setTemplate('mysubscription/checkout/onepage/review/button.phtml');
        $parentBlock->setChild('mysubscription.checkout.review.button', $msButton);
        return $this;
    }

    /*
     * Add the message while placing subscription order
     * */
    public function addMessageHtml($parentBlock){
        $msMessage = $this->getLayout()
            ->createBlock('mysubscription/checkout_onepage_review_info_item', 'mysubscription.checkout.review.message')
            ->setTemplate('mysubscription/checkout/onepage/review/message.phtml');
        $parentBlock->setChild('mysubscription.checkout.review.message', $msMessage);
        return $this;
    }
}