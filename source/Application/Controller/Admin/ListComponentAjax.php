<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
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
        $aColumns = $this->getColNames();
        foreach ($aColumns as $iPos => $aCol) {
            if (isset($aCol[4]) && $aCol[4] == 1 && $sId == $aCol[1] . '.' . $aCol[0]) {
                return Registry::getRequest()->getRequestEscapedParameter('_' . $iPos);
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
     * Empty function, developer should override this method according requirements
     *
     * @return string
     */
    protected function getQuery()
    {
        return '';
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
        return 'select ' . $this->getQueryCols() . $sQ;
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
            ContainerFacade::dispatch(new AfterAdminAjaxRequestProcessedEvent());
        } else {
            $sQAdd = $this->getQuery();

            // formatting SQL queries
            $sQ = $this->getDataQuery($sQAdd);
            $sCountQ = $this->getCountQuery($sQAdd);

            $this->outputResponse($this->getData($sCountQ, $sQ));
        }
    }

    /**
     * Returns column id to sort
     *
     * @return int
     */
    protected function getSortCol()
    {
        $aVisibleNames = $this->getVisibleColNames();
        $iCol = Registry::getRequest()->getRequestEscapedParameter('sort');
        $iCol = $iCol ? ((int) str_replace('_', '', $iCol)) : 0;
        $iCol = (!isset($aVisibleNames[$iCol])) ? 0 : $iCol;

        return $iCol;
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
            $sId = Registry::getRequest()->getRequestEscapedParameter('cmpid');
        }

        if ($sId && isset($this->_aColumns[$sId])) {
            return $this->_aColumns[$sId];
        }

        return $this->_aColumns;
    }

    /**
     * Returns array of identifiers which are used as identifiers for specific actions
     * in AJAX and further in this processor class
     *
     * @return array
     */
    protected function getIdentColNames()
    {
        $aColNames = $this->getColNames();
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
     * Returns array of col names which are requested by AJAX call and will be fetched from DB
     *
     * @return array
     */
    protected function getVisibleColNames()
    {
        $aColNames = $this->getColNames();
        $aUserCols = Registry::getRequest()->getRequestEscapedParameter('aCols');
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
     * Formats and returns chunk of SQL query string with definition of
     * fields to load from DB
     *
     * @return string
     */
    protected function getQueryCols()
    {
        $sQ = $this->buildColsQuery($this->getVisibleColNames(), false) . ", ";
        $sQ .= $this->buildColsQuery($this->getIdentColNames());

        return " $sQ ";
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

            $sViewTable = $this->getViewName($aCol[1]);
            if (!$blIdentCols && $this->isExtendedColumn($aCol[0])) {
                $sQ .= $this->getExtendedColQuery($sViewTable, $aCol[0], $iCnt);
            } else {
                $sQ .= $sViewTable . '.' . $aCol[0] . ' as _' . $iCnt;
            }
        }

        return $sQ;
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
     * Formats and returns part of SQL query for sorting
     *
     * @return string
     */
    protected function getSorting()
    {
        return ' order by _' . $this->getSortCol() . ' ' . $this->getSortDir() . ' ';
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
        $iLimit = (int) Registry::getRequest()->getRequestEscapedParameter("results");
        $iLimit = $iLimit ? $iLimit : $this->_iSqlLimit;

        return " limit $iStart, $iLimit ";
    }

    /**
     * Returns part of SQL query for filtering DB data
     *
     * @return string
     */
    protected function getFilter()
    {
        $sQ = '';
        $aFilter = Registry::getRequest()->getRequestEscapedParameter('aFilter');
        if (is_array($aFilter) && count($aFilter)) {
            $aCols = $this->getVisibleColNames();
            $oDb = DatabaseProvider::getDb();
            $oStr = Str::getStr();

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

                    $sQ .= $this->getViewName($aCols[$iCol][1]) . '.' . $aCols[$iCol][0];
                    $sQ .= ' like ' . $oDb->Quote('%' . $sValue . '%') . ' ';
                }
            }
        }

        return $sQ;
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
        if ($sQ && ($sFilter = $this->getFilter())) {
            $sQ .= ((stristr($sQ, 'where') === false) ? 'where' : ' and ') . $sFilter;
        }

        return $sQ;
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
        $rs = DatabaseProvider::getDb()->select($sQ);
        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                $aReturn[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        return $aReturn;
    }

    /**
     * Checks user input and returns SQL sorting direction key
     *
     * @return string
     */
    protected function getSortDir()
    {
        $sDir = Registry::getRequest()->getRequestEscapedParameter('dir');
        if (!in_array($sDir, $this->_aPosDir)) {
            $sDir = $this->_aPosDir[0];
        }

        return $sDir;
    }

    /**
     * Returns position from where data must be loaded
     *
     * @return int
     */
    protected function getStartIndex()
    {
        return (int) Registry::getRequest()->getRequestEscapedParameter('startIndex');
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
        return (int) DatabaseProvider::getMaster()->getOne($sQ);
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
        return DatabaseProvider::getMaster(
            DatabaseProvider::FETCH_MODE_ASSOC
        )->getAll($sQ);
    }

    /**
     * Outputs JSON encoded data
     *
     * @param array $aData data to output
     */
    protected function outputResponse($aData)
    {
        $this->output(json_encode($aData));
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
     * Return the view name of the given table if a view exists, otherwise the table name itself
     *
     * @param string $sTable table name
     *
     * @return string
     */
    protected function getViewName($sTable)
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        return $tableViewNameGenerator->getViewName($sTable, Registry::getRequest()->getRequestEscapedParameter('editlanguage'));
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
        $sQ = $this->addFilter($sQ);
        $sCountQ = $this->addFilter($sCountQ);

        $aResponse['startIndex'] = $iStart = $this->getStartIndex();
        $aResponse['sort'] = '_' . $this->getSortCol();
        $aResponse['dir'] = $this->getSortDir();

        $debug = ContainerFacade::getParameter('oxid_debug_mode');
        if ($debug) {
            $aResponse['countsql'] = $sCountQ;
        }

        $aResponse['records'] = [];

        // skip further execution if no records were found ...
        if (($iTotal = $this->getTotalCount($sCountQ))) {
            $sQ .= $this->getSorting();
            $sQ .= $this->getLimit($iStart);

            if ($debug) {
                $aResponse['datasql'] = $sQ;
            }

            $aResponse['records'] = $this->getDataFields($sQ);
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

        $sShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();
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
        $blDeleteCacheOnLogout = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blClearCacheOnLogout');

        if (!$blDeleteCacheOnLogout) {
            $this->resetCaches();

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
        $blDeleteCacheOnLogout = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blClearCacheOnLogout');

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

            $this->resetContentCache();
        }
    }

    /**
     * Resets output caches
     */
    protected function resetCaches()
    {
    }
}
