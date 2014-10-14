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
 * List of articles for a selected Manufacturer.
 * Collects list of articles, according to it generates links for list gallery,
 * metatags (for search engines). Result - "manufacturerlist.tpl" template.
 * OXID eShop -> (Any selected shop product category).
 */
class ManufacturerList extends aList
{
    /**
     * List type
     * @var string
     */
    protected $_sListType = 'manufacturer';

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
     * Recommlist
     * @var object
     */
    protected $_oRecommList = null;

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
     * Executes parent::render(), loads active Manufacturer, prepares article
     * list sorting rules. Loads list of articles which belong to this Manufacturer
     * Generates page navigation data
     * such as previous/next window URL, number of available pages, generates
     * metatags info (oxubase::_convertForMetaTags()) and returns name of
     * template to render.
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        oxUBase::render();

        // load Manufacturer
        if ( $this->getManufacturerTree() ) {
            if ( ( $oManufacturer = $this->getActManufacturer() ) ) {
                if ( $oManufacturer->getId() != 'root' ) {
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
     * Returns product link type (OXARTICLE_LINKTYPE_MANUFACTURER)
     *
     * @return int
     */
    protected function _getProductLinkType()
    {
        return OXARTICLE_LINKTYPE_MANUFACTURER;
    }

    /**
     * Sets Manufacturer item sorting config
     *
     * @param string $sCnid    sortable Manufacturer id
     * @param string $sSortBy  sort field
     * @param string $sSortDir sort direction (optional)
     *
     * @deprecated since v4.7.3/5.0.3 (2013-01-07); dublicated code
     *
     * @return null
     */
    public function setItemSorting( $sCnid, $sSortBy, $sSortDir = null )
    {
        parent::setItemSorting( $sCnid, $sSortBy, $sSortDir );
    }

    /**
     * Returns Manufacturer sorting config
     *
     * @param string $sCnid sortable item id
     *
     * @deprecated since v4.7.3/5.0.3 (2013-01-07); dublicated code
     *
     * @return string
     */
    public function getSorting( $sCnid )
    {
        return parent::getSorting( $sCnid );
    }

    /**
     * Loads and returns article list of active Manufacturer.
     *
     * @param oxmanufacturer $oManufacturer Manufacturer object
     *
     * @return array
     */
    protected function _loadArticles( $oManufacturer )
    {
        $sManufacturerId = $oManufacturer->getId();

        // load only articles which we show on screen
        $iNrofCatArticles = (int) $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
        $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 1;

        $oArtList = oxNew( 'oxarticlelist' );
        $oArtList->setSqlLimit( $iNrofCatArticles * $this->_getRequestPageNr(), $iNrofCatArticles );
        $oArtList->setCustomSorting( $this->getSortingSql( $this->getSortIdent() ) );

        // load the articles
        $this->_iAllArtCnt = $oArtList->loadManufacturerArticles( $sManufacturerId, $oManufacturer );

        // counting pages
        $this->_iCntPages = round( $this->_iAllArtCnt / $iNrofCatArticles + 0.49 );

        return array( $oArtList, $this->_iAllArtCnt );
    }

    /**
     * Returns active product id to load its seo meta info
     *
     * @return string
     */
    protected function _getSeoObjectId()
    {
        $sId = null;
        if ( ( $oManufacturer = $this->getActManufacturer() ) ) {
            $sId = $oManufacturer->getId();
        }
        return $sId;
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
        if ( oxRegistry::getUtils()->seoIsActive() && ( $oManufacturer = $this->getActManufacturer() ) ) {
            if ( $iPage ) {
                // only if page number > 0
                $sUrl = $oManufacturer->getBaseSeoLink( $iLang, $iPage );
            }
        } else {
            $sUrl = parent::_addPageNrParam( $sUrl, $iPage, $iLang );
        }
        return $sUrl;
    }

    /**
     * Returns current view Url
     *
     * @return string
     */
    public function generatePageNavigationUrl()
    {
        if ( ( oxRegistry::getUtils()->seoIsActive() && ( $oManufacturer = $this->getActManufacturer() ) ) ) {
            return $oManufacturer->getLink();
        } else {
            return parent::generatePageNavigationUrl( );
        }
    }

    /**
     * Template variable getter. Returns active object's reviews
     *
     * @return array
     */
    public function hasVisibleSubCats()
    {
        if ( $this->_blVisibleSubCats === null ) {
            $this->_blVisibleSubCats = false;
            if ( ( $oManufacturerTree = $this->getManufacturerTree() ) ) {
                if ( ( $oManufacturer = $this->getActManufacturer() ) ) {
                    if ( $oManufacturer->getId() == 'root' ) {
                        $this->_blVisibleSubCats = $oManufacturerTree->count();
                        $this->_oSubCatList = $oManufacturerTree;
                    }
                }
            }
        }
        return $this->_blVisibleSubCats;
    }

    /**
     * Template variable getter. Returns active object's reviews
     *
     * @return array
     */
    public function getSubCatList()
    {
        if ( $this->_oSubCatList === null ) {
            $this->_oSubCatList = $this->hasVisibleSubCats() ? $this->_oSubCatList : array();
        }
        return $this->_oSubCatList;
    }

    /**
     * Template variable getter. Returns active object's reviews
     *
     * @return array
     */
    public function getArticleList()
    {
        if ( $this->_aArticleList === null ) {
            $this->_aArticleList = array();
            if ( ( $oManufacturerTree = $this->getManufacturerTree() ) ) {
                if ( ( $oManufacturer = $this->getActManufacturer() ) && ( $oManufacturer->getId() != 'root' ) && $oManufacturer->getIsVisible() ) {
                    list( $aArticleList, $iAllArtCnt ) = $this->_loadArticles( $oManufacturer );
                    if ( $iAllArtCnt ) {
                        $this->_aArticleList = $aArticleList;
                    }
                }
            }
        }
        return $this->_aArticleList;
    }

    /**
     * Template variable getter. Returns template location
     *
     * @return string
     */
    public function getTitle()
    {
        if ( $this->_sCatTitle === null ) {
            $this->_sCatTitle = '';
            if ( $oManufacturerTree = $this->getManufacturerTree() ) {
                if ( $oManufacturer = $this->getActManufacturer() ) {
                    $this->_sCatTitle = $oManufacturer->oxmanufacturers__oxtitle->value;
                }
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
        $aPath = null;
        if ( $oManufacturerTree = $this->getManufacturerTree() ) {
            $aPath = $oManufacturerTree->getPath();
        }
        return $aPath;
    }

    /**
     * Template variable getter. Returns active Manufacturer
     *
     * @return object
     */
    public function getActiveCategory()
    {
        if ( $this->_oActCategory === null ) {
            $this->_oActCategory = false;
            if ( ( $oManufacturerTree = $this->getManufacturerTree() ) ) {
                if ( $oManufacturer = $this->getActManufacturer() ) {
                    $this->_oActCategory = $oManufacturer;
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
            if ( ( $oManufacturerTree = $this->getManufacturerTree() ) ) {
                $this->_sCatTreePath  = $oManufacturerTree->getPath();
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
        $sSuffix = null;
        if ( $this->getActManufacturer()->oxmanufacturers__oxshowsuffix->value ) {
            $sSuffix = $this->getConfig()->getActiveShop()->oxshops__oxtitlesuffix->value;
        }
        return $sSuffix;
    }

    /**
     * Calls and returns result of parent:: _collectMetaKeyword();
     *
     * @param mixed $aCatPath                category path
     * @param bool  $blRemoveDuplicatedWords remove dublicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword( $aCatPath, $blRemoveDuplicatedWords = true )
    {
        return parent::_collectMetaKeyword( $aCatPath );
    }

    /**
     * Meta tags - description and keywords - generator for search
     * engines. Uses string passed by parameters, cleans HTML tags,
     * string duplicates, special chars. Also removes strings defined
     * in $myConfig->aSkipTags (Admin area).
     *
     * @param mixed $aCatPath  category path
     * @param int   $iLength   max length of result, -1 for no truncation
     * @param bool  $blDescTag if true - performs additional dublicate cleaning
     *
     * @return  string  $sString    converted string
     */
    protected function _prepareMetaDescription( $aCatPath, $iLength = 1024, $blDescTag = false )
    {
        return parent::_collectMetaDescription( $aCatPath, $iLength, $blDescTag );
    }

    /**
     * returns object, assosiated with current view.
     * (the object that is shown in frontend)
     *
     * @param int $iLang language id
     *
     * @return object
     */
    protected function _getSubject( $iLang )
    {
        return $this->getActManufacturer();
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
        if ( $oManufacturer = $this->getActManufacturer() ) {
            $sAddParams .= "&amp;mnid=" . $oManufacturer->getId();
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

        $oCatTree = $this->getManufacturerTree();

        if ( $oCatTree ) {
            foreach ( $oCatTree->getPath() as $oCat ) {
                $aCatPath = array();
                $aCatPath['link'] = $oCat->getLink();
                $aCatPath['title'] = $oCat->oxmanufacturers__oxtitle->value;

                $aPaths[] = $aCatPath;
            }
        }

        return $aPaths;
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
