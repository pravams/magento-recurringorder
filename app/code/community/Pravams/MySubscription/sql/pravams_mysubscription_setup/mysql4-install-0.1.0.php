<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */

$installer = $this;
$installer->startSetup();

/*
 * Create table 'mysubscription/msquote'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/msquote'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Entity Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',            
        ), 'Store Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default' => '1',
        ), 'Is Active')
        ->addColumn('items_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'default'  => '0',
        ), 'Items Count')
        ->addColumn('items_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default' => '0.0000'
        ), 'Items Qty')
        ->addColumn('is_virtual', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default' => '0'
        ), 'Is Virtual')
        ->addColumn('is_multi_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default' => '0'
        ), 'Is Multi Shipping')
        ->addColumn('store_to_base_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default' => '0.0000',
        ), 'Store To Base Rate')
        ->addColumn('store_to_quote_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default' => '0.0000',
        ), 'Store to Quote Rate')
        ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Base Currency Code')
        ->addColumn('store_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Store Currency Code')
        ->addColumn('quote_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Quote Currency Code')
        ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default' => '0.0000'
        ), 'Grand Total')
        ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default' => '0.0000'
        ), 'Base Grand Total')
        ->addColumn('checkout_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(            
        ), 'Checkout Method')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'default'  => '0'
        ), 'Customer Id')
        ->addColumn('customer_tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'default'  => '0'
        ), 'Customer Tax Class Id')
        ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'default'  => '0'
        ), 'Customer Group Id')
        ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(            
        ), 'Customer Email')
        ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        ), 'Customer Prefix')
        ->addColumn('customer_firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Customer Firstname')
        ->addColumn('customer_middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        ), 'Customer Middlename')
        ->addColumn('customer_lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Customer Lastname')
        ->addColumn('customer_suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        ), 'Customer Suffix')
        ->addColumn('customer_dob', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Customer Dob')
        ->addColumn('global_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(            
        ), 'Global Currency Code')
        ->addColumn('base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Base To Global Rate')
        ->addColumn('base_to_quote_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Base To Quote Rate')
        ->addColumn('customer_gender', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Customer Gender')
        ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Subtotal')
        ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(            
        ), 'Base Subtotal')
        ->addColumn('msorder_state', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default'  => '0',
        ), 'Order state')
        ->addIndex($installer->getIdxName('mysubscription/msquote', array('customer_id', 'store_id', 'is_active')),
                array('customer_id', 'store_id', 'is_active'))
        ->addIndex($installer->getIdxName('mysubscription/msquote', array('store_id')),
                array('store_id'))
        ->addForeignKey($installer->getFkName('mysubscription/msquote', 'store_id', 'core/store', 'store_id'),
                'store_id', $installer->getTable('core/store'), 'store_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Quote');
$installer->getConnection()->createTable($table);


/*
 * Create table 'mysubscription/msquoteitem'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/msquote_item'))
        ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Item Id')
        ->addColumn('msquote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
        ), 'MySubscription Quote Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,  
        ), 'Product Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
        ), 'Store Id')
        ->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
        ), 'Parent Item Id')
        ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(        
        ), 'Sku')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(        
        ), 'Name')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(        
        ), 'Description')
        ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(        
            'nullable' => false,
            'default'  => '0.0000',
        ), 'Qty')
        ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000',
        ), 'Price')
        ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000',
        ), 'Base Price')
        ->addColumn('tax_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default'  => '0.0000',
        ), 'Tax Percent')
        ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default'  => '0.0000',
        ), 'Tax Percent')
        ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default'  => '0.0000',
        ), 'Base Tax Amount')
        ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'default'   => '0'
        ), 'Product Tax Class Id')
        ->addColumn('row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
            'default'   => '0.0000',
        ), 'Row Total')
        ->addColumn('base_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
            'default'   => '0.0000'
        ), 'Base Row Total')
        ->addColumn('product_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(        
        ), 'Product Type')
        ->addIndex($installer->getIdxName('mysubscription/msquote_item', array('parent_item_id')),
            array('parent_item_id'))
        ->addIndex($installer->getIdxName('mysubscription/msquote_item', array('product_id')),
            array('product_id'))
        ->addIndex($installer->getIdxName('mysubscription/msquote_item', array('msquote_id')),
            array('msquote_id'))
        ->addIndex($installer->getIdxName('mysubscription/msquote_item', array('store_id')),
            array('store_id'))    
        ->addForeignKey($installer->getFkName('mysubscription/msquote_item', 'parent_item_id', 'mysubscription/msquote_item', 'item_id'),
            'parent_item_id', $installer->getTable('mysubscription/msquote_item'), 'item_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('mysubscription/msquote_item', 'product_id', 'catalog/product', 'entity_id'),
            'product_id', $installer->getTable('catalog/product'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('mysubscription/msquote_item', 'msquote_id', 'mysubscription/msquote', 'entity_id'),
            'msquote_id', $installer->getTable('mysubscription/msquote'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('mysubscription/msquote_item', 'store_id', 'core/store', 'store_id'),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Quote Item');

$installer->getConnection()->createTable($table);

/*
 * Create table 'mysubscription/msquoteitemoption'
 * */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/msquote_item_option'))
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Option Id')
        ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Item Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Product Id')
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false
        ), 'Code')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Value')
        ->addIndex($installer->getIdxName('mysubscription/msquote_item_option', array('item_id')),
            array('item_id'))
        ->addForeignKey($installer->getFkName('mysubscription/msquote_item_option', 'item_id', 'mysubscription/msquote_item', 'item_id'),
            'item_id', $installer->getTable('mysubscription/msquote_item'), 'item_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Quote Item Option');

