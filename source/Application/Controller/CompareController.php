<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\EshopCommunity\Internal\Common\Container\Container;

/**
 * Comparing Products.
 * Takes a few products and show attribute values to compare them.
 */
class CompareController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Number of possible compare pages.
     *
     * @var integer
     */
    protected $_iCntPages = 1;

    /**
     * Number of user's orders.
     *
     * @var integer
     */
    protected $_iOrderCnt = null;

    /**
     * Number of articles per page.
     *
     * @var integer
     */
    protected $_iArticlesPerPage = 3;

    /**
     * Number of user's orders.
     *
     * @var integer
     */
    protected $_iCompItemsCnt = null;

    /**
     * Items which are currently to show in comparison.
     *
     * @var array
     */
    protected $_aCompItems = null;

    /**
     * Article list in comparison.
     *
     * @var object
     */
    protected $_oArtList = null;

    /**
     * Article attribute list in comparison.
     *
     * @var object
     */
    protected $_oAttributeList = null;

    /**
     * Recomendation list
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/compare/compare.tpl';

    /**
     * Array of id to form recommendation list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;

    /**
     * moves current article to the left in compare items array
     */
    public function moveLeft() //#777C
    {
        $sArticleId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aid');
        if ($sArticleId && ($aItems = $this->getCompareItems())) {
            $sPrevArticleId = null;

            $blFound = false;
            foreach ($aItems as $sOxid => $sVal) {
                if ($sOxid == $sArticleId) {
                    $blFound = true;
                }
                if (!$blFound) {
                    $sPrevArticleId = $sOxid;
                }
            }

            if ($sPrevArticleId) {
                $aNewItems = [];
                foreach ($aItems as $sOxid => $sVal) {
                    if ($sOxid == $sPrevArticleId) {
                        $aNewItems[$sArticleId] = true;
                    } elseif ($sOxid == $sArticleId) {
                        $aNewItems[$sPrevArticleId] = true;
                    } else {
                        $aNewItems[$sOxid] = true;
                    }
                }

                $this->setCompareItems($aNewItems);
            }
        }
    }

    /**
     * moves current article to the right in compare items array
     */
    public function moveRight() //#777C
    {
        $sArticleId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aid');
        if ($sArticleId && ($aItems = $this->getCompareItems())) {
            $sNextArticleId = 0;

            $blFound = false;
            foreach ($aItems as $sOxid => $sVal) {
                if ($blFound) {
                    $sNextArticleId = $sOxid;
                    $blFound = false;
                }
                if ($sOxid == $sArticleId) {
                    $blFound = true;
                }
            }

            if ($sNextArticleId) {
                $aNewItems = [];
                foreach ($aItems as $sOxid => $sVal) {
                    if ($sOxid == $sArticleId) {
                        $aNewItems[$sNextArticleId] = true;
                    } elseif ($sOxid == $sNextArticleId) {
                        $aNewItems[$sArticleId] = true;
                    } else {
                        $aNewItems[$sOxid] = true;
                    }
                }
                $this->setCompareItems($aNewItems);
            }
        }
    }

    /**
     * changes default template for compare in popup
     */
    public function inPopup() // #777C
    {
        $this->_sThisTemplate = 'compare_popup.tpl';
        $this->_iArticlesPerPage = -1;
    }

    /**
     * Articlelist count in comparison setter
     *
     * @param integer $iCount compare items count
     */
    public function setCompareItemsCnt($iCount)
    {
        $this->_iCompItemsCnt = $iCount;
    }

    /**
     * Template variable getter. Returns article list count in comparison
     *
     * @return integer
     */
    public function getCompareItemsCnt()
    {
        if ($this->_iCompItemsCnt === null) {
            $this->_iCompItemsCnt = 0;
            if ($aItems = $this->getCompareItems()) {
                $this->_iCompItemsCnt = count($aItems);
            }
        }

        return $this->_iCompItemsCnt;
    }

    /**
     * Compare item $_aCompItems getter
     *
     * @return null
     */
    public function getCompareItems()
    {
        if ($this->_aCompItems === null) {
            $aItems = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('aFiltcompproducts');
            if (is_array($aItems) && count($aItems)) {
                $this->_aCompItems = $aItems;
            }
        }

        return $this->_aCompItems;
    }

    /**
     * Compare item $_aCompItems setter
     *
     * @param array $aItems compare items i new order
     */
    public function setCompareItems($aItems)
    {
        $this->_aCompItems = $aItems;
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('aFiltcompproducts', $aItems);
    }

    /**
     *  $_iArticlesPerPage setter
     *
     * @param int $iNumber article count in compare page
     */
    protected function _setArticlesPerPage($iNumber)
    {
        $this->_iArticlesPerPage = $iNumber;
    }

    /**
     *  turn off paging
     */
    public function setNoPaging()
    {
        $this->_setArticlesPerPage(0);
    }


    /**
     * Template variable getter. Returns comparison's article
     * list in order per page
     *
     * @return object
     */
    public function getCompArtList()
    {
        if ($this->_oArtList === null) {
            if (($aItems = $this->getCompareItems())) {
                // counts how many pages
                $oList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $oList->loadIds(array_keys($aItems));

                // cut page articles
                if ($this->_iArticlesPerPage > 0) {
                    $this->_iCntPages = ceil($oList->count() / $this->_iArticlesPerPage);
                    $aItems = $this->_removeArticlesFromPage($aItems, $oList);
                }

                $this->_oArtList = $this->_changeArtListOrder($aItems, $oList);
            }
        }

        return $this->_oArtList;
    }

    /**
     * Template variable getter. Returns attribute list
     *
     * @return object
     */
    public function getAttributeList()
    {
        if ($this->_oAttributeList === null) {
            $this->_oAttributeList = false;
            if ($oArtList = $this->getCompArtList()) {
                $aProductIds = array_keys($oArtList);
                foreach ($oArtList as $oArticle) {
                    if ($oArticle->getParentId()) {
                        $aProductIds[] = $oArticle->getParentId();
                    }
                }
                $oAttributeList = oxNew(\OxidEsales\Eshop\Application\Model\AttributeList::class);
                $this->_oAttributeList = $oAttributeList->loadAttributesByIds($aProductIds);
            }
        }

        return $this->_oAttributeList;
    }

    /**
     * Return array of id to form recommend list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ($this->_aSimilarRecommListIds === null) {
            $this->_aSimilarRecommListIds = false;

            if ($oArtList = $this->getCompArtList()) {
                $this->_aSimilarRecommListIds = array_keys($oArtList);
            }
        }

        return $this->_aSimilarRecommListIds;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = false;
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
    }

    /**
     * Cuts page articles
     *
     * @param array  $aItems article array
     * @param object $oList  article list array
     *
     * @return array $aNewItems
     */
    protected function _removeArticlesFromPage($aItems, $oList)
    {
        //#1106S $aItems changed to $oList.
        //2006-08-10 Alfonsas, compare arrows fixed, array position is very important here, preserve it.
        $aListKeys = $oList->arrayKeys();
        $aItemKeys = array_keys($aItems);
        $aKeys = array_intersect($aItemKeys, $aListKeys);
        $aNewItems = [];
        $iActPage = $this->getActPage();
        for ($i = $this->_iArticlesPerPage * $iActPage; $i < $this->_iArticlesPerPage * $iActPage + $this->_iArticlesPerPage; $i++) {
            if (!isset($aKeys[$i])) {
                break;
            }
            $aNewItems[$aKeys[$i]] = & $aItems[$aKeys[$i]];
        }

        return $aNewItems;
    }

    /**
     * Changes order of list elements
     *
     * @param array  $aItems article array
     * @param object $oList  article list array
     *
     * @return array $oNewList
     */
    protected function _changeArtListOrder($aItems, $oList)
    {
        // #777C changing order of list elements, according to $aItems
        $oNewList = [];
        $iCnt = 0;
        $iActPage = $this->getActPage();
        foreach ($aItems as $sOxid => $sVal) {
            //#4391T, skipping non loaded products
            if (!isset($oList[$sOxid])) {
                continue;
            }

            $iCnt++;
            $oNewList[$sOxid] = $oList[$sOxid];

            // hide arrow if article is first in the list
            $oNewList[$sOxid]->hidePrev = false;
            if ($iActPage == 0 && $iCnt == 1) {
                $oNewList[$sOxid]->hidePrev = true;
            }

            // hide arrow if article is last in the list
            $oNewList[$sOxid]->hideNext = false;
            if (($iActPage + 1) == $this->_iCntPages && $iCnt == count($aItems)) {
                $oNewList[$sOxid]->hideNext = true;
            }
        }

        return $oNewList;
    }

    /**
     * changes default template for compare in popup
     *
     * @return null
     */
    public function getOrderCnt()
    {
        if ($this->_iOrderCnt === null) {
            $this->_iOrderCnt = 0;
            if ($oUser = $this->getUser()) {
                $this->_iOrderCnt = $oUser->getOrderCount();
            }
        }

        return $this->_iOrderCnt;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_ACCOUNT', \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage(), false);
        $aPath['link'] = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($this->getViewConfig()->getSelfLink() . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('PRODUCT_COMPARISON', \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage(), false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Return true, if the review manager should be shown.
     *
     * @return bool
     */
    public function isUserAllowedToManageOwnReviews()
    {
        return (bool) $this
            ->getConfig()
            ->getConfigParam('blAllowUsersToManageTheirReviews');
    }

    /**
     * Get the total number of reviews for the active user.
     *
     * @return integer Number of reviews
     */
    public function getReviewAndRatingItemsCount()
    {
        return $this
            ->getContainer()
            ->getUserReviewAndRatingBridge()
            ->getReviewAndRatingListCount($this->getUser()->getId());
    }

    /**
     * @return Container
     */
    private function getContainer()
    {
        return Container::getInstance();
    }
}
