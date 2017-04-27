<?php
/**
 * Pravams Payment Module
 *
 * @category    Pravams
 * @package     Pravams_Payment
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isBankTransferEnabled(){
        return Mage::getStoreConfig('mysubscription/ms_payment/banktransfer', Mage::app()->getStore()->getStoreId());
    }

    public function isCodEnabled(){
        return Mage::getStoreConfig('mysubscription/ms_payment/cod', Mage::app()->getStore()->getStoreId());
    }

    public function isCcEnabled(){
        return Mage::getStoreConfig('mysubscription/ms_payment/cc', Mage::app()->getStore()->getStoreId());
    }

    public function isCheckEnabled(){
        return Mage::getStoreConfig('mysubscription/ms_payment/check', Mage::app()->getStore()->getStoreId());
    }

    public function isPoEnabled(){
        return Mage::getStoreConfig('mysubscription/ms_payment/po', Mage::app()->getStore()->getStoreId());
    }
}