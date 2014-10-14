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
 * Article list manager.
 * Collects list of article according to collection rules (categories, etc.).
 *
 * @package model
 */
class oxArticleList extends oxList
{
    /**
     * @var string SQL addon for sorting
     */
    protected $_sCustomSorting;

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxarticle';

    /**
     * Set to true if Select Lists should be laoded
     *
     * @var bool
     */
    protected $_blLoadSelectLists = false;

    /**
     * Set Custom Sorting, simply an order by....
     *
     * @param string $sSorting Custom sorting
     *
     * @return null
     */
    public function setCustomSorting( $sSorting )
    {
        $this->_sCustomSorting = $sSorting;
    }

    /**
     * Call enableSelectLists() for loading select lists in lst articles
     *
     * @return null
     */
    public function enableSelectLists()
    {
        $this->_blLoadSelectLists = true;
    }

    /**
     * Loads selectlists for each artile in list if they exists
     * Returns true on success.
     *
     * @param string $sSelect SQL select string
     *
     * @return bool
     */
    public function selectString( $sSelect )
    {
        startProfile("loadinglists");
        $oRes = parent::selectString( $sSelect );
        stopProfile("loadinglists");

        return $oRes;
    }

    /**
     * Get history article id's from session or cookie.
     *
     * @return array
     */
    public function getHistoryArticles()
    {
        $aResult = array();

        if ($aArticlesIds = $this->getSession()->getVariable('aHistoryArticles')) {
            $aResult = $aArticlesIds;
        } elseif ( $sArticlesIds = oxRegistry::get("oxUtilsServer")->getOxCookie('aHistoryArticles')) {
            $aResult = explode('|', $sArticlesIds);
        }

        return $aResult;
    }

    /**
     * Set history article id's to session or cookie
     *
     * @param array $aArticlesIds array history article ids
     *
     * @return null
     */
    public function setHistoryArticles($aArticlesIds)
    {
        if ($this->getSession()->getId()) {
            $this->getSession()->setVariable('aHistoryArticles', $aArticlesIds);
            // clean cookie, if session started
            oxRegistry::get("oxUtilsServer")->setOxCookie('aHistoryArticles', '');
        } else {
            oxRegistry::get("oxUtilsServer")->setOxCookie('aHistoryArticles', implode('|', $aArticlesIds));
        }
    }

    /**
     * Loads up to 4 history (normally recently seen) articles from session, and adds $sArtId to history.
     * Returns article id array.
     *
     * @param string $sArtId Article ID
     * @param int    $iCnt   product count
     *
     * @return array
     */
    public function loadHistoryArticles( $sArtId, $iCnt = 4 )
    {
        $aHistoryArticles = $this->getHistoryArticles();
        $aHistoryArticles[] = $sArtId;

        // removing dublicates
        $aHistoryArticles = array_unique( $aHistoryArticles );
        if ( count( $aHistoryArticles ) > ( $iCnt + 1 ) ) {
            array_shift( $aHistoryArticles );
        }

        $this->setHistoryArticles( $aHistoryArticles );

        //remove current article and return array
        //asignment =, not ==
        if ( ( $iCurrentArt = array_search( $sArtId, $aHistoryArticles ) ) !== false ) {
            unset( $aHistoryArticles[$iCurrentArt] );
        }

        $aHistoryArticles = array_values( $aHistoryArticles );
        $this->loadIds( $aHistoryArticles );
        $this->sortByIds( $aHistoryArticles );
    }

    /**
     * sort this list by given order.
     *
     * @param array $aIds ordered ids
     *
     * @return null
     */
    public function sortByIds( $aIds )
    {
        $this->_aOrderMap = array_flip( $aIds );
        uksort($this->_aArray, array($this, '_sortByOrderMapCallback'));
    }

