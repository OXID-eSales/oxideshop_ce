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
 * List of articles for a selected vendor.
 * Collects list of articles, according to it generates links for list gallery,
 * meta tags (for search engines). Result - "vendorlist.tpl" template.
 * OXID eShop -> (Any selected shop product category).
 */
class VendorList extends aList
{
    /**
     * List type
     * @var string
     */
    protected $_sListType = 'vendor';

    /**
     * List type
     * @var string
     */
    protected $_blVisibleSubCats = null;

    /**
     * List type
     * @var string
     */
    protected $_oSubCatList = null;

    /**
     * Template location
     *
     * @var string
     */
    protected $_sTplLocation = null;

    /**
     * Template location
     *
     * @var string
     */
    protected $_sCatTitle = null;

    /**
     * Page navigation
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Marked which defines if current view is sortable or not
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_INDEX;

    /**
     * Vendor list object.
     *
     * @var object
     */
    protected $_oVendorTree  = null;

    /**
     * Executes parent::render(), loads active vendor, prepares article
     * list sorting rules. Loads list of articles which belong to this vendor
     * Generates page navigation data
     * such as previous/next window URL, number of available pages, generates
     * meta tags info (oxUBase::_convertForMetaTags()) and returns name of
     * template to render.
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        oxUBase::render();

        // load vendor
        if ( ( $this->_getVendorId() && $this->getVendorTree() ) ) {
            if ( ( $oVendor = $this->getActVendor() ) ) {
                if ( $oVendor->getId() != 'root' ) {
                    // load the articles
                    $this->getArticleList();

                    // checking if requested page is correct
                    $this->_checkRequestedPage();

                    // processing list articles
                    $this->_processListArticles();
                }
            }
        }

        return $this->_sThisTemplate;
    }

    /**
     * Returns product link type (OXARTICLE_LINKTYPE_VENDOR)
     *
     * @return int
     */
    protected function _getProductLinkType()
    {
        return OXARTICLE_LINKTYPE_VENDOR;
    }

    /**
     * Sets vendor item sorting config
     *
     * @param string $sCnid    sortable vendor id
     * @param string $sSortBy  sort field
     * @param string $sSortDir sort direction (optional)
     *
     * @deprecated since v4.7.3/5.0.3 (2013-01-07); duplicated code
     *
     * @return null
     */
    public function setItemSorting( $sCnid, $sSortBy, $sSortDir = null )
    {
        parent::setItemSorting( $sCnid, $sSortBy, $sSortDir );
    }

    /**
     * Returns vendor sorting config
     *
     * @param string $sCnid sortable item id
     *
     * @deprecated since v4.7.3/5.0.3 (2013-01-07); duplicated code
     *
     * @return string
     */
    public function getSorting( $sCnid )
    {
        return parent::getSorting( $sCnid );
    }

    /**
     * Loads and returns article list of active vendor.
     *
     * @param object $oVendor vendor object
     *
     * @return array
     */
    protected function _loadArticles( $oVendor )
    {
        $sVendorId = $oVendor->getId();

        // load only articles which we show on screen
        $iNrOfCatArticles = (int) $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
        $iNrOfCatArticles = $iNrOfCatArticles ? $iNrOfCatArticles : 1;

        $oArtList = oxNew( 'oxArticleList' );
        $oArtList->setSqlLimit( $iNrOfCatArticles * $this->_getRequestPageNr(), $iNrOfCatArticles );
        $oArtList->setCustomSorting( $this->getSortingSql( $this->getSortIdent() ) );

        // load the articles
        $this->_iAllArtCnt = $oArtList->loadVendorArticles( $sVendorId, $oVendor );

        // counting pages
        $this->_iCntPages = round( $this->_iAllArtCnt / $iNrOfCatArticles + 0.49 );

        return array( $oArtList, $this->_iAllArtCnt );
    }

    /**
     * Returns active product id to load its seo meta info
     *
     * @return string
     */
    protected function _getSeoObjectId()
    {
        if ( ( $oVendor = $this->getActVendor() ) ) {
            return $oVendor->getId();
        }
    }

    /**
     * Modifies url by adding page parameters. When seo is on, url is additionally
     * formatted by SEO engine
     *
     * @param string $sUrl  current url
     * @param int    $iPage page number
     * @param int    $iLang active language id
     *
     * @return string
     */
    protected function _addPageNrParam( $sUrl, $iPage, $iLang = null)
    {
        if ( oxRegistry::getUtils()->seoIsActive() && ( $oVendor = $this->getActVendor() ) ) {
            if ( $iPage ) {
                // only if page number > 0
                $sUrl = $oVendor->getBaseSeoLink( $iLang, $iPage );
            }
        } else {
            $sUrl = oxUBase::_addPageNrParam( $sUrl, $iPage, $iLang );
        }
        return $sUrl;
    }

    /**
     * Returns current view Url
     *
     * @return string
     */
    public function generatePageNavigationUrl( )
    {
        if ( ( oxRegistry::getUtils()->seoIsActive() && ( $oVendor = $this->getActVendor() ) ) ) {
            return $oVendor->getLink();
        } else {
            return parent::generatePageNavigationUrl( );
        }
    }

    /**
     * Returns if vendor has visible sub-cats and load them.
     *
     * @return bool
     */
    public function hasVisibleSubCats()
    {
        if ( $this->_blVisibleSubCats === null ) {
            $this->_blVisibleSubCats = false;
            if ( ($this->_getVendorId() && $oVendorTree = $this->getVendorTree() ) ) {
                if ( ( $oVendor = $this->getActVendor() ) ) {
                    if ( $oVendor->getId() == 'root' ) {
                        $this->_blVisibleSubCats = $oVendorTree->count();
                        $this->_oSubCatList = $oVendorTree;
                    }
                }
            }
        }
        return $this->_blVisibleSubCats;
    }

