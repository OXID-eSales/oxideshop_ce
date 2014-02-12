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
 * Includes extended class.
 */

// view indexing state for search engines:
define( 'VIEW_INDEXSTATE_INDEX', 0 );           //  index without limitations
define( 'VIEW_INDEXSTATE_NOINDEXNOFOLLOW', 1 ); //  no index / no follow
define( 'VIEW_INDEXSTATE_NOINDEXFOLLOW', 2 );   //  no index / follow

/**
 * Base view class.
 * Class is responsible for managing of components that must be
 * loaded and executed before any regular operation.
 */
class oxUBase extends oxView
{
    /**
     * Facebook widget status marker
     * @var bool
     */
    protected $_blFbWidgetsOn = null;

    /**
     * Characters which should be removed while preparing meta keywords
     * @var string
     */
    protected $_sRemoveMetaChars = '.\+*?[^]$(){}=!<>|:&';

    /**
     * Array of component objects.
     *
     * @var object
     */
    protected $_oaComponents = array();

    /**
     * Flag if current view is an order view
     *
     * @var bool
     */
    protected $_blIsOrderStep = false;

    /**
     * List type
     *
     * @var string
     */
    protected $_sListType = null;

    /**
     * Possible list display types
     *
     * @var array
     */
    protected $_aListDisplayTypes = array( 'grid', 'line', 'infogrid' );

    /**
     * List display type
     *
     * @var string
     */
    protected $_sListDisplayType = null;

    /**
     * List display type
     *
     * @var string
     */
    protected $_sCustomListDisplayType = null;

    /**
     * Active articles category object.
     *
     * @var oxcategory
     */
    protected $_oActCategory = null;

    /**
     * Active Manufacturer object.
     *
     * @var oxManufacturer
     */
    protected $_oActManufacturer = null;

    /**
     * Active vendor object.
     *
     * @var oxvendor
     */
    protected $_oActVendor = null;

    /**
     * Active recommendation's list
     * @var object
     */
    protected $_oActiveRecommList = null;

    /**
     * Active search object - stdClass object which keeps navigation info
     *
     * @var stdClass
     */
    protected $_oActSearch = null;

    /**
     * Marked which defines if current view is sortable or not
     * @var bool
     */
    protected $_blShowSorting = false;

    /**
     * Load currency option
     * @var bool
     */
    protected $_blLoadCurrency = null;

    /**
     * Load Manufacturers option
     * @var bool
     */
    protected $_blLoadManufacturerTree = null;

    /**
     * Dont show emty cats
     * @var bool
     */
    protected $_blDontShowEmptyCats = null;

    /**
     * Load language option
     * @var bool
     */
    protected $_blLoadLanguage = null;

    /**
     * Item count in category top navigation
     * @var integer
     */
    protected $_iTopCatNavItmCnt = null;

    /**
     * Rss links
     * @var array
     */
    protected $_aRssLinks = null;

    /**
     * List's "order by"
     * @var string
     */
    protected $_sListOrderBy = null;

    /**
     * Order direction of list
     * @var string
     */
    protected $_sListOrderDir = null;

    /**
     * Meta description
     * @var string
     */
    protected $_sMetaDescription = null;

    /**
     * Meta keywords
     * @var string
     */
    protected $_sMetaKeywords = null;

    /**
     * Start page meta description CMS ident
     *
     * @var string
     */
    protected $_sMetaDescriptionIdent = null;

    /**
     * Start page meta keywords CMS ident
     *
     * @var string
     */
    protected $_sMetaKeywordsIdent = null;

    /**
     * Additional params for url.
     * @var string
     */
    protected $_sAdditionalParams = null;

    /**
     * Active currency object.
     * @var object
     */
    protected $_oActCurrency = null;

    /**
     * Private sales on/off state
     * @var bool
     */
    protected $_blEnabledPrivateSales = null;

    /**
     * Sign if any new component is added. On this case will be
     * executed components stored in oxBaseView::_aComponentNames
     * plus oxBaseView::_aComponentNames.
     * @var bool
     */
    protected $_blCommonAdded = false;

    /**
     * Current view search engine indexing state:
     *     VIEW_INDEXSTATE_INDEX - index without limitations
     *     VIEW_INDEXSTATE_NOINDEXNOFOLLOW - no index / no follow
     *     VIEW_INDEXSTATE_NOINDEXFOLLOW - no index / follow
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_INDEX;

    /**
     * If true, forces oxUbase::noIndex returns VIEW_INDEXSTATE_NOINDEXFOLLOW
     * ( oxUbase::$_iViewIndexState = VIEW_INDEXSTATE_NOINDEXFOLLOW; index / follow)
     *
     * @var bool
     */
    protected $_blForceNoIndex = false;

    /**
     * Number of products in comparelist.
     * @var integer
     */
    protected $_iCompItemsCnt = null;

    /**
     * Default content id
     *
     * @return string
     */
    protected $_sContentId = null;

    /**
     * Default content
     *
     * @return oxContent
     */
    protected $_oContent = null;

    /**
     * View id
     *
     * @var string
     */
    protected $_sViewResetID = null;

    /**
     * Display sorting in templates
     * @var bool
     *
     * @deprecated in v4.7/5.0 on 2013-01-04; use _blShowSorting property
     */
    protected $_blActiveSorting = null;

    /**
     * Menue list
     * @var array
     */
    protected $_aMenueList = null;

    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * @var array
     */
    protected $_aComponentNames = array(
                                    'oxcmp_user'       => 1, // 0 means dont init if cached
                                    'oxcmp_lang'       => 0,
                                    'oxcmp_cur'        => 1,
                                    'oxcmp_shop'       => 1,
                                    'oxcmp_categories' => 0,
                                    'oxcmp_utils'      => 1,
                                    'oxcmp_news'       => 0,
                                    'oxcmp_basket'     => 1
                                  );

    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation. User may modify this himself.
     * @var array
     */
    protected $_aUserComponentNames = array();

    /**
     * Current view product object
     *
     * @var oxarticle
     */
    protected $_oProduct = null;

    /**
     * Number of current list page.
     * @var integer
     */
    protected $_iActPage = null;

    /**
     * A list of articles.
     * @var array
     */
    protected $_aArticleList = null;

    /**
     * Manufacturer list object.
     * @var object
     */
    protected $_oManufacturerTree  = null;

    /**
     * Category tree object.
     * @var oxcategorylist
     */
    protected $_oCategoryTree  = null;

    /**
     * Top 5 article list.
     * @var array
     */
    protected $_aTop5ArticleList  = null;

    /**
     * Bargain article list.
     * @var array
     */
    protected $_aBargainArticleList  = null;

    /**
     * If order price to low
     * @var integer
     */
    protected $_blLowOrderPrice = null;

    /**
     * Min order price
     * @var string
     */
    protected $_sMinOrderPrice  = null;

    /**
     * Real newsletter status
     * @var string
     */
    protected $_iNewsRealStatus  = null;

    /**
     * Url parameters which block redirection
     *
     * @return null
     */
    protected $_aBlockRedirectParams = array( 'fnc', 'stoken', 'force_sid', 'force_admin_sid' );

    /**
     * Root vendor object
     * @var object
     */
    protected $_oRootVendor = null;

    /**
     * Vendor id
     * @var string
     */
    protected $_sVendorId = null;

    /**
     * Manufacturer list for search
     * @var array
     */
    protected $_aManufacturerlist = null;

    /**
     * Root manufacturer object
     * @var object
     */
    protected $_oRootManufacturer = null;

    /**
     * Manufacturer id
     * @var string
     */
    protected $_sManufacturerId = null;

    /**
     * Category tree for search
     *
     * @deprecated in v4.7.5/5.0,5 on 2013-03-27; not used in code, not needed
     *
     * @var array
     */
    protected $_aSearchCatTree = null;

    /**
     * Has user news subscribed
     * @var bool
     */
    protected $_blNewsSubscribed = null;

    /**
     * Delivery address
     * @var object
     */
    protected $_oDelAddress = null;

    /**
     * Category tree path
     * @var string
     */
    protected $_sCatTreePath = null;

    /**
     * Loaded contents array (cache)
     * @var array
     */
    protected $_aContents = array();

    /**
     * Sign if to load and show top5articles action
     * @var bool
     */
    protected $_blTop5Action = false;

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = false;

    /**
     * check all "must-be-fields" if they are completely
     *
     * @var array
     */
    protected $_aMustFillFields = null;

    /**
     * Show tags cloud
     * @var bool
     */
    protected $_blShowTagCloud = true;

    /**
     * If active root category was changed
     * @var bool
     */
    protected $_blRootCatChanged = false;

    /**
     * User address
     * @var array
     */
    protected $_aInvoiceAddress = null;

    /**
     * User delivery address
     * @var array
     */
    protected $_aDeliveryAddress = null;

    /**
     * Logged in user name
     * @var string
     */
    protected $_sActiveUsername = null;

    /**
     * Components which needs to be initialized/rendered (depending
     * on cache and its cache status)
     * @var array
     */
    protected static $_aCollectedComponentNames = null;

    /**
     * If active load components
     * By default active
     *
     * @var array
     */
    protected $_blLoadComponents = true;


    /**
     * Sorting columns list
     *
     * @var array
     */
    protected $_aSortColumns = null;

    /**
     * Returns component names
     *
     * @return array
     */
    protected function _getComponentNames()
    {
        if ( self::$_aCollectedComponentNames === null ) {
            self::$_aCollectedComponentNames = array_merge( $this->_aComponentNames, $this->_aUserComponentNames );

            // #1721: custom component handling. At the moment it is not possible to override this variable in oxubase,
            // so we added this array to config.inc.php file
            if ( ( $aUserCmps = $this->getConfig()->getConfigParam( 'aUserComponentNames' ) ) ) {
                self::$_aCollectedComponentNames = array_merge( self::$_aCollectedComponentNames, $aUserCmps );
            }

            if ( oxConfig::getParameter( '_force_no_basket_cmp' ) ) {
                unset( self::$_aCollectedComponentNames['oxcmp_basket'] );
            }
        }

        // resetting array pointer
        reset( self::$_aCollectedComponentNames );
        return self::$_aCollectedComponentNames;
    }

