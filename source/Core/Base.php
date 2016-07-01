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

namespace OxidEsales\Eshop\Core;

/**
 * Defining triggered action type.
 */
DEFINE('ACTION_NA', 0);
DEFINE('ACTION_DELETE', 1);
DEFINE('ACTION_INSERT', 2);
DEFINE('ACTION_UPDATE', 3);
DEFINE('ACTION_UPDATE_STOCK', 4);

use Exception;
use object_ResultSet;
use oxObjectException;
use oxRegistry;
use oxField;
use oxDb;
use oxUtilsObject;

class Base extends \oxSuperCfg
{

    /**
     * Unique object ID. Normally representing record oxid field value
     *
     * @var string
     */
    protected $_sOXID = null;

    /**
     * ID of running shop session (default null).
     *
     * @var int
     */
    protected $_iShopId = null;

    /**
     * Whether instance
     *
     * @var bool
     */
    protected $_blIsSimplyClonable = true;

    /**
     * Name of current class.
     *
     * @var string
     */
    protected $_sClassName = 'oxbase';

    /**
     * Core database table name. $sCoreTable could be only original data table name and not view name.
     *
     * @var string
     */
    protected $_sCoreTable = null;

    /**
     * Current view name where object record is supposed to be SELECTED from.
     *
     * @var string
     */
    protected $_sViewTable = null;

    /**
     * Field name list
     *
     * @var array
     */
    protected $_aFieldNames = array('oxid' => 0);

    /**
     * Cache key. Assigned to object depending on active view. Is used for object caching identification in lazy loading mechanism.
     *
     * @var string
     */
    protected $_sCacheKey = null;

    /**
     * Set $_blUseLazyLoading to true if you want to load only actually used fields not full objet, depending on views.
     *
     * @var bool
     */
    protected $_blUseLazyLoading = false;

    /**
     * Field name array of ignored fields when doing record update() (eg. oxarticles__oxtime)
     *
     * @var array
     */
    protected $_aSkipSaveFields = array('oxtimestamp');

    /**
     * Enable skip save fields usage
     *
     * @var bool
     */
    protected $_blUseSkipSaveFields = true;

    /**
     * SQL query string for searching in DB (??)
     *
     * @var string
     */
    protected $_sExistKey = 'oxid';

    /**
     * $_blIsDerived is set to true if object instance originally belongs to another shop (oxshopid is not curretn shop)
     * Use $this->isDerived() to access the value of this variable;
     *
     * @var bool
     */
    protected $_blIsDerived = null;

    /**
     * Disables field cache when set to true.
     * This variable is used in case when lazy loading is performed on one object
     * in order to force other class instances to use lady loading as well.
     * So far this functionality is used in lists, when first element is lazy loaded, we must lazy load others as well
     * as otherwise we will get empty objects on the first load.
     *
     * @var bool
     */
    protected static $_blDisableFieldCaching = array();

    /**
     * Marks that current object is managed by SEO
     *
     * @var bool
     */
    protected $_blIsSeoObject = false;

    /**
     * Flag allowing seo update for certain objects
     *
     * @var bool
     */
    protected $_blUpdateSeo = true;

    /**
     * Read only for object
     *
     * @var bool
     */
    protected $_blReadOnly = false;

    /**
     * Indicates if the item is list element
     *
     * @var bool
     */
    protected $_blIsInList = false;

    /**
     * Marks if object was loaded from db or not yet.
     *
     * @var bool
     */
    protected $_isLoaded = false;

    /**
     * store objects atributes values
     *
     * @var array
     */
    protected $_aInnerLazyCache = null;

    /**
     * Marker that multilanguage is OFF
     *
     * @var bool
     */
    protected $_blEmployMultilanguage = false;

    /**
     * Getting use skip fields or not
     *
     * @return bool
     */
    public function getUseSkipSaveFields()
    {
        return $this->_blUseSkipSaveFields;
    }

    /**
     * Setting use skip fields or not
     *
     * @param bool $useSkipSaveFields - true or false
     */
    public function setUseSkipSaveFields($useSkipSaveFields)
    {
        $this->_blUseSkipSaveFields = $useSkipSaveFields;
    }

    /**
     * Class constructor, sets active shop.
     */
    public function __construct()
    {
        // set active shop
        $myConfig = $this->getConfig();
        $this->_sCacheKey = $this->getViewName();

        $this->_addSkippedSaveFieldsForMapping();
        $this->_disableLazyLoadingForCaching();

        if ($this->_blUseLazyLoading) {
            $this->_sCacheKey .= $myConfig->getActiveView()->getClassName();
        } else {
            $this->_sCacheKey .= 'allviews';
        }

        //do not cache for admin?
        if ($this->isAdmin()) {
            $this->_sCacheKey = null;
        }

        $this->setShopId($myConfig->getShopId());
    }