    /**
     * Returns vendor subcategories
     *
     * @return array
     */
    public function getSubCatList()
    {
        if ( $this->_oSubCatList === null ) {
            $this->_oSubCatList = array();
            if ( $this->hasVisibleSubCats() ) {
                return $this->_oSubCatList;
            }
        }
        return $this->_oSubCatList;
    }

    /**
     * Get vendor article list
     *
     * @return array
     */
    public function getArticleList()
    {
        if ( $this->_aArticleList === null ) {
            $this->_aArticleList = array();
                if ( ( $oVendor = $this->getActVendor() ) && ( $oVendor->getId() != 'root' ) ) {
                    list( $aArticleList, $iAllArtCnt ) = $this->_loadArticles( $oVendor );
                    if ( $iAllArtCnt ) {
                        $this->_aArticleList = $aArticleList;
                    }
                }
        }
        return $this->_aArticleList;
    }

    /**
     * Return vendor title
     *
     * @return string
     */
    public function getTitle()
    {
        if ( $this->_sCatTitle === null ) {
            $this->_sCatTitle = '';
                if ( $oVendor = $this->getActVendor() ) {
                    $this->_sCatTitle = $oVendor->oxvendor__oxtitle->value;
                }
        }
        return $this->_sCatTitle;
    }

    /**
     * Template variable getter. Returns category path array
     *
     * @return array
     */
    public function getTreePath()
    {
        if ( $this->_getVendorId() && $oVendorTree = $this->getVendorTree() ) {
            return $oVendorTree->getPath();
        }
    }

    protected function _getVendorId()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'cnid' );
    }

    /**
     * Template variable getter. Returns active vendor
     *
     * @return object
     */
    public function getActiveCategory()
    {
        if ( $this->_oActCategory === null ) {
            $this->_oActCategory = false;
            if ( ( $this->_getVendorId() && $oVendorTree = $this->getVendorTree() ) ) {
                if ( $oVendor = $this->getActVendor() ) {
                    $this->_oActCategory = $oVendor;
                }
            }
        }
        return $this->_oActCategory;
    }

    /**
     * Template variable getter. Returns template location
     *
     * @return string
     */
    public function getCatTreePath()
    {
        if ( $this->_sCatTreePath === null ) {
            $this->_sCatTreePath = false;
            if ( ( $oVendorTree = $this->getVendorTree() ) ) {
                $this->_sCatTreePath  = $oVendorTree->getPath();
            }
        }
        return $this->_sCatTreePath;
    }

    /**
     * Returns title suffix used in template
     *
     * @return string
     */
    public function getTitleSuffix()
    {
        if ( $this->getActVendor()->oxvendor__oxshowsuffix->value ) {
            return $this->getConfig()->getActiveShop()->oxshops__oxtitlesuffix->value;
        }
    }

    /**
     * Returns current view keywords separated by comma
     * (calls parent::_collectMetaKeyword())
     *
     * @param string $sKeywords               data to use as keywords
     * @param bool   $blRemoveDuplicatedWords remove duplicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords = true )
    {
        return parent::_collectMetaKeyword( $sKeywords );
    }

    /**
     * Returns current view meta description data
     * (calls parent::_collectMetaDescription())
     *
     * @param string $sMeta     category path
     * @param int    $iLength   max length of result, -1 for no truncation
     * @param bool   $blDescTag if true - performs additional duplicate cleaning
     *
     * @return string
     */
    protected function _prepareMetaDescription( $sMeta, $iLength = 1024, $blDescTag = false )
    {
        return parent::_collectMetaDescription( $sMeta, $iLength, $blDescTag );
    }

    /**
     * returns object, associated with current view.
     * (the object that is shown in frontend)
     *
     * @param int $iLang language id
     *
     * @return object
     */
    protected function _getSubject( $iLang )
    {
        return $this->getActVendor();
    }

    /**
     * Returns additional URL parameters which must be added to list products dynamic urls
     *
     * @return string
     */
    public function getAddUrlParams()
    {
        $sAddParams  = parent::getAddUrlParams();
        $sAddParams .= ($sAddParams?'&amp;':'') . "listtype={$this->_sListType}";
        if ( $oVendor = $this->getActVendor() ) {
            $sAddParams .= "&amp;cnid=v_" . $oVendor->getId();
        }
        return $sAddParams;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $oCatTree = $this->getVendorTree();

        if ( $oCatTree ) {
            foreach ( $oCatTree->getPath() as $oCat ) {
                $aCatPath = array();

                $aCatPath['link'] = $oCat->getLink();
                $aCatPath['title'] = $oCat->oxcategories__oxtitle->value;

                $aPaths[] = $aCatPath;
            }
        }

        return $aPaths;
    }


     /**
     * Returns vendor tree
     *
     * @return oxVendorList
     */
    public function getVendorTree()
    {
        if ( $this->_getVendorId() && $this->_oVendorTree === null) {
            $oVendorTree = oxNew( 'oxVendorList' );
            $oVendorTree->buildVendorTree( 'vendorlist', $this->getActVendor()->getId(), $this->getConfig()->getShopHomeURL() );
            $this->_oVendorTree = $oVendorTree;
        }

        return $this->_oVendorTree;
    }

    /**
     * Vendor tree setter
     *
     * @param oxVendorList $oVendorTree vendor tree
     *
     * @return null
     */
    public function setVendorTree( $oVendorTree )
    {
        $this->_oVendorTree = $oVendorTree;
    }

    /**
     * Template variable getter. Returns array of attribute values
     * we do have here in this category
     *
     * @return array
     */
    public function getAttributes()
    {
        return null;
    }

}