    /**
     * In non admin mode checks if request was NOT processed by seo handler.
     * If NOT, then tries to load alternative SEO url and if url is available -
     * redirects to it. If no alternative path was found - 404 header is emitted
     * and page is rendered
     *
     * @return null
     */
    protected function _processRequest()
    {
        $myUtils = oxRegistry::getUtils();

        // non admin, request is not empty and was not processed by seo engine
        if ( !isSearchEngineUrl() && $myUtils->seoIsActive() && ( $sStdUrl = getRequestUrl( '', true ) ) ) {

            // fetching standard url and looking for it in seo table
            if ( $this->_canRedirect() && ( $sRedirectUrl = oxRegistry::get("oxSeoEncoder")->fetchSeoUrl( $sStdUrl ) ) ) {
                $myUtils->redirect( $this->getConfig()->getCurrentShopUrl() . $sRedirectUrl, false );
            } elseif (VIEW_INDEXSTATE_INDEX == $this->noIndex()) {
                // forcing to set noindex/follow meta
                $this->_forceNoIndex();

                if ( $this->getConfig()->getConfigParam('blSeoLogging') ) {
                    $sShopId = $this->getConfig()->getShopId();
                    $sLangId = oxRegistry::getLang()->getBaseLanguage();
                    $sIdent  = md5( strtolower( $sStdUrl ) . $sShopId . $sLangId );

                    // logging "not found" url
                    $oDb = oxDb::getDb();
                    $oDb->execute( "replace oxseologs ( oxstdurl, oxident, oxshopid, oxlang )
                                    values ( " . $oDb->quote( $sStdUrl ) . ", '{$sIdent}', '{$sShopId}', '{$sLangId}' ) " );
                }
            }
        }
    }

    /**
     * Calls self::_processRequest(), initializes components which needs to
     * be loaded, sets current list type, calls parent::init()
     *
     * @return null
     */
    public function init()
    {
        $this->_processRequest();

        // storing current view
        $blInit = true;


        // init all components if there are any
        if ( $this->_blLoadComponents ) {
            foreach ( $this->_getComponentNames() as $sComponentName => $blNotCacheable ) {
                // do not override initiated components
                if ( !isset( $this->_oaComponents[$sComponentName] ) ) {
                    // component objects MUST be created to support user called functions
                    $oComponent = oxNew( $sComponentName );
                    $oComponent->setParent( $this );
                    $oComponent->setThisAction( $sComponentName );
                    $this->_oaComponents[$sComponentName] = $oComponent;
                }

                // do we really need to initiate them ?
                if ( $blInit ) {
                    $this->_oaComponents[$sComponentName]->init();

                    // executing only is view does not have action method
                    if ( !method_exists( $this, $this->getFncName() ) ) {
                        $this->_oaComponents[$sComponentName]->executeFunction( $this->getFncName() );
                    }
                }
            }
        }

        parent::init();
    }

    /**
     * If current view ID is not set - forms and returns view ID
     * according to language and currency.
     *
     * @return string $this->_sViewId
     */
    public function getViewId()
    {
        if ( $this->_sViewId ) {
            return $this->_sViewId;
        }

        $myConfig = $this->getConfig();
        $iLang = oxRegistry::getLang()->getBaseLanguage();
        $iCur  = (int) $myConfig->getShopCurrency();


            $this->_sViewId =  "ox|$iLang|$iCur";

        $this->_sViewId .= "|".( (int) $this->_blForceNoIndex ).'|'.((int)$this->isRootCatChanged());

        // #0004798: SSL should be included in viewId
        if ($myConfig->isSsl()) {
            $this->_sViewId .= "|ssl";
        }

        // #0002866: external global viewID addition
        if (function_exists('customGetViewId')) {
            $oExtViewId = customGetViewId();

            if ($oExtViewId !== null) {
                $this->_sViewId .= '|'.md5(serialize($oExtViewId));
            }
        }
        return $this->_sViewId;
    }


    /**
     * Template variable getter. Returns true if sorting is on
     *
     * @return bool
     */
    public function showSorting()
    {
        return $this->_blShowSorting && $this->getConfig()->getConfigParam( 'blShowSorting' );
    }

    /**
     * Set array of component objects
     *
     * @param array $aComponents array of components objects
     *
     * @return null
     */
    public function setComponents( $aComponents = null )
    {
        $this->_oaComponents = $aComponents;
    }

    /**
     * Get array of component objects
     *
     * @return array
     */
    public function getComponents()
    {
        return $this->_oaComponents;
    }

    /**
     * Get component object
     *
     * @param string $sName name of component object
     *
     * @return object
     */
    public function getComponent( $sName )
    {
        if ( isset( $sName ) && isset( $this->_oaComponents[$sName] ) ) {
            return $this->_oaComponents[$sName];
        }
    }
    /**
     * Set flag if current view is an order view
     *
     * @param bool $blIsOrderStep flag if current view is an order view
     *
     * @return null
     */
    public function setIsOrderStep( $blIsOrderStep = null )
    {
        $this->_blIsOrderStep = $blIsOrderStep;
    }

    /**
     * Get flag if current view is an order view
     *
     * @return bool
     */
    public function getIsOrderStep()
    {
        return $this->_blIsOrderStep;
    }


    /**
     * Active category setter
     *
     * @param oxCategory $oCategory active category
     *
     * @return null
     */
    public function setActiveCategory( $oCategory )
    {
        $this->_oActCategory = $oCategory;
    }

    /**
     * Returns active category
     *
     * @return null
     */
    public function getActiveCategory()
    {
        return $this->_oActCategory;
    }

    /**
     * Get list type
     *
     * @return string list type
     */
    public function getListType()
    {
        if ( $this->_sListType == null ) {
            if ( $sListType = oxConfig::getParameter( 'listtype' ) ) {
                $this->_sListType = $sListType;
            } elseif ( $sListType = $this->getConfig()->getGlobalParameter( 'listtype' ) ) {
                $this->_sListType = $sListType;
            }
        }
        return $this->_sListType;
    }

    /**
     * Returns list type
     *
     * @return string
     */
    public function getListDisplayType()
    {
        if ( $this->_sListDisplayType == null ) {
            $this->_sListDisplayType = $this->getCustomListDisplayType();

            if ( !$this->_sListDisplayType ) {
                $this->_sListDisplayType = $this->getConfig()->getConfigParam( 'sDefaultListDisplayType' );
            }

            $this->_sListDisplayType = in_array( ( string ) $this->_sListDisplayType, $this->_aListDisplayTypes ) ? $this->_sListDisplayType : 'infogrid';

            // writing to session
            if ( oxConfig::getParameter( 'ldtype' ) ) {
                oxSession::setVar( 'ldtype', $this->_sListDisplayType );
            }
        }
        return $this->_sListDisplayType;
    }

    /**
     * Returns changed default list type
     *
     * @return string
     */
    public function getCustomListDisplayType()
    {
        if ( $this->_sCustomListDisplayType == null ) {
            $this->_sCustomListDisplayType = oxConfig::getParameter( 'ldtype' );

            if ( !$this->_sCustomListDisplayType ) {
                $this->_sCustomListDisplayType = oxRegistry::getSession()->getVariable( 'ldtype' );
            }
        }
        return $this->_sCustomListDisplayType;
    }

    /**
     * List type setter
     *
     * @param string $sType type of list
     *
     * @return null
     */
    public function setListType( $sType )
    {
        $this->_sListType = $sType;
        $this->getConfig()->setGlobalParameter( 'listtype', $sType );
    }

    /**
     * Returns currency swiching option
     *
     * @return bool
     */
    public function loadCurrency()
    {
        if ( $this->_blLoadCurrency == null ) {
            $this->_blLoadCurrency = false;
            if ( $blLoadCurrency = $this->getConfig()->getConfigParam( 'bl_perfLoadCurrency' ) ) {
                $this->_blLoadCurrency = $blLoadCurrency;
            }
        }
        return $this->_blLoadCurrency;
    }

    /**
     * Returns true if empty categories are not loaded
     *
     * @return bool
     */
    public function dontShowEmptyCategories()
    {
        if ( $this->_blDontShowEmptyCats == null ) {
            $this->_blDontShowEmptyCats = false;
            if ( $blDontShowEmptyCats = $this->getConfig()->getConfigParam( 'blDontShowEmptyCategories' ) ) {
                $this->_blDontShowEmptyCats = $blDontShowEmptyCats;
            }
        }
        return $this->_blDontShowEmptyCats;
    }

    /**
     * Returns true if empty categories are not loaded
     *
     * @return bool
     */
    public function showCategoryArticlesCount()
    {
        return $this->getConfig()->getConfigParam( 'bl_perfShowActionCatArticleCnt' );
    }

    /**
     * Returns if language should be loaded
     *
     * @return bool
     */
    public function isLanguageLoaded()
    {
        if ( $this->_blLoadLanguage == null ) {
            $this->_blLoadLanguage = false;
            if ( $blLoadLanguage = $this->getConfig()->getConfigParam( 'bl_perfLoadLanguages' ) ) {
                $this->_blLoadLanguage = $blLoadLanguage;
            }
        }
        return $this->_blLoadLanguage;
    }

    /**
     * Returns item count in top navigation of categories
     *
     * @return integer
     */
    public function getTopNavigationCatCnt()
    {
        if ( $this->_iTopCatNavItmCnt == null ) {
            $iTopCatNavItmCnt = $this->getConfig()->getConfigParam( 'iTopNaviCatCount' );
            $this->_iTopCatNavItmCnt = $iTopCatNavItmCnt ? $iTopCatNavItmCnt : 5;
        }
        return $this->_iTopCatNavItmCnt;
    }

    /**
     * addRssFeed adds link to rss
     *
     * @param string $sTitle feed page title
     * @param string $sUrl   feed url
     * @param int    $key    feed number
     *
     * @return null
     */
    public function addRssFeed($sTitle, $sUrl, $key = null)
    {
        if (!is_array($this->_aRssLinks)) {
            $this->_aRssLinks = array();
        }

        $sUrl = oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession($sUrl);

        if ($key === null) {
            $this->_aRssLinks[] = array('title'=>$sTitle, 'link' => $sUrl);
        } else {
            $this->_aRssLinks[$key] = array('title'=>$sTitle, 'link' => $sUrl);
        }
    }

    /**
     * Returns sorted column parameter name
     *
     * @return string
     */
    public function getSortOrderByParameterName()
    {
        return 'listorderby';
    }

     /**
     * Returns sorted column direction parameter name
     *
     * @return string
     */
    public function getSortOrderParameterName()
    {
        return 'listorder';
    }


    /**
     * Returns page sort indentificator. It is used as intentificator in session variable aSorting[ident]
     *
     * @return string
     */
    public function getSortIdent()
    {
        return 'alist';
    }

    /**
     * Returns default category sorting for selected category
     *
     * @return null
     */
    public function getDefaultSorting()
    {
        return null;
    }

    /**
     * Returns default category sorting for selected category
     *
     * @return array
     */
    public function getUserSelectedSorting()
    {
        $aSorting = null;
        $oStr = getStr();
        $oConfig = oxRegistry::getConfig();
        $aSortDirections = array( 'desc', 'asc' );
        $aSortColumns = $this->getSortColumns();

        $sSortBy  = $oConfig->getParameter( $this->getSortOrderByParameterName() );
        $sSortDir = $oConfig->getParameter( $this->getSortOrderParameterName() );

        if ( $sSortBy && oxDb::getInstance()->isValidFieldName( $sSortBy ) && /*in_array( $oStr->strtolower($sSortBy), $aSortColumns ) &&*/
            $sSortDir && oxRegistry::getUtils()->isValidAlpha( $sSortDir ) && in_array( $oStr->strtolower($sSortDir), $aSortDirections ) ) {
            $aSorting = array ( 'sortby' => $sSortBy, 'sortdir' => $sSortDir );
        }

        return $aSorting;
    }


    /**
     * Returns sorting variable from session
     *
     * @param string $sSortIdent sorting indent
     *
     * @return array
     */
    public function getSavedSorting( $sSortIdent )
    {
        $aSorting = oxRegistry::getSession()->getVariable( 'aSorting' );
        if ( isset( $aSorting[$sSortIdent] ) ) {
            return $aSorting[$sSortIdent];
        }
    }

    /**
     * Set sorting column name
     *
     * @param string $sColumn - column name
     *
     * @return string
     */
    public function setListOrderBy( $sColumn )
    {
        $this->_sListOrderBy = $sColumn;
    }

    /**
     * Set sorting directions
     *
     * @param string $sDirection - direction desc / asc
     *
     * @return string
     */
    public function setListOrderDirection( $sDirection )
    {
        $this->_sListOrderDir = $sDirection;
    }



    /**
     * Retrieves from session or gets new sorting parameters for
     * search and category lists. Sets new sorting parameters
     * (reverse or new column sort) to session.
     *
     * Session variables:
     * <b>listorderby</b>, <b>listorder</b>
     *
     * @deprecated since v4.7.3/5.0.3 (2013-01-07); use getSorting();
     *
     * @return null
     */
    public function prepareSortColumns()
    {
        $aSortColumns = $this->getConfig()->getConfigParam( 'aSortCols' );
        $aSortDir = array( 'desc', 'asc' );
        if ( count( $aSortColumns ) > 0 ) {

            $this->_aSortColumns = $aSortColumns;

            $sCnid = oxRegistry::getConfig()->getRequestParameter( 'cnid' );


            $sSortBy  = oxRegistry::getConfig()->getRequestParameter( $this->getSortOrderByParameterName() );
            $sSortDir = oxRegistry::getConfig()->getRequestParameter( $this->getSortOrderParameterName() );

            $oStr = getStr();
            if ( (!$sSortBy || !in_array( $oStr->strtolower($sSortBy), $aSortColumns) || !in_array( $oStr->strtolower($sSortDir), $aSortDir) ) && $aSorting = $this->getSorting( $sCnid ) ) {
                $sSortBy  = $aSorting['sortby'];
                $sSortDir = $aSorting['sortdir'];
            }

            if ( $sSortBy && oxDb::getInstance()->isValidFieldName( $sSortBy ) &&
                 $sSortDir && oxRegistry::getUtils()->isValidAlpha( $sSortDir ) ) {

                $this->_sListOrderBy  = $sSortBy;
                $this->_sListOrderDir = $sSortDir;

                // caching sorting config
                $this->setItemSorting( $sCnid, $sSortBy, $sSortDir );
            }
        }
    }

    /**
     * Template variable getter. Returns string after the list is ordered by
     *
     * @return array
     */
    public function getListOrderBy()
    {
        //if column is with table name split it
        $aColumns = explode('.', $this->_sListOrderBy);

        if ( is_array($aColumns) && count($aColumns) > 1 ) {
           return $aColumns[1];
        }

        return $this->_sListOrderBy;
    }

    /**
     * Template variable getter. Returns list order direction
     *
     * @return array
     */
    public function getListOrderDirection()
    {
        return $this->_sListOrderDir;
    }

    /**
     * Sets the view parameter "meta_description"
     *
     * @param string $sDescription prepared string for description
     *
     * @return null
     */
    public function setMetaDescription ( $sDescription )
    {
        return $this->_sMetaDescription = $sDescription;
    }

    /**
     * Sets the view parameter 'meta_keywords'
     *
     * @param string $sKeywords prepared string for meta keywords
     *
     * @return null
     */
    public function setMetaKeywords( $sKeywords )
    {
        return $this->_sMetaKeywords = $sKeywords;
    }

    /**
     * Fetches meta data (description or keywords) from seo table
     *
     * @param string $sDataType data type "oxkeywords" or "oxdescription"
     *
     * @return string
     */
    protected function _getMetaFromSeo( $sDataType )
    {
        $sOxid  = $this->_getSeoObjectId();
        $iLang  = oxRegistry::getLang()->getBaseLanguage();
        $sShop  = $this->getConfig()->getShopId();

        if ( $sOxid && oxRegistry::getUtils()->seoIsActive() &&
             ( $sKeywords = oxRegistry::get("oxSeoEncoder")->getMetaData( $sOxid, $sDataType, $sShop, $iLang) ) ) {
            return $sKeywords;
        }
    }

    /**
     * Fetches meta data (description or keywords) from content table
     *
     * @param string $sMetaIdent meta content ident
     *
     * @return string
     */
    protected function _getMetaFromContent( $sMetaIdent )
    {
        if ( $sMetaIdent ) {
            $oContent = oxNew( 'oxcontent' );
            if ( $oContent->loadByIdent( $sMetaIdent ) &&
                 $oContent->oxcontents__oxactive->value ) {
                return getStr()->strip_tags( $oContent->oxcontents__oxcontent->value );
            }
        }
    }

    /**
     * Template variable getter. Returns meta keywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        if ( $this->_sMetaKeywords === null ) {
            $this->_sMetaKeywords = false;

            // set special meta keywords ?
            if ( ( $sKeywords = $this->_getMetaFromSeo( 'oxkeywords' ) ) ) {
                $this->_sMetaKeywords = $sKeywords;
            } elseif ( ( $sKeywords = $this->_getMetaFromContent( $this->_sMetaKeywordsIdent ) ) ) {
                $this->_sMetaKeywords = $this->_prepareMetaKeyword( $sKeywords, false );
            } else {
                $this->_sMetaKeywords = $this->_prepareMetaKeyword( false, true );
            }
        }

        return $this->_sMetaKeywords;
    }

    /**
     * Template variable getter. Returns meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        if ( $this->_sMetaDescription === null ) {
            $this->_sMetaDescription = false;

            // set special meta description ?
            if ( ( $sDescription = $this->_getMetaFromSeo( 'oxdescription' ) ) ) {
                $this->_sMetaDescription = $sDescription;
            } elseif ( ( $sDescription = $this->_getMetaFromContent( $this->_sMetaDescriptionIdent ) ) ) {
                $this->_sMetaDescription = $this->_prepareMetaDescription( $sDescription );
            } else {
                $this->_sMetaDescription = $this->_prepareMetaDescription( false );
            }
        }

        return $this->_sMetaDescription;
    }

    /**
     * Get active currency
     *
     * @return object
     */
    public function getActCurrency()
    {
        return $this->_oActCurrency;
    }

    /**
     * Active currency setter
     *
     * @param object $oCur corrency object
     *
     * @return object
     */
    public function setActCurrency( $oCur )
    {
        $this->_oActCurrency = $oCur;
    }

    /**
     * Template variable getter. Returns comparison article list count.
     *
     * @return integer
     */
    public function getCompareItemCount()
    {
        if ( $this->_iCompItemsCnt === null ) {
            $aItems = oxRegistry::getSession()->getVariable('aFiltcompproducts');
            $this->_iCompItemsCnt = is_array($aItems)?count($aItems):0;
        }
        return $this->_iCompItemsCnt;
    }

    /**
     * Forces output noindex meta data for current view
     *
     * @return null
     */
    protected function _forceNoIndex()
    {
        $this->_blForceNoIndex = true;
    }

    /**
     * Marks that current view is marked as noindex, nofollow and
     * article details links must contain nofollow tags
     *
     * @return int
     */
    public function noIndex()
    {
        if ( $this->_blForceNoIndex ) {
            $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXFOLLOW;
        } elseif ( oxConfig::getParameter( 'cur' ) ) {
            $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;
        } else {
            switch ( oxConfig::getParameter( 'fnc' ) ) {
                case 'tocomparelist':
                case 'tobasket':
                    $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;
                    break;
            }
        }
        return $this->_iViewIndexState;
    }

    /**
     * Returns if sorting is active and can be displayed
     *
     * @return bool
     *
     * @deprecated in v4.7/5.0 on 2013-01-04; use oxUbase::showSorting()
     */
    public function isSortingActive()
    {
        return $this->_blActiveSorting;
    }

    /**
     * Template variable getter. Returns header menue list
     *
     * @return array
     */
    public function getMenueList()
    {
        return $this->_aMenueList;
    }

    /**
     * Header menue list setter
     *
     * @param array $aMenue menu list
     *
     * @return null
     */
    public function setMenueList( $aMenue )
    {
        $this->_aMenueList = $aMenue;
    }


    /**
     * Sets number of articles per page to config value
     *
     * @return null
     */
    protected function _setNrOfArtPerPage()
    {
        $myConfig  = $this->getConfig();

        //setting default values to avoid possible errors showing article list
        $iNrofCatArticles = $myConfig->getConfigParam( 'iNrofCatArticles' );

        $iNrofCatArticles = ( $iNrofCatArticles ) ? $iNrofCatArticles : 10;

        // checking if all needed data is set
        switch ( $this->getListDisplayType() ) {
            case 'grid':
                $aNrofCatArticles = $myConfig->getConfigParam( 'aNrofCatArticlesInGrid' );
                break;
            case 'line':
            case 'infogrid':
            default:
                $aNrofCatArticles = $myConfig->getConfigParam( 'aNrofCatArticles' );
        }

        if ( !is_array( $aNrofCatArticles ) || !isset( $aNrofCatArticles[0] ) ) {
            $aNrofCatArticles = array( $iNrofCatArticles );
            $myConfig->setConfigParam( 'aNrofCatArticles', $aNrofCatArticles );
        } else {
            $iNrofCatArticles = $aNrofCatArticles[0];
        }

        $oViewConf = $this->getViewConfig();
        //value from user input
        if ( ( $iNrofArticles = (int) oxConfig::getParameter( '_artperpage' ) ) ) {
            // M45 Possibility to push any "Show articles per page" number parameter
            $iNrofCatArticles = ( in_array( $iNrofArticles, $aNrofCatArticles ) ) ? $iNrofArticles : $iNrofCatArticles;
            $oViewConf->setViewConfigParam( 'iartPerPage', $iNrofCatArticles );
            oxSession::setVar( '_artperpage', $iNrofCatArticles );
        } elseif ( ( $iSessArtPerPage = oxRegistry::getSession()->getVariable( '_artperpage' ) )&& is_numeric( $iSessArtPerPage ) ) {
            // M45 Possibility to push any "Show articles per page" number parameter
            $iNrofCatArticles = ( in_array( $iSessArtPerPage, $aNrofCatArticles ) ) ? $iSessArtPerPage : $iNrofCatArticles;
            $oViewConf->setViewConfigParam( 'iartPerPage', $iSessArtPerPage );
            $iNrofCatArticles = $iSessArtPerPage;
        } else {
            $oViewConf->setViewConfigParam( 'iartPerPage', $iNrofCatArticles );
        }

        //setting number of articles per page to config value
        $myConfig->setConfigParam( 'iNrofCatArticles', $iNrofCatArticles );
    }

    /**
     * Override this function to return object it which is used to identify its seo meta info
     *
     * @return null
     */
    protected function _getSeoObjectId()
    {
    }

    /**
     * Returns current view meta description data
     *
     * @param string $sMeta                   category path
     * @param int    $iLength                 max length of result, -1 for no truncation
     * @param bool   $blRemoveDuplicatedWords if true - performs additional duplicate cleaning
     *
     * @return  string  $sString    converted string
     */
    protected function _prepareMetaDescription( $sMeta, $iLength = 1024, $blRemoveDuplicatedWords = false )
    {
        if ( $sMeta ) {

            $oStr = getStr();
            if ( $iLength != -1 ) {
                /* *
                 * performance - we do not need a huge amount of initial text.
                 * assume that effective text may be double longer than $iLength
                 * and simple truncate it
                 */
                $iELength = ( $iLength * 2 );
                $sMeta = $oStr->substr( $sMeta, 0, $iELength );
            }

            // decoding html entities
            $sMeta = $oStr->html_entity_decode( $sMeta );
            // stripping HTML tags
            $sMeta = $oStr->strip_tags( $sMeta );

            // removing some special chars
            $sMeta = $oStr->cleanStr( $sMeta );

            // removing duplicate words
            if ( $blRemoveDuplicatedWords ) {
                $sMeta = $this->_removeDuplicatedWords( $sMeta, $this->getConfig()->getConfigParam( 'aSkipTags' ) );
            }

            // some special cases
            $sMeta = str_replace( ' ,', ',', $sMeta );
            $aPattern = array( "/,[\s\+\-\*]*,/", "/\s+,/" );
            $sMeta = $oStr->preg_replace( $aPattern, ',', $sMeta );
            $sMeta = oxRegistry::get("oxUtilsString")->minimizeTruncateString( $sMeta, $iLength );
            $sMeta = $oStr->htmlspecialchars( $sMeta );

            return trim( $sMeta );
        }
    }

    /**
     * Returns current view keywords separated by comma
     *
     * @param string $sKeywords               data to use as keywords
     * @param bool   $blRemoveDuplicatedWords if true - performs additional duplicate cleaning
     *
     * @return string of keywords separated by comma
     */
    protected function _prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords = true )
    {

        $sString = $this->_prepareMetaDescription( $sKeywords, -1, false );

        if ( $blRemoveDuplicatedWords ) {
            $sString = $this->_removeDuplicatedWords( $sString, $this->getConfig()->getConfigParam( 'aSkipTags' ) );
        }

       return trim( $sString );
    }

