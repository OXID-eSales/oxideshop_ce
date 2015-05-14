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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 */

/**
 * Class handling multilanguage data fields
 *
 */
class oxI18n extends oxBase
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
     * @param string $iLang string (default null)
     */
    public function setLanguage($iLang = null)
    {
        $this->_iLanguage = (int) $iLang;
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
     * @param bool $blEmployMultilanguage New $this->_blEmployMultilanguage value
     */
    public function setEnableMultilang($blEmployMultilanguage)
    {
        if ($this->_blEmployMultilanguage != $blEmployMultilanguage) {
            $this->_blEmployMultilanguage = $blEmployMultilanguage;
            if (!$blEmployMultilanguage) {
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
     * @param string $sFieldName Field name
     *
     * @return bool
     */
    public function isMultilingualField($sFieldName)
    {
        $sFieldName = strtolower($sFieldName);
        if (isset($this->_aFieldNames[$sFieldName])) {
            return (bool) $this->_aFieldNames[$sFieldName];
        }

        //not inited field yet
        //and note that this is should be called only in first call after tmp dir is empty
        startProfile('!__CACHABLE2__!');
        $blIsMultilang = (bool) $this->_getFieldStatus($sFieldName);
        stopProfile('!__CACHABLE2__!');

        return (bool) $blIsMultilang;
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
     * @param integer $iLanguage Load this language compatible data
     * @param string  $sOxid     object ID
     *
     * @return bool
     */
    public function loadInLang($iLanguage, $sOxid)
    {
        // set new lang to this object
        $this->setLanguage($iLanguage);
        // reset
        $this->_sViewTable = false;

        return $this->load($sOxid);
    }

    /**
     * Lazy loading cache key modifier.
     *
     * @param string $sCacheKey  kache  key
     * @param bool   $blOverride marker to force override cache key
     */
    public function modifyCacheKey($sCacheKey, $blOverride = false)
    {
        if ($blOverride) {
            $this->_sCacheKey = $sCacheKey . "|i18n";
        } else {
            $this->_sCacheKey .= $sCacheKey;
        }

        if (!$sCacheKey) {
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
        $aLanguages = oxRegistry::getLang()->getLanguageNames();

        $aObjFields = $this->_getTableFields(
            getViewName($this->_sCoreTable, -1, -1),
            true
        );
        $aMultiLangFields = array();

        //selecting all object multilang fields
        foreach ($aObjFields as $sKey => $sValue) {

            //skipping oxactive field
            if (preg_match('/^oxactive(_(\d{1,2}))?$/', $sKey)) {
                continue;
            }

            $iFieldLang = $this->_getFieldLang($sKey);

            //checking, if field is multilanguage
            if ($this->isMultilingualField($sKey) || $iFieldLang > 0) {
                $sNewKey = preg_replace('/_(\d{1,2})$/', '', $sKey);
                $aMultiLangFields[$sNewKey][] = (int) $iFieldLang;
            }
        }

        // if no multilanguage fields, return default languages array
        if (count($aMultiLangFields) < 1) {
            return $aLanguages;
        }

        // select from non-multilanguage core view (all ml tables joined to one)
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $query = "select * from " . getViewName($this->_sCoreTable, -1, -1) . " where oxid = " . $oDb->quote($this->getId());
        $rs = $oDb->getAll($query);

        $aNotInLang = $aLanguages;

        // checks if object field data is not empty in all available languages
        // and formats not available in languages array
        if (is_array($rs) && count($rs[0])) {
            foreach ($aMultiLangFields as $sFieldId => $aMultiLangIds) {

                foreach ($aMultiLangIds as $sMultiLangId) {
                    $sFieldName = ($sMultiLangId == 0) ? $sFieldId : $sFieldId . '_' . $sMultiLangId;
                    if ($rs['0'][strtoupper($sFieldName)]) {
                        unset($aNotInLang[$sMultiLangId]);
                        continue;
                    }
                }
            }
        }

        $aIsInLang = array_diff($aLanguages, $aNotInLang);

        return $aIsInLang;
    }

    /**
     * Returns _aFieldName[] value. 0 means - non multilanguage, 1 - multilanguage field.
     * This method is slow, so we should make sure it is called only when tmp dir is cleaned (and then the results are cached).
     *
     * @param string $sFieldName Field name
     *
     * @return int
     */
    protected function _getFieldStatus($sFieldName)
    {
        $aAllField = $this->_getAllFields(true);
        if (isset($aAllField[strtolower($sFieldName) . "_1"])) {
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
     * @param bool $blForceFullStructure Whether to force loading of full data structure
     *
     * @return array
     */
    protected function _getNonCachedFieldNames($blForceFullStructure = false)
    {
        //Tomas
        //TODO: this place could be optimized. please check what we can do.
        $aFields = parent::_getNonCachedFieldNames($blForceFullStructure);

        if (!$this->_blEmployMultilanguage) {
            return $aFields;
        }

        //lets do some pointer manipulation
        if ($aFields) {
            //non admin fields
            $aWorkingFields = & $aFields;
        } else {
            //most likely admin fields so we remove another language
            $aWorkingFields = & $this->_aFieldNames;
        }

        //we have an array of fields, lets remove multilanguage fields
        foreach ($aWorkingFields as $sName => $sVal) {
            if ($this->_getFieldLang($sName)) {
                unset($aWorkingFields[$sName]);
            } else {
                $aWorkingFields[$sName] = $this->_getFieldStatus($sName);
            }
        }

        return $aWorkingFields;
    }

    /**
     * Gets multilanguage field language. In case of oxtitle_2 it will return 2. 0 is returned if language ending is not defined.
     *
     * @param string $sFieldName Field name
     *
     * @return bool
     */
    protected function _getFieldLang($sFieldName)
    {
        if (false === strpos($sFieldName, '_')) {
            return 0;
        }
        if (preg_match('/_(\d{1,2})$/', $sFieldName, $aRegs)) {
            return $aRegs[1];
        } else {
            return 0;
        }
    }

    /**
     * Returns DB field name for update.
     *
     * @param string $sField Field name
     *
     * @return string
     */
    public function getUpdateSqlFieldName($sField)
    {
        $iLang = $this->getLanguage();
        if ($iLang && $this->_blEmployMultilanguage && $this->isMultilingualField($sField)) {
            $sField .= "_" . $iLang;
        }

        return $sField;
    }

    /**
     * Checks whether certain field has changed, and sets update seo flag if needed.
     * It can only set the value to false, so it allows for multiple calls to the method,
     * and if atleast one requires seo update, other checks won't override that.
     * Will try to get multilang table name for relevant field check.
     *
     * @param string $sField Field name that will be checked
     */
    protected function _setUpdateSeoOnFieldChange($sField)
    {
        parent::_setUpdateSeoOnFieldChange($this->getUpdateSqlFieldName($sField));
    }


    /**
     * return update fields SQL part
     *
     * @param string $sTable              table name to be updated
     * @param bool   $blUseSkipSaveFields use skip save fields array?
     *
     * @return string
     */
    protected function _getUpdateFieldsForTable($sTable, $blUseSkipSaveFields = true)
    {
        $sCoreTable = $this->getCoreTableName();

        $blSkipMultilingual = false;
        $blSkipCoreFields = false;

        if ($sTable != $sCoreTable) {
            $blSkipCoreFields = true;
        }
        if ($this->_blEmployMultilanguage) {
            if ($sTable != getLangTableName($sCoreTable, $this->getLanguage())) {
                $blSkipMultilingual = true;
            }
        }

        $sSql = '';
        $blSep = false;
        foreach (array_keys($this->_aFieldNames) as $sKey) {
            $sKeyLowercase = strtolower($sKey);
            if ($sKeyLowercase != 'oxid') {
                if ($this->_blEmployMultilanguage) {
                    if ($blSkipMultilingual && $this->isMultilingualField($sKey)) {
                        continue;
                    }
                    if ($blSkipCoreFields && !$this->isMultilingualField($sKey)) {
                        continue;
                    }
                } else {
                    // need to explicitly check field language
                    $iFieldLang = $this->_getFieldLang($sKey);
                    if ($iFieldLang) {
                        if ($sTable != getLangTableName($sCoreTable, $iFieldLang)) {
                            continue;
                        }
                    } elseif ($blSkipCoreFields) {
                        continue;
                    }
                }
            }

            $sLongName = $this->_getFieldLongName($sKey);
            $oField = $this->$sLongName;

            if (!$blUseSkipSaveFields || ($blUseSkipSaveFields && !in_array($sKeyLowercase, $this->_aSkipSaveFields))) {
                $sKey = $this->getUpdateSqlFieldName($sKey);
                $sSql .= (($blSep) ? ',' : '') . $sKey . " = " . $this->_getUpdateFieldValue($sKey, $oField);
                $blSep = true;
            }
        }

        return $sSql;
    }

    /**
     * Get object fields sql part for base table
     * used for updates or inserts:
     * return e.g.  fldName1 = 'value1',fldName2 = 'value2'...
     *
     * @param bool $blUseSkipSaveFields forces usage of skip save fields array (default is true)
     *
     * @return string
     */
    protected function _getUpdateFields($blUseSkipSaveFields = true)
    {
        return $this->_getUpdateFieldsForTable($this->getCoreTableName(), $blUseSkipSaveFields);
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
        $blRet = parent::_update();

        if ($blRet) {
            //also update multilang table if it is separate
            $aUpdateTables = array();
            if ($this->_blEmployMultilanguage) {
                $sCoreTable = $this->getCoreTableName();
                $sLangTable = getLangTableName($sCoreTable, $this->getLanguage());
                if ($sCoreTable != $sLangTable) {
                    $aUpdateTables[] = $sLangTable;
                }
            } else {
                $aUpdateTables = $this->_getLanguageSetTables();
            }
            foreach ($aUpdateTables as $sLangTable) {
                $sUpdate = "insert into $sLangTable set " . $this->_getUpdateFieldsForTable($sLangTable, $this->getUseSkipSaveFields()) .
                           " on duplicate key update " . $this->_getUpdateFieldsForTable($sLangTable);

                $blRet = (bool) oxDb::getDb()->execute($sUpdate);
            }
        }

        // currently only multilanguage objects are SEO
        // if current object is managed by SEO and SEO is ON
        if ($blRet && $this->_blIsSeoObject && $this->getUpdateSeo() && $this->isAdmin()) {
            // marks all object db entries as expired
            oxRegistry::get("oxSeoEncoder")->markAsExpired($this->getId(), null, 1, $this->getLanguage());
        }

        return $blRet;
    }

    /**
     * return all DB tables for the language sets
     *
     * @param string $sCoreTableName core table name [optional]
     *
     * @return array
     */
    protected function _getLanguageSetTables($sCoreTableName = null)
    {
        $sCoreTableName = $sCoreTableName ? $sCoreTableName : $this->getCoreTableName();

        return oxNew('oxDbMetaDataHandler')->getAllMultiTables($sCoreTableName);
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
        $blRet = parent::_insert();

        if ($blRet) {
            //also insert to multilang tables if it is separate
            foreach ($this->_getLanguageSetTables() as $sTable) {
                $sSq = "insert into $sTable set " . $this->_getUpdateFieldsForTable($sTable, $this->getUseSkipSaveFields());
                $blRet = $blRet && (bool) oxDb::getDb()->execute($sSq);
            }
        }

        return $blRet;
    }

    /**
     * Returns actual object view or table name
     *
     * @param string $sTable  Original table name
     * @param int    $sShopID Shop ID
     *
     * @return string
     */
    protected function _getObjectViewName($sTable, $sShopID = null)
    {
        if (!$this->_blEmployMultilanguage) {
            return parent::_getObjectViewName($sTable, $sShopID);
        }

        return getViewName($sTable, $this->getLanguage(), $sShopID);
    }

    /**
     * Returns meta field or simple array of all object fields.
     * This method is slow and normally is called before field cache is built.
     * Make sure it is not called after first page is loaded and cache data is fully built (until tmp dir is cleaned).
     *
     * @param bool $blReturnSimple Set $blReturnSimple to true when you need simple array (meta data array is returned otherwise)
     *
     * @see oxBase::_getTableFields()
     *
     * @return array
     */
    protected function _getAllFields($blReturnSimple = false)
    {
        if ($this->_blEmployMultilanguage) {
            return parent::_getAllFields($blReturnSimple);
        } else {
            $sViewName = $this->getViewName();
            if (!$sViewName) {
                return array();
            }

            return $this->_getTableFields($sViewName, $blReturnSimple);
        }
    }

    /**
     * Adds additional field to meta structure. Skips language fields
     *
     * @param string $sName   Field name
     * @param string $sStatus Field status (0-non multilang field, 1-multilang field)
     * @param string $sType   Field type
     * @param string $sLength Field Length
     *
     * @return null;
     */
    protected function _addField($sName, $sStatus, $sType = null, $sLength = null)
    {
        if ($this->_blEmployMultilanguage && $this->_getFieldLang($sName)) {
            return;
        }

        return parent::_addField($sName, $sStatus, $sType, $sLength);
    }

    /**
     * check if db field can be null
     * for multilingual fields it checks only the base fields as they may be
     * coming from outer join views, so oxbase would return that they always
     * support null (while in reality updates to their lang set table with null
     * would fail)
     *
     * @param string $sFieldName db field name
     *
     * @return bool
     */
    protected function _canFieldBeNull($sFieldName)
    {
        $sFieldName = preg_replace('/_\d{1,2}$/', '', $sFieldName);

        return parent::_canFieldBeNull($sFieldName);
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        $blDeleted = parent::delete($sOXID);
        if ($blDeleted) {
            $oDB = oxDb::getDb();
            $sOXID = $oDB->quote($sOXID);

            //delete the record
            foreach ($this->_getLanguageSetTables() as $sSetTbl) {
                $oDB->execute("delete from {$sSetTbl} where oxid = {$sOXID}");
            }
        }

        return $blDeleted;
    }
}
