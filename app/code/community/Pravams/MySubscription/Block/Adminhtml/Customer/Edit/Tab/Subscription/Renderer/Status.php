<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Adminhtml_Customer_Edit_Tab_Subscription_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /*
     * Render subscription status
     * @param Varien_Object
     * @return string
     * */
    public function render(Varien_Object $row)
    {
        return $row->getStatus($row->getMsorderState());
    }
}