    /**
     * Magic setter. If using lazy loading, adds setted field to fields array
     *
     * @param string $fieldName name value
     * @param mixed $fieldValue value
     */
    public function __set($fieldName, $fieldValue)
    {
        $this->$fieldName = $fieldValue;
        if ($this->_blUseLazyLoading && strpos($fieldName, $this->_sCoreTable . '__') === 0) {
            $preparedFieldName = str_replace($this->_sCoreTable . '__', '', $fieldName);
            if ($preparedFieldName !== 'oxnid'
                && (!isset($this->_aFieldNames[$preparedFieldName]) || !$this->_aFieldNames[$preparedFieldName])
            ) {
                $allFieldsList = $this->_getAllFields(true);
                if (isset($allFieldsList[strtolower($preparedFieldName)])) {
                    $fieldStatus = $this->_getFieldStatus($preparedFieldName);
                    $this->_addField($preparedFieldName, $fieldStatus);
                }
            }
        }
    }

    /**
     * Magic getter for older versions and template variables
     *
     * @param string $variableName variable name
     *
     * @return mixed
     */
    public function __get($variableName)
    {
        switch ($variableName) {
            case 'blIsDerived':
                return $this->isDerived();
                break;
            case 'sOXID':
                return $this->getId();
                break;
            case 'blReadOnly':
                return $this->isReadOnly();
                break;
        }

        // implementing lazy loading fields
        // This part of the code is slow and normally is called before field cache is built.
        // Make sure it is not called after first page is loaded and cache data is fully built.
        if ($this->_blUseLazyLoading && stripos($variableName, $this->_sCoreTable . "__") === 0) {
            if ($this->getId()) {
                //lazy load it
                $fieldName = str_replace($this->_sCoreTable . '__', '', $variableName);
                $cacheFieldName = strtoupper($fieldName);

                $fieldStatus = $this->_getFieldStatus($fieldName);

                $viewName = $this->getGetterViewName();
                $id = $this->getId();

                try {
                    if ($this->_aInnerLazyCache === null) {
                        $database = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
                        $query = 'SELECT * FROM ' . $viewName . ' WHERE `oxid` = ' . $database->quote($id);
                        $queryResult = $database->select($query);
                        if ($queryResult && $queryResult->count()) {
                            $this->_aInnerLazyCache = array_change_key_case($queryResult->fields, CASE_UPPER);
                            if (array_key_exists($cacheFieldName, $this->_aInnerLazyCache)) {
                                $fieldValue = $this->_aInnerLazyCache[$cacheFieldName];
                            } else {
                                return null;
                            }
                        } else {
                            return null;
                        }
                    } elseif (array_key_exists($cacheFieldName, $this->_aInnerLazyCache)) {
                        $fieldValue = $this->_aInnerLazyCache[$cacheFieldName];
                    } else {
                        return null;
                    }

                    $this->_addField($fieldName, $fieldStatus);
                    $this->_setFieldData($fieldName, $fieldValue);

                    //save names to cache for next loading
                    if ($this->_sCacheKey) {
                        $myUtils = oxRegistry::getUtils();
                        $cacheKey = 'fieldnames_' . $this->_sCoreTable . '_' . $this->_sCacheKey;
                        $fieldNames = $myUtils->fromFileCache($cacheKey);
                        $fieldNames[$fieldName] = $fieldStatus;
                        $myUtils->toFileCache($cacheKey, $fieldNames);
                    }
                } catch (Exception $e) {
                    return null;
                }

                //do not use field cache for this page
                //as if we use it for lists then objects are loaded empty instead of lazy loading.
                self::$_blDisableFieldCaching[get_class($this)] = true;
            }

            oxUtilsObject::getInstance()->resetInstanceCache(get_class($this));
        }

        //returns stdClass implementing __toString() method due to uknown scenario where this var should be used.
        if (!isset($this->$variableName)) {
            $this->$variableName = null;
        }

        return $this->$variableName;
    }

    /**
     * Get view name for magic getter
     *
     * @return string
     */
    protected function getGetterViewName()
    {
        return $this->getViewName();
    }

    /**
     * Magic isset() handler. Workaround for detecting if protected properties are set.
     *
     * @param mixed $variableName Supplied class variable
     *
     * @return bool
     */
    public function __isset($variableName)
    {
        return isset($this->$variableName);
    }

    /**
     * Magic function invoked on object cloning. Basically takes care about cloning properly DB fields.
     */
    public function __clone()
    {
        if (!$this->_blIsSimplyClonable) {
            foreach ($this->_aFieldNames as $fieldName => $fieldValue) {
                $fieldLongName = $this->_getFieldLongName($fieldName);
                if (is_object($this->$fieldLongName)) {
                    $this->$fieldLongName = clone $this->$fieldLongName;
                }
            }
        }
    }