$installer->getConnection()->createTable($table);

/*
 * Create table 'mysubscription/msprofile'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/msprofile'))
        ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true
        ), 'Mysubscription Profile Id')
        ->addColumn('msquote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
        ), 'MySubscription Quote Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false
        ), 'Updated At')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true
        ), 'Store Id')
        ->addColumn('time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true
        ), 'Scheduled Time')
        ->addColumn('next_order_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true
        ), 'Next Order Time')
        ->addColumn('frequency', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Scheduled Frequency')
        ->addColumn('frequency_val', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
        ), 'Frequency Value')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
        ->addIndex($installer->getIdxName('mysubscription/msprofile', array('msquote_id')),
            array('msquote_id'))
        ->addIndex($installer->getIdxName('mysubscription/msprofile', array('store_id')),
            array('store_id'))
        ->addForeignKey($installer->getFkName('mysubscription/msprofile', 'msquote_id', 'mysubscription/msquote', 'entity_id'),
            'msquote_id', $installer->getTable('mysubscription/msquote'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('mysubscription/msprofile', 'store_id', 'core/store', 'store_id'),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Profile');

$installer->getConnection()->createTable($table);

/*
 * Create table 'mysubscription/ms_address'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/ms_address'))
        ->addColumn('address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true
        ), 'Mysubscription Address Id')
        ->addColumn('msquote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0'
        ), 'Mysubscription Quote Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false
        ), 'Updated At')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true
        ), 'Customer Id')
        ->addColumn('save_in_address_book', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'default'  => '0'
        ), 'Save In Address Book')
        ->addColumn('customer_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true
        ), 'Customer Address Id')
        ->addColumn('address_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Address Type')
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Email')
        ->addColumn('prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        ), 'Prefix')
        ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Firstname')
        ->addColumn('middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        ), 'Middlename')
        ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Lastname')
        ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        ), 'Suffix')
        ->addColumn('company', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Company')
        ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Street')
        ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'City')
        ->addColumn('region', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Region')
        ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true
        ), 'Region Id')
        ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Postcode')
        ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Country Id')
        ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Telephone')
        ->addColumn('fax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Fax')
        ->addColumn('same_as_billing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
        ), 'Same As Billing')
        ->addColumn('free_shipping', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'   => '0',
        ), 'Free Shipping')
        ->addColumn('collect_shipping_rates', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
        ), 'Collect Shipping Rates')
        ->addColumn('shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Shipping Method')
        ->addColumn('shipping_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Shipping Description')
        ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Weight')
        ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Subtotal')
        ->addColumn('base_subtotal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Base Subtotal')
        ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Tax Amount')
        ->addColumn('base_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Base Tax Amount')
        ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Shipping Amount')
        ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Grand Total')
        ->addColumn('base_grand_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Base Grand Total')
        ->addIndex($installer->getIdxName('mysubscription/ms_address', array('msquote_id')),
            array('msquote_id'))
        ->addForeignKey($installer->getFkName('mysubscription/ms_address', 'msquote_id', 'mysubscription/msquote', 'entity_id'),
            'msquote_id', $installer->getTable('mysubscription/msquote'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Address');
$installer->getConnection()->createTable($table);

/*
 * Create table 'mysubscription/ms_payment'
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/ms_payment'))
        ->addColumn('payment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true
        ), 'Mysubscription Payment Id')
        ->addColumn('msquote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
        ), 'Mysubscription Quote Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Method')
        ->addColumn('cc_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Cc Type')
        ->addColumn('cc_number_enc', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Cc Number Enc')
        ->addColumn('cc_last4', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Cc Last4')
        ->addColumn('cc_cid_enc', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Cc Cid Enc')
        ->addColumn('cc_owner', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Cc Owner')
        ->addColumn('cc_exp_month', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default'  => '0',
        ), 'Cc Exp Month')
        ->addColumn('cc_exp_year', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default'  => '0',
        ), 'Cc Exp Year')
        ->addColumn('cc_ss_owner', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Cc Ss Owner')
        ->addColumn('cc_ss_start_month', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default'  => '0',
        ), 'Cc Ss Start Month')
        ->addColumn('cc_ss_start_year', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'default'  => '0',
        ), 'Cc Ss Start Year')
        ->addColumn('po_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Po Number')
        ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Additional Information')
        ->addIndex($installer->getIdxName('mysubscription/ms_payment', array('msquote_id')),
            array('msquote_id'))
        ->addForeignKey($installer->getFkName('mysubscription/ms_payment', 'msquote_id', 'mysubscription/msquote', 'entity_id'),
            'msquote_id', $installer->getTable('mysubscription/msquote'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Payment');
$installer->getConnection()->createTable($table);

/*
 * Create table 'mysubscription/ms_shipping_rate' 
 */