    /**
     * callback function only used from sortByIds
     *
     * @param string $key1 1st key
     * @param string $key2 2nd key
     *
     * @see oxArticleList::sortByIds
     *
     * @return int
     */
    protected function _sortByOrderMapCallback($key1, $key2)
    {
        if (isset($this->_aOrderMap[$key1])) {
            if (isset($this->_aOrderMap[$key2])) {
                $iDiff = $this->_aOrderMap[$key2] - $this->_aOrderMap[$key1];
                if ($iDiff > 0) {
                    return -1;
                } elseif ($iDiff < 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                // first is here, but 2nd is not - 1st gets more priority
                return -1;
            }
        } elseif (isset($this->_aOrderMap[$key2])) {
            // first is not here, but 2nd is - 2nd gets more priority
            return 1;
        } else {
            // both unset, equal
            return 0;
        }
    }

    /**
     * Loads newest shops articles from DB.
     *
     * @param int $iLimit Select limit
     *
     * @return null;
     */
    public function loadNewestArticles( $iLimit = null )
    {
        //has module?
        $myConfig = $this->getConfig();

        if ( !$myConfig->getConfigParam( 'bl_perfLoadPriceForAddList' ) ) {
            $this->getBaseObject()->disablePriceLoad();
        }

        $this->_aArray = array();
        switch( $myConfig->getConfigParam( 'iNewestArticlesMode' ) ) {
            case 0:
                // switched off, do nothing
                break;
            case 1:
                // manually entered
                $this->loadActionArticles( 'oxnewest', $iLimit );
                break;
            case 2:
                $sArticleTable = getViewName('oxarticles');
                if ( $myConfig->getConfigParam( 'blNewArtByInsert' ) ) {
                    $sType = 'oxinsert';
                } else {
                    $sType = 'oxtimestamp';
                }
                $sSelect  = "select * from $sArticleTable ";
                $sSelect .= "where oxparentid = '' and ".$this->getBaseObject()->getSqlActiveSnippet()." and oxissearch = 1 order by $sType desc ";
                if (!($iLimit = (int) $iLimit)) {
                    $iLimit = $myConfig->getConfigParam( 'iNrofNewcomerArticles' );
                }
                $sSelect .= "limit " . $iLimit;

                $this->selectString($sSelect);
                break;
        }

    }

    /**
     * Load top 5 articles
     *
     * @param int $iLimit Select limit
     *
     * @return null
     */
    public function loadTop5Articles( $iLimit = null )
    {
        //has module?
        $myConfig = $this->getConfig();

        if ( !$myConfig->getConfigParam( 'bl_perfLoadPriceForAddList' ) ) {
            $this->getBaseObject()->disablePriceLoad();
        }

        switch( $myConfig->getConfigParam( 'iTop5Mode' ) ) {
            case 0:
                // switched off, do nothing
                break;
            case 1:
                // manually entered
                $this->loadActionArticles( 'oxtop5', $iLimit );
                break;
            case 2:
                $sArticleTable = getViewName('oxarticles');

                //by default limit 5
                $sLimit = ( $iLimit > 0 ) ? "limit " . $iLimit : 'limit 5';

                $sSelect  = "select * from $sArticleTable ";
                $sSelect .= "where ".$this->getBaseObject()->getSqlActiveSnippet()." and $sArticleTable.oxissearch = 1 ";
                $sSelect .= "and $sArticleTable.oxparentid = '' and $sArticleTable.oxsoldamount>0 ";
                $sSelect .= "order by $sArticleTable.oxsoldamount desc $sLimit";

                $this->selectString($sSelect);
                break;
        }
    }

     /**
     * Loads shop AktionArticles.
     *
     * @param string $sActionID Action id
     * @param int    $iLimit    Select limit
     *
     * @return null
     */
    public function loadActionArticles( $sActionID, $iLimit = null )
    {
        // Performance
        if ( !trim( $sActionID) ) {
            return;
        }

        $sShopID        = $this->getConfig()->getShopId();
        $sActionID      = oxDb::getDb()->quote(strtolower( $sActionID));

        //echo $sSelect;
        $oBaseObject    = $this->getBaseObject();
        $sArticleTable  = $oBaseObject->getViewName();
        $sArticleFields = $oBaseObject->getSelectFields();

        $oBase = oxNew("oxactions");
        $sActiveSql = $oBase->getSqlActiveSnippet();
        $sViewName = $oBase->getViewName();

        $sLimit = ( $iLimit > 0 ) ? "limit " . $iLimit : '';

        $sSelect = "select $sArticleFields from oxactions2article
                              left join $sArticleTable on $sArticleTable.oxid = oxactions2article.oxartid
                              left join $sViewName on $sViewName.oxid = oxactions2article.oxactionid
                              where oxactions2article.oxshopid = '$sShopID' and oxactions2article.oxactionid = $sActionID and $sActiveSql
                              and $sArticleTable.oxid is not null and " .$oBaseObject->getSqlActiveSnippet(). "
                              order by oxactions2article.oxsort $sLimit";

        $this->selectString( $sSelect );
    }


    /**
     * Loads shop AktionArticles.
     *
     * @param string $sActionID Action id
     * @param int    $iLimit    Select limit
     *
     * @deprecated since v5.0.1 (2012-11-15); use oxArticleList::loadActionArticles()
     *
     * @return null
     */
    public function loadAktionArticles( $sActionID, $iLimit = null )
    {
        $this->loadActionArticles( $sActionID, $iLimit = null );

    }

    /**
     * Loads article cross selling
     *
     * @param string $sArticleId Article id
     *
     * @return null
     */
    public function loadArticleCrossSell( $sArticleId )
    {
        $myConfig = $this->getConfig();

        // Performance
        if ( !$myConfig->getConfigParam( 'bl_perfLoadCrossselling' ) ) {
            return null;
        }

        $oBaseObject   = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();

        $sArticleId = oxDb::getDb()->quote($sArticleId);

        $sSelect  = "SELECT $sArticleTable.*
                        FROM $sArticleTable INNER JOIN oxobject2article ON oxobject2article.oxobjectid=$sArticleTable.oxid ";
        $sSelect .= "WHERE oxobject2article.oxarticlenid = $sArticleId ";
        $sSelect .= " AND " .$oBaseObject->getSqlActiveSnippet();

        // #525 bidirectional cross selling
        if ( $myConfig->getConfigParam( 'blBidirectCross' ) ) {
            $sSelect = "
                (
                    SELECT $sArticleTable.* FROM $sArticleTable
                        INNER JOIN oxobject2article AS O2A1 on
                            ( O2A1.oxobjectid = $sArticleTable.oxid AND O2A1.oxarticlenid = $sArticleId )
                    WHERE 1
                    AND " . $oBaseObject->getSqlActiveSnippet() . "
                    AND ($sArticleTable.oxid != $sArticleId)
                )
                UNION
                (
                    SELECT $sArticleTable.* FROM $sArticleTable
                        INNER JOIN oxobject2article AS O2A2 ON
                            ( O2A2.oxarticlenid = $sArticleTable.oxid AND O2A2.oxobjectid = $sArticleId )
                    WHERE 1
                    AND " . $oBaseObject->getSqlActiveSnippet() . "
                    AND ($sArticleTable.oxid != $sArticleId)
                )";
        }

        $this->setSqlLimit( 0, $myConfig->getConfigParam( 'iNrofCrossellArticles' ));
        $this->selectString( $sSelect );
    }

    /**
     * Loads article accessories
     *
     * @param string $sArticleId Article id
     *
     * @return null
     */
    public function loadArticleAccessoires( $sArticleId )
    {
        $myConfig = $this->getConfig();

        // Performance
        if ( !$myConfig->getConfigParam( 'bl_perfLoadAccessoires' ) ) {
            return;
        }

        $sArticleId = oxDb::getDb()->quote($sArticleId);

        $oBaseObject   = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();

        $sSelect  = "select $sArticleTable.* from oxaccessoire2article left join $sArticleTable on oxaccessoire2article.oxobjectid=$sArticleTable.oxid ";
        $sSelect .= "where oxaccessoire2article.oxarticlenid = $sArticleId ";
        $sSelect .= " and $sArticleTable.oxid is not null and " .$oBaseObject->getSqlActiveSnippet();
        //sorting articles
        $sSelect .= " order by oxaccessoire2article.oxsort";

        $this->selectString( $sSelect );
    }

    /**
     * Loads only ID's and create Fake objects for cmp_categories.
     *
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return null
     */
    public function loadCategoryIds( $sCatId, $aSessionFilter )
    {
        $sArticleTable = $this->getBaseObject()->getViewName();
        $sSelect = $this->_getCategorySelect( $sArticleTable.'.oxid as oxid', $sCatId, $aSessionFilter );

        $this->_createIdListFromSql( $sSelect );
    }

    /**
     * Loads articles for the give Category
     *
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     * @param int    $iLimit         Limit
     *
     * @return integer total Count of Articles in this Category
     */
    public function loadCategoryArticles( $sCatId, $aSessionFilter, $iLimit = null )
    {
        $sArticleFields = $this->getBaseObject()->getSelectFields();

        $sSelect = $this->_getCategorySelect( $sArticleFields, $sCatId, $aSessionFilter );

        // calc count - we can not use count($this) here as we might have paging enabled
        // #1970C - if any filters are used, we can not use cached category article count
        $iArticleCount = null;
        if ( $aSessionFilter) {
            $iArticleCount = oxDb::getDb()->getOne( $this->_getCategoryCountSelect( $sCatId, $aSessionFilter ) );
        }

        if ($iLimit = (int) $iLimit) {
            $sSelect .= " LIMIT $iLimit";
        }

        $this->selectString( $sSelect );

        if ( $iArticleCount !== null ) {
            return $iArticleCount;
        }

        // this select is FAST so no need to hazzle here with getNrOfArticles()
        return oxRegistry::get("oxUtilsCount")->getCatArticleCount( $sCatId );
    }

    /**
     * Loads articles for the recommlist
     *
     * @param string $sRecommId       Recommlist ID
     * @param string $sArticlesFilter Additional filter for recommlist's items
     *
     * @return integer total Count of Articles in this Category
     */
    public function loadRecommArticles( $sRecommId, $sArticlesFilter = null )
    {
        $sSelect = $this->_getArticleSelect( $sRecommId, $sArticlesFilter);
        $this->selectString( $sSelect );
    }

    /**
     * Loads only ID's and create Fake objects.
     *
     * @param string $sRecommId       Recommlist ID
     * @param string $sArticlesFilter Additional filter for recommlist's items
     *
     * @return null
     */
    public function loadRecommArticleIds( $sRecommId, $sArticlesFilter )
    {
        $sSelect = $this->_getArticleSelect( $sRecommId, $sArticlesFilter );

        $sArtView = getViewName( 'oxarticles' );
        $sPartial = substr( $sSelect, strpos( $sSelect, ' from ' ) );
        $sSelect  = "select distinct $sArtView.oxid $sPartial ";

        $this->_createIdListFromSql( $sSelect );
    }

    /**
     * Returns the appropriate SQL select
     *
     * @param string $sRecommId       Recommlist ID
     * @param string $sArticlesFilter Additional filter for recommlist's items
     *
     * @return string
     */
    protected function _getArticleSelect( $sRecommId, $sArticlesFilter = null )
    {
        $sRecommId = oxDb::getDb()->quote($sRecommId);

        $sArtView = getViewName( 'oxarticles' );
        $sSelect  = "select distinct $sArtView.*, oxobject2list.oxdesc from oxobject2list ";
        $sSelect .= "left join $sArtView on oxobject2list.oxobjectid = $sArtView.oxid ";
        $sSelect .= "where (oxobject2list.oxlistid = $sRecommId) ".$sArticlesFilter;

        return $sSelect;
    }

    /**
     * Loads only ID's and create Fake objects for cmp_categories.
     *
     * @param string $sSearchStr          Search string
     * @param string $sSearchCat          Search within category
     * @param string $sSearchVendor       Search within vendor
     * @param string $sSearchManufacturer Search within manufacturer
     *
     * @return null;
     */
    public function loadSearchIds( $sSearchStr = '', $sSearchCat = '', $sSearchVendor = '', $sSearchManufacturer = '' )
    {
        $oDb = oxDb::getDb();
        $sSearchCat    = $sSearchCat?$sSearchCat:null;
        $sSearchVendor = $sSearchVendor?$sSearchVendor:null;
        $sSearchManufacturer = $sSearchManufacturer?$sSearchManufacturer:null;

        $sWhere = null;

        if ( $sSearchStr ) {
            $sWhere = $this->_getSearchSelect( $sSearchStr );
        }

        $sArticleTable = getViewName('oxarticles');

        // longdesc field now is kept on different table
        $sDescJoin  = '';
        if ( is_array( $aSearchCols = $this->getConfig()->getConfigParam( 'aSearchCols' ) ) ) {
            if ( in_array( 'oxlongdesc', $aSearchCols ) || in_array( 'oxtags', $aSearchCols ) ) {
                $sDescView  = getViewName( 'oxartextends' );
                $sDescJoin  = " LEFT JOIN $sDescView ON {$sDescView}.oxid={$sArticleTable}.oxid ";
            }
        }

        // load the articles
        $sSelect  =  "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable $sDescJoin where ";

        // must be additional conditions in select if searching in category
        if ( $sSearchCat ) {
            $sO2CView = getViewName('oxobject2category');
            $sSelect  = "select $sArticleTable.oxid from $sO2CView as oxobject2category, $sArticleTable $sDescJoin ";
            $sSelect .= "where oxobject2category.oxcatnid=".$oDb->quote( $sSearchCat )." and oxobject2category.oxobjectid=$sArticleTable.oxid and ";
        }
        $sSelect .= $this->getBaseObject()->getSqlActiveSnippet();
        $sSelect .= " and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1 ";

        // #671
        if ( $sSearchVendor ) {
            $sSelect .= " and $sArticleTable.oxvendorid = ".$oDb->quote( $sSearchVendor )." ";
        }

        if ( $sSearchManufacturer ) {
            $sSelect .= " and $sArticleTable.oxmanufacturerid = ".$oDb->quote( $sSearchManufacturer )." ";
        }
        $sSelect .= $sWhere;

        if ($this->_sCustomSorting) {
            $sSelect .= " order by {$this->_sCustomSorting} ";
        }

        $this->_createIdListFromSql($sSelect);
    }

    /**
     * Loads Id list of appropriate price products
     *
     * @param float $dPriceFrom Starting price
     * @param float $dPriceTo   Max price
     *
     * @return null;
     */
    public function loadPriceIds( $dPriceFrom, $dPriceTo )
    {
        $sSelect = $this->_getPriceSelect( $dPriceFrom, $dPriceTo );
        $this->_createIdListFromSql( $sSelect );
    }

    /**
     * Loads articles, that price is bigger than passed $dPriceFrom and smaller
     * than passed $dPriceTo. Returns count of selected articles.
     *
     * @param double $dPriceFrom Price from
     * @param double $dPriceTo   Price to
     * @param object $oCategory  Active category object
     *
     * @return integer
     */
    public function loadPriceArticles( $dPriceFrom, $dPriceTo, $oCategory = null)
    {
        $sSelect =  $this->_getPriceSelect( $dPriceFrom, $dPriceTo );

        startProfile("loadPriceArticles");
        $this->selectString( $sSelect);
        stopProfile("loadPriceArticles");

        if ( !$oCategory ) {
            return $this->count();
        }

        return oxRegistry::get("oxUtilsCount")->getPriceCatArticleCount( $oCategory->getId(), $dPriceFrom, $dPriceTo );
    }

    /**
     * Loads Products for specified vendor
     *
     * @param string $sVendorId Vendor id
     *
     * @return null;
     */
    public function loadVendorIDs( $sVendorId)
    {
        $sSelect = $this->_getVendorSelect($sVendorId);
        $this->_createIdListFromSql($sSelect);
    }

    /**
     * Loads Products for specified Manufacturer
     *
     * @param string $sManufacturerId Manufacturer id
     *
     * @return null;
     */
    public function loadManufacturerIDs( $sManufacturerId)
    {
        $sSelect = $this->_getManufacturerSelect($sManufacturerId);
        $this->_createIdListFromSql($sSelect);
    }

    /**
     * Loads articles that belongs to vendor, passed by parameter $sVendorId.
     * Returns count of selected articles.
     *
     * @param string $sVendorId Vendor ID
     * @param object $oVendor   Active vendor object
     *
     * @return integer
     */
    public function loadVendorArticles( $sVendorId, $oVendor = null )
    {
        $sSelect = $this->_getVendorSelect($sVendorId);
        $this->selectString( $sSelect);

        return oxRegistry::get("oxUtilsCount")->getVendorArticleCount( $sVendorId );
    }

    /**
     * Loads articles that belongs to Manufacturer, passed by parameter $sManufacturerId.
     * Returns count of selected articles.
     *
     * @param string $sManufacturerId Manufacturer ID
     * @param object $oManufacturer   Active Manufacturer object
     *
     * @return integer
     */
    public function loadManufacturerArticles( $sManufacturerId, $oManufacturer = null )
    {
        $sSelect = $this->_getManufacturerSelect($sManufacturerId);
        $this->selectString( $sSelect);

        return oxRegistry::get("oxUtilsCount")->getManufacturerArticleCount( $sManufacturerId );
    }

    /**
     * Loads a list of articles having
     *
     * @param string $sTag  Searched tag
     * @param int    $iLang Active language
     *
     * @return int
     */
    public function loadTagArticles( $sTag, $iLang )
    {
        $oListObject = $this->getBaseObject();
        $sArticleTable = $oListObject->getViewName();
        $sArticleFields = $oListObject->getSelectFields();
        $sViewName = getViewName( 'oxartextends', $iLang );

        $oTag = oxNew( 'oxtag', $sTag );
        $oTag->addUnderscores();
        $sTag = $oTag->get();

        $sQ = "select {$sArticleFields} from {$sViewName} inner join {$sArticleTable} on ".
              "{$sArticleTable}.oxid = {$sViewName}.oxid where {$sArticleTable}.oxparentid = '' AND match ( {$sViewName}.oxtags ) ".
              "against( ".oxDb::getDb()->quote( "\"".$sTag."\"" )." IN BOOLEAN MODE )";

        // checking stock etc
        if ( ( $sActiveSnippet = $oListObject->getSqlActiveSnippet() ) ) {
            $sQ .= " and {$sActiveSnippet}";
        }

        if ( $this->_sCustomSorting ) {
            $sSort = $this->_sCustomSorting;
            if (strpos($sSort, '.') === false) {
                $sSort = $sArticleTable.'.'.$sSort;
            }
            $sQ .= " order by $sSort ";
        }

        $this->selectString( $sQ );

        // calc count - we can not use count($this) here as we might have paging enabled
        return oxRegistry::get("oxUtilsCount")->getTagArticleCount( $sTag, $iLang );
    }

    /**
     * Returns array of article ids belonging to current tags
     *
     * @param string $sTag  current tag
     * @param int    $iLang active language
     *
     * @return array
     */
    public function getTagArticleIds( $sTag, $iLang )
    {
        $oListObject = $this->getBaseObject();
        $sArticleTable = $oListObject->getViewName();
        $sViewName = getViewName( 'oxartextends', $iLang );

        $oTag = oxNew( 'oxtag', $sTag );
        $oTag->addUnderscores();
        $sTag = $oTag->get();

        $sQ = "select {$sViewName}.oxid from {$sViewName} inner join {$sArticleTable} on ".
            "{$sArticleTable}.oxid = {$sViewName}.oxid where {$sArticleTable}.oxparentid = '' and {$sArticleTable}.oxissearch = 1 and ".
            "match ( {$sViewName}.oxtags ) ".
            "against( ".oxDb::getDb()->quote( "\"".$sTag."\"" )." IN BOOLEAN MODE )";

        // checking stock etc
        if ( ( $sActiveSnippet = $oListObject->getSqlActiveSnippet() ) ) {
            $sQ .= " and {$sActiveSnippet}";
        }

        if ( $this->_sCustomSorting ) {
            $sSort = $this->_sCustomSorting;
            if (strpos($sSort, '.') === false) {
                $sSort = $sArticleTable.'.'.$sSort;
            }
            $sQ .= " order by $sSort ";
        }

        return $this->_createIdListFromSql( $sQ );
    }

    /**
     * Load the list by article ids
     *
     * @param array $aIds Article ID array
     *
     * @return null;
     */
    public function loadIds($aIds)
    {
        if (!count($aIds)) {
            $this->clear();
            return;
        }

        foreach ($aIds as $iKey => $sVal) {
            $aIds[$iKey] = oxDb::getInstance()->escapeString($sVal);
        }

        $oBaseObject    = $this->getBaseObject();
        $sArticleTable  = $oBaseObject->getViewName();
        $sArticleFields = $oBaseObject->getSelectFields();

        $sSelect  = "select $sArticleFields from $sArticleTable ";
        $sSelect .= "where $sArticleTable.oxid in ( '".implode("','", $aIds)."' ) and ";
        $sSelect .= $oBaseObject->getSqlActiveSnippet();

        $this->selectString($sSelect);
    }

    /**
     * Loads the article list by orders ids
     *
     * @param array $aOrders user orders array
     *
     * @return null;
     */
    public function loadOrderArticles($aOrders)
    {
        if (!count($aOrders)) {
            $this->clear();
            return;
        }

        foreach ($aOrders as $oOrder) {
            $aOrdersIds[] = $oOrder->getId();
        }

        $oBaseObject    = $this->getBaseObject();
        $sArticleTable  = $oBaseObject->getViewName();
        $sArticleFields = $oBaseObject->getSelectFields();
        $sArticleFields = str_replace( "`$sArticleTable`.`oxid`", "`oxorderarticles`.`oxartid` AS `oxid`", $sArticleFields );

        $sSelect  = "SELECT $sArticleFields FROM oxorderarticles ";
        $sSelect .= "left join $sArticleTable on oxorderarticles.oxartid = $sArticleTable.oxid ";
        $sSelect .= "WHERE oxorderarticles.oxorderid IN ( '".implode("','", $aOrdersIds)."' ) ";
        $sSelect .= "order by $sArticleTable.oxid ";

        $this->selectString( $sSelect );

        // not active or not available products must not have button "tobasket"
        $sNow = date('Y-m-d H:i:s');
        foreach ( $this as $oArticle ) {
            if ( !$oArticle->oxarticles__oxactive->value  &&
             (  $oArticle->oxarticles__oxactivefrom->value > $sNow ||
                $oArticle->oxarticles__oxactiveto->value < $sNow
             )) {
                $oArticle->setBuyableState( false );
            }
        }
    }

    /**
     * Loads list of low stock state products
     *
     * @param array $aBasketContents product ids array
     *
     * @return null
     */
    public function loadStockRemindProducts( $aBasketContents )
    {
        if ( is_array( $aBasketContents ) && count( $aBasketContents ) ) {
            $oDb = oxDb::getDb();
            foreach ( $aBasketContents as $oBasketItem ) {
                $aArtIds[] = $oDb->quote($oBasketItem->getProductId());
            }

            $oBaseObject = $this->getBaseObject();

            $sFieldNames = $oBaseObject->getSelectFields();
            $sTable      = $oBaseObject->getViewName();

            // fetching actual db stock state and reminder status
            $sQ = "select {$sFieldNames} from {$sTable} where {$sTable}.oxid in ( ".implode( ",", $aArtIds )." ) and
                          oxremindactive = '1' and oxstock <= oxremindamount";
            $this->selectString( $sQ );

            // updating stock reminder state
            if ( $this->count() ) {
                $sQ = "update {$sTable} set oxremindactive = '2' where {$sTable}.oxid in ( ".implode( ",", $aArtIds )." ) and
                              oxremindactive = '1' and oxstock <= oxremindamount";
                $oDb->execute( $sQ );
            }
        }
    }

    /**
     * Calculates, updates and returns next price renew time
     *
     * @return int
     */
    public function renewPriceUpdateTime()
    {
        $oDb = oxDb::getDb();

        // fetching next update time
        $sQ = "select unix_timestamp( oxupdatepricetime ) from %s where oxupdatepricetime > 0 order by oxupdatepricetime asc";
        $iTimeToUpdate = $oDb->getOne( sprintf( $sQ, "`oxarticles`" ), false, false );


        // next day?
        $iCurrUpdateTime = oxRegistry::get("oxUtilsDate")->getTime();
        $iNextUpdateTime = $iCurrUpdateTime + 3600 * 24;

        // renew next update time
        if ( !$iTimeToUpdate || $iTimeToUpdate > $iNextUpdateTime ) {
            $iTimeToUpdate = $iNextUpdateTime;
        }

        $this->getConfig()->saveShopConfVar( "int", "iTimeToUpdatePrices", $iTimeToUpdate );

        return $iTimeToUpdate;
    }

    /**
     * Updates prices where new price > 0, update time != '0000-00-00 00:00:00'
     * and <= CURRENT_TIMESTAMP. Returns update execution state (result of oxDb::execute())
     *
     * @param bool $blForceUpdate if true, forces price update without timeout check, default value is FALSE
     *
     * @return mixed
     */
    public function updateUpcomingPrices( $blForceUpdate = false )
    {
        $blUpdated = false;

        if ( $blForceUpdate || $this->_canUpdatePrices() ) {

            $oDb = oxDb::getDb();

            $oDb->startTransaction();

            $sCurrUpdateTime = date( "Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() );

            // Collect article id's for later recalculation.
            $sQ = "SELECT `oxid` FROM `oxarticles`
                   WHERE `oxupdatepricetime` > 0 AND `oxupdatepricetime` <= '{$sCurrUpdateTime}'";
            $aUpdatedArticleIds = $oDb->getCol( $sQ, false, false );

            // updating oxarticles
            $sQ = "UPDATE %s SET
                       `oxprice`  = IF( `oxupdateprice` > 0, `oxupdateprice`, `oxprice` ),
                       `oxpricea` = IF( `oxupdatepricea` > 0, `oxupdatepricea`, `oxpricea` ),
                       `oxpriceb` = IF( `oxupdatepriceb` > 0, `oxupdatepriceb`, `oxpriceb` ),
                       `oxpricec` = IF( `oxupdatepricec` > 0, `oxupdatepricec`, `oxpricec` ),
                       `oxupdatepricetime` = 0,
                       `oxupdateprice`     = 0,
                       `oxupdatepricea`    = 0,
                       `oxupdatepriceb`    = 0,
                       `oxupdatepricec`    = 0
                   WHERE
                       `oxupdatepricetime` > 0 AND
                       `oxupdatepricetime` <= '{$sCurrUpdateTime}'" ;
            $blUpdated = $oDb->execute( sprintf( $sQ, "`oxarticles`" ) );


            // renew update time in case update is not forced
            if ( !$blForceUpdate ) {
                $this->renewPriceUpdateTime();
            }

            $oDb->commitTransaction();

            // recalculate oxvarminprice and oxvarmaxprice for parent
            if ( is_array($aUpdatedArticleIds) ) {
                foreach ($aUpdatedArticleIds as $sArticleId) {
                    $oArticle = oxNew('oxarticle');
                    $oArticle->load($sArticleId);
                    $oArticle->onChange();
                }
            }

        }

        return $blUpdated;
    }

    /**
     * fills the list simply with keys of the oxid and the position as value for the given sql
     *
     * @param string $sSql SQL select
     *
     * @return null
     */
    protected function _createIdListFromSql( $sSql)
    {
        $rs = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->select( $sSql );
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $rs->fields = array_change_key_case($rs->fields, CASE_LOWER);
                $this[$rs->fields['oxid']] =  $rs->fields['oxid']; //only the oxid
                $rs->moveNext();
            }
        }
    }

