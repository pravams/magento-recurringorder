<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml(){
        $this->addTabAfter('subscription',array(
            'label' => Mage::helper('customer')->__('Recurring Cart Profiles'),
            'class' => 'ajax',
            'url'   => $this->getUrl('*/index/listprofile', array('_current' => true)),
        ), 'tags');

        parent::_beforeToHtml();
    }
}