<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Helper_Bundle extends Mage_Core_Helper_Abstract
{
    protected  $_price = 0;
    protected  $_basePrice = 0;
    protected  $_taxPercent = 0;
    protected  $_taxAmount = 0;
    protected  $_baseTaxAmount = 0;
    protected  $_taxId = 0;

    /*
     * bundle item from the quote
     * */
    protected $_bundle;

    /*
     * Update product price for bundle items
     * */
    public function updatePrice($msquote){
        if($this->_price > 0){
            return;
        }
        $items = $msquote->getAllItems();
        foreach($items as $_item){
            if($_item->getProductType() == 'simple'){
                $this->_percent = $_item->getTaxPercent();
                $this->_taxClassId = $_item->getTaxClassId();
                $this->_taxAmount += $_item->getTaxAmount();
                $this->_baseTaxAmount +=  $_item->getBaseTaxAmount();
                $this->_price += $_item->getPrice();
                $this->_basePrice += $_item->getBasePrice();

            }else if($_item->getProductType() == 'bundle'){
                $this->_bundle = $_item;
            }
        }
        if($this->_bundle){
            $this->_bundle->setPrice($this->_price)
                ->setBasePrice($this->_basePrice)
                ->setTaxPercent($this->_percent)
                ->setTaxAmount($this->_taxAmount)
                ->setBaseTaxAmount($this->_baseTaxAmount)
                ->setTaxClassId($this->_taxClassId)
                ->save();
        }
        return;
    }
}