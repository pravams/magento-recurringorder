<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_Mysubscription_Model_Resource_Msquote_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /*
     * Collection Msquote instance
     *
     * @var Pravams_MySubscription_Model_Msquote
     * */
    protected $_msquote;

    /*
     * Product Ids array
     * @var array
     * */
    protected $_productIds = array();

    /*
     * Initialize resource Model
     * */
    protected function _construct(){
        $this->_init('mysubscription/msquote_item');
    }

    /*
     * Retrieve store Id (From Msquote)
     * @return int
     * */
    public function getStoreId(){
        return (int)$this->_msquote->getStoreId();
    }

    /*
     * Set Quote object to collection 
     */
    public function setMsquote($msquote){
        $this->_msquote = $msquote;
        $msquoteId = $msquote->getId();
        if($msquoteId){
            $this->addFieldToFilter('msquote_id', $msquote->getId());
        }else{
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
                
        return $this;
    }
    
    public function save(){
        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->getId()){
//        if(Mage::getSingleton('mysubscription/cart')->getCheckoutSession()->getMsquoteId()){
              $this->_setIsLoaded(true);
        }
        parent::save();
    }

    /*
     * After load processing
     *
     * @return Pravams_MySubscription_Model_Resource_Msquote_Item_Collection
     * */
    protected function _afterLoad(){
        parent::_afterLoad();

        /*
         * Assign parent items
         * */
        foreach ($this as $item) {
            if ($item->getParentItemId()) {
                $item->setParentItem($item->getItemById($item->getParentItemId()));
            }
            if ($this->_msquote) {
                $item->setMsquote($this->_msquote);
            }
        }

        /*
         * Assign options and products
         * */
        $this->_assignOptions();
//        $this->_assignProducts();

        return $this;
    }

    /*
     * Add options to items
     *
     * @return Pravams_MySubscription_Model_Resource_Msquote_Item_Collection
     * */
    protected function _assignOptions(){
        $itemIds = array_keys($this->_items);
        $optionsCollection = Mage::getModel('mysubscription/msquote_item_option')->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionsCollection->getOptionsByItem($item));
        }
        $productIds = $optionsCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Add products to items and item options
     *
     * @return Mage_Sales_Model_Resource_Quote_Item_Collection
     */
//    protected function _assignProducts()
//    {
//        Varien_Profiler::start('QUOTE:'.__METHOD__);
//        $productIds = array();
//        foreach ($this as $item) {
//            $productIds[] = (int)$item->getProductId();
//        }
//        $this->_productIds = array_merge($this->_productIds, $productIds);
//
//        $productCollection = Mage::getModel('catalog/product')->getCollection()
//            ->setStoreId($this->getStoreId())
//            ->addIdFilter($this->_productIds)
//            ->addAttributeToSelect(Mage::getSingleton('sales/quote_config')->getProductAttributes())
//            ->addOptionsToResult()
//            ->addStoreFilter()
//            ->addUrlRewrite()
//            ->addTierPriceData();
//
//        Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
//            'collection'            => $productCollection,
//            'store_id'              => $this->getStoreId(),
//        ));
//        Mage::dispatchEvent('sales_quote_item_collection_products_after_load', array(
//            'product_collection'    => $productCollection
//        ));
//
//        $recollectQuote = false;
//        foreach ($this as $item) {
//            $product = $productCollection->getItemById($item->getProductId());
//            if ($product) {
//                $product->setCustomOptions(array());
//                $qtyOptions         = array();
//                $optionProductIds   = array();
//                foreach ($item->getOptions() as $option) {
//                    /**
//                     * Call type-specific logic for product associated with quote item
//                     */
//                    $product->getTypeInstance(true)->assignProductToOption(
//                        $productCollection->getItemById($option->getProductId()),
//                        $option,
//                        $product
//                    );
//
//                    if (is_object($option->getProduct()) && $option->getProduct()->getId() != $product->getId()) {
//                        $optionProductIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
//                    }
//                }
//
//                if ($optionProductIds) {
//                    foreach ($optionProductIds as $optionProductId) {
//                        $qtyOption = $item->getOptionByCode('product_qty_' . $optionProductId);
//                        if ($qtyOption) {
//                            $qtyOptions[$optionProductId] = $qtyOption;
//                        }
//                    }
//                }
//
//                $item->setQtyOptions($qtyOptions)->setProduct($product);
//            } else {
//                $item->isDeleted(true);
//                $recollectQuote = true;
//            }
//            $item->checkData();
//        }
//
//        if ($recollectQuote && $this->_msquote) {
//            $this->_msquote->collectTotals();
//        }
//        Varien_Profiler::stop('QUOTE:'.__METHOD__);
//
//        return $this;
//    }
}
?>
