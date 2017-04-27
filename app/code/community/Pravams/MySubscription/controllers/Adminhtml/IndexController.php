<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initCustomer($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    public function listprofileAction(){
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewprofileAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function changeStatusAction(){
        $params = $this->getRequest()->getParams();
        $profileId = $params['profile_id'];
        $status = $params['status'];
        $profile = Mage::getModel('mysubscription/profile')->load($profileId);
        $profile->changeStatus($params);
        $result = $this->getLayout()->createBLock('mysubscription/adminhtml_customer_edit_tab_subscription_view', 'adminhtml.mysubscription.viewprofile.status');
        $result->setTemplate('mysubscription/view/status.phtml');
        $this->getResponse()->setBody($result->toHtml());
    }
}