    /**
     * Returns sql to fetch ids of articles fitting current filter
     *
     * @param string $sCatId  category id
     * @param array  $aFilter filters for this category
     *
     * @return string
     */
    protected function _getFilterIdsSql( $sCatId, $aFilter )
    {
        $sO2CView = getViewName( 'oxobject2category' );
        $sO2AView = getViewName( 'oxobject2attribute' );

        $sFilter = '';
        $iCnt    = 0;

        $oDb = oxDb::getDb();
        foreach ( $aFilter as $sAttrId => $sValue ) {
            if ( $sValue ) {
                if ( $sFilter ) {
                    $sFilter .= ' or ';
                }
                $sValue  = $oDb->quote( $sValue );
                $sAttrId = $oDb->quote( $sAttrId );

                $sFilter .= "( oa.oxattrid = {$sAttrId} and oa.oxvalue = {$sValue} )";
                $iCnt++;
            }
        }
        if ( $sFilter ) {
            $sFilter = "WHERE $sFilter ";
        }

        $sFilterSelect = "select oc.oxobjectid as oxobjectid, count(*) as cnt from ";
        $sFilterSelect.= "(SELECT * FROM $sO2CView WHERE $sO2CView.oxcatnid = '$sCatId' GROUP BY $sO2CView.oxobjectid, $sO2CView.oxcatnid) as oc ";
        $sFilterSelect.= "INNER JOIN $sO2AView as oa ON ( oa.oxobjectid = oc.oxobjectid ) ";
        return $sFilterSelect . "{$sFilter} GROUP BY oa.oxobjectid HAVING cnt = $iCnt ";
    }

