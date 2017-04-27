<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Msquote extends Mage_Core_Model_Abstract
{

    const ACTIVE = '1';
    const INACTIVE = '0';

    protected $_items = null;
    
    /*
     * Msquote Customer Model Object
     * 
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;
    
    /*
     * Msquote Profile for subscription
     * @var Pravams_MySubscription_Model_Profile
     */
    protected $_profile;
    
    /*
     * Msquote address
     */
    protected $_addresses = array();
    
    /*
     * Msquote shipping method
     */
    protected $_shippingMethod;
    
    /*
     * Msquote payment method
     */
    protected $_payment;

    /*
     * Flag to update item during add to cart
     * */
    public $_updateItem;

    /*
     * Flag to make the quote virtual
     * */
    protected $_virtual;

    /*
     * init resource model
     */    
    protected function _construct(){
        $this->_init('mysubscription/msquote');
    }
    
    /*
     * Save items collection
     * 
     * @return Pravams_MySubscription_Model_Msquote
     */
    protected function _afterSave(){
        parent::_afterSave();
        
        if(null !== $this->_items){
            $this->getItemsCollection()->save();
        }

        Mage::helper('mysubscription/bundle')->updatePrice($this);

        return $this;
    }

    /*
     * Get the msquote virtual flag
     * */
    public function getIsVirtual(){
        return $this->getData('is_virtual');
    }

    /*
     * Get msquote store identifier
     * @return int
     * */
    public function getStoreId(){
        if (!$this->hasStoreId()) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_getData('store_id');
    }

    /*
     * Get msquote store model object
     * @return Mage_Core_Model_Store
     * */
    public function getStore(){
        return Mage::app()->getStore($this->getStoreId());
    }

    /*
     * Declare msquote store model
     * @param Mage_Core_Model_Store $store
     * @return Mage_Sales_Model_Quote
     * */
    public function setStore(Mage_Core_Model_Store $store){
        $this->setStoreId($store->getId());
        return $this;
    }

    /*
     * Add product to quote
     */
    public function addProduct(Mage_Catalog_Model_Product $product, $request = null){
        return $this->addProductAdvanced(
                $product,
                $request,
                Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL);        
    }
    
    /*
     * Advanced function to add product to quote 
     */
    public function addProductAdvanced(Mage_Catalog_Model_Product $product, $request = null, $processMode = null){
        if($request === null){
            $request = 1;
        }
        if(is_numeric($request)){
            $request = new Varien_Object(array('qty' => $request));
        }
        if(!($request instanceof Varien_Object)){
            Mage::throwException(Mage::helper('sales')->__('Invalid request for adding product to quote'));
        }
        
        $cartCandidates = $product->getTypeInstance(true)
            ->prepareForCartAdvanced($request, $product, $processMode);

        /*
         * Error message
         */
        if(is_string($cartCandidates)){
            return $cartCandidates;
        }
        
        /*
         * If prepare process return one object
         */
        if(!is_array($cartCandidates)){
            $cartCandidates = array($cartCandidates);
        }
        
        $parentItem = null;
        $errors = array();
        $items = array();
        
        foreach($cartCandidates as $candidate){
            //Child items can be sticked together only within their parent
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            $candidate->setStickWithinParent($stickWithinParent);
            $item = $this->_addCatalogProduct($candidate, $candidate->getCartQty());
            if($request->getResetCount() && !$stickWithinParent && $item->getId() === $request->getId()) {
                $item->setData('qty', 0);
            }
            $items[] = $item;
            
            /*
             * As parent item we should always use the item of first added product
             */
            if(!$parentItem){
                $parentItem = $item;
            }
            if($parentItem && $candidate->getParentProductId()){
                $item->setParentItem($parentItem);
            }
            
            /*
             * We specify qty after we know about parent stock
             */
            $item->addQty($candidate->getCartQty());
            
            //collect errors instead of throwing first one
            if($item->getHasError()){
                $message = $item->getMessage();
                if(!in_array($message, $errors)){
                    $errors[] = $message;
                }
            }
        }
        if(!empty($errors)){
            Mage::throwException(impode("\n", $errors));
        }
        return $item;
    }
    
    /*
     * Add catalog product object data to quote
     */
    protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty = 1){
        $newItem = false;
        $item = $this->getItemByProduct($product);
        if(!$item){
            $item = Mage::getModel('mysubscription/msquote_item');
            $item->setMsquote($this);
            if(Mage::app()->getStore()->isAdmin()){
                $item->setStoreId($this->getStore()->getId());
            }else{
                $item->setStoreId(Mage::app()->getStore()->getId());
            }
            $newItem = true;
        }
        
        /*
         * We can't modify existing child items
         */
        if($item->getId() && $product->getParentProductId()) {
            return $item;
        }

        $item->setOptions($product->getCustomOptions())
            ->setProduct($product);
        
        // Add only item that is not in quote already (there can be other new or already saved item)
        if($newItem){
            $this->addItem($item);
        }else{
            $this->_updateItem = true;
        }
        
        return $item;
    }

    /*
     * Retrieve quote Item by product id
     * @param Mage_Catalog_Model_Product
     * @return Pravams_MySubscription_Model_Msquote_Item || false
     * */
    public function getItemByProduct($product){
        foreach($this->getAllItems() as $item){
            if ($item->representProduct($product)) {
                return $item;
            }
        }
        return false;
    }
    
    /*
     * Adding new item to quote 
     */
    public function addItem(Pravams_MySubscription_Model_Msquote_Item $item){
        $item->setMsquote($this);
        if(!$item->getId()){
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /*
     * Retrieve msquote items array
     * @return array
     * */
    public function getAllItems(){
        $items = array();
        $itemsCollect = Mage::getModel('mysubscription/msquote_item')
            ->getCollection()
            ->addFieldToFilter('msquote_id', $this->getId());
        foreach ($itemsCollect as $item) {
            if (!$item->isDeleted()) {
                $items[] = Mage::getModel('mysubscription/msquote_item')->load($item->getItemId());
            }
        }
        return $items;
    }

    /*
     * Retrieve quote item collection
     */
    public function getItemsCollection($useCache = true){
        if($this->hasItemsCollection()){
            return $this->getData('items_collection');
        }
        if(is_null($this->_items)){
            $this->_items = Mage::getModel('mysubscription/msquote_item')->getCollection();
            $this->_items->setMsquote($this);
        }

        return $this->_items;
    }

    /*
     * Check if the Msquote has any items
     * @return bool
     * */
    public function hasItems(){
        if($this->getItemsCollection()->count()){
            return true;
        }else{
            return false;
        }
    }

    /*
     * Retrieve the customer from the Msquote
     * */
    public function getCustomer(){
        if(is_null($this->_customer)){
            $this->_customer = Mage::getModel('customer/customer');
            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
                if (!$this->_customer->getId()) {
                    $this->_customer->setCustomerId(null);
                }
            }
        }

        return $this->_customer;
    }

    /*
     * @return Mage_Customer_Model_Address
     * */
    protected function _getAddress($addressId){
        $address = Mage::getModel('customer/address')->load($addressId);
        if(!$address->getId()){
            return null;
        }
        return $address;
    }

    /*
     * Retrieve the Billing Address from the msquote
     * @return Pravams_MySubscription_Model_Address
     * */
    public function getMsBillingAddress(){
        $msquoteId = $this->getId();
        $billing = Mage::getModel('mysubscription/address')->getBillingAddress($this);
        $billing->setMsquote($this);
        return $billing;
    }

    /*
     * @return Mage_Customer_Model_Address
     * */
    public function getBillingAddress(){
        if(array_key_exists('0', $this->_addresses)){
            return $this->_addresses[0];
        }

        $billing = $this->getMsBillingAddress();
        $billingId = $billing->getCustomerAddressId();
        $customerBilling = $this->_getAddress($billingId);
        $this->_addresses[] = $customerBilling;
        return $customerBilling;
    }

    /*
     * Retrieve the Shipping Address from the msquote
     * @return Pravams_MySubscription_Model_Address
     * */
    public function getMsShippingAddress(){
        $msquoteId = $this->getId();
        $shipping = Mage::getModel('mysubscription/address')->getShippingAddress($this);
        $shipping->setMsquote($this);
        return $shipping;
    }

    /*
     * Retrieve shipping address from the Customer Address
     * @return Mage_Customer_Model_Address
     * */
    public function getShippingAddress(){
        if(array_key_exists('1', $this->_addresses)){
            return $this->_addresses[1];
        }
        $shipping = $this->getMsShippingAddress();
        $shippingId = $shipping->getCustomerAddressId();
        $customerShipping = $this->_getAddress($shippingId);
        $this->_addresses[] = $customerShipping;
        return $customerShipping;
    }

    /*
     * Retrieve the Shipping rate from Msquote
     * */
    public function getShippingRateByCode($method){
        $msquoteId = $this->getId();
        $shipping = $this->getMsShippingAddress();
        $addressId = $shipping->getAddressId();

        $rate = Mage::getModel('mysubscription/shippingrate')->getCollection()
                ->addFieldToFilter('address_id', $addressId)
                ->addFieldToFilter('method', $method)
                ->getFirstItem();
        return $rate;
    }

    /*
     * Retrieve the Payment method from Msquote
     * */
    public function getPayment(){
        $payment = Mage::getModel('mysubscription/payment')->getPaymentMethod($this);
        $this->_payment = $payment;
        return $payment;
    }

    /*
     * Define customer object
     * 
     * @param Mage_Customer_Model_Customer
     * @return Pravams_MySubscription_Model_Msquote
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer){
        $this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        Mage::helper('core')->copyFieldSet('customer_account', 'to_quote', $customer, $this);
        return $this;
    }

    /*
     * set profile data to the msquote
     */
    public function setProfile(Pravams_MySubscription_Model_Profile $profile){
        $profile->save();
        $this->_profile = $profile;
        return $this;
    }
    
    /*
     * set the billing and shipping address
     */
    public function setMsAddress($msaddress, $address, $type){
        $shippingAmount = ($this->_virtual) ? 0 : $address->getShippingAmount();

        $msaddress->setMsquoteId($this->getId())
                ->setCustomerId($address->getCustomerId())
                ->setSaveInAddressBook($address->getSaveInAddressBook())
                ->setCustomerAddressId($address->getCustomerAddressId())
                ->setAddressType($type)
                ->setEmail($address->getEmail())
                ->setPrefix($address->getPrefix())
                ->setFirstname($address->getFirstname())
                ->setMiddlename($address->getMiddlename())
                ->setLastname($address->getLastname())
                ->setSuffix($address->getSuffix())
                ->setCompany($address->getCompany())
                ->setStreet($address->getStreet(Mage_Sales_Model_Quote_Address::DEFAULT_DEST_STREET))
                ->setCity($address->getCity())
                ->setRegion($address->getRegion())
                ->setRegionId($address->getRegionId())
                ->setPostcode($address->getPostcode())
                ->setCountryId($address->getCountryId())
                ->setTelephone($address->getTelephone())
                ->setFax($address->getFax())
                ->setSameAsBilling($address->getSamesAsBilling())
                ->setFreeShipping($address->getFreeShipping())
                ->setCollectShippingRates($address->getCollectShippingRates())
                ->setShippingMethod($address->getShippingMethod())
                ->setShippingDescription($address->getShippingDescription())
                ->setWeight($address->getWeight())
                ->setSubtotal($this->getSubtotal())
                ->setBaseSubtotal($this->getBaseSubtotal())
                ->setShippingAmount($shippingAmount)
                ->setGrandTotal($this->getSubtotal() + $shippingAmount)
                ->setBaseGrandTotal($this->getBaseSubtotal() + $shippingAmount)
                ;
        
        $msaddress->save();
        
        $this->_addresses[] = $msaddress;

        return $this;        
    }
    
    /*
     * set shipping method
     */
    public function setMsShippingMethod($msShippingMethod, $shippingAddress){
        $msAddress = $this->_addresses[1];
        $shippingMethod = $shippingAddress->getShippingMethod();
        if($shippingMethod){
            $rate = $shippingAddress->getShippingRateByCode($shippingMethod);
            if($msAddress->getId()){
                $msShippingMethod->setAddressId($msAddress->getAddressId())
                    ->setCarrier($rate->getCarrier())
                    ->setCarrierTitle($rate->getCarrierTitle())
                    ->setCode($shippingMethod)
                    ->setMethod($rate->getMethod())
                    ->setMethodDescription($rate->getMethodDescription())
                    ->setPrice($rate->getPrice())
                    ->setMethodTitle($rate->getMethodTitle())
                ;
                $shippingAddress->setShippingAmount($rate->getPrice());
                $shippingAddress->setShippingDescription($rate->getCarrierTitle());
                $msShippingMethod->save();
            }
        }

        $this->_shippingMethod = $shippingMethod;
        
        return $this;
    }
    
    /*
     * set the payment method
     */
    public function setPayment($mspayment, $payment){
        $mspayment->setMsquoteId($this->getId())
                ->setMethod($payment->getMethod())
                ->setCcType($payment->getCcType())
                ->setCcNumberEnc($payment->getCcNumberEnc())
                ->setCcLast4($payment->getCcLast4())
                ->setCcCidEnc($payment->getCcCidEnc())
                ->setCcOwner($payment->getCcOwner())
                ->setCcExpMonth($payment->getCcExpMonth())
                ->setCcExpYear($payment->getCcExpYear())
                ->setCcSsOwner($payment->getCcSsOwner())
                ->setCcSsStartMonth($payment->getCcSsStartMonth())
                ->setCcSsStartYear($payment->getCcSsStartYear())
                ->setPoNumber($payment->getPoNumber())
                ->setAdditionalData($payment->getAdditionalData())
                ;
        $mspayment->save();
        $this->_payment = $mspayment;
        return $this;
    }
    
    /*
     * set cart qty in Msquote
     * @return Pravams_MySubscription_Model_Msquote
     */
    public function setCartQty(){
        if(!$this->getItemsCount()){
            $this->setItemsCount(1);
        }else{
            $this->setItemsCount($this->getItemsCount() + 1);
        }
        
        $cartRequest = Mage::getSingleton('mysubscription/cart')->_requestInfo;
        $cartItemQty = $cartRequest->getQty();
        
        $this->setItemsQty($cartItemQty + $this->getItemsQty());
        return $this;
    }
    
    /*
     * Row totals
     * 
     * @return Pravams_MySubscription_Model_Msquote
     */
    public function updateRowTotals(){
        $itemsCollect = Mage::getModel('mysubscription/msquote_item')
                ->getCollection()
                ->addFieldToFilter('msquote_id', $this->getId());

        $quoteIsVirtualFlag = true;
        $this->setBaseSubtotal(0);
        $this->setGrandTotal(0);

        $taxAmount = 0;
        $bundleFlagLoop = false;
        foreach($itemsCollect as $_item){
            $quoteIsVirtualFlag = true;
            $msitem = Mage::getModel('mysubscription/msquote_item')->load($_item->getItemId());
            $msitem->setRowTotals($msitem->getPrice());

            if(!$bundleFlagLoop){
                $taxAmount = $taxAmount + $msitem->getTaxAmount();
                $baseSubTotal = $msitem->getBaseRowTotal() + $this->getBaseSubtotal();
                $subtotal = $baseSubTotal;

                $grandTotal = $msitem->getRowTotal() + $msitem->getTaxAmount() + $this->getGrandTotal();
                $baseGrandTotal = $grandTotal;
            }

            $baseCurrency = Mage::app()->getStore()->getBaseCurrency();
            
            $this->setGlobalCurrency($baseCurrency->getCurrencyCode())
                 ->setBaseSubtotal($baseSubTotal)
                 ->setSubtotal($subtotal)
                 ->setBaseGrandTotal($baseGrandTotal)
                 ->setGrandTotal($grandTotal);

            $msitem->save();

            if( ($msitem->getProductType() == "virtual") || $msitem->getProductType() == "downloadable" ){
                $quoteIsVirtualFlag = false;
            }
            if($msitem->getProductType() == "bundle"){
                $bundleFlagLoop = true;
            }
        }

        if(!$quoteIsVirtualFlag){
            $this->_virtual = true;
            $this->setIsVirtual(true);
        }else{
            $this->_virtual = false;
            $this->setIsVirtual(false);
        }

        /*
         * update the row totals for address
         * */
        $billingAddress = Mage::getModel('mysubscription/address')->getBillingAddress($this);

        if(!$this->_virtual){
            $shippingAddress = Mage::getModel('mysubscription/address')->getShippingAddress($this);
            $grandTotal = $shippingAddress->getShippingAmount() + $shippingAddress->getSubtotal() + $taxAmount;
            if($shippingAddress->getId()){
                $shippingAddress->setTaxAmount($taxAmount)
                    ->setBaseTaxAmount($taxAmount)
                    ->setGrandTotal($grandTotal)
                    ->setBaseGrandTotal($grandTotal)
                    ->save();
            }
        }else{
            $grandTotal = $billingAddress->getSubtotal() + $taxAmount;
        }

        if($billingAddress->getId()){
            $billingAddress->setTaxAmount($taxAmount)
                ->setBaseTaxAmount($taxAmount)
                ->setGrandTotal($grandTotal)
                ->setBaseGrandTotal($grandTotal)
                ->save();
        }

        return $this;
    }
    
    /*
     * setting default totals
     */
    public function collectTotals(){
        $baseCurrency = $this->getStore()->getBaseCurrency();
        $globalCurrencyCode = Mage::app()->getBaseCurrencyCode();

        if($this->hasForcedCurrency()){
            $quoteCurrency = $this->getForcedCurrency();
        } else {
            $quoteCurrency = $this->getStore()->getCurrentCurrency();
        }

        $this->setGlobalCurrencyCode($baseCurrency->getCurrencyCode())
             ->setBaseCurrencyCode($baseCurrency->getCurrencyCode())
             ->setStoreCurrencyCode($baseCurrency->getCurrencyCode())
             ->setQuoteCurrencyCode($baseCurrency->getCurrencyCode())
             ->setBaseSubtotal(0)
             ->setSubtotal(0)
             ->setBaseGrandTotal(0)
             ->setGrandTotal(0);

        //deprecated, by Magento
        $this->setStoreToBaseRate($baseCurrency->getRate($globalCurrencyCode));
        $this->setStoreToQuote($baseCurrency->getRate($quoteCurrency));

        $this->setBaseToGlobalRate($baseCurrency->getRate($globalCurrencyCode));
        $this->setBaseToQuoteRate($baseCurrency->getRate($quoteCurrency));

        return $this;
    }
}
?>
