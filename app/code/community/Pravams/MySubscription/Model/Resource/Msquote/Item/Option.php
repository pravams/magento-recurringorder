<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Model_Resource_Msquote_Item_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    /*
     * Main table and field initialization
     * */
    protected function _construct(){
        $this->_init('mysubscription/msquote_item_option', 'option_id');
    }
}