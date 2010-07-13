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
require_once 'Mage/Catalog/controllers/ProductController.php';

/**
 * Enhanced Catalog Product controller that can do 301 redirect to full product URL.
 *
 * @category   Soluvas
 * @package    Soluvas_MagentoTweaks
 * @author     Soluvas Developers <info@soluvas.com>
 */
class Soluvas_MagentoTweaks_CatalogproductController extends Mage_Catalog_ProductController
{
	
    /**
     * View product action
     */
    public function viewAction()
    {
        if ($product = $this->_initProduct()) {
        	// Do a redirect if not using full product URL
        	$storeId = Mage::app()->getStore()->getId();
        	if (Mage::getStoreConfig('magentotweaks/catalog/fullproductpath', $storeId) == 1 &&
        		Mage::getStoreConfig('magentotweaks/catalog/redirectproduct', $storeId) == 1) {
	    		if ($this->getRequest()->getParam('category') == null) {
	    			if ($product->getCategoryId() != false) {
	    				// do a 301 redirect permanent to long product url
	    				return $this->getResponse()->setRedirect($product->getProductUrl(), 301);
	    			}
	    		}
        	}
        	
        	
        	Mage::dispatchEvent('catalog_controller_product_view', array('product'=>$product));

            if ($this->getRequest()->getParam('options')) {
                $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                Mage::getSingleton('catalog/session')->addNotice($notice);
            }

            Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());
            Mage::getModel('catalog/design')->applyDesign($product, Mage_Catalog_Model_Design::APPLY_FOR_PRODUCT);

            $this->_initProductLayout($product);
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('tag/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        }
        else {
            if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
        }
    }
}