    /**
     * Removes duplicated words (not case sensitive)
     *
     * @param mixed $aInput    array of string or string
     * @param array $aSkipTags in admin defined strings
     *
     * @return string of words separated by comma
     */
    protected function _removeDuplicatedWords( $aInput, $aSkipTags = array() )
    {
        $oStr = getStr();
        if ( is_array( $aInput ) ) {
            $aInput = implode( " ", $aInput );
        }

        // removing some usually met characters..
        $aInput = $oStr->preg_replace( "/[".preg_quote( $this->_sRemoveMetaChars, "/" )."]/", " ", $aInput );

        // splitting by word
        $aStrings = $oStr->preg_split( "/[\s,]+/", $aInput );

        if ( $sCount = count( $aSkipTags ) ) {
            for ( $iNum = 0; $iNum < $sCount; $iNum++ ) {
                $aSkipTags[$iNum] = $oStr->strtolower( $aSkipTags[$iNum] );
            }
        }
        $sCount = count($aStrings);
        for ( $iNum = 0; $iNum < $sCount; $iNum++ ) {
            $aStrings[$iNum] = $oStr->strtolower( $aStrings[$iNum] );
            // removing in admin defined strings
            if ( !$aStrings[$iNum] || in_array( $aStrings[$iNum], $aSkipTags ) ) {
                unset( $aStrings[$iNum] );
            }
        }

        // duplicates
        return implode( ', ', array_unique( $aStrings ) );
    }

