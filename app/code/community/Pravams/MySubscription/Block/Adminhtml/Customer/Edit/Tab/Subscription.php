<?php
/**
 * Pravams MySubscription Module
 *
 * @category    Pravams
 * @package     Pravams_MySubscription
 * @copyright   Copyright (c) 2014 Pravams LLC. (http://www.pravams.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pravams_MySubscription_Block_Adminhtml_Customer_Edit_Tab_Subscription extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct(){
        parent::__construct();
        $this->setId('customer_subscription_grid');
        $this->setDefaultSort('main_table.created_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection(){
        $profileCollection = Mage::getModel('mysubscription/profile')->getAllProfiles();
        $this->setCollection($profileCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('profile_id', array(
            'header'    => Mage::helper('Customer')->__('Profile #'),
            'width'     => '100',
            'index'     => 'profile_id'
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('customer')->__('Created On'),
            'index'     => 'created_at',
            'type'     => 'datetime',
        ));

        $this->addColumn('time', array(
            'header'    => Mage::helper('customer')->__('Start Date'),
            'index'     => 'time',
            'type'     => 'datetime',
        ));

        $this->addColumn('frequency', array(
            'header'    => Mage::helper('customer')->__('Frequency'),
            'index'     => 'frequency',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name',
        ));

        $this->addColumn('msorder_state', array(
            'header'    => Mage::helper('customer')->__('Status'),
            'renderer'  => 'mysubscription/adminhtml_customer_edit_tab_subscription_renderer_status',
            'index'     => 'msorder_state',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row){
        return $this->getUrl('*/index/viewprofile', array('profile_id'=> $row->getProfileId()));
    }

    protected function _afterToHtml($html){
        $jsScript = "<script type=\"text/javascript\">
        var grid = customer_subscription_gridJsObject.trOnClick;
        var row = customer_subscription_gridJsObject.rows;
        row.each(function(item){
          Event.stopObserving(item,'click',grid);
          Event.observe(item, 'click', showProfile);
        });
        var eachProfile = \"<div id='customer_info_tabs_subscription_content_view'></div>\";
        Element.insert('edit_form', eachProfile);
        function showProfile(){
            var url = this.readAttribute('title');
            new Ajax.Request(url ,{
                onSuccess: function(response){
                    Element.setStyle(('customer_info_tabs_subscription_content'),{
                        display: 'none'
                    });
                    Element.setStyle(('customer_info_tabs_subscription_content_view'),{
                        display: ''
                    });
                    Element.update('customer_info_tabs_subscription_content_view', response.responseText);
                }
            });
        }
        </script>";
        $html .= $jsScript;
        return $html;
    }

//    protected function _afterToHtml($html)
//    {
//        return $html;
//    }

//    public function getGridUrl()
//    {
//        return $this->getUrl('*/*/listprofile', array('_current' => true));
//    }
}