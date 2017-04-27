<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Model_Profile extends Mage_Core_Model_Abstract
{
    const ACTIVE = "Active";
    const INACTIVE = "In Active";

    protected function _construct()
    {
        $this->_init('mysubscription/profile');
    }

    /*
     * Retrieve the current profile during checkout
     * @return Pravams_MySubscription_Model_Profile
     * */
    public function getMsProfile($msQuoute){
        $profile = Mage::getModel('mysubscription/profile')->getCollection();
        $profile->addFieldToFilter('msquote_id', $msQuoute->getId());
        $msProfile = $profile->getFirstItem();
        return $msProfile;
    }

    /*
     * Retrieve the active profiles for subscription
     * @return Pravams_MySubscription_Model_Resource_Profile_Collection
     * */
    public function getActiveProfiles(){
        $profiles = Mage::getModel('mysubscription/profile')->getCollection();
        $profiles->getSelect()->join(
            array('msquote' => $profiles->getTable('mysubscription/msquote')),
                'main_table.msquote_id = msquote.entity_id AND msquote.msorder_state ='.Pravams_MySubscription_Model_Msquote::ACTIVE
            );
        return $profiles;
    }

    /*
     * Retrieve the ALL profiles for subscription
     * @return Pravams_MySubscription_Model_Resource_Profile_Collection
     * */
    public function getAllProfiles(){
        $customerId = Mage::getSingleton('customer/session')->getId();

        $profiles = Mage::getModel('mysubscription/profile')->getCollection();
        $profiles->getSelect()->join(
            array('msquote' => $profiles->getTable('mysubscription/msquote')),
            'main_table.msquote_id = msquote.entity_id'
        );
        $profiles->getSelect()->where('msquote.customer_id = ?', $customerId);
        return $profiles;
    }

    /*
     * Check if ths profile has items in the quote
     * @return bool
     * */
    public function hasItems(){
        $msquote = $this->getMsQuote($this->getId());
        return $msquote->hasItems();
    }

    /*
     * Return the time in d/M/Y format
     * @return date
     * */
    public function getStartDate(){
        $time = $this->getTime();
        return date('d/m/Y', strtotime($time));
    }

    /*
     * Return the profile status
     * @return string
     * */
    public function getStatus($msorderState = null){
        $state = ($msorderState) ? $msorderState : $this->getMsorderState();
        $status = ($state == Pravams_MySubscription_Model_Msquote::ACTIVE) ?
            Pravams_MySubscription_Model_Profile::ACTIVE : Pravams_MySubscription_Model_Profile::INACTIVE;
        return $status;
    }

    /*
     * Retrieve the Quote for the selected Profile
     * @return Pravams_MySubscription_Model_Msquote
     * */
    public function getMsQuote($profileId){
        $profile = Mage::getModel('mysubscription/profile')->load($profileId);
        $msQuote = Mage::getModel('mysubscription/msquote')->load($profile->getMsquoteId());
        return $msQuote;
    }

    /*
     * Change the subscription profile status
     * */
    public function changeStatus($params){
        $profileId = $params['profile_id'];
        $status = $params['status'];

        $msQuote = $this->getMsQuote($profileId);
        $msQuote->setMsorderState($status);
        $msQuote->save();
        return;
    }
}