    /**
     * Returns filtered articles sql "oxid in (filtered ids)" part
     *
     * @param string $sCatId  category id
     * @param array  $aFilter filters for this category
     *
     * @return string
     */
    protected function _getFilterSql( $sCatId, $aFilter )
    {
        $sArticleTable = getViewName( 'oxarticles' );
        $aIds = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll( $this->_getFilterIdsSql( $sCatId, $aFilter ) );
        $sIds = '';

        if ( $aIds ) {
            foreach ( $aIds as $aArt ) {
                if ( $sIds ) {
                    $sIds .= ', ';
                }
                $sIds .= oxDb::getDb()->quote( current( $aArt ) );
            }

            if ( $sIds ) {
                $sFilterSql = " and $sArticleTable.oxid in ( $sIds ) ";
            }
        // bug fix #0001695: if no articles found return false
        } elseif ( !( current( $aFilter ) == '' && count( array_unique( $aFilter ) ) == 1 ) ) {
            $sFilterSql = " and false ";
        }

        return $sFilterSql;
    }

    /**
     * Creates SQL Statement to load Articles, etc.
     *
     * @param string $sFields        Fields which are loaded e.g. "oxid" or "*" etc.
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     */
    protected function _getCategorySelect( $sFields, $sCatId, $aSessionFilter )
    {
        $sArticleTable = getViewName( 'oxarticles' );
        $sO2CView      = getViewName( 'oxobject2category' );

        // ----------------------------------
        // sorting
        $sSorting = '';
        if ( $this->_sCustomSorting ) {
            $sSorting = " {$this->_sCustomSorting} , ";
        }

        // ----------------------------------
        // filtering ?
        $sFilterSql = '';
        $iLang = oxRegistry::getLang()->getBaseLanguage();
        if ( $aSessionFilter && isset( $aSessionFilter[$sCatId][$iLang] ) ) {
            $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
        }

        $oDb = oxDb::getDb();

        $sSelect = "SELECT $sFields, $sArticleTable.oxtimestamp FROM $sO2CView as oc left join $sArticleTable
                    ON $sArticleTable.oxid = oc.oxobjectid
                    WHERE ".$this->getBaseObject()->getSqlActiveSnippet()." and $sArticleTable.oxparentid = ''
                    and oc.oxcatnid = ".$oDb->quote($sCatId)." $sFilterSql ORDER BY $sSorting oc.oxpos, oc.oxobjectid ";

        return $sSelect;
    }