    /**
     * Returns array of params => values which are used in hidden forms and as additional url params.
     * NOTICE: this method SHOULD return raw (non encoded into entities) parameters, because values
     * are processed by htmlentities() to avoid security and broken templates problems
     *
     * @return array
     */
    public function getNavigationParams()
    {
        $aParams['cnid'] = $this->getCategoryId();
        $aParams['mnid'] = oxConfig::getParameter( 'mnid' );

        $aParams['listtype'] = $this->getListType();
        $aParams['ldtype'] = $this->getCustomListDisplayType();
        $aParams['actcontrol'] = $this->getClassName();

        $aParams['recommid'] = oxConfig::getParameter( 'recommid' );

        $aParams['searchrecomm'] = oxConfig::getParameter( 'searchrecomm', true );
        $aParams['searchparam']  = oxConfig::getParameter( 'searchparam', true );
        $aParams['searchtag']    = oxConfig::getParameter( 'searchtag', true );

        $aParams['searchvendor'] = oxConfig::getParameter( 'searchvendor' );
        $aParams['searchcnid']   = oxConfig::getParameter( 'searchcnid' );
        $aParams['searchmanufacturer'] = oxConfig::getParameter( 'searchmanufacturer' );

        return $aParams;
    }

    /**
     * Sets sorting item config
     *
     * @param string $sSortIdent sortable item id
     * @param string $sSortBy    sort field
     * @param string $sSortDir   sort direction (optional)
     *
     * @return null
     */
    public function setItemSorting( $sSortIdent, $sSortBy, $sSortDir = null )
    {
        $aSorting = oxRegistry::getSession()->getVariable( 'aSorting' );
        $aSorting[$sSortIdent]['sortby']  = $sSortBy;
        $aSorting[$sSortIdent]['sortdir'] = $sSortDir ? $sSortDir : null;

        oxRegistry::getSession()->setVariable( 'aSorting', $aSorting );
    }

    /**
     * Returns sorting config for current item
     *
     * @param string $sSortIdent sortable item id
     *
     * @return array
     */
    public function getSorting( $sSortIdent )
    {
        $aSorting = null;

        if ( $aSorting = $this->getUserSelectedSorting() ) {
            $this->setItemSorting( $sSortIdent, $aSorting['sortby'], $aSorting['sortdir'] );
        } elseif ( !$aSorting = $this->getSavedSorting( $sSortIdent ) ) {
            $aSorting = $this->getDefaultSorting();
        }

        if ( $aSorting ) {
            $this->setListOrderBy( $aSorting['sortby'] );
            $this->setListOrderDirection( $aSorting['sortdir'] );
        }

        return $aSorting;
    }

    /**
     * Returns part of SQL query with sorting params
     *
     * @param string $sIdent sortable item id
     *
     * @return string
     */
    public function getSortingSql( $sIdent )
    {
        $aSorting = $this->getSorting( $sIdent );
        if ( is_array( $aSorting ) ) {
            return implode( " ", $aSorting );
        }
    }

    /**
     * Returns title suffix used in template
     *
     * @return string
     */
    public function getTitleSuffix()
    {
        return $this->getConfig()->getActiveShop()->oxshops__oxtitlesuffix->value;
    }

    /**
     * Returns title page suffix used in template in lists
     *
     * @return string
     */
    public function getTitlePageSuffix()
    {
    }

    /**
     * Returns title prefix used in template
     *
     * @return string
     *
     */
    public function getTitlePrefix()
    {
        return $this->getConfig()->getActiveShop()->oxshops__oxtitleprefix->value;
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
        return null;
    }

