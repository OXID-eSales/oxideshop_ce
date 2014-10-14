<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Comparing Products.
 * Takes a few products and show attribute values to compare them.
 */
class Compare extends oxUBase
{
    /**
     * Number of possible compare pages.
     * @var integer
     */
    protected $_iCntPages = 1;

    /**
     * Number of user's orders.
     * @var integer
     */
    protected $_iOrderCnt = null;

    /**
     * Number of articles per page.
     * @var integer
     */
    protected $_iArticlesPerPage = 3;

    /**
     * Number of user's orders.
     * @var integer
     */
    protected $_iCompItemsCnt = null;

    /**
     * Items which are currently to show in comparison.
     * @var array
     */
    protected $_aCompItems = null;

    /**
     * Article list in comparison.
     * @var object
     */
    protected $_oArtList = null;

    /**
     * Article attribute list in comparison.
     * @var object
     */
    protected $_oAttributeList = null;

    /**
     * Recomendation list
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Page navigation
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Show tags cloud
     * @var bool
     */
    protected $_blShowTagCloud = false;


    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/compare/compare.tpl';

    /**
     * Array of id to form recommendation list.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;


    /**
     * moves current article to the left in compare items array
     *
     * @return null
     */
    public function moveLeft() //#777C
    {
        $sArticleId = oxConfig::getParameter( 'aid' );
        if ( $sArticleId && ( $aItems = $this->getCompareItems() ) ) {
            $sPrevArticleId = null;

            $blFound = false;
            foreach ( $aItems as $sOxid => $sVal ) {
                if ( $sOxid == $sArticleId ) {
                    $blFound = true;
                }
                if ( !$blFound ) {
                    $sPrevArticleId = $sOxid;
                }
            }

            if ( $sPrevArticleId ) {

                $aNewItems = array();
                foreach ( $aItems as $sOxid => $sVal ) {
                    if ( $sOxid == $sPrevArticleId ) {
                        $aNewItems[$sArticleId] = true;
                    } elseif ( $sOxid == $sArticleId ) {
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
     *
     * @return null
     */
    public function moveRight()  //#777C
    {
        $sArticleId = oxConfig::getParameter( 'aid' );
        if ( $sArticleId && ( $aItems = $this->getCompareItems() ) ) {
            $sNextArticleId = 0;

            $blFound = false;
            foreach ( $aItems as $sOxid => $sVal ) {
                if ( $blFound ) {
                    $sNextArticleId = $sOxid;
                    $blFound = false;
                }
                if ( $sOxid == $sArticleId ) {
                    $blFound = true;
                }
            }

            if ( $sNextArticleId ) {

                $aNewItems = array();
                foreach ( $aItems as $sOxid => $sVal ) {
                    if ( $sOxid == $sArticleId ) {
                        $aNewItems[$sNextArticleId] = true;
                    } elseif ( $sOxid == $sNextArticleId ) {
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
     *
     * @return null
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
     *
     * @return integer
     */
    public function setCompareItemsCnt( $iCount )
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
        if ( $this->_iCompItemsCnt === null ) {
            $this->_iCompItemsCnt = 0;
            if ( $aItems = $this->getCompareItems() ) {
                $this->_iCompItemsCnt = count( $aItems );
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
        if ( $this->_aCompItems === null ) {
            $aItems = oxSession::getVar( 'aFiltcompproducts' );
            if ( is_array($aItems) && count($aItems) ) {
                $this->_aCompItems = $aItems;
            }
        }
        return $this->_aCompItems;
    }

    /**
     * Compare item $_aCompItems setter
     *
     * @param array $aItems compare items i new order
     *
     * @return null
     */
    public function setCompareItems( $aItems)
    {
        $this->_aCompItems = $aItems;
        oxSession::setVar( 'aFiltcompproducts', $aItems );
    }

    /**
     *  $_iArticlesPerPage setter
     *
     * @param int $iNumber article count in compare page
     *
     * @return null
     */
    protected function _setArticlesPerPage( $iNumber)
    {
        $this->_iArticlesPerPage = $iNumber;
    }

    /**
     *  turn off paging
     *
     * @return null
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
        if ( $this->_oArtList === null ) {
            if ( ( $aItems = $this->getCompareItems() ) ) {
                // counts how many pages
                $oList = oxNew( 'oxarticlelist' );
                $oList->loadIds( array_keys( $aItems ) );

                // cut page articles
                if ( $this->_iArticlesPerPage > 0 ) {
                    $this->_iCntPages = round( $oList->count() / $this->_iArticlesPerPage + 0.49 );
                    $aItems = $this->_removeArticlesFromPage( $aItems, $oList );
                }

                $this->_oArtList = $this->_changeArtListOrder( $aItems, $oList );
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
        if ( $this->_oAttributeList === null ) {
            $this->_oAttributeList = false;
            if ( $oArtList = $this->getCompArtList()) {
                $oAttributeList = oxNew( 'oxattributelist' );
                $this->_oAttributeList = $oAttributeList->loadAttributesByIds( array_keys( $oArtList ) );
            }
        }
        return $this->_oAttributeList;
    }

    /**
     * Return array of id to form recommend list.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ( $this->_aSimilarRecommListIds === null ) {
            $this->_aSimilarRecommListIds = false;

            if ( $oArtList = $this->getCompArtList() ) {
                $this->_aSimilarRecommListIds = array_keys( $oArtList );
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
        if ( $this->_oPageNavigation === null ) {
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
    protected function _removeArticlesFromPage( $aItems, $oList )
    {
        //#1106S $aItems changed to $oList.
        //2006-08-10 Alfonsas, compare arrows fixed, array position is very important here, preserve it.
        $aListKeys = $oList->arrayKeys();
        $aItemKeys = array_keys($aItems);
        $aKeys = array_intersect( $aItemKeys, $aListKeys );
        $aNewItems = array();
        $iActPage = $this->getActPage();
        for ( $i = $this->_iArticlesPerPage * $iActPage; $i < $this->_iArticlesPerPage * $iActPage + $this->_iArticlesPerPage; $i++ ) {
            if ( !isset($aKeys[$i])) {
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
    protected function _changeArtListOrder( $aItems, $oList )
    {
        // #777C changing order of list elements, according to $aItems
        $oNewList = array();
        $iCnt = 0;
        $iActPage = $this->getActPage();
        foreach ( $aItems as $sOxid => $sVal ) {

            //#4391T, skipping non loaded products
            if (!isset ($oList[$sOxid]) ) {
                continue;
            }

            $iCnt++;
            $oNewList[$sOxid] = $oList[$sOxid];

            // hide arrow if article is first in the list
            $oNewList[$sOxid]->hidePrev = false;
            if ( $iActPage == 0 && $iCnt==1 ) {
                $oNewList[$sOxid]->hidePrev = true;
            }

            // hide arrow if article is last in the list
            $oNewList[$sOxid]->hideNext = false;
            if ( ( $iActPage + 1 ) == $this->_iCntPages && $iCnt == count( $aItems ) ) {
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
        if ( $this->_iOrderCnt === null ) {
            $this->_iOrderCnt = 0;
            if ( $oUser = $this->getUser() ) {
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
        $aPaths = array();
        $aPath = array();

        $aPath['title'] = oxRegistry::getLang()->translateString( 'MY_ACCOUNT', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = oxRegistry::get("oxSeoEncoder")->getStaticUrl( $this->getViewConfig()->getSelfLink() . 'cl=account' );
        $aPaths[] = $aPath;

        $aPath['title'] = oxRegistry::getLang()->translateString( 'PRODUCT_COMPARISON', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
