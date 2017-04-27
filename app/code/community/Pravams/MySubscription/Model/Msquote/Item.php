<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Msquote_Item extends Mage_Core_Model_Abstract
{
    /*
     * Item options array
     * @var array
     * */
    protected $_options = array();

    /*
     * Item options by code cache
     * */
    protected $_optionsByCode = array();

    /*
     * Not represent options
     *
     * @var array
     * */
    protected $_notRepresentOptions = array('info_buyRequest');

    /*
     * Flag starting that options were successfully saved
     * */
    protected $_flagOptionsSaved = null;

    /*
     * Msquote Model Object
     * @var Pravams_MySubscription_Model_Msquote
     * */
//    protected $_msquote;

    /*
     * Class Constructor
     * */
    protected function _construct(){
        $this->_init('mysubscription/msquote_item');
    }

    /*
     * Save item options
     *
     * @return Pravams_MySubscription_Model_Msquote_Item
     * */
    protected function _saveItemOptions(){
        foreach ($this->_options as $index => $option) {
            if ($option->isDeleted()) {
                $option->delete();
                unset($this->_options[$index]);
                unset($this->_optionsByCode[$option->getCode()]);
            } else {
                $option->save();
            }
        }

        // Report to watchers that options were saved
        $this->_flagOptionsSaved = true;

        return $this;
    }

    /*
     * Save model plus its options
     * Ensures saving options in case when resource model was not changed
     * */
    public function save(){
        $hasDataChanges = $this->hasDataChanges();
        $this->_flagOptionsSaved = false;

        parent::save();

        if ($hasDataChanges && !$this->_flagOptionsSaved) {
            $this->_saveItemOptions();
        }
    }

    /*
     * Save item options after item saved
     *
     * @return Pravams_MySubscription_Model_Msquote_Item
     * */
    protected function _afterSave(){
        $this->_saveItemOptions();
        return parent::_afterSave();
    }

    /*
     * Iniialize quote item options
     *
     * @param array $options
     * @return Pravams_MySubscription_Model_Msquote_Item
     * */
    public function setOptions($options){
        foreach($options as $option){
            $this->addOption($option);
        }
        return $this;
    }

    /*
     * Get all items options
     *
     * @return array
     * */
    public function getOptions(){
        return $this->_options;
    }

    /*
     * Get all item options as array with codes in array key
     * @return array
     * */
    public function getOptionsByCode(){
        return $this->_optionsByCode;
    }

    /*
     * Get item option by code
     *
     * @param string $code
     * @return Pravams_MySubscription_Model_Msquote_Item_Option || null
     * */
    public function getOptionByCode($code){
        if (isset($this->_optionsByCode[$code]) && !$this->_optionsByCode[$code]->isDeleted()) {
            return $this->_optionsByCode[$code];
        }
        return null;
    }

    /*
     * Register option code
     *
     * @param Pravams_MySubscription_Model_Msquote_Item_Option option
     * @return Pravams_MySubscription_Model_Msquote_Item
     * */
    protected function _addOptionCode($option){
        if (!isset($this->_optionsByCode[$option->getCode()])) {
            $this->_optionsByCode[$option->getCode()] = $option;
        } else {
            Mage::throwException(Mage::helper('sales')->__('An item option with code %s already exists.', $option->getCode()));
        }
        return $this;
    }

    /*
     * Add option to item
     *
     * @param Pravams_MySubscription_Model_Msquote_Item_Option | Varien_Object $option
     * @return Pravams_MySubscription_Model_Msquote_Item
     * */
    public function addOption($option){
        if (is_array($option)) {
            $option = Mage::getModel('mysubscription/msquote_item_option')->setData($option)
                ->setItem($this);
        } elseif (($option instanceof Varien_Object) && !($option instanceof Pravams_MySubscription_Model_Msquote_Item_Option)) {
            $option = Mage::getModel('mysubscription/msquote_item_option')->setData($option->getData())
                ->setProduct($option->getProduct())
                ->setItem($this);
        } elseif ($option instanceof Pravams_MySubscription_Model_Msquote_Item_Option) {
            $option->setItem($this);
        } else {
            Mage::throwException(Mag::helper('sales')->__('Invalid item option format'));
        }

        if ($exOption = $this->getOptionByCode($option->getCode())) {
            $exOption->addData($option->getData());
        } else {
            $this->_addOptionCode($option);
            $this->_options[] = $option;
        }
        return $this;
    }

    /*
     * Quote item before save prepate data process
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if($this->getMsquote()){
            $this->setMsquoteId($this->getMsquote()->getId());
        }
        
        /*
         * Setting the parent item id for the child item
         */
        if($this->getParentItem()){
            $this->setParentItemId($this->getParentItem()->getId());
        }                
        return $this;
    }

    /*
     * Prepare qty
     */
    protected function _prepareQty($qty){
        $qty = Mage::app()->getLocale()->getNumber($qty);
        $qty = ($qty > 0) ? $qty : 1;
        return $qty;
    }
    /*
     * Retrieve quote model object
     */
    public function getMsquote()
    {
        return $this->_msquote;
    }    
    
    public function setMsquote(Pravams_MySubscription_Model_Msquote $msquote)
    {
        $this->_msquote = $msquote;
        $this->setMsquoteId($msquote->getId());
        return $this;
    }
    
    /*
     * Adding qty to quote item
     */
    public function addQty($qty){
        $oldQty = $this->getQty();
        $qty = $this->_prepareQty($qty);
        
        if(!$this->getParentItem() || !$this->getId()){
            $this->setQtyAdd($qty);
            $this->setQty($oldQty + $qty);
        }
        return $this;
    }

    /*
     * Check product representation in item
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * */
    public function representProduct($product){
        $itemProduct = $this->getProduct();
        if(!$product || $itemProduct->getId() != $product->getId()){
            return false;
        }

        /*
         * Check may be product is planned to be a child of some quote item - in this case we limit search
         * only within same patent item
         * */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }

        // Check options
        $itemOptions = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if (!$this->compareOptions($itemOptions, $productOptions)) {
            return false;
        }
        if (!$this->compareOptions($productOptions, $itemOptions)) {
            return false;
        }
        return true;
    }

    /*
     * Check if two options array are identical
     * First options array is prerogative
     * Second options array is checked against first one
     *
     * @param array $options1
     * @param array $options2
     * @return bool
     * */
    public function compareOptions($options1, $options2){
        foreach($options1 as $option){
            $code = $option->getCode();
            if (in_array($code, $this->_notRepresentOptions)) {
                continue;
            }
            if (!isset($options2[$code])
                || ($options2[$code]->getValue() === null)
                || $options2[$code]->getValue() != $option->getValue()
            ) {
                return false;
            }
        }
        return true;
    }

    /*
     * Declare quote item qty
     */
    public function setQty($qty){
        $qty = $this->_prepareQty($qty);
//        $oldQty = $this->_getData('qty');
        $this->setData('qty', $qty);
        
        return $this;
    }        

    /*
     * Set the product price
     * */
    public function setMsprice($product){
        if($product->getSpecialPrice() > 0){
            $this->setPrice($product->getSpecialPrice());
            $this->setBasePrice($product->getSpecialPrice());
        }else{
            $this->setPrice($product->getPrice());
            $this->setBasePrice($product->getPrice());
        }
    return $this;
    }

    /*
     * Set the product data during add to cart
     * */
    public function setProduct($product)
    {
        $this->setData('product', $product)
            ->setProductId($product->getId())
            ->setSku($product->getData('sku'))
            ->setName($product->getName())
            ->setDescription($product->getDescription())
            ->setProductType($product->getTypeId())
            ->setTaxClassId($product->getTaxClassId());

        $this->setMsprice($product);

        $this->setRowTotals($product->getPrice());
        
        return $this;
    }
    
    public function setRowTotals($productPrice){
        $taxPercent = $this->getTaxRate();
        $taxBaseTotal = ($taxPercent * $productPrice) / 100;
        $taxTotal = $taxBaseTotal * $this->getQty();
        
        $this->setTaxPercent($taxPercent);
        $this->setBaseTaxAmount($taxBaseTotal);
        $this->setTaxAmount($taxTotal);
        
        $this->setBaseRowTotal($productPrice * $this->getQty());
        $this->setRowTotal($this->getBaseRowTotal());
        return $this;
    }
    
    public function getTaxRate(){
        
        $msQuote = Mage::getSingleton('mysubscription/cart')->getMsquote();
        if(!$msQuote->getId()){
            return '0';
        }
        
        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        $store = Mage::app()->getStore();
        
        $shippingAddress = Mage::getModel('mysubscription/address')->getShippingAddress($msQuote);
        if(!$shippingAddress->getAddressId()){
            $request = $taxCalculationModel->getRateOriginRequest($store);
            return $taxCalculationModel->getRate($request);
        }
        
        $request = new Varien_Object();
        $request->setCountryId($shippingAddress->getCountryId())
                ->setRegionId($shippingAddress->getRegionId())
                ->setPostcode($shippingAddress->getPostcode())
                ->setStore($store->getId())
                ->setCustomerClassId($msQuote->getCustomerTaxClassId())
                ->setProductClassId($this->getTaxClassId())
                ->setStore($store)
                ;
        
        return $taxCalculationModel->getRate($request);
    }

    public function getProduct(){
        $productId = $this->getProductId();
        $msQuote = Mage::getSingleton('mysubscription/cart')->getMsquote();
        return Mage::getModel('catalog/product')
            ->setStoreId($msQuote->getStoreId())
            ->load($productId);
    }
}
?>