    /**
     * returns additional url params for dynamic url building
     *
     * @return string
     */
    public function getDynUrlParams()
    {
        $sRet = '';
        $sListType = $this->getListType();

        switch ($sListType) {
            default:
                break;
            case 'search':
                $sRet .= "&amp;listtype={$sListType}";
                if ( $sSearchParamForLink = rawurlencode( oxConfig::getParameter( 'searchparam', true ) ) ) {
                    $sRet .= "&amp;searchparam={$sSearchParamForLink}";
                }

                if ( ( $sVar = oxConfig::getParameter( 'searchcnid', true ) ) ) {
                    $sRet .= '&amp;searchcnid='.rawurlencode( rawurldecode( $sVar ) );
                }
                if ( ( $sVar = oxConfig::getParameter( 'searchvendor', true ) ) ) {
                    $sRet .= '&amp;searchvendor='.rawurlencode( rawurldecode( $sVar ) );
                }
                if ( ( $sVar = oxConfig::getParameter( 'searchmanufacturer', true ) ) ) {
                    $sRet .= '&amp;searchmanufacturer='.rawurlencode( rawurldecode( $sVar ) );
                }
                break;
            case 'tag':
                $sRet .= "&amp;listtype={$sListType}";
                if ( $sParam = rawurlencode( oxConfig::getParameter( 'searchtag', true ) ) ) {
                    $sRet .= "&amp;searchtag={$sParam}";
                }
                break;
        }

        return $sRet;
    }

    /**
     * get link of current view
     *
     * @param int $iLang requested language
     *
     * @return string
     */
    public function getLink( $iLang = null )
    {
        if ( !isset( $iLang ) ) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }

        $oDisplayObj = null;
        $blTrySeo = false;
        if ( oxRegistry::getUtils()->seoIsActive() ) {
            $blTrySeo = true;
            $oDisplayObj = $this->_getSubject( $iLang );
        }
        $iActPageNr = $this->getActPage();

        if ( $oDisplayObj ) {
            return $this->_addPageNrParam( $oDisplayObj->getLink( $iLang ), $iActPageNr, $iLang );
        }

        $myConfig = $this->getConfig();

        if ( $blTrySeo ) {
            $oEncoder = oxRegistry::get("oxSeoEncoder");
            if ( ( $sSeoUrl = $oEncoder->getStaticUrl( $myConfig->getShopHomeURL( $iLang ) . $this->_getSeoRequestParams(), $iLang ) ) ) {
                return $this->_addPageNrParam( $sSeoUrl, $iActPageNr, $iLang );
            }
        }

        $sUrl = oxRegistry::get("oxUtilsUrl")->processUrl( $myConfig->getShopCurrentURL( $iLang ) . $this->_getRequestParams(), true, null, $iLang);

