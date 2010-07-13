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
 * Enhanced Product collection model.
 *
 * @category   Soluvas
 * @package    Soluvas_MagentoTweaks
 * @author     Soluvas Developers <info@soluvas.com>
 */
class Soluvas_MagentoTweaks_Model_Reportsmysql4productindexviewedcollection extends Mage_Reports_Model_Mysql4_Product_Index_Viewed_Collection
{

	/**
	 * Enhanced to return full product URLs;
	 */
    protected function _addUrlRewrite()
    {
        $urlRewrites = null;
        if ($this->_cacheConf) {
            if (!($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'].'urlrewrite'))) {
                $urlRewrites = null;
            } else {
                $urlRewrites = unserialize($urlRewrites);
            }
        }

        if (!$urlRewrites) {
            $productIds = array();
            foreach($this->getItems() as $item) {
                $productIds[] = $item->getEntityId();
            }
            if (!count($productIds)) {
                return;
            }

            $select = $this->getConnection()->select()
                ->from($this->getTable('core/url_rewrite'), array('product_id', 'request_path'))
                ->where('store_id=?', Mage::app()->getStore()->getId())
                ->where('is_system=?', 1);
            if (Mage::getStoreConfig('magentotweaks/catalog/fullproductpath') == 0)
                $select = $select->where('category_id=? OR category_id is NULL', $this->_urlRewriteCategory);
            $select = $select->where('product_id IN(?)', $productIds)
                ->order('category_id DESC'); // more priority is data with category id
            $urlRewrites = array();

            foreach ($this->getConnection()->fetchAll($select) as $row) {
                if (!isset($urlRewrites[$row['product_id']])) {
                    $urlRewrites[$row['product_id']] = $row['request_path'];
                }
            }

            if ($this->_cacheConf) {
                Mage::app()->saveCache(
                    serialize($urlRewrites),
                    $this->_cacheConf['prefix'].'urlrewrite',
                    array_merge($this->_cacheConf['tags'], array(Mage_Catalog_Model_Product_Url::CACHE_TAG)),
                    $this->_cacheLifetime
                );
            }
        }

        foreach($this->getItems() as $item) {
            if (isset($urlRewrites[$item->getEntityId()])) {
                $item->setData('request_path', $urlRewrites[$item->getEntityId()]);
            }
        }
    }
	
}