    /**
     * Clone this object - similar to Copy Constructor.
     *
     * @param object $object Object to copy
     */
    public function oxClone($object)
    {
        $classVariables = get_object_vars($object);
        while (list($name, $value) = each($classVariables)) {
            if (is_object($object->$name)) {
                $this->$name = clone $object->$name;
            } else {
                $this->$name = $object->$name;
            }
        }
    }

    /**
     * Returns update seo flag
     *
     * @return boolean
     */
    public function getUpdateSeo()
    {
        return $this->_blUpdateSeo;
    }

    /**
     * Sets update seo flag
     *
     * @param boolean $updateSeo
     */
    public function setUpdateSeo($updateSeo)
    {
        $this->_blUpdateSeo = $updateSeo;
    }

    /**
     * Checks whether certain field has changed, and sets update seo flag if needed.
     * It can only set the value to false, so it allows for multiple calls to the method,
     * and if atleast one requires seo update, other checks won't override that.
     *
     * @param string $fieldName Field name that will be checked
     */
    protected function _setUpdateSeoOnFieldChange($fieldName)
    {
        if ($this->getId() && in_array($fieldName, $this->getFieldNames())) {
            $database = oxDb::getDb();
            $tableName = $this->getCoreTableName();
            $quotedOxid = $database->quote($this->getId());
            $title = $database->getOne("select `{$fieldName}` from `{$tableName}` where `oxid` = {$quotedOxid}");
            $fieldValue = "{$tableName}__{$fieldName}";
            $currentTime = $this->$fieldValue->value;

            if ($title == $currentTime) {
                $this->setUpdateSeo(false);
            }
        }
    }

    /**
     * Sets the names to main and view tables, loads metadata of each table.
     *
     * @param string $tableName      Name of DB object table
     * @param bool   $forceAllFields Forces initialisation of all fields overriding lazy loading functionality
     */
    public function init($tableName = null, $forceAllFields = false)
    {
        if ($tableName) {
            $this->_sCoreTable = $tableName;
        }

        // reset view table
        $this->_sViewTable = false;

        if (count($this->_aFieldNames) <= 1) {
            $this->_initDataStructure($forceAllFields);
        }
    }

    /**
     * Assigns DB field values to object fields. Returns true on success.
     *
     * @param array $dbRecord Associative data values array
     *
     * @return null
     */
    public function assign($dbRecord)
    {
        if (!is_array($dbRecord)) {
            return;
        }

        reset($dbRecord);
        while (list($name, $value) = each($dbRecord)) {
            $this->_setFieldData($name, $value);
        }

        $oxidField = $this->_getFieldLongName('oxid');
        $this->_sOXID = $this->$oxidField->value;
    }

    /**
     * Returns object class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->_sClassName;
    }

    /**
     * Return object core table name
     *
     * @return string
     */
    public function getCoreTableName()
    {
        return $this->_sCoreTable;
    }

    /**
     * Returns unique object id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sOXID;
    }

    /**
     * Sets unique object id
     *
     * @param string $oxid Record ID
     *
     * @return string
     */
    public function setId($oxid = null)
    {
        if ($oxid) {
            $this->_sOXID = $oxid;
        } else {
            if ($this->getCoreTableName() == 'oxobject2category') {
                $objectId = $this->oxobject2category__oxobjectid;
                $categoryId = $this->oxobject2category__oxcatnid;
                $shopID = $this->oxobject2category__oxshopid;
                $this->_sOXID = md5($objectId . $categoryId . $shopID);
            } else {
                $this->_sOXID = oxUtilsObject::getInstance()->generateUID();
            }
        }

        $idFieldName = $this->getCoreTableName() . '__oxid';
        $this->$idFieldName = new oxField($this->_sOXID, oxField::T_RAW);

        return $this->_sOXID;
    }

    /**
     * Sets original object shop ID
     *
     * @param int $shopId New shop ID
     */
    public function setShopId($shopId)
    {
        $this->_iShopId = $shopId;
    }

    /**
     * Return original object shop id
     *
     * @return int
     */
    public function getShopId()
    {
        return $this->_iShopId;
    }

    /**
     * Returns main table data is actually selected from (could be a view name as well)
     *
     * @param bool $forceCoreTableUsage (optional) use core views
     *
     * @return string
     */
    public function getViewName($forceCoreTableUsage = null)
    {
        if (!$this->_sViewTable || ($forceCoreTableUsage !== null)) {
            if ($forceCoreTableUsage === true) {
                return $this->getCoreTableName();
            }

            $forceCoreTableUsage = $this->checkIfCoreTableNeeded($forceCoreTableUsage);

            if (($forceCoreTableUsage !== null) && $forceCoreTableUsage) {
                $shopId = -1;
            } else {
                $shopId = oxRegistry::getConfig()->getShopId();
            }

            $viewName = getViewName($this->getCoreTableName(), $this->_blEmployMultilanguage == false ? -1 : $this->getLanguage(), $shopId);
            if ($forceCoreTableUsage !== null) {
                return $viewName;
            }
            $this->_sViewTable = $viewName;
        }

        return $this->_sViewTable;
    }

