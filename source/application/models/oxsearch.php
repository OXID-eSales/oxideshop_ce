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
 * Implements search
 *
 * @package model
 */
class oxSearch extends oxSuperCfg
{
    /**
     * Active language id
     *
     * @var int
     */
    protected $_iLanguage = 0;

    /**
     * Class constructor. Executes search lenguage setter
     *
     * @return null
     */
    public function __construct()
    {
        $this->setLanguage();
    }

    /**
     * Search language setter. If no param is passed, will be taken default shop language
     *
     * @param string $iLanguage string (default null)
     *
     * @return null;
     */
    public function setLanguage( $iLanguage = null )
    {
        if ( !isset( $iLanguage ) ) {
            $this->_iLanguage = oxRegistry::getLang()->getBaseLanguage();
        } else {
            $this->_iLanguage = $iLanguage;
        }
    }

    /**
     * Returns a list of articles according to search parameters. Returns matched
     *
     * @param string $sSearchParamForQuery       query parameter
     * @param string $sInitialSearchCat          initial category to seearch in
     * @param string $sInitialSearchVendor       initial vendor to seearch for
     * @param string $sInitialSearchManufacturer initial Manufacturer to seearch for
     * @param string $sSortBy                    sort by
     *
     * @return oxarticlelist
     */
    public function getSearchArticles( $sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false )
    {
        // sets active page
        $this->iActPage = (int) oxConfig::getParameter( 'pgNr' );
        $this->iActPage = ($this->iActPage < 0)?0:$this->iActPage;

        // load only articles which we show on screen
        //setting default values to avoid possible errors showing article list
        $iNrofCatArticles = $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
        $iNrofCatArticles = $iNrofCatArticles?$iNrofCatArticles:10;

        $oArtList = oxNew( 'oxarticlelist' );
        $oArtList->setSqlLimit( $iNrofCatArticles * $this->iActPage, $iNrofCatArticles );

        $sSelect = $this->_getSearchSelect( $sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, $sSortBy );
        if ( $sSelect ) {
            $oArtList->selectString( $sSelect );
        }

        return $oArtList;
    }

    /**
     * Returns the amount of articles according to search parameters.
     *
     * @param string $sSearchParamForQuery       query parameter
     * @param string $sInitialSearchCat          initial category to seearch in
     * @param string $sInitialSearchVendor       initial vendor to seearch for
     * @param string $sInitialSearchManufacturer initial Manufacturer to seearch for
     *
     * @return int
     */
    public function getSearchArticleCount( $sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false )
    {
        $iCnt = 0;
        $sSelect = $this->_getSearchSelect( $sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, false );
        if ( $sSelect ) {

            $sPartial = substr( $sSelect, strpos( $sSelect, ' from ' ) );
            $sSelect  = "select count( ".getViewName( 'oxarticles', $this->_iLanguage ).".oxid ) $sPartial ";

            $iCnt = oxDb::getDb()->getOne( $sSelect );
        }
        return $iCnt;
    }

    /**
     * Returns the appropriate SQL select for a search according to search parameters
     *
     * @param string $sSearchParamForQuery       query parameter
     * @param string $sInitialSearchCat          initial category to search in
     * @param string $sInitialSearchVendor       initial vendor to search for
     * @param string $sInitialSearchManufacturer initial Manufacturer to search for
     * @param string $sSortBy                    sort by
     *
     * @return string
     */
    protected function _getSearchSelect( $sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        $oDb = oxDb::getDb();

        // performance
        if ( $sInitialSearchCat ) {
            // lets search this category - is no such category - skip all other code
            $oCategory = oxNew( 'oxcategory' );
            $sCatTable = $oCategory->getViewName();

            $sQ  = "select 1 from $sCatTable where $sCatTable.oxid = ".$oDb->quote( $sInitialSearchCat )." ";
            $sQ .= "and ".$oCategory->getSqlActiveSnippet();
            if ( !$oDb->getOne( $sQ ) ) {
                return;
            }
        }

        // performance:
        if ( $sInitialSearchVendor ) {
            // lets search this vendor - if no such vendor - skip all other code
            $oVendor   = oxNew( 'oxvendor' );
            $sVndTable = $oVendor->getViewName();

            $sQ  = "select 1 from $sVndTable where $sVndTable.oxid = ".$oDb->quote( $sInitialSearchVendor )." ";
            $sQ .= "and ".$oVendor->getSqlActiveSnippet();
            if ( !$oDb->getOne( $sQ ) ) {
                return;
            }
        }

        // performance:
        if ( $sInitialSearchManufacturer ) {
            // lets search this Manufacturer - if no such Manufacturer - skip all other code
            $oManufacturer   = oxNew( 'oxmanufacturer' );
            $sManTable = $oManufacturer->getViewName();

            $sQ  = "select 1 from $sManTable where $sManTable.oxid = ".$oDb->quote( $sInitialSearchManufacturer )." ";
            $sQ .= "and ".$oManufacturer->getSqlActiveSnippet();
            if ( !$oDb->getOne( $sQ ) ) {
                return;
            }
        }

        $sWhere = null;

        if ( $sSearchParamForQuery ) {
            $sWhere = $this->_getWhere( $sSearchParamForQuery );
        } elseif ( !$sInitialSearchCat && !$sInitialSearchVendor && !$sInitialSearchManufacturer ) {
            //no search string
            return null;
        }

        $oArticle = oxNew( 'oxarticle' );
        $sArticleTable = $oArticle->getViewName();
        $sO2CView      = getViewName( 'oxobject2category' );

        $sSelectFields = $oArticle->getSelectFields();

        // longdesc field now is kept on different table
        $sDescJoin  = '';
        if ( is_array( $aSearchCols = $this->getConfig()->getConfigParam( 'aSearchCols' ) ) ) {
            if ( in_array( 'oxlongdesc', $aSearchCols ) || in_array( 'oxtags', $aSearchCols ) ) {
                $sDescView  = getViewName( 'oxartextends', $this->_iLanguage );
                $sDescJoin  = " LEFT JOIN {$sDescView} ON {$sArticleTable}.oxid={$sDescView}.oxid ";
            }
        }

        //select articles
        $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} {$sDescJoin} where ";