    /**
     * Creates SQL Statement to load Articles Count, etc.
     *
     * @param string $sCatId         Category tree ID
     * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     */
    protected function _getCategoryCountSelect( $sCatId, $aSessionFilter )
    {
        $sArticleTable = getViewName( 'oxarticles' );
        $sO2CView      = getViewName( 'oxobject2category' );


        // ----------------------------------
        // filtering ?
        $sFilterSql = '';
        $iLang = oxRegistry::getLang()->getBaseLanguage();
        if ( $aSessionFilter && isset( $aSessionFilter[$sCatId][$iLang] ) ) {
            $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
        }

        $oDb = oxDb::getDb();

        $sSelect = "SELECT COUNT(*) FROM $sO2CView as oc left join $sArticleTable
                    ON $sArticleTable.oxid = oc.oxobjectid
                    WHERE ".$this->getBaseObject()->getSqlActiveSnippet()." and $sArticleTable.oxparentid = ''
                    and oc.oxcatnid = ".$oDb->quote($sCatId)." $sFilterSql ";

        return $sSelect;
    }

    /**
     * Forms and returns SQL query string for search in DB.
     *
     * @param string $sSearchString searching string
     *
     * @return string
     */
    protected function _getSearchSelect( $sSearchString )
    {
        // check if it has string at all
        if ( !$sSearchString || !str_replace( ' ', '', $sSearchString ) ) {
            return '';
        }

        $oDb = oxDb::getDb();
        $myConfig = $this->getConfig();

        $sArticleTable = $this->getBaseObject()->getViewName();

        $aSearch = explode( ' ', $sSearchString);

        $sSearch  = ' and ( ';
        $blSep = false;

        // #723
        if ( $myConfig->getConfigParam( 'blSearchUseAND' ) ) {
            $sSearchSep = ' and ';
        } else {
            $sSearchSep = ' or ';
        }

        $aSearchCols = $myConfig->getConfigParam( 'aSearchCols' );
        $myUtilsString = oxRegistry::get("oxUtilsString");
        foreach ( $aSearch as $sSearchString) {

            if ( !strlen( $sSearchString ) ) {
                continue;
            }

            if ( $blSep ) {
                $sSearch .= $sSearchSep;
            }
            $blSep2   = false;
            $sSearch .= '( ';

            $sUml = $myUtilsString->prepareStrForSearch($sSearchString);
            foreach ( $aSearchCols as $sField ) {

                if ( $blSep2) {
                    $sSearch  .= ' or ';
                }

                // as long description now is on different table table must differ
                if ( $sField == 'oxlongdesc' || $sField == 'oxtags') {
                    $sSearchTable = getViewName( 'oxartextends' );
                } else {
                    $sSearchTable = $sArticleTable;
                }

                $sSearch .= $sSearchTable.'.'.$sField.' like '.$oDb->quote('%'.$sSearchString.'%') . ' ';
                if ( $sUml ) {
                    $sSearch  .= ' or '.$sSearchTable.'.'.$sField.' like '.$oDb->quote('%'.$sUml.'%');
                }
                $blSep2 = true;
            }
            $sSearch  .= ' ) ';
            $blSep = true;
        }
        $sSearch .= ' ) ';

        return $sSearch;
    }