    /**
     * Additional check if core table name should be returned in getViewName
     *
     * @param mixed $forceCoreTableUsage
     * @return mixed
     */
    protected function checkIfCoreTableNeeded($forceCoreTableUsage)
    {
        return $forceCoreTableUsage;
    }

    /**
     * Lazy loading cache key modifier.
     *
     * @param string $cacheKey Cache  key
     * @param bool   $override Marker to force override cache key
     */
    public function modifyCacheKey($cacheKey, $override = false)
    {
        if ($override) {
            $this->_sCacheKey = $cacheKey;
        } else {
            $this->_sCacheKey .= $cacheKey;
        }
    }

    /**
     * Disables lazy loading mechanism and init object fully
     */
    public function disableLazyLoading()
    {
        $this->_blUseLazyLoading = false;
        $this->_initDataStructure(true);
    }

    /**
     * Returns true in case the item represented by this object is derived from parent shop
     *
     * @return bool
     */
    public function isDerived()
    {
        return $this->_blIsDerived;
    }

    /**
     * Returns true in case the item represented by this object is derived from parent shop
     *
     * @param bool $value if derived
     */
    public function setIsDerived($value)
    {
        $this->_blIsDerived = $value;
    }

    /**
     * Returns true, if object has multi language fields (if object is derived from oxi18n class).
     * In oxBase it is always returns false, as oxBase treats all fields as non multi language.
     *
     * @return bool
     */
    public function isMultiLang()
    {
        return false;
    }

    /**
     * Loads object data from DB (object data ID is passed to method). Returns
     * true on success.
     * could throw oxObjectException F ?
     *
     * @param string $oxid Object ID
     *
     * @return bool
     */
    public function load($oxid)
    {
        //getting at least one field before lazy loading the object
        $this->_addField('oxid', 0);
        $query = $this->buildSelectString(array($this->getViewName() . '.oxid' => $oxid));
        $this->_isLoaded = $this->assignRecord($query);

        return $this->_isLoaded;
    }

    /**
     * Returns object "loaded" state
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_isLoaded;
    }

    /**
     * Builds and returns SQL query string.
     *
     * @param mixed $whereCondition SQL select WHERE conditions array (default false)
     *
     * @return array
     */
    public function buildSelectString($whereCondition = null)
    {
        $database = oxDb::getDb();

        $get = $this->getSelectFields();
        $query = "select $get from " . $this->getViewName() . ' where 1 ';

        if ($whereCondition) {
            reset($whereCondition);
            while (list($name, $value) = each($whereCondition)) {
                $query .= ' and ' . $name . ' = ' . $database->quote($value);
            }
        }

        return $query;
    }

    /**
     * Performs SQL query, assigns record field values to object. Returns true on success.
     *
     * @param string $select SQL statement
     *
     * @return bool
     */
    public function assignRecord($select)
    {
        $record = $this->getRecordByQuery($select);

        if ($record != false && $record->count() > 0) {
            $this->assign($record->fields);
            return true;
        }

        return false;
    }

    /**
     * Get record
     *
     * @param string $query
     *
     * @return mixed|Object_ResultSet
     */
    protected function getRecordByQuery($query)
    {
        return oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->select($query);
    }

    /**
     * Gets field data
     *
     * @param string $fieldName name (eg. 'oxtitle') of a data field to get
     *
     * @return mixed value of a data field
     */
    public function getFieldData($fieldName)
    {
        $longFieldName = $this->_getFieldLongName($fieldName);

        return $this->$longFieldName->value;
    }

    /**
     * Function builds the field list used in select.
     *
     * @param bool $forceCoreTableUsage (optional) use core views
     *
     * @return string
     */
    public function getSelectFields($forceCoreTableUsage = null)
    {
        $selectFields = array();

        $viewName = $this->getViewName($forceCoreTableUsage);

        foreach ($this->_aFieldNames as $key => $field) {
            if ($viewName) {
                $selectFields[] = "`$viewName`.`$key`";
            } else {
                $selectFields[] = ".`$key`";
            }

        }

        return implode(', ', $selectFields);
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
        $oxid = $oxid ? : $this->getId();
        if (!$oxid || !$this->allowDerivedDelete()) {
            return false;
        }

        $this->_removeElement2ShopRelations($oxid);

        $database = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $coreTable = $this->getCoreTableName();
        $deleteQuery = "delete from {$coreTable} where oxid = " . $database->quote($oxid);
        $affectedRows = $database->execute($deleteQuery);
        if ($blDelete = (bool) $affectedRows) {
            $this->onChange(ACTION_DELETE, $oxid);
        }

        return $blDelete;
    }

    /**
     * Removes relevant mapping data for selected object if it is a multishop inheritable table
     *
     * @param string $oxid Object ID
     */
    protected function _removeElement2ShopRelations($oxid)
    {
    }

