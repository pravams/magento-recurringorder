<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
class Pravams_MySubscription_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{        
    /*
     * get 'one step checkout' step data
     */
    public function getSteps()
    {
        $steps = array();
        $stepCodes = $this->_getStepCodes();
        
        if ($this->isCustomerLoggedIn()) {
            $stepCodes = array_diff($stepCodes, array('login'));
            
            /*
            * if subscription can be applied
            */
            if(Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems()){
//            if(Mage::getSingleton('mysubscription/cart')->getMsquote()->getId()){

               array_unshift($stepCodes, 'mysubscription');

               $this->getCheckout()->setStepData('mysubscription', array(
                   'label'     => Mage::helper('mysubscription')->__('Recurring Cart Information'),
                   'is_show'   => $this->isShow(),
                   'allow'     => true
               ));

               $this->getCheckout()->setStepData('billing', 'allow', false);
            }
        }
        
        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        
        return $steps;
    }
    
    /*
     * Get active step
     */
    public function getActiveStep()
    {
        if(Mage::getSingleton('mysubscription/cart')->getMsquote()->hasItems()){
            $activeStep = 'mysubscription';
        }else{
            $activeStep = 'billing';
        }
        
        return ($this->isCustomerLoggedIn()) ? $activeStep : 'login';
    }
}
?>
