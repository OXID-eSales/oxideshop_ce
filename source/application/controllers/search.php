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
 * Articles searching class.
 * Performs searching through articles in database.
 */
class Search extends oxUBase
{
    /**
     * Count of all found articles.
     * @var integer
     */
    protected $_iAllArtCnt     = 0;

    /**
     * Number of possible pages.
     * @var integer
     */
    protected $_iCntPages = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/search/search.tpl';

    /**
     * List type
     * @var string
     */
    protected $_sListType = 'search';

    /**
     * Marked which defines if current view is sortable or not
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * If search was empty
     * @var bool
     */
    protected $_blEmptySearch = null;

    /**
     * Similar recommendation lists
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Search parameter for Html
     * @var string
     */
    protected $_sSearchParamForHtml = null;

    /**
     * Search parameter
     * @var string
     */
    protected $_sSearchParam = null;

    /**
     * Searched category
     * @var string
     */
    protected $_sSearchCatId = null;

    /**
     * Searched vendor
     * @var string
     */
    protected $_sSearchVendor = null;

    /**
     * Searched manufacturer
     * @var string
     */
    protected $_sSearchManufacturer = null;

    /**
     * If called class is search
     * @var bool
     */
    protected $_blSearchClass = null;

    /**
     * Page navigation
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Array of id to form recommendation list.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;


    /**
     * Fetches search parameter from GET/POST/session, prepares search
     * SQL (search::GetWhere()), and executes it forming the list of
     * found articles. Article list is stored at search::_aArticleList
     * array.
     *
     * @return null
     */
    public function init()
    {
        parent::init();

        $myConfig = $this->getConfig();

        // #1184M - specialchar search
        $sSearchParamForQuery = oxConfig::getParameter( 'searchparam', true );

        // searching in category ?
        $sInitialSearchCat = $this->_sSearchCatId = rawurldecode( oxConfig::getParameter( 'searchcnid' ) );

        // searching in vendor #671
        $sInitialSearchVendor = rawurldecode( oxConfig::getParameter( 'searchvendor' ) );

        // searching in Manufacturer #671
        $sInitialSearchManufacturer = $this->_sSearchManufacturer = rawurldecode( oxConfig::getParameter( 'searchmanufacturer' ) );

        $this->_blEmptySearch = false;
        if ( !$sSearchParamForQuery && !$sInitialSearchCat && !$sInitialSearchVendor && !$sInitialSearchManufacturer ) {
            //no search string
            $this->_aArticleList = null;
            $this->_blEmptySearch = true;
            return false;
        }

        // config allows to search in Manufacturers ?
        if ( !$myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) ) {
            $sInitialSearchManufacturer = null;
        }

        // searching ..
        $oSearchHandler = oxNew( 'oxsearch' );
        $oSearchList = $oSearchHandler->getSearchArticles( $sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, $this->getSortingSql( $this->getSortIdent() ) );

        // list of found articles
        $this->_aArticleList = $oSearchList;
        $this->_iAllArtCnt    = 0;

        // skip count calculation if no articles in list found
        if ( $oSearchList->count() ) {
            $this->_iAllArtCnt = $oSearchHandler->getSearchArticleCount( $sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer );
        }

