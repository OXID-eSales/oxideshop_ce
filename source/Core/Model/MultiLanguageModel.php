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

namespace OxidEsales\EshopCommunity\Core\Model;

use oxObjectException;
use oxRegistry;
use oxDb;

/**
 * Class handling multilanguage data fields
 */
class MultiLanguageModel extends \oxBase
{

    /**
     * Name of class.
     *
     * @var string
     */
    protected $_sClassName = 'oxI18n';

    /**
     * Active object language.
     *
     * @var int
     */
    protected $_iLanguage = null;

    /**
     * Sometimes you need to deal with all fields not only with active
     * language, then set to false (default is true).
     *
     * @var bool
     */
    protected $_blEmployMultilanguage = true;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();

        //T2008-02-22
        //lets try to differentiate cache keys for oxI18n and oxBase
        //in order not to load cached structure for the instances of oxbase classe called on same table
        if ($this->_sCacheKey) {
            $this->_sCacheKey .= "_i18n";
        }
    }

    /**
     * Sets object language.
     *
     * @param string $lang string (default null)
     */
    public function setLanguage($lang = null)
    {
        $this->_iLanguage = (int) $lang;
        // reset
        $this->_sViewTable = false;
    }

    /**
     * Returns object language
     *
     * @return int
     */
    public function getLanguage()
    {
        if ($this->_iLanguage === null) {
            $this->_iLanguage = oxRegistry::getLang()->getBaseLanguage();
        }

        return $this->_iLanguage;
    }

    /**
     * Object multilanguage mode setter (set true to enable multilang mode).
     * This setter affects init() method so it should be called before init() is executed
     *
     * @param bool $employMultilanguage New $this->_blEmployMultilanguage value
     */
    public function setEnableMultilang($employMultilanguage)
    {
        if ($this->_blEmployMultilanguage != $employMultilanguage) {
            $this->_blEmployMultilanguage = $employMultilanguage;
            if (!$employMultilanguage) {
                //#63T
                $this->modifyCacheKey("_nonml");
            }
            // reset
            $this->_sViewTable = false;
            if (count($this->_aFieldNames) > 1) {
                $this->_initDataStructure();
            }
        }
    }

    /**
     * Checks if this field is multlingual
     * (returns false if language = 0)
     *
     * @param string $fieldName Field name
     *
     * @return bool
     */
    public function isMultilingualField($fieldName)
    {
        $fieldName = strtolower($fieldName);
        if (isset($this->_aFieldNames[$fieldName])) {
            return (bool) $this->_aFieldNames[$fieldName];
        }

        //not inited field yet
        //and note that this is should be called only in first call after tmp dir is empty
        startProfile('!__CACHABLE2__!');
        $isMultilang = (bool) $this->_getFieldStatus($fieldName);
        stopProfile('!__CACHABLE2__!');

        return (bool) $isMultilang;
    }

    /**
     * Returns true, if object has multilanguage fields.
     * In oxi18n it is always returns true.
     *
     * @return bool
     */
    public function isMultilang()
    {
        return true;
    }

    /**
     * Loads object data from DB in passed language, returns true on success.
     *
     * @param integer $language Load this language compatible data
     * @param string  $oxid     object ID
     *
     * @return bool
     */
    public function loadInLang($language, $oxid)
    {
        // set new lang to this object
        $this->setLanguage($language);
        // reset
        $this->_sViewTable = false;

        return $this->load($oxid);
    }

    /**
     * Lazy loading cache key modifier.
     *
     * @param string $cacheKey kache  key
     * @param bool   $override marker to force override cache key
     */
    public function modifyCacheKey($cacheKey, $override = false)
    {
        if ($override) {
            $this->_sCacheKey = $cacheKey . "|i18n";
        } else {
            $this->_sCacheKey .= $cacheKey;
        }

        if (!$cacheKey) {
            $this->_sCacheKey = null;
        }
    }

    /**
     * Returns an array of languages in which object multilanguage
     * fields are already setted
     *
     * @return array
     */
    public function getAvailableInLangs()
    {
        $languages = oxRegistry::getLang()->getLanguageNames();

        $objFields = $this->_getTableFields(
            getViewName($this->_sCoreTable, -1, -1),
            true
        );
        $multiLangFields = array();

        //selecting all object multilang fields
        foreach ($objFields as $key => $value) {
            //skipping oxactive field
            if (preg_match('/^oxactive(_(\d{1,2}))?$/', $key)) {
                continue;
            }

            $fieldLang = $this->_getFieldLang($key);

            //checking, if field is multilanguage
            if ($this->isMultilingualField($key) || $fieldLang > 0) {
                $newKey = preg_replace('/_(\d{1,2})$/', '', $key);
                $multiLangFields[$newKey][] = (int) $fieldLang;
            }
        }

        // if no multilanguage fields, return default languages array
        if (count($multiLangFields) < 1) {
            return $languages;
        }

        // select from non-multilanguage core view (all ml tables joined to one)
        $db = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $query = "select * from " . getViewName($this->_sCoreTable, -1, -1) . " where oxid = " . $db->quote($this->getId());
        $rs = $db->getAll($query);

        $notInLang = $languages;

        // checks if object field data is not empty in all available languages
        // and formats not available in languages array
        if (is_array($rs) && count($rs[0])) {
            foreach ($multiLangFields as $fieldId => $multiLangIds) {
                foreach ($multiLangIds as $multiLangId) {
                    $fieldName = ($multiLangId == 0) ? $fieldId : $fieldId . '_' . $multiLangId;
                    if ($rs['0'][strtoupper($fieldName)]) {
                        unset($notInLang[$multiLangId]);
                        continue;
                    }
                }
            }
        }

        return array_diff($languages, $notInLang);
    }

    /**
     * Returns _aFieldName[] value. 0 means - non multilanguage, 1 - multilanguage field.
     * This method is slow, so we should make sure it is called only when tmp dir is cleaned (and then the results are cached).
     *
     * @param string $fieldName Field name
     *
     * @return int
     */
    protected function _getFieldStatus($fieldName)
    {
        $allField = $this->_getAllFields(true);
        if (isset($allField[strtolower($fieldName) . "_1"])) {
            return 1;
        }

        return 0;
    }

    /**
     * Returns the list of fields. This function is slower and its result is normally cached.
     * Basically we have 3 separate cases here:
     *  1. We are in admin so we need extended info for all fields (name, field length and field type)
     *  2. Object is not lazy loaded so we will return all data fields as simple array, as we need only names
     *  3. Object is lazy loaded so we will return empty array as all fields are loaded on request (in __get()).
     *
     * @param bool $forceFullStructure Whether to force loading of full data structure
     *
     * @return array
     */
    protected function _getNonCachedFieldNames($forceFullStructure = false)
    {
        //Tomas
        //TODO: this place could be optimized. please check what we can do.
        $fields = parent::_getNonCachedFieldNames($forceFullStructure);

        if (!$this->_blEmployMultilanguage) {
            return $fields;
        }

        //lets do some pointer manipulation
        if ($fields) {
            //non admin fields
            $workingFields = & $fields;
        } else {
            //most likely admin fields so we remove another language
            $workingFields = & $this->_aFieldNames;
        }

        //we have an array of fields, lets remove multilanguage fields
        foreach ($workingFields as $name => $val) {
            if ($this->_getFieldLang($name)) {
                unset($workingFields[$name]);
            } else {
                $workingFields[$name] = $this->_getFieldStatus($name);
            }
        }

        return $workingFields;
    }

    /**
     * Gets multilanguage field language. In case of oxtitle_2 it will return 2. 0 is returned if language ending is not defined.
     *
     * @param string $fieldName Field name
     *
     * @return bool
     */
    protected function _getFieldLang($fieldName)
    {
        if (false === strpos($fieldName, '_')) {
            return 0;
        }
        if (preg_match('/_(\d{1,2})$/', $fieldName, $regs)) {
            return $regs[1];
        } else {
            return 0;
        }
    }

    /**
     * Returns DB field name for update.
     *
     * @param string $field Field name
     *
     * @return string
     */
    public function getUpdateSqlFieldName($field)
    {
        $lang = $this->getLanguage();
        if ($lang && $this->_blEmployMultilanguage && $this->isMultilingualField($field)) {
            $field .= "_" . $lang;
        }

        return $field;
    }

    /**
     * Checks whether certain field has changed, and sets update seo flag if needed.
     * It can only set the value to false, so it allows for multiple calls to the method,
     * and if atleast one requires seo update, other checks won't override that.
     * Will try to get multilang table name for relevant field check.
     *
     * @param string $field Field name that will be checked
     */
    protected function _setUpdateSeoOnFieldChange($field)
    {
        parent::_setUpdateSeoOnFieldChange($this->getUpdateSqlFieldName($field));
    }


    /**
     * return update fields SQL part
     *
     * @param string $table             table name to be updated
     * @param bool   $useSkipSaveFields use skip save fields array?
     *
     * @return string
     */
    protected function _getUpdateFieldsForTable($table, $useSkipSaveFields = true)
    {
        $coreTable = $this->getCoreTableName();

        $skipMultilingual = false;
        $skipCoreFields = false;

        if ($table != $coreTable) {
            $skipCoreFields = true;
        }
        if ($this->_blEmployMultilanguage) {
            if ($table != getLangTableName($coreTable, $this->getLanguage())) {
                $skipMultilingual = true;
            }
        }

        $sql = '';
        $sep = false;
        foreach (array_keys($this->_aFieldNames) as $key) {
            $keyLowercase = strtolower($key);
            if ($keyLowercase != 'oxid') {
                if ($this->_blEmployMultilanguage) {
                    if ($skipMultilingual && $this->isMultilingualField($key)) {
                        continue;
                    }
                    if ($skipCoreFields && !$this->isMultilingualField($key)) {
                        continue;
                    }
                } else {
                    // need to explicitly check field language
                    $fieldLang = $this->_getFieldLang($key);
                    if ($fieldLang) {
                        if ($table != getLangTableName($coreTable, $fieldLang)) {
                            continue;
                        }
                    } elseif ($skipCoreFields) {
                        continue;
                    }
                }
            }

            if (!$this->checkFieldCanBeUpdated($key)) {
                continue;
            }

            $longName = $this->_getFieldLongName($key);
            $field = $this->$longName;

            if (!$useSkipSaveFields || ($useSkipSaveFields && !in_array($keyLowercase, $this->_aSkipSaveFields))) {
                $key = $this->getUpdateSqlFieldName($key);
                $sql .= (($sep) ? ',' : '') . $key . " = " . $this->_getUpdateFieldValue($key, $field);
                $sep = true;
            }
        }

        return $sql;
    }

    /**
     * If needed, check if field can be updated
     *
     * @param string $fieldName
     *
     * @return bool
     */
    protected function checkFieldCanBeUpdated($fieldName)
    {
        return true;
    }

    /**
     * Get object fields sql part for base table
     * used for updates or inserts:
     * return e.g.  fldName1 = 'value1',fldName2 = 'value2'...
     *
     * @param bool $useSkipSaveFields forces usage of skip save fields array (default is true)
     *
     * @return string
     */
    protected function _getUpdateFields($useSkipSaveFields = true)
    {
        return $this->_getUpdateFieldsForTable($this->getCoreTableName(), $useSkipSaveFields);
    }

    /**
     * Update this Object into the database, this function only works on
     * the main table, it will not save any dependend tables, which might
     * be loaded through oxlist (with exception of the active language set
     * table, which will be updated).
     *
     * @throws oxObjectException Throws on failure inserting
     *
     * @return bool
     */
    protected function _update()
    {
        $ret = parent::_update();

        if ($ret) {
            //also update multilang table if it is separate
            $updateTables = array();
            if ($this->_blEmployMultilanguage) {
                $coreTable = $this->getCoreTableName();
                $langTable = getLangTableName($coreTable, $this->getLanguage());
                if ($coreTable != $langTable) {
                    $updateTables[] = $langTable;
                }
            } else {
                $updateTables = $this->_getLanguageSetTables();
            }
            foreach ($updateTables as $langTable) {
                $insertSql = "insert into $langTable set " . $this->_getUpdateFieldsForTable($langTable, $this->getUseSkipSaveFields()) .
                             " on duplicate key update " . $this->_getUpdateFieldsForTable($langTable);

                $ret = (bool) $this->executeDatabaseQuery($insertSql);
            }
        }

        // currently only multilanguage objects are SEO
        // if current object is managed by SEO and SEO is ON
        if ($ret && $this->_blIsSeoObject && $this->getUpdateSeo() && $this->isAdmin()) {
            // marks all object db entries as expired
            oxRegistry::get("oxSeoEncoder")->markAsExpired($this->getId(), null, 1, $this->getLanguage());
        }

        return $ret;
    }

    /**
     * Return all DB tables for the language sets
     *
     * @param string $coreTableName core table name [optional]
     *
     * @return array
     */
    protected function _getLanguageSetTables($coreTableName = null)
    {
        $coreTableName = $coreTableName ? $coreTableName : $this->getCoreTableName();

        return oxNew('oxDbMetaDataHandler')->getAllMultiTables($coreTableName);
    }

    /**
     * Insert this Object into the database, this function only works
     * on the main table, it will not save any dependend tables, which
     * might be loaded through oxlist.
     *
     * @return bool
     */
    protected function _insert()
    {
        $result = parent::_insert();

        if ($result) {
            //also insert to multilang tables if it is separate
            foreach ($this->_getLanguageSetTables() as $table) {
                $sql = "insert into $table set " . $this->_getUpdateFieldsForTable($table, $this->getUseSkipSaveFields());

                $result = $result && (bool) $this->executeDatabaseQuery($sql);
            }
        }

        return $result;
    }

    /**
     * Returns actual object view or table name
     *
     * @param string $table  Original table name
     * @param int    $shopID Shop ID
     *
     * @return string
     */
    protected function _getObjectViewName($table, $shopID = null)
    {
        if (!$this->_blEmployMultilanguage) {
            return parent::_getObjectViewName($table, $shopID);
        }

        return getViewName($table, $this->getLanguage(), $shopID);
    }

    /**
     * Returns meta field or simple array of all object fields.
     * This method is slow and normally is called before field cache is built.
     * Make sure it is not called after first page is loaded and cache data is fully built (until tmp dir is cleaned).
     *
     * @param bool $returnSimple Set $returnSimple to true when you need simple array (meta data array is returned otherwise)
     *
     * @see oxBase::_getTableFields()
     *
     * @return array
     */
    protected function _getAllFields($returnSimple = false)
    {
        if ($this->_blEmployMultilanguage) {
            return parent::_getAllFields($returnSimple);
        } else {
            $viewName = $this->getViewName();
            if (!$viewName) {
                return array();
            }

            return $this->_getTableFields($viewName, $returnSimple);
        }
    }

    /**
     * Adds additional field to meta structure. Skips language fields
     *
     * @param string $name   Field name
     * @param string $status Field status (0-non multilang field, 1-multilang field)
     * @param string $type   Field type
     * @param string $length Field Length
     *
     * @return null;
     */
    protected function _addField($name, $status, $type = null, $length = null)
    {
        if ($this->_blEmployMultilanguage && $this->_getFieldLang($name)) {
            return;
        }

        return parent::_addField($name, $status, $type, $length);
    }

    /**
     * check if db field can be null
     * for multilingual fields it checks only the base fields as they may be
     * coming from outer join views, so oxbase would return that they always
     * support null (while in reality updates to their lang set table with null
     * would fail)
     *
     * @param string $fieldName db field name
     *
     * @return bool
     */
    protected function _canFieldBeNull($fieldName)
    {
        $fieldName = preg_replace('/_\d{1,2}$/', '', $fieldName);

        return parent::_canFieldBeNull($fieldName);
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $oxid Object ID(default null)
     *
     * @return bool
     */
    public function delete($oxid = null)
    {
        $deleted = parent::delete($oxid);
        if ($deleted) {
            $db = oxDb::getDb();
            $oxid = $db->quote($oxid);

            //delete the record
            foreach ($this->_getLanguageSetTables() as $setTbl) {
                $db->execute("delete from {$setTbl} where oxid = {$oxid}");
            }
        }

        return $deleted;
    }
}
