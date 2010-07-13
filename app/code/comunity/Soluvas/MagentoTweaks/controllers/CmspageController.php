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
 * Enhanced CMS Page controller that 301 Permanent redirects /{home} CMS page
 * to website front page.
 *
 * @category   Soluvas
 * @package    Soluvas_MagentoTweaks
 * @author     Soluvas Developers <info@soluvas.com>
 */
class Soluvas_MagentoTweaks_CmspageController extends Mage_Cms_PageController
{
    /**
     * View CMS page action
     *
     */
    public function viewAction()
    {
//    	Mage::log($this->getRequest());
        $pageId = $this->getRequest()
            ->getParam('page_id', $this->getRequest()->getParam('id', false));
            
        if (Mage::getStoreConfig('magentotweaks/cms/redirecthome') == 1) {
			if ($page = Mage::getModel('cms/page')->load($pageId)) {
				$pageUrl = $page->getIdentifier();
		        $homeUrl = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
		        if ($pageUrl == $homeUrl) {
		        	Mage::log('Redirecting to '. Mage::getBaseUrl());
		        	return $this->getResponse()->setRedirect(Mage::getBaseUrl(), 301);
		        }
			}
        }
        
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('noRoute');
        }
    }
}
