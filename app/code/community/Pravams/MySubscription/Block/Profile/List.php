<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Profile_List extends Mage_Core_Block_Template
{
    /*
     * Retrieve all the profiles which have been saved
     * @return Pravams_MySubscription_Model_Resource_Profile_Collection
     * */
    public function getProfiles(){
        $profiles = Mage::getModel('mysubscription/profile')->getAllProfiles();
        return $profiles;
    }
}