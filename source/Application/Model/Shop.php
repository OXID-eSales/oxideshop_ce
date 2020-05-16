<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\Registry;

/**
 * Shop manager.
 * Performs configuration and object loading or deletion.
 */
class Shop extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /** @var string Name of current class. */
    protected $_sClassName = 'oxshop';

    /** @var array Multi shop tables, set in config. */
    protected $_aMultiShopTables = null;

    /** @var array Query variables. */
    protected $_aQueries = [];

    /** @var array Database tables. */
    protected $_aTables = null;

    /** @var bool Defines if multishop inherits categories. */
    protected $_blMultiShopInheritCategories = false;

    /**
     * Database tables setter.
     *
     * @param array $aTables
     */
    public function setTables($aTables)
    {
        $this->_aTables = $aTables;
    }

    /**
     * Database tables getter.
     *
     * @return array
     */
    public function getTables()
    {
        if (is_null($this->_aTables)) {
            $aTables = $this->formDatabaseTablesArray();
            $this->setTables($aTables);
        }

        return $this->_aTables;
    }

    /**
     * Database queries setter.
     *
     * @param array $aQueries
     */
    public function setQueries($aQueries)
    {
        $this->_aQueries = $aQueries;
    }

    /**
     * Database queries getter.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->_aQueries;
    }

    /**
     * Add a query to query array.
     *
     * @param string $sQuery
     */
    public function addQuery($sQuery)
    {
        $this->_aQueries[] = $sQuery;
    }

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();

        if (!$this->isShopValid()) {
            Registry::getLogger()->error('Shop is not valid');

            return;
        }

        $this->init('oxshops');

        if ($iMax = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('iMaxShopId')) {
            $this->setMaxShopId($iMax);
        }
    }

    /**
     * Sets multi shop tables
     *
     * @param string $aMultiShopTables multi shop tables
     */
    public function setMultiShopTables($aMultiShopTables)
    {
        $this->_aMultiShopTables = $aMultiShopTables;
    }

    /**
     * Get multishop table array
     *
     * @return array
     */
    public function getMultiShopTables()
    {
        if (is_null($this->_aMultiShopTables)) {
            $this->_aMultiShopTables = [];
        }

        return $this->_aMultiShopTables;
    }

    /**
     * (Re)generates shop views
     *
     * @param bool  $multishopInheritCategories Config option blMultishopInherit_oxcategories
     * @param array $mallInherit                Array of config options blMallInherit
     *
     * @return bool is all views generated successfully
     */
    public function generateViews($multishopInheritCategories = false, $mallInherit = null)
    {
        $this->_prepareViewsQueries();
        $blSuccess = $this->_runQueries();

        $this->_cleanInvalidViews();

        return $blSuccess;
    }

    /**
     * Returns default category of the shop.
     *
     * @return string
     */
    public function getDefaultCategory()
    {
        return $this->oxshops__oxdefcat->value;
    }

    /**
     * Returns true if shop in productive mode
     *
     * @return bool
     */
    public function isProductiveMode()
    {
        return (bool) $this->oxshops__oxproductive->value;
    }

    /**
     * Creates view query and adds it to query array.
     *
     * @param string $sTable     Table name
     * @param array  $aLanguages Language array( id => abbreviation )
     */
    public function createViewQuery($sTable, $aLanguages = null)
    {
        $sStart = 'CREATE OR REPLACE SQL SECURITY INVOKER VIEW';

        if (!is_array($aLanguages)) {
            $aLanguages = [null => null];
        }

        foreach ($aLanguages as $iLang => $sLang) {
            $this->addViewLanguageQuery($sStart, $sTable, $iLang, $sLang);
        }
    }

    /**
     * Returns table field name mapping sql section for single language views
     *
     * @param string $sTable Table name
     * @param int    $iLang  Language id
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getViewSelect" in next major
     */
    protected function _getViewSelect($sTable, $iLang) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oMetaData = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $aFields = $oMetaData->getSinglelangFields($sTable, $iLang);
        foreach ($aFields as $sCoreField => $sField) {
            if ($sCoreField !== $sField) {
                $aFields[$sCoreField] = $sField . ' AS ' . $sCoreField;
            }
        }

        return implode(',', $aFields);
    }

    /**
     * Returns table fields sql section for multiple language views
     *
     * @param string $sTable table name
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getViewSelectMultilang" in next major
     */
    protected function _getViewSelectMultilang($sTable) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aFields = [];

        $oMetaData = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $aTables = array_merge([$sTable], $oMetaData->getAllMultiTables($sTable));
        foreach ($aTables as $sTableKey => $sTableName) {
            $aTableFields = $oMetaData->getFields($sTableName);
            foreach ($aTableFields as $sCoreField => $sField) {
                if (!isset($aFields[$sCoreField])) {
                    $aFields[$sCoreField] = $sField;
                }
            }
        }

        return implode(',', $aFields);
    }

    /**
     * Returns all language table view JOIN section
     *
     * @param string $sTable table name
     *
     * @return string $sSQL
     * @deprecated underscore prefix violates PSR12, will be renamed to "getViewJoinAll" in next major
     */
    protected function _getViewJoinAll($sTable) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sJoin = ' ';
        $oMetaData = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        $aTables = $oMetaData->getAllMultiTables($sTable);
        if (count($aTables)) {
            foreach ($aTables as $sTableKey => $sTableName) {
                $sJoin .= "LEFT JOIN {$sTableName} USING (OXID) ";
            }
        }

        return $sJoin;
    }

    /**
     * Returns language table view JOIN section
     *
     * @param string $sTable table name
     * @param int    $iLang  language id
     *
     * @return string $sSQL
     * @deprecated underscore prefix violates PSR12, will be renamed to "getViewJoinLang" in next major
     */
    protected function _getViewJoinLang($sTable, $iLang) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sJoin = ' ';
        $sLangTable = getLangTableName($sTable, $iLang);
        if ($sLangTable && $sLangTable !== $sTable) {
            $sJoin .= "LEFT JOIN {$sLangTable} USING (OXID) ";
        }

        return $sJoin;
    }

    /**
     * Gets all invalid views and drops them from database
     * @deprecated underscore prefix violates PSR12, will be renamed to "cleanInvalidViews" in next major
     */
    protected function _cleanInvalidViews() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oLang = Registry::getLang();
        $aLanguages = $oLang->getLanguageIds($this->getId());

        $aMultilangTables = Registry::getLang()->getMultiLangTables();
        $aMultishopTables = $this->getMultiShopTables();

        $oLang = Registry::getLang();
        $aAllShopLanguages = $oLang->getAllShopLanguageIds();

        $oViewsValidator = oxNew(\OxidEsales\Eshop\Application\Model\ShopViewValidator::class);

        $oViewsValidator->setShopId($this->getId());
        $oViewsValidator->setLanguages($aLanguages);
        $oViewsValidator->setAllShopLanguages($aAllShopLanguages);
        $oViewsValidator->setMultiLangTables($aMultilangTables);
        $oViewsValidator->setMultiShopTables($aMultishopTables);

        $aViews = $oViewsValidator->getInvalidViews();

        foreach ($aViews as $sView) {
            $oDb->execute('DROP VIEW IF EXISTS `' . $sView . '`');
        }
    }

    /**
     * Creates all view queries and adds them in query array
     * @deprecated underscore prefix violates PSR12, will be renamed to "prepareViewsQueries" in next major
     */
    protected function _prepareViewsQueries() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oLang = Registry::getLang();
        $aLanguages = $oLang->getLanguageIds($this->getId());

        $aMultilangTables = Registry::getLang()->getMultiLangTables();
        $aTables = $this->getTables();
        foreach ($aTables as $sTable) {
            $this->createViewQuery($sTable);
            if (in_array($sTable, $aMultilangTables)) {
                $this->createViewQuery($sTable, $aLanguages);
            }
        }
    }

    /**
     * Adds view language query to query array.
     *
     * @param string $queryStart
     * @param string $table
     * @param int    $languageId
     * @param string $languageAbbr
     */
    protected function addViewLanguageQuery($queryStart, $table, $languageId, $languageAbbr)
    {
        $sLangAddition = $languageAbbr === null ? '' : "_{$languageAbbr}";

        $sViewTable = "oxv_{$table}{$sLangAddition}";

        if ($languageAbbr === null) {
            $sFields = $this->_getViewSelectMultilang($table);
            $sJoin = $this->_getViewJoinAll($table);
        } else {
            $sFields = $this->_getViewSelect($table, $languageId);
            $sJoin = $this->_getViewJoinLang($table, $languageId);
        }

        if ("" === $sFields) {
            Registry::getLogger()->error("View for $table can not be generated, Please check if table exists");
            return;
        }

        $sQuery = "{$queryStart} `{$sViewTable}` AS SELECT {$sFields} FROM {$table}{$sJoin}";
        $this->addQuery($sQuery);
    }

    /**
     * Runs stored queries
     * Returns false when any of the queries fail, otherwise return true
     *
     * @return bool
     * @deprecated underscore prefix violates PSR12, will be renamed to "runQueries" in next major
     */
    protected function _runQueries() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $aQueries = $this->getQueries();
        $bSuccess = true;
        foreach ($aQueries as $sQuery) {
            try {
                $oDb->execute($sQuery);
            } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
                \OxidEsales\Eshop\Core\Registry::getLogger()->error($exception->getMessage(), [$exception]);
                $bSuccess = false;
            }
        }

        return $bSuccess;
    }

    /**
     * Forms array of tables which are available.
     *
     * @return array
     */
    protected function formDatabaseTablesArray()
    {
        $multilanguageTables = Registry::getLang()->getMultiLangTables();

        return array_unique($multilanguageTables);
    }

    /**
     * Checks whether current shop is valid.
     *
     * @return bool
     */
    protected function isShopValid()
    {
        return true;
    }
}
