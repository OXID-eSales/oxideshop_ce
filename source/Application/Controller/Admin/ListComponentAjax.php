<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterAdminAjaxRequestProcessedEvent;

/**
 * AJAX call processor class
 */
class ListComponentAjax extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Possible sort keys
     *
     * @var array
     */
    protected $_aPosDir = ['asc', 'desc'];

    /**
     * Array of DB table columns which are loaded from DB
     *
     * @var array
     */
    protected $_aColumns = [];

    /**
     * Default limit of DB entries to load from DB
     *
     * @var int
     */
    protected $_iSqlLimit = 2500;

    /**
     * Ajax container name
     *
     * @var string
     */
    protected $_sContainer = null;

    /**
     * If true extended column selection will be build
     * (currently checks if variants must be shown in lists and column name is "oxtitle")
     *
     * @var bool
     */
    protected $_blAllowExtColumns = false;

    /**
     * Gets columns array.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_aColumns;
    }

    /**
     * Sets columns array.
     *
     * @param array $aColumns columns array
     */
    public function setColumns($aColumns)
    {
        $this->_aColumns = $aColumns;
    }
    /**
     * @deprecated use self::getActionIds instead
     */
    protected function _getActionIds($sId) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getActionIds($sId);
    }

    /**
     * Required data fields are returned by indexes/position in _aColumns array. This method
     * translates "table_name.col_name" into index definition and fetches request data according
     * to it. This is usefull while using AJAX across versions.
     *
     * @param string $sId "table_name.col_name"
     *
     * @return array
     */
    protected function getActionIds($sId)
    {
        $aColumns = $this->_getColNames();
        foreach ($aColumns as $iPos => $aCol) {
            if (isset($aCol[4]) && $aCol[4] == 1 && $sId == $aCol[1] . '.' . $aCol[0]) {
                return \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('_' . $iPos);
            }
        }
    }

    /**
     * AJAX container name setter
     *
     * @param string $sName name of container
     */
    public function setName($sName)
    {
        $this->_sContainer = $sName;
    }
    /**
     * @deprecated use self::getQuery instead
     */
    protected function _getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getQuery();
    }

    /**
     * Empty function, developer should override this method according requirements
     *
     * @return string
     */
    protected function getQuery()
    {
        return '';
    }
    /**
     * @deprecated use self::getDataQuery instead
     */
    protected function _getDataQuery($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getDataQuery($sQ);
    }

    /**
     * Return fully formatted query for data loading
     *
     * @param string $sQ part of initial query
     *
     * @return string
     */
    protected function getDataQuery($sQ)
    {
        return 'select ' . $this->_getQueryCols() . $sQ;
    }
    /**
     * @deprecated use self::getCountQuery instead
     */
    protected function _getCountQuery($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getCountQuery($sQ);
    }

    /**
     * Return fully formatted query for data records count
     *
     * @param string $sQ part of initial query
     *
     * @return string
     */
    protected function getCountQuery($sQ)
    {
        return 'select count( * ) ' . $sQ;
    }

    /**
     * AJAX call processor function
     *
     * @param string $function name of action to execute (optional)
     */
    public function processRequest($function = null)
    {
        if ($function) {
            $this->$function();
            $this->dispatchEvent(new AfterAdminAjaxRequestProcessedEvent());
        } else {
            $sQAdd = $this->_getQuery();

            // formatting SQL queries
            $sQ = $this->_getDataQuery($sQAdd);
            $sCountQ = $this->_getCountQuery($sQAdd);

            $this->_outputResponse($this->_getData($sCountQ, $sQ));
        }
    }
    /**
     * @deprecated use self::getSortCol instead
     */
    protected function _getSortCol() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getSortCol();
    }

    /**
     * Returns column id to sort
     *
     * @return int
     */
    protected function getSortCol()
    {
        $aVisibleNames = $this->_getVisibleColNames();
        $iCol = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('sort');
        $iCol = $iCol ? ((int) str_replace('_', '', $iCol)) : 0;
        $iCol = (!isset($aVisibleNames[$iCol])) ? 0 : $iCol;

        return $iCol;
    }
    /**
     * @deprecated use self::getColNames instead
     */
    protected function _getColNames($sId = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getColNames($sId);
    }


    /**
     * Returns array of cotainer DB cols which must be loaded. If id is not
     * passed - all possible containers cols will be returned
     *
     * @param string $sId container id (optional)
     *
     * @return array
     */
    protected function getColNames($sId = null)
    {
        if ($sId === null) {
            $sId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cmpid');
        }

        if ($sId && isset($this->_aColumns[$sId])) {
            return $this->_aColumns[$sId];
        }

        return $this->_aColumns;
    }
    /**
     * @deprecated use self::getIdentColNames instead
     */
    protected function _getIdentColNames() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getIdentColNames();
    }

    /**
     * Returns array of identifiers which are used as identifiers for specific actions
     * in AJAX and further in this processor class
     *
     * @return array
     */
    protected function getIdentColNames()
    {
        $aColNames = $this->_getColNames();
        $aCols = [];
        foreach ($aColNames as $iKey => $aCol) {
            // ident ?
            if ($aCol[4]) {
                $aCols[$iKey] = $aCol;
            }
        }

        return $aCols;
    }
    /**
     * @deprecated use self::getVisibleColNames instead
     */
    protected function _getVisibleColNames() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getVisibleColNames();
    }

    /**
     * Returns array of col names which are requested by AJAX call and will be fetched from DB
     *
     * @return array
     */
    protected function getVisibleColNames()
    {
        $aColNames = $this->_getColNames();
        $aUserCols = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aCols');
        $aVisibleCols = [];

        // user defined some cols to load ?
        if (is_array($aUserCols)) {
            foreach ($aUserCols as $iKey => $sCol) {
                $iCol = (int) str_replace('_', '', $sCol);
                if (isset($aColNames[$iCol]) && !$aColNames[$iCol][4]) {
                    $aVisibleCols[$iCol] = $aColNames[$iCol];
                }
            }
        }

        // no user defined valid cols ? setting defauls ..
        if (!count($aVisibleCols)) {
            foreach ($aColNames as $sName => $aCol) {
                // visible ?
                if ($aCol[1] && !$aColNames[$sName][4]) {
                    $aVisibleCols[$sName] = $aCol;
                }
            }
        }

        return $aVisibleCols;
    }
    /**
     * @deprecated use self::getQueryCols instead
     */
    protected function _getQueryCols() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getQueryCols();
    }

    /**
     * Formats and returns chunk of SQL query string with definition of
     * fields to load from DB
     *
     * @return string
     */
    protected function getQueryCols()
    {
        $sQ = $this->_buildColsQuery($this->_getVisibleColNames(), false) . ", ";
        $sQ .= $this->_buildColsQuery($this->_getIdentColNames());

        return " $sQ ";
    }
    /**
     * @deprecated use self::buildColsQuery instead
     */
    protected function _buildColsQuery($aIdentCols, $blIdentCols = true) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->buildColsQuery($aIdentCols, $blIdentCols);
    }

    /**
     * Builds column selection query
     *
     * @param array $aIdentCols  columns
     * @param bool  $blIdentCols if true, means ident columns part is build
     *
     * @return string
     */
    protected function buildColsQuery($aIdentCols, $blIdentCols = true)
    {
        $sQ = '';
        foreach ($aIdentCols as $iCnt => $aCol) {
            if ($sQ) {
                $sQ .= ', ';
            }

            $sViewTable = $this->_getViewName($aCol[1]);
            if (!$blIdentCols && $this->_isExtendedColumn($aCol[0])) {
                $sQ .= $this->_getExtendedColQuery($sViewTable, $aCol[0], $iCnt);
            } else {
                $sQ .= $sViewTable . '.' . $aCol[0] . ' as _' . $iCnt;
            }
        }

        return $sQ;
    }
    /**
     * @deprecated use self::isExtendedColumn instead
     */
    protected function _isExtendedColumn($sColumn) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->isExtendedColumn($sColumn);
    }

    /**
     * Checks if current column is extended
     * (currently checks if variants must be shown in lists and column name is "oxtitle")
     *
     * @param string $sColumn column name
     *
     * @return bool
     */
    protected function isExtendedColumn($sColumn)
    {
        $blVariantsSelectionParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blVariantsSelection');

        return $this->_blAllowExtColumns && $blVariantsSelectionParameter && $sColumn == 'oxtitle';
    }
    /**
     * @deprecated use self::getExtendedColQuery instead
     */
    protected function _getExtendedColQuery($sViewTable, $sColumn, $iCnt) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getExtendedColQuery($sViewTable, $sColumn, $iCnt);
    }

    /**
     * Returns extended query part for given view/column combination
     * (if variants must be shown in lists and column name is "oxtitle")
     *
     * @param string $sViewTable view name
     * @param string $sColumn    column name
     * @param int    $iCnt       column count
     *
     * @return string
     */
    protected function getExtendedColQuery($sViewTable, $sColumn, $iCnt)
    {
        // multilanguage
        $sVarSelect = "$sViewTable.oxvarselect";

        return " IF( {$sViewTable}.{$sColumn} != '', {$sViewTable}.{$sColumn}, CONCAT((select oxart.{$sColumn} " .
                "from {$sViewTable} as oxart " .
                "where oxart.oxid = {$sViewTable}.oxparentid),', ',{$sVarSelect})) as _{$iCnt}";
    }
    /**
     * @deprecated use self::getSorting instead
     */
    protected function _getSorting() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getSorting();
    }

    /**
     * Formats and returns part of SQL query for sorting
     *
     * @return string
     */
    protected function getSorting()
    {
        return ' order by _' . $this->_getSortCol() . ' ' . $this->_getSortDir() . ' ';
    }
    /**
     * @deprecated use self::getLimit instead
     */
    protected function _getLimit($iStart) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getLimit($iStart);
    }

    /**
     * Returns part of SQL query for limiting number of entries from DB
     *
     * @param int $iStart start position
     *
     * @return string
     */
    protected function getLimit($iStart)
    {
        $iLimit = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("results");
        $iLimit = $iLimit ? $iLimit : $this->_iSqlLimit;

        return " limit $iStart, $iLimit ";
    }
    /**
     * @deprecated use self::getFilter instead
     */
    protected function _getFilter() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getFilter();
    }

    /**
     * Returns part of SQL query for filtering DB data
     *
     * @return string
     */
    protected function getFilter()
    {
        $sQ = '';
        $oConfig = $this->getConfig();
        $aFilter = $oConfig->getRequestParameter('aFilter');
        if (is_array($aFilter) && count($aFilter)) {
            $aCols = $this->_getVisibleColNames();
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $oStr = getStr();

            foreach ($aFilter as $sCol => $sValue) {
                // skipping empty filters
                if ($sValue === '') {
                    continue;
                }

                $iCol = (int) str_replace('_', '', $sCol);
                if (isset($aCols[$iCol])) {
                    if ($sQ) {
                        $sQ .= ' and ';
                    }

                    // escaping special characters
                    $sValue = str_replace(['%', '_'], ['\%', '\_'], $sValue);

                    // possibility to search in the middle ..
                    $sValue = $oStr->preg_replace('/^\*/', '%', $sValue);

                    $sQ .= $this->_getViewName($aCols[$iCol][1]) . '.' . $aCols[$iCol][0];
                    $sQ .= ' like ' . $oDb->Quote('%' . $sValue . '%') . ' ';
                }
            }
        }

        return $sQ;
    }
    /**
     * @deprecated use self::addFilter instead
     */
    protected function _addFilter($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->addFilter($sQ);
    }

    /**
     * Adds filter SQL to current query
     *
     * @param string $sQ query to add filter condition
     *
     * @return string
     */
    protected function addFilter($sQ)
    {
        if ($sQ && ($sFilter = $this->_getFilter())) {
            $sQ .= ((stristr($sQ, 'where') === false) ? 'where' : ' and ') . $sFilter;
        }

        return $sQ;
    }
    /**
     * @deprecated use self::getAll instead
     */
    protected function _getAll($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getAll($sQ);
    }

    /**
     * Returns DB records as plain indexed array
     *
     * @param string $sQ SQL query
     *
     * @return array
     */
    protected function getAll($sQ)
    {
        $aReturn = [];
        $rs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sQ);
        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                $aReturn[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        return $aReturn;
    }
    /**
     * @deprecated use self::getSortDir instead
     */
    protected function _getSortDir() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getSortDir();
    }

    /**
     * Checks user input and returns SQL sorting direction key
     *
     * @return string
     */
    protected function getSortDir()
    {
        $sDir = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('dir');
        if (!in_array($sDir, $this->_aPosDir)) {
            $sDir = $this->_aPosDir[0];
        }

        return $sDir;
    }
    /**
     * @deprecated use self::getStartIndex instead
     */
    protected function _getStartIndex() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getStartIndex();
    }

    /**
     * Returns position from where data must be loaded
     *
     * @return int
     */
    protected function getStartIndex()
    {
        return (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('startIndex');
    }
    /**
     * @deprecated use self::getTotalCount instead
     */
    protected function _getTotalCount($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getTotalCount($sQ);
    }

    /**
     * Returns amount of records which can be found according to passed SQL query
     *
     * @param string $sQ SQL query
     *
     * @return int
     */
    protected function getTotalCount($sQ)
    {
        // TODO: implement caching here

        // we can cache total count ...

        // $sCountCacheKey = md5( $sQ );

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        return (int) \OxidEsales\Eshop\Core\DatabaseProvider::getMaster()->getOne($sQ);
    }
    /**
     * @deprecated use self::getDataFields instead
     */
    protected function _getDataFields($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getDataFields($sQ);
    }

    /**
     * Returns array with DB records
     *
     * @param string $sQ SQL query
     *
     * @return array
     */
    protected function getDataFields($sQ)
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        return \OxidEsales\Eshop\Core\DatabaseProvider::getMaster(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sQ, false);
    }
    /**
     * @deprecated use self::outputResponse instead
     */
    protected function _outputResponse($aData) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->outputResponse($aData);
    }

    /**
     * Outputs JSON encoded data
     *
     * @param array $aData data to output
     */
    protected function outputResponse($aData)
    {
        $this->_output(json_encode($aData));
    }
    /**
     * @deprecated use self::output instead
     */
    protected function _output($sOut) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->output($sOut);
    }

    /**
     * Echoes given string
     *
     * @param string $sOut string to echo
     */
    protected function output($sOut)
    {
        echo $sOut;
    }
    /**
     * @deprecated use self::getViewName instead
     */
    protected function _getViewName($sTable) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getViewName($sTable);
    }

    /**
     * Return the view name of the given table if a view exists, otherwise the table name itself
     *
     * @param string $sTable table name
     *
     * @return string
     */
    protected function getViewName($sTable)
    {
        return getViewName($sTable, \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));
    }
    /**
     * @deprecated use self::getData instead
     */
    protected function _getData($sCountQ, $sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getData($sCountQ, $sQ);
    }

    /**
     * Formats data array which later will be processed by _outputResponse method
     *
     * @param string $sCountQ count query
     * @param string $sQ      data load query
     *
     * @return array
     */
    protected function getData($sCountQ, $sQ)
    {
        $sQ = $this->_addFilter($sQ);
        $sCountQ = $this->_addFilter($sCountQ);

        $aResponse['startIndex'] = $iStart = $this->_getStartIndex();
        $aResponse['sort'] = '_' . $this->_getSortCol();
        $aResponse['dir'] = $this->_getSortDir();

        $iDebug = $this->getConfig()->getConfigParam('iDebug');
        if ($iDebug) {
            $aResponse['countsql'] = $sCountQ;
        }

        $aResponse['records'] = [];

        // skip further execution if no records were found ...
        if (($iTotal = $this->_getTotalCount($sCountQ))) {
            $sQ .= $this->_getSorting();
            $sQ .= $this->_getLimit($iStart);

            if ($iDebug) {
                $aResponse['datasql'] = $sQ;
            }

            $aResponse['records'] = $this->_getDataFields($sQ);
        }

        $aResponse['totalRecords'] = $iTotal;

        return $aResponse;
    }

    /**
     * Marks article seo url as expired
     *
     * @param array $aArtIds article id's
     * @param array $aCatIds ids if categories, which must be removed from oxseo
     *
     * @return null
     */
    public function resetArtSeoUrl($aArtIds, $aCatIds = null)
    {
        if (empty($aArtIds)) {
            return;
        }

        if (!is_array($aArtIds)) {
            $aArtIds = [$aArtIds];
        }

        $sShopId = $this->getConfig()->getShopId();
        foreach ($aArtIds as $sArtId) {
            /** @var \OxidEsales\Eshop\Core\SeoEncoder $oSeoEncoder */
            \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->markAsExpired($sArtId, $sShopId, 1, null, "oxtype='oxarticle'");
        }
    }

    /**
     * Reset output cache
     */
    public function resetContentCache()
    {
        $blDeleteCacheOnLogout = $this->getConfig()->getConfigParam('blClearCacheOnLogout');

        if (!$blDeleteCacheOnLogout) {
            $this->_resetCaches();

            \OxidEsales\Eshop\Core\Registry::getUtils()->oxResetFileCache();
        }
    }

    /**
     * Resets counters values from cache. Resets price category articles, category articles,
     * vendor articles, manufacturer articles count.
     *
     * @param string $sCounterType counter type
     * @param string $sValue       reset value
     */
    public function resetCounter($sCounterType, $sValue = null)
    {
        $blDeleteCacheOnLogout = $this->getConfig()->getConfigParam('blClearCacheOnLogout');

        if (!$blDeleteCacheOnLogout) {
            $myUtilsCount = \OxidEsales\Eshop\Core\Registry::getUtilsCount();
            switch ($sCounterType) {
                case 'priceCatArticle':
                    $myUtilsCount->resetPriceCatArticleCount($sValue);
                    break;
                case 'catArticle':
                    $myUtilsCount->resetCatArticleCount($sValue);
                    break;
                case 'vendorArticle':
                    $myUtilsCount->resetVendorArticleCount($sValue);
                    break;
                case 'manufacturerArticle':
                    $myUtilsCount->resetManufacturerArticleCount($sValue);
                    break;
            }

            $this->_resetContentCache();
        }
    }

    /**
     * Resets content cache.
     */
    protected function _resetContentCache() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
    }
    /**
     * @deprecated use self::resetCaches instead
     */
    protected function _resetCaches() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->resetCaches();
    }

    /**
     * Resets output caches
     */
    protected function resetCaches()
    {
    }
}
