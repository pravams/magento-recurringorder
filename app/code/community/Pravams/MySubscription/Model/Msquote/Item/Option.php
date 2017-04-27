<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Model_Msquote_Item_Option extends Mage_Core_Model_Abstract
    implements Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    protected $_item;
    protected $_product;

    /*
     * Initialize resource model
     * */
    protected function _construct(){
        $this->_init('mysubscription/msquote_item_option');
    }

    /*
     * Checks that item option model has data changes
     *
     * @return boolean
     * */
    protected function _hasModelChanged(){
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /*
     * Set Msquote Item
     *
     * @param Pravams_MySubscription_Model_Msquote_Item $item
     * @return Pravams_MySubscription_Model_Msquote_Item_Option
     * */
    public function setItem($item){
        $this->setItemId($item->getId());
        $this->_item = $item;
        return $this;
    }

    /*
     * Get option item
     *
     * @return Pravams_MySubscription_Model_Msquote_Item
     * */
    public function getItem(){
        return $this->_item;
    }

    /*
     * Set option product
     * @param Mage_Catalog_Model_Product $product
     * @return Pravams_MySubscription_Model_Msquote_Item_Option
     * */
    public function setProduct($product){
        $this->setProductId($product->getId());
        $this->_product = $product;
        return $this;
    }

    /*
     * Get option product
     *
     * @return Mage_Catalog_Model_Product
     * */
    public function getProduct(){
        return $this->_product;
    }

    /*
     * Get option value
     *
     * @return mixed
     * */
    public function getValue(){
        return $this->_getData('value');
    }

    /*
     * Initialize item identifier before save data
     *
     * @return Pravams_MySubscription_Model_Msquote_Item_Option
     * */
    protected function _beforeSave(){
        if ($this->getItem()){
            $this->setItemId($this->getItem()->getId());
        }
        return parent::_beforeSave();
    }

    /*
     * Clone option object
     *
     * @return Pravams_MySubscription_Model_Msquote_Item_Option
     * */
    public function __clone(){
        $this->setId(null);
        $this->_item = null;
        return $this;
    }
}