    /**
     * Save this Object to database, insert or update as needed.
     *
     * @return string|bool
     */
    public function save()
    {
        if (!is_array($this->_aFieldNames)) {
            return false;
        }

        // #739A - should be executed here because of date/time formatting feature
        if ($this->isAdmin() && !$this->getConfig()->getConfigParam('blSkipFormatConversion')) {
            foreach ($this->_aFieldNames as $name => $value) {
                $longName = $this->_getFieldLongName($name);
                if (isset($this->$longName->fldtype) && $this->$longName->fldtype == 'datetime') {
                    oxRegistry::get('oxUtilsDate')->convertDBDateTime($this->$longName, true);
                } elseif (isset($this->$longName->fldtype) && $this->$longName->fldtype == 'timestamp') {
                    oxRegistry::get('oxUtilsDate')->convertDBTimestamp($this->$longName, true);
                } elseif (isset($this->$longName->fldtype) && $this->$longName->fldtype == 'date') {
                    oxRegistry::get('oxUtilsDate')->convertDBDate($this->$longName, true);
                }
            }
        }

        if ($this->exists()) {
            //do not allow derived update
            if (!$this->allowDerivedUpdate()) {
                return false;
            }

            $response = $this->_update();
            $action = ACTION_UPDATE;
        } else {
            $response = $this->_insert();
            $action = ACTION_INSERT;
        }

        $this->onChange($action);

        if ($response) {
            return $this->getId();
        } else {
            return false;
        }
    }

    /**
     * Checks if derived update is allowed (calls oxbase::isDerived)
     *
     * @return bool
     */
    public function allowDerivedUpdate()
    {
        return !$this->isDerived();
    }

    /**
     * Checks if derived delete is allowed (calls oxbase::isDerived)
     *
     * @return bool
     */
    public function allowDerivedDelete()
    {
        return !$this->isDerived();
    }

