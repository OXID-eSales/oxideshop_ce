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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Class for handling database related operations
 *
 */
class oxDbMetaDataHandler extends oxSuperCfg
{

    /**
     *
     * @var array
     */
    protected $_aDbTablesFields = null;


    /**
     *
     * @var array
     */
    protected $_aTables = null;

    /**
     *
     * @var int
     */
    protected $_iCurrentMaxLangId;

    /**
     *
     * @var array Tables which should be skipped from resetting
     */
    protected $_aSkipTablesOnReset = array("oxcountry");

    /**
     * When creating views, always use those fields from core table.
     *
     * @var array
     */
    protected $aForceOriginalFields = array('OXID');

    /**
     *  Get table fields
     *
     * @param string $sTableName  table name
     *
     * @return array
     */
    public function getFields($sTableName)
    {
        $aFields = array();
        $aRawFields = oxDb::getDb()->MetaColumns($sTableName);
        if (is_array($aRawFields)) {
            foreach ($aRawFields as $oField) {
                $aFields[$oField->name] = "{$sTableName}.{$oField->name}";
            }
        }

        return $aFields;
    }

    /**
     * Check if table exists
     *
     * @param string $sTableName table name
     *
     * @return bool
     */
    public function tableExists($sTableName)
    {
        $oDb = oxDb::getDb();
        $aTables = $oDb->getAll("show tables like " . $oDb->quote($sTableName));

        return count($aTables) > 0;
    }

