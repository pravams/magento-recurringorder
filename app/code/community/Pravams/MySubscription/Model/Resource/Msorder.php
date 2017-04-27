<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Model_Resource_Msorder extends Pravams_MySubscription_Model_Resource_Abstract
{
    protected function _construct(){
        $this->_init('mysubscription/ms_order', 'msorder_id');
    }
}