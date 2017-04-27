<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_IndexController extends Mage_Core_Controller_Front_Action
{
    /*
     * Retrieve customer session model object
     * @return Mage_Customer_Model_Session
     * */
    protected function _getSession(){
        return Mage::getSingleton('customer/session');
    }

    /*
     * Action predispatch
     *
     * Check customer authentication for some actions
     * */
    public function preDispatch(){
        parent::preDispatch();

        if(!$this->getRequest()->isDispatched()){
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'index',
        );

        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if(!preg_match($pattern, $action)){
            if(!$this->_getSession()->authenticate($this)){
                $this->setFlag('', 'no-dispatch', true);
            }
        } else{
            $this->_getSession()->setNoReferer(true);
        }
    }

    public function successAction(){
        Mage::helper('mysubscription')->clearSubscriptionCart();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction(){
        $profileId = $this->getRequest()->getParam('profile_id');
        $msquote = Mage::getModel('mysubscription/profile')->getMsQuote($profileId);
        /*
         * Load the quote into the session
         * */
        $msSession = Mage::getSingleton('mysubscription/session');
        $msSession->setMsquoteId($msquote->getId());
        $msSession->getMsquote($msquote->getId());

        $this->_redirect('checkout/cart');
    }

    public function changeStatusAction(){
        $params = $this->getRequest()->getParams();
        $profileId = $params['profile_id'];
        $profile = Mage::getModel('mysubscription/profile')->load($profileId);
        $profile->changeStatus($params);
        $this->_redirect('mysubscription/index/view/', array('profile_id'=> $profileId));
    }

    public function testCronAction(){
        Mage::getModel('mysubscription/observer')->placeOrder();
    }
}