        // must be additional conditions in select if searching in category
        if ( $sInitialSearchCat ) {
            $sCatView = getViewName( 'oxcategories', $this->_iLanguage );
            $sInitialSearchCatQuoted = $oDb->quote( $sInitialSearchCat );
            $sSelectCat  = "select oxid from {$sCatView} where oxid = $sInitialSearchCatQuoted and (oxpricefrom != '0' or oxpriceto != 0)";
            if ( $oDb->getOne($sSelectCat) ) {
                $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} $sDescJoin " .
                           "where {$sArticleTable}.oxid in ( select {$sArticleTable}.oxid as id from {$sArticleTable}, {$sO2CView} as oxobject2category, {$sCatView} as oxcategories " .
                           "where (oxobject2category.oxcatnid=$sInitialSearchCatQuoted and oxobject2category.oxobjectid={$sArticleTable}.oxid) or (oxcategories.oxid=$sInitialSearchCatQuoted and {$sArticleTable}.oxprice >= oxcategories.oxpricefrom and
                            {$sArticleTable}.oxprice <= oxcategories.oxpriceto )) and ";
            } else {
                $sSelect = "select {$sSelectFields} from {$sO2CView} as
                            oxobject2category, {$sArticleTable} {$sDescJoin} where oxobject2category.oxcatnid=$sInitialSearchCatQuoted and
                            oxobject2category.oxobjectid={$sArticleTable}.oxid and ";
            }
        }

        $sSelect .= $oArticle->getSqlActiveSnippet();
        $sSelect .= " and {$sArticleTable}.oxparentid = '' and {$sArticleTable}.oxissearch = 1 ";

        if ( $sInitialSearchVendor ) {
            $sSelect .= " and {$sArticleTable}.oxvendorid = " . $oDb->quote( $sInitialSearchVendor ) . " ";
        }

        if ( $sInitialSearchManufacturer ) {
            $sSelect .= " and {$sArticleTable}.oxmanufacturerid = " . $oDb->quote( $sInitialSearchManufacturer ) . " ";
        }

        $sSelect .= $sWhere;

        if ( $sSortBy ) {
            $sSelect .= " order by {$sSortBy} ";
        }

        return $sSelect;
    }

    /**
     * Forms and returns SQL query string for search in DB.
     *
     * @param string $sSearchString searching string
     *
     * @return string
     */
    protected function _getWhere( $sSearchString )
    {
        $oDb = oxDb::getDb();
        $myConfig = $this->getConfig();
        $blSep    = false;
        $sArticleTable = getViewName( 'oxarticles', $this->_iLanguage );

        $aSearchCols = $myConfig->getConfigParam( 'aSearchCols' );
        if ( !(is_array( $aSearchCols ) && count( $aSearchCols ) ) ) {
            return '';
        }

        $oTempArticle = oxNew( 'oxarticle' );
        $sSearchSep   = $myConfig->getConfigParam( 'blSearchUseAND' )?'and ':'or ';
        $aSearch  = explode( ' ', $sSearchString );
        $sSearch  = ' and ( ';
        $myUtilsString = oxRegistry::get("oxUtilsString");
        $oLang = oxRegistry::getLang();

        foreach ( $aSearch as $sSearchString ) {

            if ( !strlen( $sSearchString ) ) {
                continue;
            }

            if ( $blSep ) {
                $sSearch .= $sSearchSep;
            }

            $blSep2 = false;
            $sSearch  .= '( ';

            foreach ( $aSearchCols as $sField ) {

                if ( $blSep2 ) {
                    $sSearch  .= ' or ';
                }

                // as long description now is on different table table must differ
                if ( $sField == 'oxlongdesc' || $sField == 'oxtags' ) {
                    $sSearchField = getViewName( 'oxartextends', $this->_iLanguage ).".{$sField}";
                } else {
                    $sSearchField = "{$sArticleTable}.{$sField}";
                }

                $sSearch .= " {$sSearchField} like ".$oDb->quote( "%$sSearchString%" );

                // special chars ?
                if ( ( $sUml = $myUtilsString->prepareStrForSearch( $sSearchString ) ) ) {
                    $sSearch  .= " or {$sSearchField} like ".$oDb->quote( "%$sUml%" );
                }

                $blSep2 = true;
            }
            $sSearch  .= ' ) ';

            $blSep = true;
        }

        $sSearch .= ' ) ';

        return $sSearch;
    }
}