    /**
     * Builds SQL for selecting articles by price
     *
     * @param double $dPriceFrom Starting price
     * @param double $dPriceTo   Max price
     *
     * @return string
     */
    protected function _getPriceSelect( $dPriceFrom, $dPriceTo )
    {
        $oBaseObject   = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();
        $sSelectFields = $oBaseObject->getSelectFields();

        $sSelect  = "select {$sSelectFields} from {$sArticleTable} where oxvarminprice >= 0 ";
        $sSelect .= $dPriceTo ? "and oxvarminprice <= " . (double)$dPriceTo . " " : " ";
        $sSelect .= $dPriceFrom ? "and oxvarminprice  >= " . (double)$dPriceFrom . " " : " ";

        $sSelect .= " and ".$oBaseObject->getSqlActiveSnippet()." and {$sArticleTable}.oxissearch = 1";

        if ( !$this->_sCustomSorting ) {
            $sSelect .= " order by {$sArticleTable}.oxvarminprice asc , {$sArticleTable}.oxid";
        } else {
            $sSelect .= " order by {$this->_sCustomSorting}, {$sArticleTable}.oxid ";
        }

        return $sSelect;
    }

    /**
     * Builds vendor select SQL statement
     *
     * @param string $sVendorId Vendor ID
     *
     * @return string
     */
    protected function _getVendorSelect( $sVendorId )
    {
        $sArticleTable = getViewName('oxarticles');
        $oBaseObject = $this->getBaseObject();
        $sFieldNames = $oBaseObject->getSelectFields();
        $sSelect  = "select $sFieldNames from $sArticleTable ";
        $sSelect .= "where $sArticleTable.oxvendorid = ".oxDb::getDb()->quote($sVendorId)." ";
        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''  ";

        if ( $this->_sCustomSorting ) {
            $sSelect .= " ORDER BY {$this->_sCustomSorting} ";
        }

        return $sSelect;
    }

