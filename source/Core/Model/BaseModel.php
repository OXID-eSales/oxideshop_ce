<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Model;

/**
 * Defining triggered action type.
 */
DEFINE('ACTION_NA', 0);
DEFINE('ACTION_DELETE', 1);
DEFINE('ACTION_INSERT', 2);
DEFINE('ACTION_UPDATE', 3);
DEFINE('ACTION_UPDATE_STOCK', 4);

use Exception;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;
use oxObjectException;
use \OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelDeleteEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelDeleteEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelInsertEvent;

/**
 * Class BaseModel
 * @package OxidEsales\EshopCommunity\Core\Model
 */
class BaseModel extends \OxidEsales\Eshop\Core\Base
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
    protected $_aFieldNames = ['oxid' => 0];

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
    protected $_aSkipSaveFields = ['oxtimestamp'];

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
    protected static $_blDisableFieldCaching = [];

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
     * @param string $fieldName  name value
     * @param mixed  $fieldValue value
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
                        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
                        $query = 'SELECT * FROM ' . $viewName . ' WHERE `oxid` = :oxid';
                        $queryResult = $database->select($query, [
                            ':oxid' => $id
                        ]);
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
                        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
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

            \OxidEsales\Eshop\Core\Registry::getUtilsObject()->resetInstanceCache(get_class($this));
        }

        //returns stdClass implementing __toString() method due to uknown scenario where this var should be used.
        if (!$this->isPropertyLoaded($variableName)) {
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
        return $this->isPropertyLoaded($variableName) || $this->isPropertyField($variableName);
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
        foreach ($classVariables as $name => $value) {
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
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $tableName = $this->getCoreTableName();
            $title = $database->getOne("select `{$fieldName}` from `{$tableName}` where `oxid` = :oxid", [
                ':oxid' => $this->getId()
            ]);
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

        foreach ($dbRecord as $name => $value) {
            $this->_setFieldData($name, $value);
        }

        $oxidField = $this->_getFieldLongName('oxid');
        if ($this->$oxidField instanceof Field) {
            $this->_sOXID = $this->$oxidField->value;
        }
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
                $this->_sOXID = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID();
            }
        }

        $idFieldName = $this->getCoreTableName() . '__oxid';
        $this->$idFieldName = new Field($this->_sOXID, Field::T_RAW);

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
                $shopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();
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
        $query = $this->buildSelectString([$this->getViewName() . '.oxid' => $oxid]);
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
     * @param null|array $whereCondition SQL select WHERE conditions array (default false)
     *
     * @return string
     */
    public function buildSelectString($whereCondition = null)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $get = $this->getSelectFields();
        $query = "select $get from " . $this->getViewName() . ' where 1 ';

        if ($whereCondition) {
            reset($whereCondition);
            foreach ($whereCondition as $name => $value) {
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
     * @return mixed
     */
    protected function getRecordByQuery($query)
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($query);
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

        return ($this->$longFieldName instanceof Field) ? $this->$longFieldName->value : null;
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
        $selectFields = [];

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
     * Delete this object from the database, returns true if entry was deleted.
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

        $this->dispatchEvent(new BeforeModelDeleteEvent($this));

        $this->_removeElement2ShopRelations($oxid);

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $coreTable = $this->getCoreTableName();
        $deleteQuery = "delete from {$coreTable} where oxid = :oxid";
        $affectedRows = $database->execute($deleteQuery, [
            ':oxid' => $oxid
        ]);
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
     * @throws Exception
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
                    \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($this->$longName, true);
                } elseif (isset($this->$longName->fldtype) && $this->$longName->fldtype == 'timestamp') {
                    \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBTimestamp($this->$longName, true);
                } elseif (isset($this->$longName->fldtype) && $this->$longName->fldtype == 'date') {
                    \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($this->$longName, true);
                }
            }
        }

        $return = false;

        $action = null;
        $response = null;
        /** We must check on the master database, if an entry exists, so we switch to master connection.*/
        \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        if ($this->exists()) {
            //only update if derived update is allowed
            if ($this->allowDerivedUpdate()) {
                $response = $this->_update();
                $action = ACTION_UPDATE;
            }
        } else {
            $response = $this->_insert();
            $action = ACTION_INSERT;
            $this->dispatchEvent(new AfterModelInsertEvent($this));
        }

        $this->onChange($action);

        if ($response) {
            $return = $this->getId();
        }

        return $return;
    }

    /**
     * Checks if derived update is allowed (calls \OxidEsales\Eshop\Core\Model\BaseModel::isDerived)
     *
     * @return bool
     */
    public function allowDerivedUpdate()
    {
        return !$this->isDerived();
    }

    /**
     * Checks if derived delete is allowed (calls \OxidEsales\Eshop\Core\Model\BaseModel::isDerived)
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
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $query = "select {$this->_sExistKey} from {$viewName} where {$this->_sExistKey} = :oxid";

        return ( bool ) $database->getOne($query, [
            ':oxid' => $oxid
        ]);
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
        $this->dispatchEvent(new BeforeModelUpdateEvent($this));
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
        if (ACTION_DELETE == $action) {
            $this->dispatchEvent(new AfterModelDeleteEvent($this));
        } else {
            $this->dispatchEvent(new AfterModelUpdateEvent($this));
        }
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
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();

        $cacheKey = $table . '_allfields_' . $returnSimpleArray;
        $metaFields = $myUtils->fromFileCache($cacheKey);

        if ($metaFields) {
            return $metaFields;
        }

        $metaFields = \OxidEsales\Eshop\Core\DatabaseProvider::getInstance()->getTableDescription($table);

        if (!$returnSimpleArray) {
            $myUtils->toFileCache($cacheKey, $metaFields);

            return $metaFields;
        }

        //returning simple array
        $result = [];
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
     * @see \OxidEsales\Eshop\Core\Model\BaseModel::_getTableFields()
     *
     * @return array
     */
    protected function _getAllFields($returnSimple = false)
    {
        if (!$this->getCoreTableName()) {
            return [];
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
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();

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
     * @return array|bool
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

        return ['oxid' => 0];
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
        if ($this->isPropertyLoaded($fieldLongName)) {
            return;
        }

        //defining the field
        $field = false;

        if (isset($type)) {
            $field = new Field();
            $field->fldtype = $type;
            //T2008-01-29
            //can't clone as the fields are objects and are not fully cloned
            $this->_blIsSimplyClonable = false;
        }

        if (isset($length)) {
            if (!$field) {
                $field = new Field();
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
    protected function _setFieldData($fieldName, $fieldValue, $dataType = Field::T_TEXT)
    {
        $longFieldName = $this->_getFieldLongName($fieldName);
        //$sLongFieldName = $this->_sCoreTable . "__" . strtolower($sFieldName);

        // doing this because in lazy loaded lists on first load it is harmful to have initialised fields but not yet set
        // situation: only first article is loaded fully for "select oxid from oxarticles"
        //if ($this->_blUseLazyLoading && !isset($this->$sLongFieldName))
        //    return;


        //in non lazy loading case we just add a field and do not care about it more
        if (!$this->_blUseLazyLoading
            && !$this->isPropertyLoaded($longFieldName)
        ) {
            $fieldsList = $this->_getAllFields(true);
            if (isset($fieldsList[strtolower($fieldName)])) {
                $this->_addField($fieldName, $this->_getFieldStatus($fieldName));
            }
        }
        // if we have a double field we replace "," with "." in case somebody enters it in european format
        $isPropertyLoaded = $this->isPropertyLoaded($longFieldName);
        if ($isPropertyLoaded
            && isset($this->$longFieldName->fldtype)
            && $this->$longFieldName->fldtype == 'double'
        ) {
            $fieldValue = str_replace(',', '.', $fieldValue);
        }

        // isset is REQUIRED here not to use getter
        if ($isPropertyLoaded
            && is_object($this->$longFieldName)
        ) {
            $this->$longFieldName->setValue($fieldValue, $dataType);
        } else {
            $this->$longFieldName = new Field($fieldValue, $dataType);
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
                return property_exists($metaInfo, 'default_value') ? $metaInfo->default_value : null;
            }
        }

        return false;
    }

    /**
     * returns quoted field value for using in update statement
     *
     * @param string $fieldName name of field
     * @param Field  $field     field object
     *
     * @return string
     */
    protected function _getUpdateFieldValue($fieldName, $field)
    {
        $fieldValue = null;
        if ($field instanceof Field) {
            $fieldValue = $field->getRawValue();
        } elseif (isset($field->value)) {
            $fieldValue = $field->value;
        }

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
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
     * @throws DatabaseException On database errors
     *
     * @return bool Will always return true. On failure an exception is thrown.
     */
    protected function _update()
    {
        //do not allow derived item update
        if (!$this->allowDerivedUpdate()) {
            return false;
        }

        if (!$this->getId()) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\ObjectException::class);
            $exception->setMessage('EXCEPTION_OBJECT_OXIDNOTSET');
            $exception->setObject($this);
            throw $exception;
        }
        $coreTableName = $this->getCoreTableName();

        $idKey = \OxidEsales\Eshop\Core\Registry::getUtils()->getArrFldName($coreTableName . '.oxid');
        $this->$idKey = new Field($this->getId(), Field::T_RAW);
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $updateQuery = "update {$coreTableName} set " . $this->_getUpdateFields() .
                       " where {$coreTableName}.oxid = " . $database->quote($this->getId());

        $this->beforeUpdate();
        $this->executeDatabaseQuery($updateQuery);

        return true;
    }

    /**
     * Execute a query on the database.
     *
     * @param string $query The command to execute on the database.
     * @param array  $params Parameters to fill the querry
     *
     * @return int The number of affected rows.
     */
    protected function executeDatabaseQuery($query, $params = [])
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        return $database->execute($query, $params);
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
        $myConfig = $this->getConfig();
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();

        // let's get a new ID
        if (!$this->getId()) {
            $this->setId();
        }

        $idKey = $myUtils->getArrFldName($this->getCoreTableName() . '.oxid');
        $this->$idKey = new Field($this->getId(), Field::T_RAW);
        $insertSql = "Insert into {$this->getCoreTableName()} set ";

        $shopIdField = $myUtils->getArrFldName($this->getCoreTableName() . '.oxshopid');

        if ($this->isPropertyLoaded($shopIdField)
            && (!$this->isPropertyField($shopIdField) || !$this->$shopIdField->value)
        ) {
            $this->$shopIdField = new Field(
                $myConfig->getShopId(),
                Field::T_RAW
            );
        }

        $insertSql .= $this->_getUpdateFields($this->getUseSkipSaveFields());

        return $result = (bool) $this->executeDatabaseQuery($insertSql);
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
        $fieldNames = $this->_aFieldNames;

        if (!$this->isAdmin() && $this->_blUseLazyLoading) {
            $fieldNames = $this->_getNonCachedFieldNames(true);
        }

        return array_keys($fieldNames);
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
     * Returns true if the property is loaded.
     *
     * @param  string $name
     *
     * @return bool
     */
    public function isPropertyLoaded($name)
    {
        return property_exists($this, $name) && $this->$name !== null;
    }

    /**
     * adds and activefrom/activeto to the query
     *
     * @param string $query
     * @param string $tableName
     *
     * @return string
     */
    protected function addSqlActiveRangeSnippet($query, $tableName)
    {
        $dateObj = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
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

    /**
     * Returns true if the property is a Field.
     *
     * @param  string $name
     *
     * @return bool
     */
    private function isPropertyField($name)
    {
        return $this->$name instanceof Field;
    }
}