        // fallback to old non seo url
        return $this->_addPageNrParam( $sUrl, $iActPageNr, $iLang );
    }

    /**
     * Returns view object canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
    }

    /**
     * Return array of id to form recommend list.
     * Should be overridden if need.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        return false;
    }
    /**
     * Template variable getter. Returns search parameter for Html
     * So far this method is implemented in search (search.php) view.
     *
     * @return null
     */
    public function getSearchParamForHtml()
    {
    }

    /**
     * collects _GET parameters used by eShop and returns uri
     *
     * @param bool $blAddPageNr if TRUE - page number will be added
     *
     * @return string
     */
    protected function _getRequestParams( $blAddPageNr  = true )
    {
        $sClass = $this->getClassName();
        $sFnc   = $this->getFncName();

        $aFnc = array( 'tobasket', 'login_noredirect', 'addVoucher', 'moveleft', 'moveright' );
        if ( in_array( $sFnc, $aFnc ) ) {
            $sFnc = '';
        }

        // #680
        $sURL = "cl={$sClass}";
        if ( $sFnc ) {
            $sURL .= "&amp;fnc={$sFnc}";
        }
        if ( $sVal = oxConfig::getParameter( 'cnid' ) ) {
            $sURL .= "&amp;cnid={$sVal}";
        }
        if ( $sVal = oxConfig::getParameter( 'mnid' ) ) {
            $sURL .= "&amp;mnid={$sVal}";
        }
        if ( $sVal= oxConfig::getParameter( 'anid' ) ) {
            $sURL .= "&amp;anid={$sVal}";
        }

        if ( $sVal = basename( oxConfig::getParameter( 'page' ) ) ) {
            $sURL .= "&amp;page={$sVal}";
        }

        if ( $sVal = basename( oxConfig::getParameter( 'tpl' ) ) ) {
            $sURL .= "&amp;tpl={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'oxloadid' ) ) {
            $sURL .= "&amp;oxloadid={$sVal}";
        }

        $iPgNr = (int) oxConfig::getParameter( 'pgNr' );
        // don't include page number for navigation
        // it will be done in oxubase::generatePageNavigation
        if ( $blAddPageNr && $iPgNr > 0 ) {
            $sURL .= "&amp;pgNr={$iPgNr}";
        }

        // #1184M - specialchar search
        if ( $sVal = rawurlencode( oxConfig::getParameter( 'searchparam', true ) ) ) {
            $sURL .= "&amp;searchparam={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'searchcnid' ) ) {
            $sURL .= "&amp;searchcnid={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'searchvendor' ) ) {
            $sURL .= "&amp;searchvendor={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'searchmanufacturer' ) ) {
            $sURL .= "&amp;searchmanufacturer={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'searchrecomm' ) ) {
            $sURL .= "&amp;searchrecomm={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'searchtag' ) ) {
            $sURL .= "&amp;searchtag={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'recommid' ) ) {
            $sURL .= "&amp;recommid={$sVal}";
        }

        return $sURL;
    }

    /**
     * collects _GET parameters used by eShop SEO and returns uri
     *
     * @return string
     */
    protected function _getSeoRequestParams()
    {
        $sClass = $this->getClassName();
        $sFnc   = $this->getFncName();

        // #921 S
        $aFnc = array( 'tobasket', 'login_noredirect', 'addVoucher' );
        if ( in_array( $sFnc, $aFnc ) ) {
            $sFnc = '';
        }

        // #680
        $sURL = "cl={$sClass}";
        if ( $sFnc ) {
            $sURL .= "&amp;fnc={$sFnc}";
        }
        if ( $sVal = basename( oxConfig::getParameter( 'page' ) ) ) {
            $sURL .= "&amp;page={$sVal}";
        }

        if ( $sVal = basename( oxConfig::getParameter( 'tpl' ) ) ) {
            $sURL .= "&amp;tpl={$sVal}";
        }

        if ( $sVal = oxConfig::getParameter( 'oxloadid' ) ) {
            $sURL .= "&amp;oxloadid={$sVal}";
        }

        $iPgNr = (int) oxConfig::getParameter( 'pgNr' );
        if ( $iPgNr > 0 ) {
            $sURL .= "&amp;pgNr={$iPgNr}";
        }

        return $sURL;
    }

    /**
     * Returns show category search
     *
     * @return bool
     */
    public function showSearch()
    {
        $blShow = true;
        if ( $this->getConfig()->getConfigParam( 'blDisableNavBars' ) && $this->getIsOrderStep() ) {
            $blShow = false;
        }
        return (int) $blShow;
    }

    /**
     * Returns RSS links
     *
     * @return array
     */
    public function getRssLinks()
    {
        return $this->_aRssLinks;
    }

    /**
     * Template variable getter. Returns sorting columns
     *
     * @return array
     */
    public function getSortColumns()
    {
        if ( $this->_aSortColumns === null ) {
            $this->setSortColumns( $this->getConfig()->getConfigParam( 'aSortCols' ) );
        }
        return $this->_aSortColumns;
    }


    /**
     * Set sorting columns
     *
     * @param array $aSortColumns array of column names array('name1', 'name2',...)
     *
     * @return null
     */
    public function setSortColumns( $aSortColumns )
    {
        $this->_aSortColumns = $aSortColumns;
    }

    /**
     * Returns if tags will be edit
     *
     * @return bool
     */
    public function getEditTags()
    {
    }

    /**
     * Template variable getter. Returns search string
     *
     * @return string
     */
    public function getRecommSearch()
    {
    }

    /**
     * Template variable getter. Returns payment id
     *
     * @return string
     */
    public function getPaymentList()
    {
    }

    /**
     * Template variable getter. Returns active recommendation lists
     *
     * @return string
     */
    public function getActiveRecommList()
    {
        if ( $this->_oActiveRecommList === null ) {
            $this->_oActiveRecommList = false;
            if ( $sOxid = oxConfig::getParameter( 'recommid' ) ) {
                $this->_oActiveRecommList = oxNew( 'oxrecommlist' );
                $this->_oActiveRecommList->load( $sOxid );
            }
        }
        return $this->_oActiveRecommList;
    }

    /**
     * Template variable getter. Returns accessoires of article
     *
     * @return object
     */
    public function getAccessoires()
    {
    }

    /**
     * Template variable getter. Returns crosssellings
     *
     * @return object
     */
    public function getCrossSelling()
    {
    }

    /**
     * Template variable getter. Returns similar article list
     *
     * @return object
     */
    public function getSimilarProducts()
    {
    }

    /**
     * Template variable getter. Returns list of customer also bought thies products
     *
     * @return object
     */
    public function getAlsoBoughtTheseProducts()
    {
    }

    /**
     * Return the active article id
     *
     * @return string | bool
     */
    public function getArticleId()
    {
    }

    /**
     * Returns current view title. Default is search for translation of PAGE_TITLE_{view_class_name}
     *
     * @return string
     */
    public function getTitle()
    {
        $sTranslationName = 'PAGE_TITLE_'.strtoupper($this->getConfig()->getActiveView()->getClassName());
        $sTranslated = oxRegistry::getLang()->translateString( $sTranslationName, oxRegistry::getLang()->getBaseLanguage(), false );
        return $sTranslationName == $sTranslated? null : $sTranslated;
    }

    /**
     * Returns active lang suffix
     *
     * @return string
     */
    public function getActiveLangAbbr()
    {
        // Performance
        if ( !$this->getConfig()->getConfigParam( 'bl_perfLoadLanguages' ) ) {
            return;
        }

        if ( !isset($this->_sActiveLangAbbr ) ) {
            $aLanguages = oxRegistry::getLang()->getLanguageArray();
            while ( list( $sKey, $oVal ) = each( $aLanguages ) ) {
                if ( $oVal->selected ) {
                    $this->_sActiveLangAbbr = $oVal->abbr;
                    break;
                }
            }
        }

        return $this->_sActiveLangAbbr;
    }

    /**
     * Sets and caches default parameters for shop object and returns it.
     *
     * @param oxShop $oShop current shop object
     *
     * @return object $oShop current shop object
     */
    public function addGlobalParams( $oShop = null)
    {
        $oViewConf = parent::addGlobalParams( $oShop );

        $this->_setNrOfArtPerPage();

        return $oViewConf;
    }

    /**
     * Template variable getter. Returns additional params for url
     *
     * @return string
     */
    public function getAdditionalParams()
    {
        if ( $this->_sAdditionalParams === null ) {
            // #1018A
            $this->_sAdditionalParams  = parent::getAdditionalParams();
            $this->_sAdditionalParams .= 'cl='.$this->getConfig()->getTopActiveView()->getClassName();

            // #1834M - specialchar search
            $sSearchParamForLink = rawurlencode( oxConfig::getParameter( 'searchparam', true ) );
            if ( isset( $sSearchParamForLink ) ) {
                $this->_sAdditionalParams .= "&amp;searchparam={$sSearchParamForLink}";
            }
            if ( ( $sVar = oxConfig::getParameter( 'searchtag' ) ) ) {
                $this->_sAdditionalParams .= '&amp;searchtag='.rawurlencode( rawurldecode( $sVar ) );
            }
            if ( ( $sVar = oxConfig::getParameter( 'searchcnid' ) ) ) {
                $this->_sAdditionalParams .= '&amp;searchcnid='.rawurlencode( rawurldecode( $sVar ) );
            }
            if ( ( $sVar = oxConfig::getParameter( 'searchvendor' ) ) ) {
                $this->_sAdditionalParams .= '&amp;searchvendor='.rawurlencode( rawurldecode( $sVar ) );
            }
            if ( ( $sVar = oxConfig::getParameter( 'searchmanufacturer' ) ) ) {
                $this->_sAdditionalParams .= '&amp;searchmanufacturer='.rawurlencode( rawurldecode( $sVar ) );
            }
            if ( ( $sVar = oxConfig::getParameter( 'cnid' ) ) ) {
                $this->_sAdditionalParams .= '&amp;cnid='.rawurlencode( rawurldecode( $sVar ) );
            }
            if ( ( $sVar = oxConfig::getParameter( 'mnid' ) ) ) {
                $this->_sAdditionalParams .= '&amp;mnid='.rawurlencode( rawurldecode( $sVar ) );
            }
        }

        return $this->_sAdditionalParams;
    }

    /**
     * Generates URL for page navigation
     *
     * @return string $sUrl String with working page url.
     */
    public function generatePageNavigationUrl()
    {
        return $this->getConfig()->getShopHomeURL().$this->_getRequestParams( false );
    }

    /**
     * Adds page number parameter to url and returns modified url, if page number 0 drops from url
     *
     * @param string $sUrl  url to add page number
     * @param int    $iPage active page number
     * @param int    $iLang language id
     *
     * @return string
     */
    protected function _addPageNrParam( $sUrl, $iPage, $iLang = null )
    {
        if ( $iPage ) {
            if ( ( strpos( $sUrl, 'pgNr=' ) ) ) {
                $sUrl = preg_replace('/pgNr=[0-9]*/', 'pgNr='.$iPage, $sUrl);
            } else {
                $sUrl .= ( ( strpos( $sUrl, '?' ) === false ) ? '?' : '&amp;' ) . 'pgNr='.$iPage;
            }
        } else {
           $sUrl = preg_replace('/pgNr=[0-9]*/', '', $sUrl);
           $sUrl = preg_replace('/\&amp\;\&amp\;/', '&amp;', $sUrl);
           $sUrl = preg_replace('/\?\&amp\;/', '?', $sUrl);
           $sUrl = preg_replace('/\&amp\;$/', '', $sUrl);
        }
        return $sUrl;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return null
     */
    public function getPageNavigation()
    {

    }

    /**
     * Template variable getter. Returns page navigation with default 7 positions
     *
     * @param int $iPositionCount - paging positions count ( 0 - unlimited )
     *
     * @return object
     */
    public function getPageNavigationLimitedTop( $iPositionCount = 7 )
    {
        $this->_oPageNavigation = $this->generatePageNavigation( $iPositionCount );
        return $this->_oPageNavigation;
    }

    /**
     * Template variable getter. Returns page navigation with default 11 positions
     *
     * @param int $iPositionCount - paging positions count ( 0 - unlimited )
     *
     * @return object
     */
    public function getPageNavigationLimitedBottom( $iPositionCount = 11 )
    {
        $this->_oPageNavigation = $this->generatePageNavigation( $iPositionCount );
        return $this->_oPageNavigation;
    }


    /**
     * Generates variables for page navigation
     *
     * @param int $iPositionCount - paging positions count ( 0 - unlimited )
     *
     * @return  stdClass    $pageNavigation Object with page navigation data
     */
    public function generatePageNavigation( $iPositionCount = 0 )
    {
        startProfile('generatePageNavigation');

        $pageNavigation = new stdClass();

        $pageNavigation->NrOfPages = $this->_iCntPages;
        $iActPage = $this->getActPage();
        $pageNavigation->actPage   = $iActPage + 1;
        $sUrl = $this->generatePageNavigationUrl();

        if ( $iPositionCount == 0 || ($iPositionCount >= $pageNavigation->NrOfPages) ) {
             $iStartNo = 2;
             $iFinishNo = $pageNavigation->NrOfPages;
             $bStart = false;
             $bFinish =false;
        } else {
            $iTmpVal = $iPositionCount - 3;
            $iTmpVal2 = floor( ( $iPositionCount - 4 ) / 2 );

            // actual page is at the start
            if ( $pageNavigation->actPage <= $iTmpVal ) {
                $iStartNo = 2;
                $iFinishNo = $iTmpVal + 1;
                $bStart = false;
                $bFinish = true;
            // actual page is at the end
            } elseif ( $pageNavigation->actPage >= $pageNavigation->NrOfPages - $iTmpVal ) {
                $iStartNo = $pageNavigation->NrOfPages - $iTmpVal;
                $iFinishNo = $pageNavigation->NrOfPages - 1;
                $bStart = true;
                $bFinish = false;
            // actual page is in the midle
            } else {
                $iStartNo = $pageNavigation->actPage - $iTmpVal2;
                $iFinishNo = $pageNavigation->actPage + $iTmpVal2;
                $bStart = true;
                $bFinish = true;
            }
        }

        if ( $iActPage > 0) {
            $pageNavigation->previousPage = $this->_addPageNrParam( $sUrl, $iActPage - 1 );
        }

        if ( $iActPage < $pageNavigation->NrOfPages - 1 ) {
            $pageNavigation->nextPage = $this->_addPageNrParam( $sUrl, $iActPage + 1 );
        }

        if ( $pageNavigation->NrOfPages > 1 ) {

            for ( $i=1; $i < $pageNavigation->NrOfPages + 1; $i++ ) {

                if ( $i == 1 || $i == $pageNavigation->NrOfPages || ( $i >= $iStartNo && $i <= $iFinishNo ) ) {
                    $page = new stdClass();
                    $page->url = $this->_addPageNrParam( $sUrl, $i - 1 );
                    $page->selected = ( $i == $pageNavigation->actPage ) ? 1 : 0;
                    $pageNavigation->changePage[$i] = $page;
                }
            }

            // first/last one
            $pageNavigation->firstpage = $this->_addPageNrParam( $sUrl, 0 );
            $pageNavigation->lastpage  = $this->_addPageNrParam( $sUrl, $pageNavigation->NrOfPages - 1 );
        }

        stopProfile('generatePageNavigation');

        return $pageNavigation;
    }

    /**
     * While ordering disables navigation controls if oxConfig::blDisableNavBars
     * is on and executes parent::render()
     *
     * @return null
     */
    public function render()
    {
        foreach ( array_keys( $this->_oaComponents ) as $sComponentName ) {
            $this->_aViewData[$sComponentName] = $this->_oaComponents[$sComponentName]->render();
        }

        parent::render();

        if ( $this->getIsOrderStep() ) {

            // disabling navigation during order ...
            if ( $this->getConfig()->getConfigParam( 'blDisableNavBars' ) ) {
                $this->_iNewsRealStatus = 1;
                $this->setShowNewsletter( 0 );
            }
        }
        return $this->_sThisTemplate;
    }

    /**
     * Returns current view product object (if it is loaded)
     *
     * @return oxarticle
     */
    public function getViewProduct()
    {
        return $this->getProduct();
    }

    /**
     * Sets view product
     *
     * @param oxarticle $oProduct view product object
     *
     * @return null
     */
    public function setViewProduct( $oProduct )
    {
        $this->_oProduct = $oProduct;
    }

    /**
     * Returns view product list
     *
     * @return array
     */
    public function getViewProductList()
    {
        return $this->_aArticleList;
    }

    /**
     * Active page getter
     *
     * @return int
     */
    public function getActPage()
    {
        if ( $this->_iActPage === null ) {
            $this->_iActPage = ( int ) oxConfig::getParameter( 'pgNr' );
            $this->_iActPage = ( $this->_iActPage < 0 ) ? 0 : $this->_iActPage;
        }
        return $this->_iActPage;
    }

    /**
     * Active tag info object getter. Object properties:
     *  - sTag current tag
     *  - link link leading to tag article list
     *
     * @return stdClass
     */
    public function getActTag()
    {
        if ( $this->_oActTag === null ) {
            $this->_oActTag = new stdClass();
            $this->_oActTag->sTag = $sTag = oxConfig::getParameter("searchtag", 1);
            $oSeoEncoderTag = oxRegistry::get("oxSeoEncoderTag");

            $sLink = false;
            if ( oxRegistry::getUtils()->seoIsActive() ) {
                $sLink = $oSeoEncoderTag->getTagUrl( $sTag, oxRegistry::getLang()->getBaseLanguage() );
            }

            $this->_oActTag->link = $sLink ? $sLink : $this->getConfig()->getShopHomeURL().$oSeoEncoderTag->getStdTagUri( $sTag, false );
        }
        return $this->_oActTag;
    }

    /**
     * Returns active vendor set by categories component; if vendor is
     * not set by component - will create vendor object and will try to
     * load by id passed by request
     *
     * @return oxvendor
     */
    public function getActVendor()
    {
        // if active vendor is not set yet - trying to load it from request params
        // this may be usefull when category component was unable to load active vendor
        // and we still need some object to mount navigation info
        if ( $this->_oActVendor === null ) {
            $this->_oActVendor = false;
            $sVendorId = oxConfig::getParameter( 'cnid' );
            $sVendorId = $sVendorId ? str_replace( 'v_', '', $sVendorId ) : $sVendorId;
            $oVendor = oxNew( 'oxvendor' );
            if ( $oVendor->load( $sVendorId ) ) {
                $this->_oActVendor = $oVendor;
            }
        }

        return $this->_oActVendor;
    }

    /**
     * Returns active Manufacturer set by categories component; if Manufacturer is
     * not set by component - will create Manufacturer object and will try to
     * load by id passed by request
     *
     * @return oxmanufacturer
     */
    public function getActManufacturer()
    {
        // if active Manufacturer is not set yet - trying to load it from request params
        // this may be usefull when category component was unable to load active Manufacturer
        // and we still need some object to mount navigation info
        if ( $this->_oActManufacturer === null ) {

            $this->_oActManufacturer = false;
            $sManufacturerId = oxConfig::getParameter( 'mnid' );
            $oManufacturer = oxNew( 'oxmanufacturer' );
            if ( $oManufacturer->load( $sManufacturerId ) ) {
                $this->_oActManufacturer = $oManufacturer;
            }
        }

        return $this->_oActManufacturer;
    }

    /**
     * Active vendor setter
     *
     * @param oxVendor $oVendor active vendor
     *
     * @return null
     */
    public function setActVendor( $oVendor )
    {
        $this->_oActVendor = $oVendor;
    }

    /**
     * Active Manufacturer setter
     *
     * @param oxManufacturer $oManufacturer active Manufacturer
     *
     * @return null
     */
    public function setActManufacturer( $oManufacturer )
    {
        $this->_oActManufacturer = $oManufacturer;
    }

    /**
     * Returns fake object which is used to mount navigation info
     *
     * @return stdClass
     */
    public function getActSearch()
    {
        if ( $this->_oActSearch === null ) {
            $this->_oActSearch = new stdClass();
            $sUrl = $this->getConfig()->getShopHomeURL();
            $this->_oActSearch->link = "{$sUrl}cl=search";
        }
        return $this->_oActSearch;
    }

    /**
     * Returns category tree (if it is loaded)
     *
     * @return oxCategoryList
     */
    public function getCategoryTree()
    {
        return $this->_oCategoryTree;
    }

    /**
     * Category list setter
     *
     * @param oxCategoryList $oCatTree category tree
     *
     * @return null
     */
    public function setCategoryTree( $oCatTree )
    {
        $this->_oCategoryTree = $oCatTree;
    }

    /**
     * Returns Manufacturer tree (if it is loaded0
     *
     * @return oxManufacturerlist
     */
    public function getManufacturerTree()
    {
        return $this->_oManufacturerTree;
    }

    /**
     * Manufacturer tree setter
     *
     * @param oxManufacturerlist $oManufacturerTree Manufacturer tree
     *
     * @return null
     */
    public function setManufacturerTree( $oManufacturerTree )
    {
        $this->_oManufacturerTree = $oManufacturerTree;
    }

    /**
     * Returns additional URL parameters which must be added to list products urls
     *
     * @return string
     */
    public function getAddUrlParams()
    {
    }

    /**
     * Template variable getter. Returns Top 5 article list.
     * Parameter oxubase::$_blTop5Action must be set to true.
     *
     * @param integer $iCount - product count in list
     *
     * @return array
     */
    public function getTop5ArticleList( $iCount = null )
    {
        if ( $this->_blTop5Action ) {
            if ( $this->_aTop5ArticleList === null ) {
                $this->_aTop5ArticleList = false;
                $myConfig = $this->getConfig();
                if ( $myConfig->getConfigParam( 'bl_perfLoadAktion' ) ) {
                    // top 5 articles
                    $oArtList = oxNew( 'oxarticlelist' );
                    $oArtList->loadTop5Articles( $iCount );
                    if ( $oArtList->count() ) {
                        $this->_aTop5ArticleList = $oArtList;
                    }
                }
            }
        }
        return $this->_aTop5ArticleList;
    }

    /**
     * Template variable getter. Returns bargain article list
     * Parameter oxubase::$_blBargainAction must be set to true.
     *
     * @return array
     */
    public function getBargainArticleList()
    {
        if ( $this->_blBargainAction ) {
            if ( $this->_aBargainArticleList === null ) {
                $this->_aBargainArticleList = array();
                if ( $this->getConfig()->getConfigParam( 'bl_perfLoadAktion' ) ) {
                    $oArtList = oxNew( 'oxarticlelist' );
                    $oArtList->loadActionArticles( 'OXBARGAIN' );
                    if ( $oArtList->count() ) {
                        $this->_aBargainArticleList = $oArtList;
                    }
                }
            }
        }
        return $this->_aBargainArticleList;
    }

    /**
     * Template variable getter. Returns if order price is lower than
     * minimum order price setup (config param "iMinOrderPrice")
     *
     * @deprecated in v4.8/5.1 on 2013-10-14; use oxBasket method
     *
     * @return bool
     */
    public function isLowOrderPrice()
    {
        if ( $this->_blLowOrderPrice === null && ( $oBasket = $this->getSession()->getBasket() ) ) {
            $this->_blLowOrderPrice = $oBasket->isBelowMinOrderPrice();
        }

        return $this->_blLowOrderPrice;
    }

    /**
     * Template variable getter. Returns formatted min order price value
     *
     * @deprecated in v4.8/5.1 on 2013-10-14; use oxBasket method
     *
     * @return string
     */
    public function getMinOrderPrice()
    {
        if ( $this->_sMinOrderPrice === null && $this->isLowOrderPrice() ) {
            $dMinOrderPrice = oxPrice::getPriceInActCurrency( $this->getConfig()->getConfigParam( 'iMinOrderPrice' ) );
            $this->_sMinOrderPrice = oxRegistry::getLang()->formatCurrency( $dMinOrderPrice );
        }
        return $this->_sMinOrderPrice;
    }

    /**
     * Template variable getter. Returns if newsletter is realy active (for user.tpl)
     *
     * @return integer
     */
    public function getNewsRealStatus()
    {
        return $this->_iNewsRealStatus;
    }

    /**
     * Checks if current request parameters does not block SEO redirection process
     *
     * @return bool
     */
    protected function _canRedirect()
    {
        foreach ( $this->_aBlockRedirectParams as $sParam ) {
            if ( oxConfig::getParameter( $sParam ) !== null ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Empty active product getter
     *
     * @return null
     */
    public function getProduct()
    {
    }

    /**
     * Template variable getter. Returns Manufacturerlist for search
     *
     * @return array
     */
    public function getManufacturerlist()
    {
        return $this->_aManufacturerlist;
    }

    /**
     * Sets Manufacturerlist for search
     *
     * @param array $aList manufacturer list
     *
     * @return null
     */
    public function setManufacturerlist( $aList )
    {
        $this->_aManufacturerlist = $aList;
    }

    /**
     * Sets root vendor
     *
     * @param object $oVendor vendor object
     *
     * @return null
     */
    public function setRootVendor( $oVendor )
    {
        $this->_oRootVendor = $oVendor;
    }

    /**
     * Template variable getter. Returns root vendor
     *
     * @return object
     */
    public function getRootVendor()
    {
        return $this->_oRootVendor;
    }

    /**
     * Sets root Manufacturer
     *
     * @param object $oManufacturer manufacturer object
     *
     * @return null
     */
    public function setRootManufacturer( $oManufacturer )
    {
        $this->_oRootManufacturer = $oManufacturer;
    }

    /**
     * Template variable getter. Returns root Manufacturer
     *
     * @return object
     */
    public function getRootManufacturer()
    {
        return $this->_oRootManufacturer;
    }

    /**
     * Template variable getter. Returns vendor id
     *
     * @return string
     */
    public function getVendorId()
    {
        if ( $this->_sVendorId === null ) {
            $this->_sVendorId = false;
            if ( ( $oVendor = $this->getActVendor() ) ) {
                $this->_sVendorId = $oVendor->getId();
            }
        }
        return $this->_sVendorId;
    }

    /**
     * Template variable getter. Returns Manufacturer id
     *
     * @return string
     */
    public function getManufacturerId()
    {
        if ( $this->_sManufacturerId === null ) {
            $this->_sManufacturerId = false;
            if ( ( $oManufacturer = $this->getActManufacturer() ) ) {
                $this->_sManufacturerId = $oManufacturer->getId();
            }
        }
        return $this->_sManufacturerId;
    }

    /**
     * Template variable getter. Returns category tree for search
     *
     *  @deprecated in v4.7.5/5.0,5 on 2013-03-27; not used in code, not needed
     *
     * @return array
     */
    public function getSearchCatTree()
    {
        return $this->_aSearchCatTree;
    }

    /**
     * Sets category tree for search
     *
     * @param array $aTree category tree
     *
     * @deprecated in v4.7.5/5.0,5 on 2013-03-27; not used in code, not needed
     *
     * @return null
     */
    public function setSearchCatTree( $aTree )
    {
        $this->_aSearchCatTree = $aTree;
    }

    /**
     * Template variable getter. Returns more category
     *
     * @return object
     */
    public function getCatMoreUrl()
    {
        return $this->getConfig()->getShopHomeURL().'cnid=oxmore';
    }

    /**
     * Template variable getter. Returns category path
     *
     * @return string
     */
    public function getCatTreePath()
    {
        return $this->_sCatTreePath;
    }

    /**
     * Loads and returns oxcontent object requested by its ident
     *
     * @param string $sIdent content identifier
     *
     * @return oxcontent
     */
    public function getContentByIdent( $sIdent )
    {
        if ( !isset( $this->_aContents[$sIdent] ) ) {
            $this->_aContents[$sIdent] = oxNew( 'oxcontent' );
            $this->_aContents[$sIdent]->loadByIdent( $sIdent );
        }
        return $this->_aContents[$sIdent];
    }

    /**
     * Default content category getter, returns FALSE by default
     *
     * @return bool
     */
    public function getContentCategory()
    {
        return false;
    }

    /**
     * Returns array of fields which must be filled during registration
     *
     * @return array | bool
     */
    public function getMustFillFields()
    {
        if ( $this->_aMustFillFields === null ) {
            $this->_aMustFillFields = false;

            // passing must-be-filled-fields info
            $aMustFillFields = $this->getConfig()->getConfigParam( 'aMustFillFields' );
            if ( is_array( $aMustFillFields ) ) {
                $this->_aMustFillFields = array_flip( $aMustFillFields );
            }
        }
        return $this->_aMustFillFields;
    }

    /**
     * Returns if field is required.
     *
     * @param string $sField required field to check
     *
     * @return array | bool
     */
    public function isFieldRequired( $sField )
    {
        if ( $aMustFillFields = $this->getMustFillFields() ) {
            if ( isset( $aMustFillFields[$sField] ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Form id getter. This id used to prevent double guestbook, review entry submit
     *
     * @return string
     */
    public function getFormId()
    {
        if ( $this->_sFormId === null ) {
            $this->_sFormId = oxUtilsObject::getInstance()->generateUId();
            oxSession::setVar( 'sessionuformid', $this->_sFormId );
        }

        return $this->_sFormId;
    }

    /**
     * Checks if session session form id matches with form id
     *
     * @return bool
     */
    public function canAcceptFormData()
    {
        if ( $this->_blCanAcceptFormData === null ) {
            $this->_blCanAcceptFormData = false;

            $sFormId = oxConfig::getParameter( "uformid" );
            $sSessionFormId = oxRegistry::getSession()->getVariable( "sessionuformid" );

            // testing if form and session ids matches
            if ( $sFormId && $sFormId === $sSessionFormId ) {
                $this->_blCanAcceptFormData = true;
            }

            // regenerating form data
            $this->getFormId();
        }
        return $this->_blCanAcceptFormData;
    }

    /**
     * return last finished promotion list
     *
     * @return oxActionList
     */
    public function getPromoFinishedList()
    {
        if (isset($this->_oPromoFinishedList)) {
            return $this->_oPromoFinishedList;
        }
        $this->_oPromoFinishedList = oxNew( 'oxActionList' );
        $this->_oPromoFinishedList->loadFinishedByCount(2);
        return $this->_oPromoFinishedList;
    }

    /**
     * return current promotion list
     *
     * @return oxActionList
     */
    public function getPromoCurrentList()
    {
        if (isset($this->_oPromoCurrentList)) {
            return $this->_oPromoCurrentList;
        }
        $this->_oPromoCurrentList = oxNew( 'oxActionList' );
        $this->_oPromoCurrentList->loadCurrent();
        return $this->_oPromoCurrentList;
    }

    /**
     * return future promotion list
     *
     * @return oxActionList
     */
    public function getPromoFutureList()
    {
        if (isset($this->_oPromoFutureList)) {
            return $this->_oPromoFutureList;
        }
        $this->_oPromoFutureList = oxNew( 'oxActionList' );
        $this->_oPromoFutureList->loadFutureByCount(2);
        return $this->_oPromoFutureList;
    }

    /**
     * should promotions list be shown?
     *
     * @return bool
     */
    public function getShowPromotionList()
    {
        if (isset($this->_blShowPromotions)) {
            return $this->_blShowPromotions;
        }
        $this->_blShowPromotions = false;
        if (oxNew('oxActionList')->areAnyActivePromotions()) {
            $this->_blShowPromotions = ( count( $this->getPromoFinishedList() ) + count( $this->getPromoCurrentList() ) + count( $this->getPromoFutureList() ) ) > 0;
        }
        return $this->_blShowPromotions;
    }

    /**
     * Checks if private sales is on
     *
     * @return bool
     */
    public function isEnabledPrivateSales()
    {
        if ( $this->_blEnabledPrivateSales === null ) {
            $this->_blEnabledPrivateSales = (bool) $this->getConfig()->getConfigParam( 'blPsLoginEnabled' );
            if ( $this->_blEnabledPrivateSales && ( $blCanPreview = oxRegistry::getUtils()->canPreview() ) !== null ) {
                $this->_blEnabledPrivateSales = !$blCanPreview;
            }
        }
        return $this->_blEnabledPrivateSales;
    }

    /**
     * Returns input field validation error array (if available)
     *
     * @return array
     */
    public function getFieldValidationErrors()
    {
        return oxRegistry::get("oxInputValidator")->getFieldValidationErrors();
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return null
     */
    public function getBreadCrumb()
    {
        return null;
    }

    /**
     * Sets if active root category was changed
     *
     * @param bool $blRootCatChanged root category changed
     *
     * @return null
     */
    public function setRootCatChanged( $blRootCatChanged )
    {
        $this->_blRootCatChanged = $blRootCatChanged;
    }

    /**
     * Template variable getter. Returns true if active root category was changed
     *
     * @return bool
     */
    public function isRootCatChanged()
    {
        return $this->_blRootCatChanged;
    }

    /**
     * Template variable getter. Returns user address
     *
     * @return array
     */
    public function getInvoiceAddress()
    {
        if ( $this->_aInvoiceAddress == null ) {
            $aAddress = oxConfig::getParameter( 'invadr');
            if ( $aAddress ) {
                $this->_aInvoiceAddress = $aAddress;
            }
        }
        return $this->_aInvoiceAddress;
    }

    /**
     * Template variable getter. Returns user delivery address
     *
     * @return array
     */
    public function getDeliveryAddress()
    {
        if ( $this->_aDeliveryAddress == null ) {
            $oConfig = $this->getConfig();
            //do not show deladr if address was reloaded
            if ( !$oConfig->getRequestParameter( 'reloadaddress' ) ) {
                $this->_aDeliveryAddress = $oConfig->getRequestParameter( 'deladr');
            }
        }
        return $this->_aDeliveryAddress;
    }

    /**
     * Template variable getter. Returns user delivery address
     *
     * @param array $aDeliveryAddress delivery address
     *
     * @return null
     */
    public function setDeliveryAddress($aDeliveryAddress)
    {
        $this->_aDeliveryAddress = $aDeliveryAddress;
    }

    /**
     * Template variable setter. Sets user address
     *
     * @param array $aAddress user address
     *
     * @return null
     */
    public function setInvoiceAddress( $aAddress )
    {
        $this->_aInvoiceAddress = $aAddress;
    }

    /**
     * Template variable getter. Returns logged in user name
     *
     * @return string
     */
    public function getActiveUsername()
    {
        if ( $this->_sActiveUsername == null ) {
            $this->_sActiveUsername = false;
            $sUsername = oxConfig::getParameter( 'lgn_usr' );
            if ( $sUsername ) {
                $this->_sActiveUsername = $sUsername;
            } elseif ( $oUser = $this->getUser() ) {
                $this->_sActiveUsername = $oUser->oxuser__oxusername->value;
            }
        }
        return $this->_sActiveUsername;
    }

    /**
     * Template variable getter. Returns user id from wishlist
     *
     * @return string
     */
    public function getWishlistUserId()
    {
        return oxConfig::getParameter( 'wishid' );
    }

    /**
     * Template variable getter. Returns searched category id
     *
     * @return string
     */
    public function getSearchCatId()
    {
    }

    /**
     * Template variable getter. Returns searched vendor id
     *
     * @return string
     */
    public function getSearchVendor()
    {
    }

    /**
     * Template variable getter. Returns searched Manufacturer id
     *
     * @return string
     */
    public function getSearchManufacturer()
    {
    }

     /**
     * Template variable getter. Returns last seen products
     *
     * @return array
     */
    public function getLastProducts()
    {

    }

    /**
     * Returns added basket item notification message type
     *
     * @return int
     */
    public function getNewBasketItemMsgType()
    {
        return (int) $this->getConfig()->getConfigParam( "iNewBasketItemMessage" );
    }

    /**
     * Checks if feature is enabled
     *
     * @param string $sName feature name
     *
     * @return bool
     */
    public function isActive( $sName )
    {
        return $this->getConfig()->getConfigParam( "bl".$sName."Enabled" );
    }

    /**
     * Returns TRUE if facebook widgets are on
     *
     * @return boolean
     */
    public function isFbWidgetVisible()
    {
        if ( $this->_blFbWidgetsOn === null ) {
            $oUtils = oxRegistry::get("oxUtilsServer");

            // reading ..
            $this->_blFbWidgetsOn = (bool) $oUtils->getOxCookie( "fbwidgetson" );
        }
        return $this->_blFbWidgetsOn;
    }

    /**
     * Checks if downloadable files are turned on
     *
     * @return bool
     */
    public function isEnabledDownloadableFiles()
    {
        return (bool) $this->getConfig()->getConfigParam( "blEnableDownloads" );
    }

    /**
     * Returns true if "Remember me" are ON
     *
     * @return boolean
     */
    public function showRememberMe()
    {
        return (bool) $this->getConfig()->getConfigParam('blShowRememberMe');
    }

    /**
     * Returns true if articles shown in shop with VAT.
     * Checks users VAT and options.
     *
     * @return boolean
     */
    public function isVatIncluded()
    {
        $blResult = true;
        $oUser = $this->getUser();
        $oConfig = $this->getConfig();

        if ( $oConfig->getConfigParam( 'blShowNetPrice' ) || $oConfig->getConfigParam( 'bl_perfCalcVatOnlyForBasketOrder' ) ) {
            $blResult = false;
        } elseif ( $oUser && $oUser->isPriceViewModeNetto() ) {
            $blResult = false;
        }

        return $blResult;
    }

    /**
     * Returns true if price calculation is activated
     *
     * @return boolean
     */
    public function isPriceCalculated()
    {
        return (bool) $this->getConfig()->getConfigParam( 'bl_perfLoadPrice' );
    }

    /**
     * Returns true if tags are ON
     *
     * @return boolean
     */
    public function showTags()
    {
         return (bool) $this->_blShowTagCloud && $this->getConfig()->getConfigParam( "blShowTags" );
    }

    /**
     * Template variable getter. Returns user name of searched wishlist
     *
     * @return string
     */
    public function getWishlistName()
    {
        if ( $this->getUser() ) {
            $sUserId = oxConfig::getParameter( 'wishid') ? oxConfig::getParameter( 'wishid' ): oxRegistry::getSession()->getVariable( 'wishid');
            if ( $sUserId ) {
                $oWishUser = oxNew( 'oxuser' );
                if ( $oWishUser->load( $sUserId ) ) {
                    return $oWishUser;
                }
            }
        }
        return false;
    }

    /**
     * Get widget link for Ajax calls
     *
     * @return string
     */
    public function getWidgetLink()
    {
        return oxRegistry::getConfig()->getWidgetUrl();
    }
}