    /**
     * Builds Manufacturer select SQL statement
     *
     * @param string $sManufacturerId Manufacturer ID
     *
     * @return string
     */
    protected function _getManufacturerSelect( $sManufacturerId )
    {
        $sArticleTable = getViewName('oxarticles');
        $oBaseObject = $this->getBaseObject();
        $sFieldNames = $oBaseObject->getSelectFields();
        $sSelect  = "select $sFieldNames from $sArticleTable ";
        $sSelect .= "where $sArticleTable.oxmanufacturerid = ".oxDb::getDb()->quote($sManufacturerId)." ";
        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''  ";

        if ( $this->_sCustomSorting ) {
            $sSelect .= " ORDER BY {$this->_sCustomSorting} ";
        }

        return $sSelect;
    }

    /**
     * Checks if price update can be executed - current time > next price update time
     *
     * @return bool
     */
    protected function _canUpdatePrices()
    {
        $oConfig = $this->getConfig();
        $blCan = false;

        // crontab is off?
        if ( !$oConfig->getConfigParam( "blUseCron" ) ) {
            $iTimeToUpdate = $oConfig->getConfigParam( "iTimeToUpdatePrices" );
            if ( !$iTimeToUpdate || $iTimeToUpdate <= oxRegistry::get("oxUtilsDate")->getTime() ) {
                $blCan = true;
            }
        }
        return $blCan;
    }

}
