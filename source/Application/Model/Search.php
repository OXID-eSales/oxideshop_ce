<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * Implements search
 *
 */
class Search extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Active language id
     *
     * @var int
     */
    protected $_iLanguage = 0;

    /**
     * Class constructor. Executes search lenguage setter
     */
    public function __construct()
    {
        $this->setLanguage();
    }

    /**
     * Search language setter. If no param is passed, will be taken default shop language
     *
     * @param string $iLanguage string (default null)
     */
    public function setLanguage($iLanguage = null)
    {
        if (!isset($iLanguage)) {
            $this->_iLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
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
     * @return ArticleList
     */
    public function getSearchArticles($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        // sets active page
        $this->iActPage = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pgNr');
        $this->iActPage = ($this->iActPage < 0) ? 0 : $this->iActPage;

        // load only articles which we show on screen
        //setting default values to avoid possible errors showing article list
        $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
        $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->setSqlLimit($iNrofCatArticles * $this->iActPage, $iNrofCatArticles);

        $sSelect = $this->_getSearchSelect($sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, $sSortBy);
        if ($sSelect) {
            $oArtList->selectString($sSelect);
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
    public function getSearchArticleCount($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false)
    {
        $iCnt = 0;
        $sSelect = $this->_getSearchSelect($sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, false);
        if ($sSelect) {
            $sPartial = substr($sSelect, strpos($sSelect, ' from '));
            $sSelect = "select count( " . getViewName('oxarticles', $this->_iLanguage) . ".oxid ) $sPartial ";

            $iCnt = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sSelect);
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
    protected function _getSearchSelect($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        if (!$sSearchParamForQuery && !$sInitialSearchCat && !$sInitialSearchVendor && !$sInitialSearchManufacturer) {
            //no search string
            return null;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // performance
        if ($sInitialSearchCat) {
            // lets search this category - is no such category - skip all other code
            $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
            $sCatTable = $oCategory->getViewName();

            $sQ = "select 1 from $sCatTable 
                where $sCatTable.oxid = :oxid ";
            $sQ .= "and " . $oCategory->getSqlActiveSnippet();

            $params = [
                ':oxid' => $sInitialSearchCat
            ];

            if (!$oDb->getOne($sQ, $params)) {
                return;
            }
        }

        // performance:
        if ($sInitialSearchVendor) {
            // lets search this vendor - if no such vendor - skip all other code
            $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
            $sVndTable = $oVendor->getViewName();

            $sQ = "select 1 from $sVndTable 
                where $sVndTable.oxid = :oxid ";
            $sQ .= "and " . $oVendor->getSqlActiveSnippet();

            $params = [
                ':oxid' => $sInitialSearchVendor
            ];

            if (!$oDb->getOne($sQ, $params)) {
                return;
            }
        }

        // performance:
        if ($sInitialSearchManufacturer) {
            // lets search this Manufacturer - if no such Manufacturer - skip all other code
            $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            $sManTable = $oManufacturer->getViewName();

            $sQ = "select 1 from $sManTable 
                where $sManTable.oxid = :oxid ";
            $sQ .= "and " . $oManufacturer->getSqlActiveSnippet();

            $params = [
                ':oxid' => $sInitialSearchManufacturer
            ];

            if (!$oDb->getOne($sQ, $params)) {
                return;
            }
        }

        $sWhere = null;
        if ($sSearchParamForQuery) {
            $sWhere = $this->_getWhere($sSearchParamForQuery);
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $sArticleTable = $oArticle->getViewName();
        $sO2CView = getViewName('oxobject2category');

        $sSelectFields = $oArticle->getSelectFields();

        // longdesc field now is kept on different table
        $sDescJoin = $this->getDescriptionJoin($sArticleTable);

        //select articles
        $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} {$sDescJoin} where ";

        // must be additional conditions in select if searching in category
        if ($sInitialSearchCat) {
            $sCatView = getViewName('oxcategories', $this->_iLanguage);
            $sInitialSearchCatQuoted = $oDb->quote($sInitialSearchCat);
            $sSelectCat = "select oxid from {$sCatView} where oxid = $sInitialSearchCatQuoted and (oxpricefrom != '0' or oxpriceto != 0)";
            if ($oDb->getOne($sSelectCat)) {
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

        if ($sInitialSearchVendor) {
            $sSelect .= " and {$sArticleTable}.oxvendorid = " . $oDb->quote($sInitialSearchVendor) . " ";
        }

        if ($sInitialSearchManufacturer) {
            $sSelect .= " and {$sArticleTable}.oxmanufacturerid = " . $oDb->quote($sInitialSearchManufacturer) . " ";
        }

        $sSelect .= $sWhere;

        if ($sSortBy) {
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
    protected function _getWhere($sSearchString)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $myConfig = $this->getConfig();
        $blSep = false;
        $sArticleTable = getViewName('oxarticles', $this->_iLanguage);

        $aSearchCols = $myConfig->getConfigParam('aSearchCols');
        if (!(is_array($aSearchCols) && count($aSearchCols))) {
            return '';
        }

        $sSearchSep = $myConfig->getConfigParam('blSearchUseAND') ? 'and ' : 'or ';
        $aSearch = explode(' ', $sSearchString);
        $sSearch = ' and ( ';
        $myUtilsString = \OxidEsales\Eshop\Core\Registry::getUtilsString();

        foreach ($aSearch as $sSearchString) {
            if (!strlen($sSearchString)) {
                continue;
            }

            if ($blSep) {
                $sSearch .= $sSearchSep;
            }

            $blSep2 = false;
            $sSearch .= '( ';

            foreach ($aSearchCols as $sField) {
                if ($blSep2) {
                    $sSearch .= ' or ';
                }

                // as long description now is on different table table must differ
                $sSearchField = $this->getSearchField($sArticleTable, $sField);

                $sSearch .= " {$sSearchField} like " . $oDb->quote("%$sSearchString%");

                // special chars ?
                if (($sUml = $myUtilsString->prepareStrForSearch($sSearchString))) {
                    $sSearch .= " or {$sSearchField} like " . $oDb->quote("%$sUml%");
                }

                $blSep2 = true;
            }
            $sSearch .= ' ) ';

            $blSep = true;
        }

        $sSearch .= ' ) ';

        return $sSearch;
    }

    /**
     * Get description join. Needed in case of searching for data in table oxartextends or its views.
     *
     * @param string $table
     *
     * @return string
     */
    protected function getDescriptionJoin($table)
    {
        $descriptionJoin = '';
        $searchColumns = $this->getConfig()->getConfigParam('aSearchCols');

        if (is_array($searchColumns) && in_array('oxlongdesc', $searchColumns)) {
            $viewName = getViewName('oxartextends', $this->_iLanguage);
            $descriptionJoin = " LEFT JOIN {$viewName } ON {$table}.oxid={$viewName }.oxid ";
        }
        return $descriptionJoin;
    }

    /**
     * Get search field name.
     * Needed in case of searching for data in table oxartextends or its views.
     *
     * @param string $table
     * @param string $field Chose table depending on field.
     *
     * @return string
     */
    protected function getSearchField($table, $field)
    {
        if ($field == 'oxlongdesc') {
            $searchField = getViewName('oxartextends', $this->_iLanguage) . ".{$field}";
        } else {
            $searchField = "{$table}.{$field}";
        }
        return $searchField;
    }
}
