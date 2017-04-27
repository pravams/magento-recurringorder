<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Helper_Product_Configuration extends Mage_Core_Helper_Abstract
{
    /*
     * retrieve product options
     * */
    public function getCustomOptions($item) {

    }

    /*
     * return product options for configurable products
     * */
    public function getConfigurableOptions($item){
        $productOptions = array();

        $product = $item->getProduct();
        $alloptions = Mage::getModel('mysubscription/msquote_item_option')->getCollection()->getOptionsByItem($item);

        foreach($alloptions as $_option){
            if( ($_option['item_id'] == $item->getItemId()) && $_option['code'] == "info_buyRequest"){
                $origProductOptions = $_option['value'];
            }
        }

        $attributes = unserialize($origProductOptions);
        $superAttributes = $attributes['super_attribute'];
        $attributesInfo = array();
        foreach($superAttributes as $key => $val){
            $optionVal = Mage::getModel('catalog/resource_eav_attribute')->load($key);
            $label = $optionVal->getFrontendLabel();
            $value = $optionVal->getSource()->getOptionText($val);

            $attributesInfoOption['label'] = $label;
            $attributesInfoOption['value'] = $value;
            $attributesInfo[] = $attributesInfoOption;
        }

        $productOptions['info_buyRequest'] = $attributes;
        $productOptions['attributes_info'] = $attributesInfo;

        return $productOptions;
    }

    /*
     * return all the product option for all types of products
     * */
    public function getProductOptions($item)
    {
        $product = $item->getProduct();
        $typeId = $product->getTypeId();
        if ($typeId == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $options = $this->getConfigurableOptions($item);
        }
        return $options;
    }
}