        $iNrofCatArticles = (int) $myConfig->getConfigParam( 'iNrofCatArticles' );
        $iNrofCatArticles = $iNrofCatArticles?$iNrofCatArticles:1;
        $this->_iCntPages  = round( $this->_iAllArtCnt / $iNrofCatArticles + 0.49 );
    }

    /**
     * Forms serach navigation URLs, executes parent::render() and
     * returns name of template to render search::_sThisTemplate.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        parent::render();

        $oConfig = $this->getConfig();
        if ( $oConfig->getConfigParam( 'bl_rssSearch' ) ) {
            $oRss = oxNew('oxrssfeed');
            $sSearch = $oConfig->getRequestParameter( 'searchparam', true );
            $sCnid = $oConfig->getRequestParameter( 'searchcnid', true );
            $sVendor = $oConfig->getRequestParameter( 'searchvendor', true );
            $sManufacturer = $oConfig->getRequestParameter( 'searchmanufacturer', true );
            $this->addRssFeed($oRss->getSearchArticlesTitle($sSearch, $sCnid, $sVendor, $sManufacturer), $oRss->getSearchArticlesUrl($sSearch, $sCnid, $sVendor, $sManufacturer), 'searchArticles');
        }

        // processing list articles
        $this->_processListArticles();

        return $this->_sThisTemplate;
    }

    /**
     * Iterates through list articles and performs list view specific tasks:
     *  - sets type of link whicn needs to be generated (Manufacturer link)
     *
     * @return null
     */
    protected function _processListArticles()
    {
        $sAddDynParams = $this->getAddUrlParams();
        if ( $sAddDynParams && ( $aArtList = $this->getArticleList() ) ) {
            $blSeo = oxRegistry::getUtils()->seoIsActive();
            foreach ( $aArtList as $oArticle ) {
                // appending std and dynamic urls
                if ( !$blSeo ) {
                    // only if seo is off..
                    $oArticle->appendStdLink( $sAddDynParams );
                }
                $oArticle->appendLink( $sAddDynParams );
            }
        }
    }

    /**
     * Returns additional URL parameters which must be added to list products urls
     *
     * @return string
     */
    public function getAddUrlParams()
    {
        $sAddParams  = parent::getAddUrlParams();
        $sAddParams .= ($sAddParams?'&amp;':'') . "listtype={$this->_sListType}";
        $oConfig = $this->getConfig();

        if ( $sParam = $oConfig->getRequestParameter( 'searchparam', true ) ) {
            $sAddParams .= "&amp;searchparam=".rawurlencode($sParam);
        }

        if ( $sParam = $oConfig->getRequestParameter( 'searchcnid' ) ) {
            $sAddParams .= "&amp;searchcnid=$sParam";
        }

        if ( $sParam = rawurldecode( $oConfig->getRequestParameter( 'searchvendor' ) ) ) {
            $sAddParams .= "&amp;searchvendor=$sParam";
        }

        if ( $sParam = rawurldecode( $oConfig->getRequestParameter( 'searchmanufacturer' ) ) ) {
            $sAddParams .= "&amp;searchmanufacturer=$sParam";
        }
        return $sAddParams;
    }

    /**
     * Sets search sorting config
     *
     * @param string $sCnid      sortable item id
     * @param string $sSortBy    sort field
     * @param string $sSortOrder sort order
     *
     * @deprecated since v4.7.3/5.0.3 (2013-01-07); dublicated code
     *
     * @return null
     */
    public function setItemSorting( $sCnid, $sSortBy, $sSortOrder  = null )
    {
        parent::setItemSorting( $sCnid, $sSortBy, $sSortOrder );
    }

    /**
     * Returns search sorting config
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
     * Template variable getter. Returns similar recommendation lists
     *
     * @return object
     */
    protected function _isSearchClass()
    {
        if ( $this->_blSearchClass === null ) {
            $this->_blSearchClass = false;
            if ( strtolower($this->getConfig()->getRequestParameter( 'cl' )) == 'search' ) {
                $this->_blSearchClass = true;
            }
        }
        return $this->_blSearchClass;
    }
    /**
     * Template variable getter. Returns if searched was empty
     *
     * @return bool
     */
    public function isEmptySearch()
    {
        return $this->_blEmptySearch;
    }

    /**
     * Template variable getter. Returns searched article list
     *
     * @return array
     */
    public function getArticleList()
    {
        return $this->_aArticleList;
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

            $aList = $this->getArticleList();
            if ( $aList && $aList->count() > 0 ) {
                $this->_aSimilarRecommListIds = $aList->arrayKeys();
            }
        }

        return $this->_aSimilarRecommListIds;
    }

    /**
     * Template variable getter. Returns search parameter for Html
     *
     * @return string
     */
    public function getSearchParamForHtml()
    {
        if ( $this->_sSearchParamForHtml === null ) {
            $this->_sSearchParamForHtml = false;
            if ( $this->_isSearchClass() ) {
                $this->_sSearchParamForHtml = $this->getConfig()->getRequestParameter( 'searchparam' );
            }
        }
        return $this->_sSearchParamForHtml;
    }

    /**
     * Template variable getter. Returns search parameter
     *
     * @return string
     */
    public function getSearchParam()
    {
        if ( $this->_sSearchParam === null ) {
            $this->_sSearchParam = false;
            if ( $this->_isSearchClass() ) {
                $this->_sSearchParam = rawurlencode( $this->getConfig()->getRequestParameter( 'searchparam', true ) );
            }
        }
        return $this->_sSearchParam;
    }

    /**
     * Template variable getter. Returns searched category id
     *
     * @return string
     */
    public function getSearchCatId()
    {
        if ( $this->_sSearchCatId === null ) {
            $this->_sSearchCatId = false;
            if ( $this->_isSearchClass() ) {
                $this->_sSearchCatId = rawurldecode( $this->getConfig()->getRequestParameter( 'searchcnid' ) );
            }
        }
        return $this->_sSearchCatId;
    }

    /**
     * Template variable getter. Returns searched vendor id
     *
     * @return string
     */
    public function getSearchVendor()
    {
        if ( $this->_sSearchVendor === null ) {
            $this->_sSearchVendor = false;
            if ( $this->_isSearchClass() ) {
                // searching in vendor #671
                $this->_sSearchVendor = rawurldecode( $this->getConfig()->getRequestParameter( 'searchvendor' ) );
            }
        }
        return $this->_sSearchVendor;
    }

    /**
     * Template variable getter. Returns searched Manufacturer id
     *
     * @return string
     */
    public function getSearchManufacturer()
    {
        if ( $this->_sSearchManufacturer === null ) {
            $this->_sSearchManufacturer = false;
            if ( $this->_isSearchClass() ) {
                // searching in Manufacturer #671
                $this->_sSearchManufacturer = rawurldecode( $this->getConfig()->getRequestParameter( 'searchmanufacturer' ) );
            }
        }
        return $this->_sSearchManufacturer;
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
     * Template variable getter. Returns active search
     *
     * @return object
     */
    public function getActiveCategory()
    {
        return $this->getActSearch();
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

        $aPath['title'] = oxRegistry::getLang()->translateString( 'SEARCH', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Returns cofig prameters blShowListDisplayType value
     *
     * @return boolean
     */
    public function canSelectDisplayType()
    {
        return $this->getConfig()->getConfigParam( 'blShowListDisplayType' );
    }

    /**
     * Checks if current request parameters does not block SEO redirection process
     *
     * @return bool
     */
    protected function _canRedirect()
    {
        return false;
    }

    /**
     * Article count getter
     *
     * @return int
     */
    public function getArticleCount()
    {
        return $this->_iAllArtCnt;
    }
}
