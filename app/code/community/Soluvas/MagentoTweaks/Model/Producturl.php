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
 * Tweaked Product_Url model
 *
 * @deprecated	Doubtably useful. Reports_Compare/Viewed always set 'DoNotUseCategoryId', plus they also
 * 		set their own $product->getRequestPath(), so it never gets processed here.
 * @category   Soluvas
 * @package    Soluvas_MagentoTweaks
 * @author     Soluvas Developers <info@soluvas.com>
 */
class Soluvas_MagentoTweaks_Model_Producturl extends Mage_Catalog_Model_Product_Url
{

    /**
     * Retrieve Product URL using UrlDataObject.
     * Tweaked to return full product URL.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $params
     * @return string
     */
    public function getUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
        $routePath      = '';
        $routeParams    = $params;

        $storeId    = $product->getStoreId();
        if (isset($params['_ignore_category'])) {
            unset($params['_ignore_category']);
            $categoryId = null;
        } else {
        	$useCategory = Mage::getStoreConfig('magentotweaks/catalog/fullproductpath', $storeId) == 1 || !$product->getDoNotUseCategoryId(); 
        	$categoryId = $product->getCategoryId() && $useCategory
                ? $product->getCategoryId() : null;
        }
//        	Mage::log('categoryId = '. $categoryId);
        
        if ($product->hasUrlDataObject()) {
            $requestPath = $product->getUrlDataObject()->getUrlRewrite();
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
        }
        else {
            $requestPath = $product->getRequestPath();
            if (empty($requestPath)) {
                $idPath = sprintf('product/%d', $product->getEntityId());
//        	Mage::log('category: '. $categoryId);
        	if ($categoryId) {
                	$idPath = sprintf('%s/%d', $idPath, $categoryId);
                }
                $rewrite = $this->getUrlRewrite();
                $rewrite->setStoreId($storeId)
                    ->loadByIdPath($idPath);
                if ($rewrite->getId()) {
                    $requestPath = $rewrite->getRequestPath();
                    $product->setRequestPath($requestPath);
                }
            }
//        	Mage::log('requestPath: '. $requestPath);
        }

        if (isset($routeParams['_store'])) {
            $storeId = Mage::app()->getStore($routeParams['_store'])->getId();
        }

        if ($storeId != Mage::app()->getStore()->getId()) {
            $routeParams['_store_to_url'] = true;
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        }
        else {
            $routePath = 'catalog/product/view';
            $routeParams['id']  = $product->getId();
            $routeParams['s']   = $product->getUrlKey();
            if ($categoryId) {
                $routeParams['category'] = $categoryId;
            }
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

//        Mage::log($routePath);
//        Mage::log($routeParams);
        return $this->getUrlInstance()->setStore($storeId)
            ->getUrl($routePath, $routeParams);
    }
	
}