$table = $installer->getConnection()
        ->newTable($installer->getTable('mysubscription/ms_shipping_rate'))
        ->addColumn('rate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Mysubscription Rate Id')
        ->addColumn('address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0'
        ), 'Mysubscription Address Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('carrier', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Carrier')
        ->addColumn('carrier_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Carrier Title')
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Code')
        ->addColumn('method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Method')
        ->addColumn('method_description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Method Description')
        ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => false,
            'default'  => '0.0000'
        ), 'Price')
        ->addColumn('error_message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Error Message')
        ->addColumn('method_title', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Method Title')
        ->addIndex($installer->getIdxName('mysubscription/ms_shipping_rate', array('address_id')),
            array('address_id'))
        ->addForeignKey($installer->getFkName('mysubscription/ms_shipping_rate', 'address_id', 'mysubscription/ms_address', 'address_id'),
            'address_id', $installer->getTable('mysubscription/ms_address'), 'address_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Mysubscription Shipping Rate');
$installer->getConnection()->createTable($table);

/*
 * Create table 'mysubscription/ms_order'
 * */

$table = $installer->getConnection()
    ->newTable($installer->getTable('mysubscription/ms_order'))
    ->addColumn('msorder_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
    ), 'Mysubscription Order Id')
    ->addColumn('msquote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => '0'
    ), 'Mysubscription Quote Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Sales Flat Order Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false
    ), 'Updated At');
$installer->getConnection()->createTable($table);

$installer->endSetup();
?>
