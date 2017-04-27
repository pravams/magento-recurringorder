<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Cart extends Varien_Object implements Pravams_MySubscription_Model_Cart_Interface
{
    public $_requestInfo;
    /*
     * Retrieve Mysubscription checkout session model
     */
    public function getCheckoutSession(){
        return Mage::getSingleton('mysubscription/session');
    }
    
    public function setMsquote(Pravams_MySubscription_Model_Msquote $msquote) {
        $this->setData('msquote', $msquote);
        return $this;
    }        
    
    public function getMsQuote(){
        if(!$this->hasData('msquote')){
            $this->setData('msquote', $this->getCheckoutSession()->getMsquote());
        }        
        return $this->_getData('msquote');
    }        
    
    public function save(){
        $msquote = $this->getMsQuote();
        $msquote->collectTotals();
        $msquote->setCartQty();
        if($msquote->_updateItem){
            $this->updateAddedItem();
        }else{
            $msquote->save();
        }

        $this->getCheckoutSession()->setMsquoteId($this->getMsQuote()->getId());
        
        return $this;
    }

    /*
     * Update cart if same item is added again
     * */
    public function updateAddedItem(){
        $msquote = $this->getMsQuote();

        $cartRequest = Mage::getSingleton('mysubscription/cart')->_requestInfo;
        $cartItemQty = $cartRequest->getQty();
        foreach($msquote->getAllItems() as $item){
            $msitem = Mage::getModel('mysubscription/msquote_item')->load($item->getItemId());
            $msItemQty = $cartItemQty + $msitem->getQty();
            $msItemOldQty = $msitem->getQty();
            if($msitem->getProductId() == $cartRequest['product']){
                $this->_saveCartItem($msitem, $msItemQty);
            }
        }

        /*
         * Update the quote object
         * */
        $msQuoteObj = Mage::getModel('mysubscription/msquote')->load($msquote->getEntityId());
        $msNewQty = $msQuoteObj->getItemsQty() + $msItemQty-$msItemOldQty;
        $msQuoteObj->setItemsQty($msNewQty)
                ->save();
        return;
    }

    /*
     * Save cart implements interface method
     */
    public function saveMsQuote() {
        $this->save();
    }
    
    /*
     * Get request for product add to cart procedure
     */
    protected function _getProductRequest($requestInfo){
        if($requestInfo instanceof Varien_Object){
            $request = $requestInfo;
        }else if(is_numeric($requestInfo)){
            $request = new Varien_Object(array('qty' => $requestInfo));
        }else{
            $request = new Varien_Object($requestInfo);
        }
        
        if(!$request->hasQty()){
            $request->setQty(1);
        }
        
        return $request;                
    }

    /*
     * Save Cart item
     * */
    protected function _saveCartItem($msitem, $msItemQty){
        $msitemPrice = $msitem->getPrice();
        $taxPercent = $msitem->getTaxPercent();
        $msitemRowTotal = $msitemPrice * $msItemQty;
        $msitemBaseRowTotal = $msitemPrice * $msItemQty;
        $msTaxAmount = $msitemRowTotal * ($taxPercent/100);

        $msitem->setQty($msItemQty);
        $msitem->setTaxAmount($msTaxAmount);
        $msitem->setBaseTaxAmount($msTaxAmount);
        $msitem->setRowTotal($msitemRowTotal);
        $msitem->setBaseRowTotal($msitemBaseRowTotal);
        $msitem->save();
        return;
    }

    /*
     * Add product to mysubscription cart
     * 
     */
    public function addProduct($productInfo, $requestInfo = null) {
        $product = $productInfo;
        $this->_requestInfo = $this->_getProductRequest($requestInfo);
        
        $msquote = $this->getMsQuote();
        $msquote->addProduct($product, $this->_requestInfo);
        return $this;
    }

    /*
     * Update items in the MySubscription Cart
     * */
    public function updateItems($mscartData){
        $msQuote = $this->getMsquote();
        $msquoteItems = $msQuote->getItemsCollection();
        foreach($msquoteItems as $_msquoteItem){
            $msitem = Mage::getModel('mysubscription/msquote_item')->load($_msquoteItem->getItemId());
            $msItemQty = $mscartData[$_msquoteItem->getItemId()]['qty'];
            $msItemOldQty = $msitem->getQty();
            if($msItemQty > 0){
                $this->_saveCartItem($msitem, $msItemQty);
            }

            /*
             * Update the qty in the Quote
             * */
            if($msItemQty){
                $msNewQty = $msQuote->getItemsQty() + $msItemQty-$msItemOldQty;
                $msQuoteObj = Mage::getModel('mysubscription/msquote')->load($msQuote->getEntityId());
                $msQuoteObj->setItemsQty($msNewQty)
                    ->save();
            }
        }

        return $this;
    }

    /*
     * Remove MySubscription items from the cart
     * */
    public function removeItem($cartItemId){
        $msQuote = $this->getMsquote();
        $msQuoteItems = Mage::getModel('mysubscription/msquote_item')->getCollection()
                    ->addFieldToFilter('msquote_id', $msQuote->getId());
        foreach($msQuoteItems as $_msitem){
            if(($_msitem->getItemId() == $cartItemId) || ($_msitem->getParentItemId() == $cartItemId)){
                $deleteItem = Mage::getModel('mysubscription/msquote_item')->load($_msitem->getItemId());
                $deleteItem->delete();
            }
        }

        if($msQuote->getItemsCount()){
            $msNewCount = $msQuote->getItemsCount() - 1;
            $msQuoteObj = Mage::getModel('mysubscription/msquote')->load($msQuote->getEntityId());
            $msQuoteObj->setItemsCount($msNewCount)
                ->save();
        }
        return $this;
    }
}
?>
