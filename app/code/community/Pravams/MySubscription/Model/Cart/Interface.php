<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */
interface Pravams_MySubscription_Model_Cart_Interface
{
    /*
     * Add product to shopping cart
     * 
     * @param int| Mage_Catalog_Model_Product $productInfo
     * @param mixed $requestInfo
     * @return Pravams_MySubscription_Model_Cart_Interface
     *      
     */
    public function addProduct($productInfo, $requestInfo = null);    
    
    /*
     * Save cart
     * @abstract
     * @return Pravams_MySubscription_Model_Cart_Interface
     */
    public function saveMsquote();
    
    /*
     * Associate quote with the cart
     * 
     * @abstract
     * @param $msquote Pravams_MySubscription_Model_Msquote
     * @return Pravams_MySubscription_Model_Cart_Interface
     */
    public function setMsquote(Pravams_MySubscription_Model_Msquote $msquote);
        
    /*
     * Get quote object associated with cart
     */
    public function getMsquote();       
}
?>