    /**
     * Checks if this object exists, returns true on success.
     *
     * @param string $oxid Object ID(default null)
     *
     * @return bool
     */
    public function exists($oxid = null)
    {
        if (!$oxid) {
            $oxid = $this->getId();
        }
        if (!$oxid) {
            return false;
        }

        $viewName = $this->getCoreTableName();
        $database = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $query = "select {$this->_sExistKey} from {$viewName} where {$this->_sExistKey} = " . $database->quote($oxid);

        return ( bool ) $database->getOne($query, false, false);
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $forceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    public function getSqlActiveSnippet($forceCoreTable = null)
    {
        $query = '';
        $tableName = $this->getViewName($forceCoreTable);

        // has 'active' field ?
        if (isset($this->_aFieldNames['oxactive'])) {
            $query = " $tableName.oxactive = 1 ";
        }

        // has 'activefrom'/'activeto' fields ?
        if (isset($this->_aFieldNames['oxactivefrom']) && isset($this->_aFieldNames['oxactiveto'])) {
            $query = $this->addSqlActiveRangeSnippet($query, $tableName);

        }

        return $query;
    }



    /**
     * This function is triggered before the record is updated.
     * If you make any update to the database record manually you should also call beforeUpdate() from your script.
     *
     * @param string $oxid Object ID(default null). Pass the ID in case object is not loaded.
     */
    public function beforeUpdate($oxid = null)
    {
    }

    /**
     * This function is triggered whenever the object is saved or deleted.
     * onChange() is triggered after saving the changes in Save() method, after deleting the instance from the database.
     * If you make any change to the database record manually you should also call onChange() from your script.
     *
     * @param int    $action Action identifier.
     * @param string $oxid   Object ID(default null). Pass the ID in case object is not loaded.
     */
    public function onChange($action = null, $oxid = null)
    {
    }

    /**
     * Sets item as list element
     */
    public function setInList()
    {
        $this->_blIsInList = true;
    }

    /**
     * Checks if this instance is one of oxList elements.
     *
     * @return bool
     */
    protected function _isInList()
    {
        return $this->_blIsInList;
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
        return getViewName($table, -1, $shopID);
    }

    /**
     * Returns meta field or simple array of all object fields.
     * This method is slow and normally is called before field cache is built.
     * Make sure it is not called after first page is loaded and cache data is fully built (until tmp dir is cleaned).
     *
     * @param string $table             Table name
     * @param bool   $returnSimpleArray Set $returnSimple to true when you need simple array (meta data array is returned otherwise)
     *
     * @return array
     */
    protected function _getTableFields($table, $returnSimpleArray = false)
    {
        $myUtils = oxRegistry::getUtils();

        $cacheKey = $table . '_allfields_' . $returnSimpleArray;
        $metaFields = $myUtils->fromFileCache($cacheKey);

        if ($metaFields) {
            return $metaFields;
        }

        $metaFields = oxDb::getInstance()->getTableDescription($table);

        if (!$returnSimpleArray) {
            $myUtils->toFileCache($cacheKey, $metaFields);

            return $metaFields;
        }

        //returning simple array
        $result = array();
        if (is_array($metaFields)) {
            foreach ($metaFields as $valueObject) {
                $result[strtolower($valueObject->name)] = 0;
            }
        }

        $myUtils->toFileCache($cacheKey, $result);

        return $result;
    }

    /**
     * Returns meta field or simple array of all object fields.
     * This method is slow and normally is called before field cache is built.
     * Make sure it is not called after first page is loaded and cache data is fully built (until tmp dir is cleaned).
     *
     * @param bool $returnSimple Set $blReturnSimple to true when you need simple array (meta data array is returned otherwise)
     *
     * @see oxBase::_getTableFields()
     *
     * @return array
     */
    protected function _getAllFields($returnSimple = false)
    {
        if (!$this->getCoreTableName()) {
            return array();
        }

        return $this->_getTableFields($this->getCoreTableName(), $returnSimple);
    }

    /**
     * Initializes object data structure.
     * Either by trying to load from cache or by calling $this->_getNonCachedFieldNames
     *
     * @param bool $forceFullStructure Set to true if you want to load full structure in any case.
     */
    protected function _initDataStructure($forceFullStructure = false)
    {
        $myUtils = oxRegistry::getUtils();

        //get field names from cache
        $fieldNamesList = null;
        $fullCacheKey = 'fieldnames_' . $this->getCoreTableName() . '_' . $this->_sCacheKey;
        if ($this->_sCacheKey && !$this->_isDisabledFieldCache()) {
            $fieldNamesList = $myUtils->fromFileCache($fullCacheKey);
        }

        if (!$fieldNamesList) {
            $fieldNamesList = $this->_getNonCachedFieldNames($forceFullStructure);
            if ($this->_sCacheKey && !$this->_isDisabledFieldCache()) {
                $myUtils->toFileCache($fullCacheKey, $fieldNamesList);
            }
        }

        if ($fieldNamesList !== false) {
            foreach ($fieldNamesList as $field => $status) {
                $this->_addField($field, $status);
            }
        }
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
        //T2008-02-22
        //so if this method is executed on cached version we see it when profiling
        startProfile('!__CACHABLE__!');

        //case 1. (admin)
        if ($this->isAdmin()) {
            $metaFields = $this->_getAllFields();
            foreach ($metaFields as $oneField) {
                if ($oneField->max_length == -1) {
                    $oneField->max_length = 10; // double or float
                }

                if ($oneField->type == 'datetime') {
                    $oneField->max_length = 20;
                }

                $this->_addField($oneField->name, $this->_getFieldStatus($oneField->name), $oneField->type, $oneField->max_length);
            }
            stopProfile('!__CACHABLE__!');

            return false;
        }

        //case 2. (just get all fields)
        if ($forceFullStructure || !$this->_blUseLazyLoading) {
            $metaFields = $this->_getAllFields(true);
            /*
            foreach ( $aMetaFields as $sFieldName => $sVal) {
                $this->_addField( $sFieldName, $this->_getFieldStatus($sFieldName));
            }*/
            stopProfile('!__CACHABLE__!');

            return $metaFields;
        }

        //case 3. (get only oxid field, so we can fetch the rest of the fields over lazy loading mechanism)
        stopProfile('!__CACHABLE__!');

        return array('oxid' => 0);
    }

    /**
     * Returns _aFieldName[] value. 0 means - non multi language, 1 - multi language field. But this is defined only in derived oxi18n class.
     * In oxBase it is always 0, as oxBase treats all fields as non multi language.
     *
     * @param string $fieldName Field name
     *
     * @return int
     */
    protected function _getFieldStatus($fieldName)
    {
        return 0;
    }

    /**
     * Adds additional field to meta structure
     *
     * @param string $fieldName   Field name
     * @param int    $fieldStatus Field name status. In derived classes it indicates multi language status.
     * @param string $type        Field type
     * @param string $length      Field Length
     *
     * @return null
     */
    protected function _addField($fieldName, $fieldStatus, $type = null, $length = null)
    {
        //preparation
        $fieldName = strtolower($fieldName);

        //adding field names element
        $this->_aFieldNames[$fieldName] = $fieldStatus;

        //already set?
        $fieldLongName = $this->_getFieldLongName($fieldName);
        if (isset($this->$fieldLongName)) {
            return;
        }

        //defining the field
        $field = false;

        if (isset($type)) {
            $field = new oxField();
            $field->fldtype = $type;
            //T2008-01-29
            //can't clone as the fields are objects and are not fully cloned
            $this->_blIsSimplyClonable = false;
        }

        if (isset($length)) {
            if (!$field) {
                $field = new oxField();
            }
            $field->fldmax_length = $length;
            $this->_blIsSimplyClonable = false;
        }

        $this->$fieldLongName = $field;
    }

    /**
     * Returns long field name in "<table>__<field_name>" format.
     *
     * @param string $fieldName Short field name
     *
     * @return string
     */
    protected function _getFieldLongName($fieldName)
    {
        //trying to avoid strpos call as often as possible
        $coreTableName = $this->getCoreTableName();
        if ($fieldName[2] == $coreTableName[2] && strpos($fieldName, $coreTableName . '__') === 0) {
            return $fieldName;
        }

        return $coreTableName . '__' . strtolower($fieldName);
    }

    /**
     * Sets data field value
     *
     * @param string $fieldName  Index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $fieldValue Value of data field
     * @param int    $dataType   Field type
     */
    protected function _setFieldData($fieldName, $fieldValue, $dataType = oxField::T_TEXT)
    {
        $longFieldName = $this->_getFieldLongName($fieldName);
        //$sLongFieldName = $this->_sCoreTable . "__" . strtolower($sFieldName);

        // doing this because in lazy loaded lists on first load it is harmful to have initialised fields but not yet set
        // situation: only first article is loaded fully for "select oxid from oxarticles"
        //if ($this->_blUseLazyLoading && !isset($this->$sLongFieldName))
        //    return;


        //in non lazy loading case we just add a field and do not care about it more
        if (!$this->_blUseLazyLoading && !isset($this->$longFieldName)) {
            $fieldsList = $this->_getAllFields(true);
            if (isset($fieldsList[strtolower($fieldName)])) {
                $this->_addField($fieldName, $this->_getFieldStatus($fieldName));
            }
        }
        // if we have a double field we replace "," with "." in case somebody enters it in european format
        if (isset($this->$longFieldName) && isset($this->$longFieldName->fldtype) && $this->$longFieldName->fldtype == 'double') {
            $fieldValue = str_replace(',', '.', $fieldValue);
        }

        // isset is REQUIRED here not to use getter
        if (isset($this->$longFieldName) && is_object($this->$longFieldName)) {
            $this->$longFieldName->setValue($fieldValue, $dataType);
        } else {
            $this->$longFieldName = new oxField($fieldValue, $dataType);
        }
    }

    /**
     * check if db field can be null
     *
     * @param string $fieldName db field name
     *
     * @return bool
     */
    protected function _canFieldBeNull($fieldName)
    {
        $metaData = $this->_getAllFields();
        foreach ($metaData as $metaInfo) {
            if (strcasecmp($metaInfo->name, $fieldName) == 0) {
                return !$metaInfo->not_null;
            }
        }

        return false;
    }

    /**
     * returns default field value
     *
     * @param string $fieldName db field name
     *
     * @return mixed
     */
    protected function _getFieldDefaultValue($fieldName)
    {
        $metaData = $this->_getAllFields();
        foreach ($metaData as $metaInfo) {
            if (strcasecmp($metaInfo->name, $fieldName) == 0) {
                return $metaInfo->default_value;
            }
        }

        return false;
    }

    /**
     * returns quoted field value for using in update statement
     *
     * @param string  $fieldName name of field
     * @param oxField $field     field object
     *
     * @return string
     */
    protected function _getUpdateFieldValue($fieldName, $field)
    {
        $fieldValue = null;
        if ($field instanceof oxField) {
            $fieldValue = $field->getRawValue();
        } elseif (isset($field->value)) {
            $fieldValue = $field->value;
        }

        $database = oxDb::getDb();
        //Check if this field value is null AND it can be null according if not returning default value
        if ((null === $fieldValue)) {
            if ($this->_canFieldBeNull($fieldName)) {
                return 'null';
            } elseif ($fieldValue = $this->_getFieldDefaultValue($fieldName)) {
                return $database->quote($fieldValue);
            }
        }

        return $database->quote($fieldValue);
    }

    /**
     * Get object fields sql part used for updates or inserts:
     * return e.g.  fldName1 = 'value1',fldName2 = 'value2'...
     *
     * @param bool $useSkipSaveFields forces usage of skip save fields array (default is true)
     *
     * @return string
     */
    protected function _getUpdateFields($useSkipSaveFields = true)
    {
        $query = '';
        $useSeparator = false;

        foreach (array_keys($this->_aFieldNames) as $oneFieldName) {
            $longName = $this->_getFieldLongName($oneFieldName);
            $field = $this->$longName;

            if (!$this->checkFieldCanBeUpdated($oneFieldName)) {
                continue;
            }

            if (!$useSkipSaveFields || ($useSkipSaveFields && !in_array(strtolower($oneFieldName), $this->_aSkipSaveFields))) {
                $query .= (($useSeparator) ? ',' : '') . $oneFieldName . ' = ' . $this->_getUpdateFieldValue($oneFieldName, $field);
                $useSeparator = true;
            }
        }

        return $query;
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
     * Update this Object into the database, this function only works on
     * the main table, it will not save any dependent tables, which might
     * be loaded through oxList.
     *
     * @throws oxObjectException Throws on failure inserting
     *
     * @return bool
     */
    protected function _update()
    {
        //do not allow derived item update
        if (!$this->allowDerivedUpdate()) {
            return false;
        }

        if (!$this->getId()) {
            $exception = oxNew('oxObjectException');
            $exception->setMessage('EXCEPTION_OBJECT_OXIDNOTSET');
            $exception->setObject($this);
            throw $exception;
        }
        $coreTableName = $this->getCoreTableName();

        $idKey = oxRegistry::getUtils()->getArrFldName($coreTableName . '.oxid');
        $this->$idKey = new oxField($this->getId(), oxField::T_RAW);
        $database = oxDb::getDb();

        $updateQuery = "update {$coreTableName} set " . $this->_getUpdateFields()
                   . " where {$coreTableName}.oxid = " . $database->quote($this->getId());

        $this->beforeUpdate();

        return (bool) $database->execute($updateQuery);
    }

    /**
     * Insert this Object into the database, this function only works
     * on the main table, it will not save any dependent tables, which
     * might be loaded through oxlist.
     *
     * @return bool
     */
    protected function _insert()
    {
        $database = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $myConfig = $this->getConfig();
        $myUtils = oxRegistry::getUtils();

        // let's get a new ID
        if (!$this->getId()) {
            $this->setId();
        }

        $idKey = $myUtils->getArrFldName($this->getCoreTableName() . '.oxid');
        $this->$idKey = new oxField($this->getId(), oxField::T_RAW);
        $insertSql = "Insert into {$this->getCoreTableName()} set ";

        $shopIdField = $myUtils->getArrFldName($this->getCoreTableName() . '.oxshopid');

        if (isset($this->$shopIdField) && !$this->$shopIdField->value) {
            $this->$shopIdField = new oxField($myConfig->getShopId(), oxField::T_RAW);
        }

        $insertSql .= $this->_getUpdateFields($this->getUseSkipSaveFields());

        return (bool) $database->execute($insertSql);
    }

    /**
     * Checks if current class disables field caching.
     * This method is primary used in unit tests.
     *
     * @return bool
     */
    protected function _isDisabledFieldCache()
    {
        $class = get_class($this);
        if (isset(self::$_blDisableFieldCaching[$class]) && self::$_blDisableFieldCaching[$class]) {
            return true;
        }

        return false;
    }

    /**
     * Add additional fields to skipped save fields
     */
    protected function _addSkippedSaveFieldsForMapping()
    {
    }

    /**
     * Disable lazy loading if cache is enabled
     */
    protected function _disableLazyLoadingForCaching()
    {
    }

    /**
     * Checks if object ID's first two chars are 'o' and 'x'. Returns true or false
     *
     * @return bool
     */
    public function isOx()
    {
        $oxid = $this->getId();
        if ($oxid[0] == 'o' && $oxid[1] == 'x') {
            return true;
        }

        return false;
    }

    /**
     * Is object readonly
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->_blReadOnly;
    }

    /**
     * Set object readonly
     *
     * @param bool $readOnly readonly flag
     */
    public function setReadOnly($readOnly)
    {
        $this->_blReadOnly = $readOnly;
    }

    /**
     * Returns array with object field names
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->_aFieldNames);
    }

    /**
     * Adds additional field name to meta structure
     *
     * @param string $name Field name
     */
    public function addFieldName($name)
    {
        //preparation
        $name = strtolower($name);
        $this->_aFieldNames[$name] = 0;
    }

    /**
     * Returns -1, means object is not multi language
     *
     * @return int
     */
    public function getLanguage()
    {
        return -1;
    }

    /**
     * adds and activefrom/activeto to the query
     * @param $query
     * @param $tableName
     *
     * @return string
     */
    protected function addSqlActiveRangeSnippet($query, $tableName)
    {
        $dateObj = oxRegistry::get('oxUtilsDate');
        $secondsToRoundForQueryCache = $this->getSecondsToRoundForQueryCache();
        $databaseFormattedDate = $dateObj->getRoundedRequestDateDBFormatted($secondsToRoundForQueryCache);
        $query = $query ? " $query or " : '';

        return " ( $query ( $tableName.oxactivefrom < '$databaseFormattedDate' and $tableName.oxactiveto > '$databaseFormattedDate' ) ) ";
    }

    /**
     *  Return a number of seconds used to define a interval for rounding timestamps
     *  e.g. this method returns the value 60 then it means timestamps should be rounded to full minutes
     *  so the query may get an cache hit because it can be stable for an interval of one minute
     *
     *  it is a own method to allow overriding in child classes
     *  @return int the amount of seconds
     */
    protected function getSecondsToRoundForQueryCache()
    {
        //set default value cache time to 60 seconds
        //because active from setting is based on minutes
        return 60;
    }
}
