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
 * Enhanced Product model that always returns a Category ID.
 *
 * @category   Soluvas
 * @package    Soluvas_MagentoTweaks
 * @author     Soluvas Developers <info@soluvas.com>
 * @see 		Mage_Catalog_Model_Product
 */
class Soluvas_MagentoTweaks_Model_Product extends Mage_Catalog_Model_Product
{

    /**
     * Enhanced Retrieve product category id, returns the first category
     * if Mage::registry('current_category') doesn't return a valid category ID.
     *
     * @return int
     */
    public function getCategoryId()
    {
        if ($category = Mage::registry('current_category')) {
            return $category->getId();
        }
        if (Mage::getStoreConfig('magentotweaks/catalog/fullproductpath', $this->getStoreId()) == 1) {
	        $categories = $this->getCategoryCollection();
			if ($categories->getSize() >= 0) {
				return $categories->getFirstItem()->getId();
			}
        }
        return false;
    }

}
