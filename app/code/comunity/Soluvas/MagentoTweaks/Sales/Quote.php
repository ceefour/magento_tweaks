<?php
/**
 * Soluvas
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * @category   Soluvas
 * @package    Soluvas_MagentoTweaks
 * @copyright  Copyright (c) 2010 Soluvas (http://www.soluvas.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote model that defaults "Ship to this address".
 */
class Soluvas_MagentoTweaks_Sales_Quote extends Mage_Sales_Model_Quote
{

    /**
     * Init resource model
     */
    protected function _construct()
    {
    	parent::_construct();
//    	Mage::log('Sales Quote constructed');
    	if (Mage::getStoreConfig('magentotweaks/checkout/register') == 1) {
        	$this->setCheckoutMethod( Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER );
    	}
    }
    
	/**
     * Retrieve quote address by type
     *
     * @param   string $type
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddressByType($type)
    {
//    	Mage::log("Soluvas_MagentoTweaks_Sales_Quote::_getAddressByType($type)");
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == $type && !$address->isDeleted()) {
                return $address;
            }
        }
        $address = Mage::getModel('sales/quote_address')->setAddressType($type);
        if (Mage::getStoreConfig('magentotweaks/checkout/sameasbilling') == 1) {
//        	Mage::log("Same as billing enabled");
	        if ($type == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
//	        	Mage::log("Soluvas_MagentoTweaks_Sales_Quote::_getAddressByType: set SameAsBilling=1");
	        	$address->setSameAsBilling(1);
	        }
        } else {
//			Mage::log("Same as billing disabled");
        }
        $this->addAddress($address);
        return $address;
    }
    
    /**
     * Re-set Checkout type to Register.
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _afterLoad()
    {
//    	Mage::log('BEFORE:_afterload getcheckout = '. $this->getCheckoutMethod());
    	$return = parent::_afterLoad();
//    	Mage::log('AFTER_LOAD:_afterload getcheckout = '. $this->getCheckoutMethod());
    	if ($this->getCheckoutMethod() == '' && Mage::getStoreConfig('magentotweaks/checkout/register') == 1) {
        	$this->setCheckoutMethod( Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER );
//        	Mage::log('AFTER_OUR:_afterLoad getcheckout = '. $this->getCheckoutMethod());
    	}
        return $return;
    }
    	
}