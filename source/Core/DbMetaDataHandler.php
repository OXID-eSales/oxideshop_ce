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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;
use oxDb;
use oxSuperCfg;

/**
 * Class for handling database related operations
 *
 */
class DbMetaDataHandler extends oxSuperCfg
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
    protected $forceOriginalFields = array('OXID');

    /**
     *  Get table fields
     *
     * @param string $tableName  table name
     *
     * @return array
     */
    public function getFields($tableName)
    {
        $fields = array();
        $rawFields = oxDb::getDb()->MetaColumns($tableName);
        if (is_array($rawFields)) {
            foreach ($rawFields as $field) {
                $fields[$field->name] = "{$tableName}.{$field->name}";
            }
        }

        return $fields;
    }

    /**
     * Check if table exists
     *
     * @param string $tableName table name
     *
     * @return bool
     */
    public function tableExists($tableName)
    {
        $db = oxDb::getDb();
        $tables = $db->getAll("show tables like " . $db->quote($tableName));

        return count($tables) > 0;
    }

    /**
     * Check if field exists in table
     *
     * @param string $fieldName field name
     * @param string $tableName table name
     *
     * @return bool
     */
    public function fieldExists($fieldName, $tableName)
    {
        $tableFields = $this->getFields($tableName);
        $tableName = strtoupper($tableName);
        if (is_array($tableFields)) {
            $fieldName = strtoupper($fieldName);
            $tableFields = array_map('strtoupper', $tableFields);
            if (in_array("{$tableName}.{$fieldName}", $tableFields)) {
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
        $result = [];

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
     * Get the index of a given table by its name
     *
     * @param string $indexName The name of the index
     * @param string $tableName The name of the table from which we want the index
     *
     * @return null|array The index with the given name
     */
    public function getIndexByName($indexName, $tableName)
    {
        $indices = $this->getIndices($tableName);

        $result = null;

        foreach ($indices as $index) {
            if ($indexName === $index['Column_name']) {
                $result = $index;
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
            $tables = oxDb::getDb()->getAll("show tables");

            foreach ($tables as $tableInfo) {
                if ($this->validateTableName($tableInfo[0])) {
                    $this->_aTables[] = $tableInfo[0];
                }
            }
        }

        return $this->_aTables;
    }

    /**
     * return all DB tables for the language sets
     *
     * @param string $table table name to check
     *
     * @return array
     */
    public function getAllMultiTables($table)
    {
        $mLTables = array();
        foreach (array_keys(oxRegistry::getLang()->getLanguageIds()) as $langId) {
            $langTableName = getLangTableName($table, $langId);
            if ($table != $langTableName && !in_array($langTableName, $mLTables)) {
                $mLTables[] = $langTableName;
            }
        }

        return $mLTables;
    }

    /**
     * Get sql for new multi-language table set creation
     *
     * @param string $table core table name
     * @param string $lang  language id
     *
     * @return string
     *
     */
    protected function _getCreateTableSetSql($table, $lang)
    {
        $tableSet = getLangTableName($table, $lang);

        $res = oxDb::getDb()->getAll("show create table {$table}");

        $collation = $this->getConfig()->isUtf() ? '' : 'COLLATE latin1_general_ci';
        return "CREATE TABLE `{$tableSet}` (" .
                "`OXID` char(32) $collation NOT NULL, " .
                "PRIMARY KEY (`OXID`)" .
                ") " . strstr($res[0][1], 'ENGINE=');
    }

    /**
     * Get sql for new multi-language field creation
     *
     * @param string $table     core table name
     * @param string $field     field name
     * @param string $newField  new field name
     * @param string $prevField previous field in table
     * @param string $tableSet  table to change (if not set take core table)
     *
     * @return string
     */
    public function getAddFieldSql($table, $field, $newField, $prevField, $tableSet = null)
    {
        if (!$tableSet) {
            $tableSet = $table;
        }
        $res = oxDb::getDb()->getAll("show create table {$table}");
        $tableSql = $res[0][1];

        // removing comments;
        $tableSql = preg_replace('/COMMENT \\\'.*?\\\'/', '', $tableSql);
        preg_match("/.*,\s+(['`]?" . preg_quote($field, '/') . "['`]?\s+[^,]+),.*/", $tableSql, $match);
        $fieldSql = $match[1];

        $sql = "";
        if (!empty($fieldSql)) {
            $fieldSql = preg_replace("/" . preg_quote($field, '/') . "/", $newField, $fieldSql);
            $sql = "ALTER TABLE `$tableSet` ADD " . $fieldSql;
            if ($this->tableExists($tableSet) && $this->fieldExists($prevField, $tableSet)) {
                $sql .= " AFTER `$prevField`";
            }
        }

        return $sql;
    }


    /**
     * Get sql for new multi-language field index creation
     *
     * @param string $table    core table name
     * @param string $field    field name
     * @param string $newField new field name
     * @param string $tableSet table to change (if not set take core table)
     *
     * @return string
     */
    public function getAddFieldIndexSql($table, $field, $newField, $tableSet = null)
    {
        $res = oxDb::getDb()->getAll("show create table {$table}");

        $tableSql = $res[0][1];

        preg_match_all("/([\w]+\s+)?\bKEY\s+(`[^`]+`)?\s*\([^)]+(\(\d++\))*\)/iU", $tableSql, $match);
        $index = $match[0];

        $usingTableSet = $tableSet ? true : false;

        if (!$tableSet) {
            $tableSet = $table;
        }

        $indexQueries = array();
        $sql = array();
        if (count($index)) {
            foreach ($index as $key => $indexQuery) {
                if (preg_match("/\([^)]*\b" . $field . "\b[^)]*\)/i", $indexQuery)) {
                    //removing index name - new will be added automaticly
                    $indexQuery = preg_replace("/(.*\bKEY\s+)`[^`]+`/", "$1", $indexQuery);

                    if ($usingTableSet) {
                        // replacing multiple fields to one (#3269)
                        $indexQuery = preg_replace("/\([^\)]+\)+/", "(`$newField`{$match[3][$key]})", $indexQuery);
                    } else {
                        //replacing previous field name with new one
                        $indexQuery = preg_replace("/\b" . $field . "\b/", $newField, $indexQuery);
                    }
                    $indexQueries[] = "ADD " . $indexQuery;
                }
            }
            if (count($indexQueries)) {
                $sql = array("ALTER TABLE `$tableSet` " . implode(", ", $indexQueries));
            }
        }

        return $sql;
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

        $table = $tableSet = "oxarticles";
        $field = $fieldSet = "oxtitle";
        $lang = 0;
        while ($this->tableExists($tableSet) && $this->fieldExists($fieldSet, $tableSet)) {
            $lang++;
            $tableSet = getLangTableName($table, $lang);
            $fieldSet = $field . '_' . $lang;
        }

        return $this->_iCurrentMaxLangId = --$lang;
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
     * @param string $table table name
     *
     * @return array
     */
    public function getMultilangFields($table)
    {
        $fields = $this->getFields($table);
        $multiLangFields = array();

        foreach ($fields as $field) {
            if (preg_match("/({$table}\.)?(?<field>.+)_1$/", $field, $matches)) {
                $multiLangFields[] = $matches['field'];
            }
        }

        return $multiLangFields;
    }

    /**
     * Get single language fields
     *
     * @param string $table table name
     * @param int    $lang  language id
     *
     * @return array
     */
    public function getSinglelangFields($table, $lang)
    {
        $langTable = getLangTableName($table, $lang);

        $baseFields = $this->getFields($table);
        $langFields = $this->getFields($langTable);

        //Some fields (for example OXID) must be taken from core table.
        $langFields = $this->filterCoreFields($langFields);

        $fields = array_merge($baseFields, $langFields);
        $singleLangFields = array();

        foreach ($fields as $fieldName => $field) {
            if (preg_match("/(({$table}|{$langTable})\.)?(?<field>.+)_(?<lang>[0-9]+)$/", $field, $matches)) {
                if ($matches['lang'] == $lang) {
                    $singleLangFields[$matches['field']] = $field;
                }
            } else {
                $singleLangFields[$fieldName] = $field;
            }
        }

        return $singleLangFields;
    }

    /**
     * Add new multi-languages fields to table. Duplicates all multi-language
     * fields and fields indexes with next available language ID
     *
     * @param string $table table name
     */
    public function addNewMultilangField($table)
    {
        $newLang = $this->getNextLangId();

        $this->ensureMultiLanguageFields($table, $newLang);
    }

    /**
     * Ensure, that all multi language fields of the given table are present.
     *
     * @param string $table The table we want to assure, that the multi language fields are present.
     */
    public function ensureAllMultiLanguageFields($table)
    {
        $max = $this->getCurrentMaxLangId();

        for ($index = 1; $index <= $max; $index++) {
            $this->ensureMultiLanguageFields($table, $index);
        }
    }

    /**
     * Resetting all multi-language fields with specific language id
     * to default value in selected table
     *
     * @param int    $langId    Language id
     * @param string $tableName Table name
     *
     * @return null
     */
    public function resetMultilangFields($langId, $tableName)
    {
        $langId = (int) $langId;

        if ($langId === 0) {
            return;
        }

        $sql = array();

        $fields = $this->getMultilangFields($tableName);
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $fieldName) {
                $fieldName = $fieldName . "_" . $langId;

                if ($this->fieldExists($fieldName, $tableName)) {
                    //resetting field value to default
                    $sql[] = "UPDATE {$tableName} SET {$fieldName} = DEFAULT;";
                }
            }
        }

        if (!empty($sql)) {
            $this->executeSql($sql);
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

        $table = $this->getAllTables();

        foreach ($table as $tableName) {
            $this->addNewMultilangField($tableName);
        }

        //updating views
        $this->updateViews();
    }

    /**
     * Resetting all multi-language fields with specific language id
     * to default value in all tables. Only if language ID > 0.
     *
     * @param int $langId Language id
     *
     * @return null
     */
    public function resetLanguage($langId)
    {
        if ((int) $langId === 0) {
            return;
        }

        $tables = $this->getAllTables();

        // removing tables which does not requires reset
        foreach ($this->_aSkipTablesOnReset as $skipTable) {
            if (($skipId = array_search($skipTable, $tables)) !== false) {
                unset($tables[$skipId]);
            }
        }

        foreach ($tables as $tableName) {
            $this->resetMultilangFields($langId, $tableName);
        }
    }

    /**
     * Executes array of sql strings
     *
     * @param array $queries SQL query array
     */
    public function executeSql($queries)
    {
        $db = oxDb::getDb();

        if (is_array($queries) && !empty($queries)) {
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $db->execute($query);
                }
            }
        }
    }

    /**
     * Updates all views
     *
     * @param array $tables array of DB table name that can store different data per shop like oxArticle
     *
     * @return bool
     */
    public function updateViews($tables = null)
    {
        set_time_limit(0);

        $db = oxDb::getDb();
        $config = oxRegistry::getConfig();

        $configFile = oxRegistry::get('oxConfigFile');
        $originalSkipViewUsageStatus = $configFile->getVar('blSkipViewUsage');
        $config->setConfigParam('blSkipViewUsage', 1);

        $this->safeGuardAdditionalMultiLanguageTables();

        $shops = $db->getAll("select * from oxshops");

        $tables = $tables ? $tables : $config->getConfigParam('aMultiShopTables');

        $success = true;
        foreach ($shops as $shopValues) {
            $shopId = $shopValues[0];
            $shop = oxNew('oxShop');
            $shop->load($shopId);
            $shop->setMultiShopTables($tables);
            $mallInherit = array();
            foreach ($tables as $table) {
                $mallInherit[$table] = $config->getShopConfVar('blMallInherit_' . $table, $shopId);
            }
            if (!$shop->generateViews(false, $mallInherit) && $success) {
                $success = false;
            }
        }

        $config->setConfigParam('blSkipViewUsage', $originalSkipViewUsageStatus);

        return $success;
    }

    /**
     * Make sure that e.g. OXID is always used from core table when creating views.
     * Otherwise we might have unwanted side effects from rows with OXIDs null in view tables.
     *
     * @param $fields Language fields array we need to filter for core fields.
     *
     * @return array
     */
    protected function filterCoreFields($fields)
    {
        foreach ($this->forceOriginalFields as $fieldname) {
            if (array_key_exists($fieldname, $fields)) {
                unset($fields[$fieldname]);
            }
        }
        return $fields;
    }

    /**
     * Ensure that all *_set* tables for all tables in config parameter 'aMultiLangTables'
     * are created.
     *
     * @return null
     */
    protected function safeGuardAdditionalMultiLanguageTables()
    {
        $maxLang = $this->getCurrentMaxLangId();
        $multiLanguageTables = $this->getConfig()->getConfigParam('aMultiLangTables');

        if (!is_array($multiLanguageTables) || empty($multiLanguageTables)) {
            return; //nothing to do
        }

        foreach ($multiLanguageTables as $table) {
            if ($this->tableExists($table)) {
                //We start with language id 1 and rely on that all fields for language 0 exists.
                //For language id 0 we have e.g. OXTITLE and logic here would expect it to
                //be OXTITLE_0, add that as new field, leading to incorrect data in views later on.
                for ($i=1; $i<=$maxLang; $i++) {
                    $this->ensureMultiLanguageFields($table, $i);
                }
            }
        }
    }

    /**
     * Make sure that all *_set* tables with all required multilanguage fields are created.
     *
     * @param $table
     * @param $languagaId
     *
     * @return null
     */
    protected function ensureMultiLanguageFields($table, $languageId)
    {
        $fields = $this->getMultilangFields($table);
        $sql = array();

        $tableSet = getLangTableName($table, $languageId);
        if (!$this->tableExists($tableSet)) {
            $sql[] = $this->_getCreateTableSetSql($table, $languageId);
        }

        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {
                $newFieldName = $field . "_" . $languageId;
                if ($languageId > 1) {
                    $previousLanguage = $languageId - 1;
                    $previousField = $field . '_' . $previousLanguage;
                } else {
                    $previousField = $field;
                }

                if (!$this->tableExists($tableSet) || !$this->fieldExists($newFieldName, $tableSet)) {
                    //getting add field sql
                    $sql[] = $this->getAddFieldSql($table, $field, $newFieldName, $previousField, $tableSet);

                    //getting add index sql on added field
                    $sql = array_merge($sql, (array) $this->getAddFieldIndexSql($table, $field, $newFieldName, $tableSet));
                }
            }
        }

        $this->executeSql($sql);
    }

    /**
     * Adds possibility to validate table names.
     *
     * @param string $tableName
     *
     * @return bool
     */
    protected function validateTableName($tableName)
    {
        return true;
    }
}