    /**
     * Check if field exists in table
     *
     * @param string $sFieldName field name
     * @param string $sTableName table name
     *
     * @return bool
     */
    public function fieldExists($sFieldName, $sTableName)
    {
        $aTableFields = $this->getFields($sTableName);
        $sTableName = strtoupper($sTableName);
        if (is_array($aTableFields)) {
            $sFieldName = strtoupper($sFieldName);
            $aTableFields = array_map('strtoupper', $aTableFields);
            if (in_array("{$sTableName}.{$sFieldName}", $aTableFields)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get the indices of a table
     *
     * @param string $tableName The name of the table for which we want the
     *
     * @return array The indices of the given table
     */
    public function getIndices($tableName)
    {
        $result = array();

        if ($this->tableExists($tableName)) {
            $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll("SHOW INDEX FROM $tableName");
        }

        return $result;
    }

    /**
     * Check, if the table has an index with the given name
     *
     * @param string $indexName The name of the index we want to check
     * @param string $tableName The table to check for the index
     *
     * @return bool Has the table the given index?
     */
    public function hasIndex($indexName, $tableName)
    {
        $result = false;

        foreach ($this->getIndices($tableName) as $index) {
            if ($indexName === $index['Column_name']) {
                $result = true;
            }
        }

        return $result;
    }


    /**
     * Get all tables names from db. Views tables are not included in
     * this list.
     *
     * @return array
     */
    public function getAllTables()
    {
        if (empty($this->_aTables)) {

            $aTables = oxDb::getDb()->getAll("show tables");

            foreach ($aTables as $aTableInfo) {
                $sTableName = $aTableInfo[0];

                $this->_aTables[] = $aTableInfo[0];
            }
        }

        return $this->_aTables;
    }

    /**
     * return all DB tables for the language sets
     *
     * @param string $sTable table name to check
     *
     * @return array
     */
    public function getAllMultiTables($sTable)
    {
        $aMLTables = array();
        foreach (array_keys(oxRegistry::getLang()->getLanguageIds()) as $iLangId) {
            $sLangTableName = getLangTableName($sTable, $iLangId);
            if ($sTable != $sLangTableName && !in_array($sLangTableName, $aMLTables)) {
                $aMLTables[] = $sLangTableName;
            }
        }

        return $aMLTables;
    }

    /**
     * Get sql for new multi-language table set creation
     *
     * @param string $sTable core table name
     * @param string $iLang  language id
     *
     * @return string
     *
     */
    protected function _getCreateTableSetSql($sTable, $iLang)
    {
        $sTableSet = getLangTableName($sTable, $iLang);

        $aRes = oxDb::getDb()->getAll("show create table {$sTable}");
        $sSql = "CREATE TABLE `{$sTableSet}` (" .
                "`OXID` char(32) COLLATE latin1_general_ci NOT NULL, " .
                "PRIMARY KEY (`OXID`)" .
                ") " . strstr($aRes[0][1], 'ENGINE=');

        return $sSql;
    }

    /**
     * Get sql for new multi-language field creation
     *
     * @param string $sTable     core table name
     * @param string $sField     field name
     * @param string $sNewField  new field name
     * @param string $sPrevField previous field in table
     * @param string $sTableSet  table to change (if not set take core table)
     *
     * @return string
     */
    public function getAddFieldSql($sTable, $sField, $sNewField, $sPrevField, $sTableSet = null)
    {
        if (!$sTableSet) {
            $sTableSet = $sTable;
        }
        $aRes = oxDb::getDb()->getAll("show create table {$sTable}");
        $sTableSql = $aRes[0][1];

        // removing comments;
        $sTableSql = preg_replace('/COMMENT \\\'.*?\\\'/', '', $sTableSql);
        preg_match("/.*,\s+(['`]?" . preg_quote($sField, '/') . "['`]?\s+[^,]+),.*/", $sTableSql, $aMatch);
        $sFieldSql = $aMatch[1];

        $sSql = "";
        if (!empty($sFieldSql)) {
            $sFieldSql = preg_replace("/" . preg_quote($sField, '/') . "/", $sNewField, $sFieldSql);
            $sSql = "ALTER TABLE `$sTableSet` ADD " . $sFieldSql;
            if ($this->tableExists($sTableSet) && $this->fieldExists($sPrevField, $sTableSet)) {
                $sSql .= " AFTER `$sPrevField`";
            }
        }

        return $sSql;
    }


    /**
     * Get sql for new multi-language field index creation
     *
     * @param string $sTable    core table name
     * @param string $sField    field name
     * @param string $sNewField new field name
     * @param string $sTableSet table to change (if not set take core table)
     *
     * @return string
     */
    public function getAddFieldIndexSql($sTable, $sField, $sNewField, $sTableSet = null)
    {
        $aRes = oxDb::getDb()->getAll("show create table {$sTable}");

        $sTableSql = $aRes[0][1];

        preg_match_all("/([\w]+\s+)?\bKEY\s+(`[^`]+`)?\s*\([^)]+\)/iU", $sTableSql, $aMatch);
        $aIndex = $aMatch[0];

        $blUsingTableSet = $sTableSet ? true : false;

        if (!$sTableSet) {
            $sTableSet = $sTable;
        }

        $aIndexSql = array();
        $aSql = array();
        if (count($aIndex)) {
            foreach ($aIndex as $sIndexSql) {
                if (preg_match("/\([^)]*\b" . $sField . "\b[^)]*\)/i", $sIndexSql)) {

                    //removing index name - new will be added automaticly
                    $sIndexSql = preg_replace("/(.*\bKEY\s+)`[^`]+`/", "$1", $sIndexSql);

                    if ($blUsingTableSet) {
                        // replacing multiple fields to one (#3269)
                        $sIndexSql = preg_replace("/\([^\)]+\)/", "(`$sNewField`)", $sIndexSql);
                    } else {
                        //replacing previous field name with new one
                        $sIndexSql = preg_replace("/\b" . $sField . "\b/", $sNewField, $sIndexSql);
                    }

                    $aIndexSql[] = "ADD " . $sIndexSql;
                }
            }
            if (count($aIndexSql)) {
                $aSql = array("ALTER TABLE `$sTableSet` " . implode(", ", $aIndexSql));
            }
        }

        return $aSql;
    }

    /**
     * Get max language ID used in shop. For checking is used table "oxarticle"
     * field "oxtitle"
     *
     * @return int
     */
    public function getCurrentMaxLangId()
    {
        if (isset($this->_iCurrentMaxLangId)) {
            return $this->_iCurrentMaxLangId;
        }

        $sTable = $sTableSet = "oxarticles";
        $sField = $sFieldSet = "oxtitle";
        $iLang = 0;
        while ($this->tableExists($sTableSet) && $this->fieldExists($sFieldSet, $sTableSet)) {
            $iLang++;
            $sTableSet = getLangTableName($sTable, $iLang);
            $sFieldSet = $sField . '_' . $iLang;
        }

        $this->_iCurrentMaxLangId = --$iLang;

        return $this->_iCurrentMaxLangId;
    }

    /**
     * Get next available language ID
     *
     * @return int
     */
    public function getNextLangId()
    {
        return $this->getCurrentMaxLangId() + 1;
    }

    /**
     * Get table multi-language fields
     *
     * @param string $sTable table name
     *
     * @return array
     */
    public function getMultilangFields($sTable)
    {
        $aFields = $this->getFields($sTable);
        $aMultiLangFields = array();

        foreach ($aFields as $sField) {
            if (preg_match("/({$sTable}\.)?(?<field>.+)_1$/", $sField, $aMatches)) {
                $aMultiLangFields[] = $aMatches['field'];
            }
        }

        return $aMultiLangFields;
    }

    /**
     * Get single language fields
     *
     * @param string $sTable table name
     * @param int    $iLang  language id
     *
     * @return array
     */
    public function getSinglelangFields($sTable, $iLang)
    {
        $sLangTable = getLangTableName($sTable, $iLang);

        $aBaseFields = $this->getFields($sTable);
        $aLangFields = $this->getFields($sLangTable);

        //Some fields (for example OXID) must be taken from core table.
        $aLangFields = $this->filterCoreFields($aLangFields);

        $aFields = array_merge($aBaseFields, $aLangFields);
        $aSingleLangFields = array();

        foreach ($aFields as $sFieldName => $sField) {
            if (preg_match("/(({$sTable}|{$sLangTable})\.)?(?<field>.+)_(?<lang>[0-9]+)$/", $sField, $aMatches)) {
                if ($aMatches['lang'] == $iLang) {
                    $aSingleLangFields[$aMatches['field']] = $sField;
                }
            } else {
                $aSingleLangFields[$sFieldName] = $sField;
            }
        }

        return $aSingleLangFields;
    }

    /**
     * Add new multi-languages fields to table. Duplicates all multi-language
     * fields and fields indexes with next available language ID
     *
     * @param string $sTable table name
     */
    public function addNewMultilangField($sTable)
    {
        $iNewLang = $this->getNextLangId();
        $this->ensureMultiLanguageFields($sTable, $iNewLang);
    }

    /**
     * Resetting all multi-language fields with specific language id
     * to default value in selected table
     *
     * @param int    $iLangId    Language id
     * @param string $sTableName Table name
     *
     * @return null
     */
    public function resetMultilangFields($iLangId, $sTableName)
    {
        $iLangId = (int) $iLangId;

        if ($iLangId === 0) {
            return;
        }

        $aSql = array();

        $aFields = $this->getMultilangFields($sTableName);
        if (is_array($aFields) && count($aFields) > 0) {
            foreach ($aFields as $sFieldName) {
                $sFieldName = $sFieldName . "_" . $iLangId;

                if ($this->fieldExists($sFieldName, $sTableName)) {
                    //resetting field value to default
                    $aSql[] = "UPDATE {$sTableName} SET {$sFieldName} = DEFAULT;";
                }
            }
        }

        if (!empty($aSql)) {
            $this->executeSql($aSql);
        }
    }

    /**
     * Add new language to database. Scans all tables and adds new
     * multi-language fields
     */
    public function addNewLangToDb()
    {
        //reset max count
        $this->_iCurrentMaxLangId = null;

        $aTable = $this->getAllTables();

        foreach ($aTable as $sTableName) {
            $this->addNewMultilangField($sTableName);
        }

        //updating views
        $this->updateViews();
    }

    /**
     * Resetting all multi-language fields with specific language id
     * to default value in all tables. Only if language ID > 0.
     *
     * @param int $iLangId Language id
     *
     * @return null
     */
    public function resetLanguage($iLangId)
    {
        if ((int) $iLangId === 0) {
            return;
        }

        $aTables = $this->getAllTables();

        // removing tables which does not requires reset
        foreach ($this->_aSkipTablesOnReset as $sSkipTable) {

            if (($iSkipId = array_search($sSkipTable, $aTables)) !== false) {
                unset($aTables[$iSkipId]);
            }
        }

        foreach ($aTables as $sTableName) {
            $this->resetMultilangFields($iLangId, $sTableName);
        }
    }

    /**
     * Executes array of sql strings
     *
     * @param array $aSql SQL query array
     */
    public function executeSql($aSql)
    {
        $oDb = oxDb::getDb();

        if (is_array($aSql) && !empty($aSql)) {
            foreach ($aSql as $sSql) {
                $sSql = trim($sSql);
                if (!empty($sSql)) {
                    $oDb->execute($sSql);
                }
            }
        }
    }

    /**
     * Updates all views
     *
     * @param array $aTables array of DB table name that can store different data per shop like oxArticle
     *
     * @return bool
     */
    public function updateViews($aTables = null)
    {
        set_time_limit(0);

        $oDb = oxDb::getDb();
        $oConfig = oxRegistry::getConfig();

        $this->safeGuardAdditionalMultiLanguageTables();

        $aShops = $oDb->getAll("select * from oxshops");

        $aTables = $aTables ? $aTables : $oConfig->getConfigParam('aMultiShopTables');

        $bSuccess = true;
        foreach ($aShops as $aShop) {
            $sShopId = $aShop[0];
            $oShop = oxNew('oxshop');
            $oShop->load($sShopId);
            $oShop->setMultiShopTables($aTables);
            $aMallInherit = array();
            foreach ($aTables as $sTable) {
                $aMallInherit[$sTable] = $oConfig->getShopConfVar('blMallInherit_' . $sTable, $sShopId);
            }
            if (!$oShop->generateViews(false, $aMallInherit) && $bSuccess) {
                $bSuccess = false;
            }
        }

        return $bSuccess;
    }

    /**
     * Make sure that e.g. OXID is always used from core table when creating views.
     * Otherwise we might have unwanted side effects from rows with OXIDs null in view tables.
     *
     * @param $fields Language fields array we need to filter for core fields.
     *
     * @return array
     */
    protected function filterCoreFields($aFields)
    {
        foreach ($this->aForceOriginalFields as $aFieldname) {
            if (array_key_exists($aFieldname, $aFields)) {
                unset($aFields[$aFieldname]);
            }
        }
        return $aFields;
    }

    /**
     * Ensure that all *_set* tables for all tables in config parameter 'aMultiLangTables'
     * are created.
     *
     * @return null
     */
    protected function safeGuardAdditionalMultiLanguageTables()
    {
        $iMaxLang = $this->getCurrentMaxLangId();
        $aMultiLanguageTables = $this->getConfig()->getConfigParam('aMultiLangTables');

        if (!is_array($aMultiLanguageTables) || empty($aMultiLanguageTables)) {
            return; //nothing to do
        }

        foreach ($aMultiLanguageTables as $sTable) {
            if ($this->tableExists($sTable)) {
                //We start with language id 1 and rely on that all fields for language 0 exists.
                //For language id 0 we have e.g. OXTITLE and logic here would expect it to
                //be OXTITLE_0, add that as new field, leading to incorrect data in views later on.
                for ($i=1;$i<=$iMaxLang;$i++) {
                    $this->ensureMultiLanguageFields($sTable, $i);
                }
            }
        }
    }

    /**
     * Make sure that all *_set* tables with all required multilanguage fields are created.
     *
     * @param $sTable
     * @param $iLanguagaId
     *
     * @return null
     */
    protected function ensureMultiLanguageFields($sTable, $iLanguageId)
    {
        $aFields = $this->getMultilangFields($sTable);

        $sTableSet = getLangTableName($sTable, $iLanguageId);
        if (!$this->tableExists($sTableSet)) {
            $aSql[] = $this->_getCreateTableSetSql($sTable, $iLanguageId);
        }

        if (is_array($aFields) && count($aFields) > 0) {
            foreach ($aFields as $sField) {
                $sNewFieldName = $sField . "_" . $iLanguageId;
                if ($iLanguageId > 1) {
                    $iPrevLang = $iLanguageId - 1;
                    $sPrevField = $sField . '_' . $iPrevLang;
                } else {
                    $sPrevField = $sField;
                }

                if (!$this->tableExists($sTableSet) || !$this->fieldExists($sNewFieldName, $sTableSet)) {

                    //getting add field sql
                    $aSql[] = $this->getAddFieldSql($sTable, $sField, $sNewFieldName, $sPrevField, $sTableSet);

                    //getting add index sql on added field
                    $aSql = array_merge($aSql, (array) $this->getAddFieldIndexSql($sTable, $sField, $sNewFieldName, $sTableSet));
                }
            }
        }

        $this->executeSql($aSql);
    }
}
