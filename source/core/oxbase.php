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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Defining triggered action type.
 */
DEFINE('ACTION_NA', 0);
DEFINE('ACTION_DELETE', 1);
DEFINE('ACTION_INSERT', 2);
DEFINE('ACTION_UPDATE', 3);
DEFINE('ACTION_UPDATE_STOCK', 4);

/**
 * Base class associated with database record
 *
 * @package core
 */
class oxBase extends oxSuperCfg
{
    /**
     * Unique object ID. Normally representing record oxid field value
     * @var string
     */
    protected $_sOXID = null;

    /**
     * ID of running shop session (default null).
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
     * @var string
     */
    protected $_sViewTable  = null;


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
     * @param bool $blUseSkipSaveFields - true or false
     *
     * @return null
     */
    public function setUseSkipSaveFields( $blUseSkipSaveFields )
    {
        $this->_blUseSkipSaveFields = $blUseSkipSaveFields;
    }

    /**
     * Class constructor, sets active shop.
     */
    public function __construct()
    {
        // set active shop
        $myConfig = $this->getConfig();
        $this->_sCacheKey = $this->getViewName();


        if ( $this->_blUseLazyLoading ) {
            $this->_sCacheKey .= $myConfig->getActiveView()->getClassName();
        } else {
            $this->_sCacheKey .= 'allviews';
        }

        //do not cache for admin?
        if ( $this->isAdmin() ) {
            $this->_sCacheKey = null;
        }

        $this->setShopId( $myConfig->getShopId() );
    }

    /**
     * Magic setter. If using lazy loading, adds setted field to fields array
     *
     * @param string $sName  name value
     * @param mixed  $sValue value
     *
     * @return null
     */
    public function __set( $sName, $sValue )
    {
        $this->$sName = $sValue;
        if ( $this->_blUseLazyLoading && strpos( $sName, $this->_sCoreTable . '__' ) === 0 ) {
            $sFieldName = str_replace( $this->_sCoreTable . "__", '', $sName );
            if ( $sFieldName != 'oxnid' && ( !isset( $this->_aFieldNames[$sFieldName] ) || !$this->_aFieldNames[$sFieldName] ) ) {
                $aAllFields = $this->_getAllFields(true);
                if ( isset( $aAllFields[strtolower($sFieldName)] ) ) {
                    $iFieldStatus = $this->_getFieldStatus( $sFieldName );
                    $this->_addField( $sFieldName, $iFieldStatus );
                }
            }
        }
    }

    /**
     * Magic getter for older versions and template variables
     *
     * @param string $sName variable name
     *
     * @return mixed
     */
    public function __get( $sName )
    {
        switch ( $sName ) {
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
        if ( $this->_blUseLazyLoading && stripos( $sName, $this->_sCoreTable . "__" ) === 0 ) {

            if ( $this->getId() ) {

                //lazy load it
                $sFieldName      = str_replace( $this->_sCoreTable . '__', '', $sName );
                $sCacheFieldName = strtoupper( $sFieldName );

                $iFieldStatus = $this->_getFieldStatus( $sFieldName );
                $sViewName    = $this->getViewName();
                $sId = $this->getId();

                try {
                    if ( $this->_aInnerLazyCache === null ) {

                        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
                        $sQ = 'SELECT * FROM ' . $sViewName . ' WHERE `oxid` = ' . $oDb->quote( $sId );
                        $rs = $oDb->select( $sQ );
                        if ( $rs && $rs->RecordCount() ) {
                            $this->_aInnerLazyCache = array_change_key_case( $rs->fields, CASE_UPPER );
                            if ( array_key_exists( $sCacheFieldName, $this->_aInnerLazyCache ) ) {
                                $sFieldValue = $this->_aInnerLazyCache[$sCacheFieldName];
                            } else {
                                return null;
                            }
                        } else {
                            return null;
                        }
                    } elseif ( array_key_exists( $sCacheFieldName, $this->_aInnerLazyCache ) ) {
                        $sFieldValue = $this->_aInnerLazyCache[$sCacheFieldName];
                    } else {
                        return null;
                    }

                    $this->_addField( $sFieldName, $iFieldStatus );
                    $this->_setFieldData( $sFieldName, $sFieldValue );

                    //save names to cache for next loading
                    if ($this->_sCacheKey) {
                        $myUtils = oxRegistry::getUtils();
                        $sCacheKey = 'fieldnames_' . $this->_sCoreTable . '_' . $this->_sCacheKey;
                        $aFieldNames = $myUtils->fromFileCache( $sCacheKey );
                        $aFieldNames[$sFieldName] = $iFieldStatus;
                        $myUtils->toFileCache( $sCacheKey, $aFieldNames );
                    }
                } catch ( Exception $e ) {
                    return null;
                }

                //do not use field cache for this page
                //as if we use it for lists then objects are loaded empty instead of lazy loading.
                self::$_blDisableFieldCaching[get_class( $this )] = true;
            }

            oxUtilsObject::getInstance()->resetInstanceCache(get_class($this));
        }

        //returns stdClass implementing __toString() method due to uknown scenario where this var should be used.
        if (!isset( $this->$sName ) ) {
            $this->$sName = null;
        }

        return $this->$sName;
    }

    /**
     * Magic isset() handler. Workaround for detecting if protected properties are set.
     *
     * @param mixed $mVar Supplied class variable
     *
     * @return bool
     */
    public function __isset($mVar)
    {
        return isset($this->$mVar);
    }

    /**
     * Magic function invoked on object cloning. Basically takes care about cloning properly DB fields.
     *
     * @return null
     */
    public function __clone()
    {
        if (!$this->_blIsSimplyClonable) {
            foreach ( $this->_aFieldNames as $sField => $sVal ) {
                $sLongName = $this->_getFieldLongName( $sField );
                if ( is_object($this->$sLongName)) {
                    $this->$sLongName = clone $this->$sLongName;
                }
            }
        }
    }

    /**
     * Clone this object - similar to Copy Constructor.
     *
     * @param object $oObject Object to copy
     *
     * @return null
     */
    public function oxClone( $oObject )
    {
        $aClasVars = get_object_vars( $oObject );
        while (list($name, $value) = each( $aClasVars )) {
            if ( is_object( $oObject->$name ) ) {
                $this->$name = clone $oObject->$name;
            } else {
                $this->$name = $oObject->$name;
            }
        }
    }

    /**
     * Sets the names to main and view tables, loads metadata of each table.
     *
     * @param string $sTableName       Name of DB object table
     * @param bool   $blForceAllFields Forces initialisation of all fields overriding lazy loading functionality
     *
     * @return null
     */
    public function init( $sTableName = null, $blForceAllFields = false )
    {
        if ( $sTableName ) {
            $this->_sCoreTable = $sTableName;
        }

        // reset view table
        $this->_sViewTable = false;

        if ( count( $this->_aFieldNames ) <= 1 ) {
            $this->_initDataStructure( $blForceAllFields );
        }
    }

    /**
     * Assigns DB field values to object fields. Returns true on success.
     *
     * @param array $dbRecord Associative data values array
     *
     * @return null
     */
    public function assign( $dbRecord )
    {
        if ( !is_array( $dbRecord ) ) {
            return;
        }


        reset($dbRecord );
        while ( list( $sName, $sValue ) = each( $dbRecord ) ) {

            // patch for IIS
            //TODO: test it on IIS do we still need it
            //if( is_array($value) && count( $value) == 1)
            //    $value = current( $value);

            $this->_setFieldData( $sName, $sValue );
        }

        $sOxidField = $this->_getFieldLongName( 'oxid' );
        $this->_sOXID = $this->$sOxidField->value;

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
     * @param string $sOXID Record ID
     *
     * @return string
     */
    public function setId( $sOXID = null )
    {
        if ( $sOXID ) {
            $this->_sOXID = $sOXID;
        } else {
            $this->_sOXID = oxUtilsObject::getInstance()->generateUID();
        }

        $sIdVarName = $this->_sCoreTable . '__oxid';
        $this->$sIdVarName = new oxField($this->_sOXID, oxField::T_RAW);

        return $this->_sOXID;
    }

    /**
     * Sets original object shop ID
     *
     * @param int $iShopId New shop ID
     *
     * @return null
     */
    public function setShopId( $iShopId )
    {
        $this->_iShopId = $iShopId;
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
     * @param bool $blForceCoreTableUsage (optional) use core views
     *
     * @return string
     */
    public function getViewName( $blForceCoreTableUsage = null )
    {
        if (!$this->_sViewTable || ( $blForceCoreTableUsage !== null )) {
            if ( $blForceCoreTableUsage === true ) {
                return $this->_sCoreTable;
            }


            if ( ( $blForceCoreTableUsage !== null ) && $blForceCoreTableUsage ) {
                $iShopId = -1;
            } else {
                $iShopId = oxRegistry::getConfig()->getShopId();
            }

            $sViewName = getViewName( $this->_sCoreTable, $this->_blEmployMultilanguage == false ? -1 : $this->getLanguage(), $iShopId );
            if ( $blForceCoreTableUsage !== null ) {
                return $sViewName;
            }
            $this->_sViewTable = $sViewName;
        }
        return $this->_sViewTable;
    }

    /**
     * Lazy loading cache key modifier.
     *
     * @param string $sCacheKey  cache  key
     * @param bool   $blOverride marker to force override cache key
     *
     * @return null
     */
    public function modifyCacheKey( $sCacheKey, $blOverride = false )
    {
        if ( $blOverride ) {
            $this->_sCacheKey = $sCacheKey;
        } else {
            $this->_sCacheKey .= $sCacheKey;
        }
    }

    /**
     * Disables lazy loading mechanism and init object fully
     *
     * @return null
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
     * @param bool $blVal if derived
     *
     * @return null
     */
    public function setIsDerived( $blVal )
    {
        $this->_blIsDerived = $blVal;
    }

    /**
     * Returns true, if object has multi language fields (if object is derived from oxi18n class).
     * In oxBase it is always returns false, as oxBase treats all fields as non multi language.
     *
     * @return bool
     */
    public function isMultilang()
    {
        return false;
    }

    /**
     * Loads object data from DB (object data ID is passed to method). Returns
     * true on success.
     * could throw oxObjectException F ?
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function load( $sOXID )
    {
        $blExistingOldForceCoreTable = $this->_blForceCoreTableUsage;

        $this->_blForceCoreTableUsage = true;

        //getting at least one field before lazy loading the object
        $this->_addField( 'oxid', 0 );
        $sSelect = $this->buildSelectString( array( $this->getViewName() . '.oxid' => $sOXID) );
        $this->_isLoaded = $this->assignRecord( $sSelect );

        $this->_blForceCoreTableUsage = $blExistingOldForceCoreTable;

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
     * @param mixed $aWhere SQL select WHERE conditions array (default false)
     *
     * @return string
     */
    public function buildSelectString( $aWhere = null)
    {
        $oDB = oxDb::getDb();

        $sGet = $this->getSelectFields();
        $sSelect = "select $sGet from " . $this->getViewName() . ' where 1 ';

        if ( $aWhere) {
            reset($aWhere);
            while (list($name, $value) = each($aWhere)) {
                $sSelect .=  ' and ' . $name . ' = '.$oDB->quote($value);
            }
        }

        // add active shop

        return $sSelect;
    }

    /**
     * Performs SQL query, assigns record field values to object. Returns true on success.
     *
     * @param string $sSelect SQL statement
     *
     * @return bool
     */
    public function assignRecord( $sSelect )
    {
        $blRet = false;

        $rs = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->select( $sSelect );

        if ($rs != false && $rs->recordCount() > 0) {
            $blRet = true;
            $this->assign( $rs->fields);
        }

        return $blRet;
    }

    /**
     * Gets field data
     *
     * @param string $sFieldName name (eg. 'oxtitle') of a data field to get
     *
     * @return mixed value of a data field
     */
    public function getFieldData( $sFieldName )
    {
        $sLongFieldName = $this->_getFieldLongName( $sFieldName );
            return $this->$sLongFieldName->value;
    }

    /**
     * Function builds the field list used in select.
     *
     * @param bool $blForceCoreTableUsage (optional) use core views
     *
     * @return string
     */
    public function getSelectFields( $blForceCoreTableUsage = null )
    {
        $aSelectFields = array();

        $sViewName = $this->getViewName( $blForceCoreTableUsage );

        foreach ( $this->_aFieldNames as $sKey => $sField ) {
            if ( $sViewName ) {
                $aSelectFields[] = "`$sViewName`.`$sKey`";
            } else {
                $aSelectFields[] = ".`$sKey`";
            }

        }

        $sSelectFields = join( ', ', $aSelectFields );
        return $sSelectFields;
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete( $sOXID = null)
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();

            //do not allow derived deletion
            if ( !$this->allowDerivedDelete() ) {
                return false;
            }
        }

        if ( !$sOXID ) {
            return false;
        }


        $oDB = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sDelete = "delete from $this->_sCoreTable where oxid = " . $oDB->quote( $sOXID );
        $oDB->execute( $sDelete );
        if ( $blDelete = ( bool ) $oDB->affected_Rows() ) {
            $this->onChange( ACTION_DELETE, $sOXID );
        }

        return $blDelete;
    }


    /**
     * Save this Object to database, insert or update as needed.
     *
     * @return string|bool
     */
    public function save()
    {
        if ( !is_array( $this->_aFieldNames ) ) {
            return false;
        }

        // #739A - should be executed here because of date/time formatting feature
        if ( $this->isAdmin() && !$this->getConfig()->getConfigParam( 'blSkipFormatConversion' ) ) {
            foreach ($this->_aFieldNames as $sName => $sVal) {
                $sLongName = $this->_getFieldLongName($sName);
                if ( isset($this->$sLongName->fldtype) && $this->$sLongName->fldtype == 'datetime' ) {
                    oxRegistry::get('oxUtilsDate')->convertDBDateTime( $this->$sLongName, true );
                } elseif ( isset($this->$sLongName->fldtype) && $this->$sLongName->fldtype == 'timestamp' ) {
                    oxRegistry::get('oxUtilsDate')->convertDBTimestamp( $this->$sLongName, true );
                } elseif ( isset($this->$sLongName->fldtype) && $this->$sLongName->fldtype == 'date' ) {
                    oxRegistry::get('oxUtilsDate')->convertDBDate( $this->$sLongName, true );
                }
            }
        }
        if ( $this->exists() ) {
            //do not allow derived update
            if ( !$this->allowDerivedUpdate() ) {
                return false;
            }

            $blRet = $this->_update();
            $sAction = ACTION_UPDATE;
        } else {
            $blRet = $this->_insert();
            $sAction = ACTION_INSERT;
        }

        $this->onChange($sAction);

        if ( $blRet ) {
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
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function exists( $sOXID = null)
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }
        if ( !$sOXID ) {
            return false;
        }

        $sViewName = $this->getCoreTableName();
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sSelect= "select {$this->_sExistKey} from {$sViewName} where {$this->_sExistKey} = " . $oDb->quote( $sOXID );

        return ( bool ) $oDb->getOne( $sSelect, false, false );
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $blForceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    public function getSqlActiveSnippet( $blForceCoreTable = null )
    {
        $sQ = '';
        $sTable = $this->getViewName($blForceCoreTable);

        // has 'active' field ?
        if ( isset( $this->_aFieldNames['oxactive'] ) ) {
            $sQ = " $sTable.oxactive = 1 ";
        }

        // has 'activefrom'/'activeto' fields ?
        if ( isset( $this->_aFieldNames['oxactivefrom'] ) && isset( $this->_aFieldNames['oxactiveto'] ) ) {

            $sDate = date( 'Y-m-d H:i:s', oxRegistry::get('oxUtilsDate')->getTime() );

            $sQ = $sQ ? " $sQ or " : '';
            $sQ = " ( $sQ ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) ) ";
        }

        return $sQ;
    }

    /**
     * This function is triggered before the record is updated.
     * If you make any update to the database record manually you should also call beforeUpdate() from your script.
     *
     * @param string $sOXID Object ID(default null). Pass the ID in case object is not loaded.
     *
     * @return null
     */
    public function beforeUpdate( $sOXID = null )
    {
    }

    /**
     * This function is triggered whenever the object is saved or deleted.
     * onChange() is triggered after saving the changes in Save() method, after deleting the instance from the database.
     * If you make any change to the database record manually you should also call onChange() from your script.
     *
     * @param int    $iAction Action identifier.
     * @param string $sOXID   Object ID(default null). Pass the ID in case object is not loaded.
     *
     * @return null
     */
    public function onChange( $iAction = null, $sOXID = null )
    {
    }


    /**
     * Sets item as list element
     *
     * @return null
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
     * @param string $sTable  Original table name
     * @param int    $sShopID Shop ID
     *
     * @return string
     */
    protected function _getObjectViewName( $sTable, $sShopID = null )
    {
        return getViewName( $sTable, -1, $sShopID);
    }


    /**
     * Returns meta field or simple array of all object fields.
     * This method is slow and normally is called before field cache is built.
     * Make sure it is not called after first page is loaded and cache data is fully built (until tmp dir is cleaned).
     *
     * @param string $sTable         table name
     * @param bool   $blReturnSimple Set $blReturnSimple to true when you need simple array (meta data array is returned otherwise)
     *
     * @return array
     */
    protected function _getTableFields($sTable, $blReturnSimple = false )
    {
        $myUtils = oxRegistry::getUtils();

        $sCacheKey   = $sTable . '_allfields_' . $blReturnSimple;
        $aMetaFields = $myUtils->fromFileCache( $sCacheKey );

        if ( $aMetaFields ) {
            return $aMetaFields;
        }

        $aMetaFields = oxDb::getInstance()->getTableDescription( $sTable );

        if ( !$blReturnSimple ) {
            $myUtils->toFileCache( $sCacheKey, $aMetaFields );
            return $aMetaFields;
        }

        //returning simple array
        $aRet = array();
        if (is_array($aMetaFields)) {
            foreach ( $aMetaFields as $oVal ) {
                $aRet[strtolower( $oVal->name )] = 0;
            }
        }

        $myUtils->toFileCache( $sCacheKey, $aRet);

        return $aRet;
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
    protected function _getAllFields($blReturnSimple = false )
    {
        if (!$this->_sCoreTable) {
            return array();
        }
        return $this->_getTableFields($this->_sCoreTable, $blReturnSimple);
    }

    /**
     * Initializes object data structure.
     * Either by trying to load from cache or by calling $this->_getNonCachedFieldNames
     *
     * @param bool $blForceFullStructure Set to true if you want to load full structure in any case.
     *
     * @return null
     */
    protected function _initDataStructure($blForceFullStructure = false )
    {
        $myUtils = oxRegistry::getUtils();

        //get field names from cache
        $aFieldNames = null;
        $sFullCacheKey = 'fieldnames_' . $this->_sCoreTable . '_' . $this->_sCacheKey;
        if ($this->_sCacheKey && !$this->_isDisabledFieldCache()) {
            $aFieldNames = $myUtils->fromFileCache( $sFullCacheKey );
        }

        if (!$aFieldNames) {
            $aFieldNames = $this->_getNonCachedFieldNames( $blForceFullStructure );
            if ($this->_sCacheKey && !$this->_isDisabledFieldCache()) {
                $myUtils->toFileCache( $sFullCacheKey, $aFieldNames );
            }
        }

        if ( $aFieldNames !== false ) {
            foreach ( $aFieldNames as $sField => $sStatus ) {
                $this->_addField( $sField, $sStatus );
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
     * @param bool $blForceFullStructure Whether to force loading of full data structure
     *
     * @return array
     */
    protected function _getNonCachedFieldNames( $blForceFullStructure = false )
    {
        //T2008-02-22
        //so if this method is executed on cached version we see it when profiling
        startProfile('!__CACHABLE__!');

        //case 1. (admin)
        if ($this->isAdmin()) {
            $aMetaFields = $this->_getAllFields();
            foreach ( $aMetaFields as $oField ) {
                if ( $oField->max_length == -1 ) {
                    $oField->max_length = 10;      // double or float
                }

                if ( $oField->type == 'datetime' ) {
                    $oField->max_length = 20;
                }

                $this->_addField( $oField->name, $this->_getFieldStatus($oField->name), $oField->type, $oField->max_length );
            }
            stopProfile('!__CACHABLE__!');
            return false;
        }

        //case 2. (just get all fields)
        if ( $blForceFullStructure || !$this->_blUseLazyLoading ) {
            $aMetaFields = $this->_getAllFields(true);
            /*
            foreach ( $aMetaFields as $sFieldName => $sVal) {
                $this->_addField( $sFieldName, $this->_getFieldStatus($sFieldName));
            }*/
            stopProfile('!__CACHABLE__!');
            return $aMetaFields;
        }

        //case 3. (get only oxid field, so we can fetch the rest of the fields over lazy loading mechanism)
        stopProfile('!__CACHABLE__!');
        return array('oxid' => 0);
    }

    /**
     * Returns _aFieldName[] value. 0 means - non multi language, 1 - multi language field. But this is defined only in derived oxi18n class.
     * In oxBase it is always 0, as oxBase treats all fields as non multi language.
     *
     * @param string $sFieldName Field name
     *
     * @return int
     */
    protected function _getFieldStatus( $sFieldName )
    {
        return 0;
    }

    /**
     * Adds additional field to meta structure
     *
     * @param string $sName   Field name
     * @param int    $iStatus Field name status. In derived classes it indicates multi language status.
     * @param string $sType   Field type
     * @param string $sLength Field Length
     *
     * @return null
     */
    protected function _addField($sName, $iStatus, $sType = null, $sLength = null )
    {
        //preparation
        $sName = strtolower( $sName );

        //adding field names element
        $this->_aFieldNames[$sName] = $iStatus;

        //already set?
        $sLongName = $this->_getFieldLongName( $sName );
        if ( isset($this->$sLongName) ) {
            return;
        }

        //defining the field
        $oField = false;

        if ( isset( $sType ) ) {
            $oField = new oxField();
            $oField->fldtype = $sType;
            //T2008-01-29
            //can't clone as the fields are objects and are not fully cloned
            $this->_blIsSimplyClonable = false;
        }

        if ( isset( $sLength ) ) {
            if ( !$oField ) {
                $oField = new oxField();
            }
            $oField->fldmax_length = $sLength;
            $this->_blIsSimplyClonable = false;
        }

        $this->$sLongName = $oField;
    }

    /**
     * Returns long field name in "<table>__<field_name>" format.
     *
     * @param string $sFieldName Short field name
     *
     * @return string
     */
    protected function _getFieldLongName( $sFieldName )
    {
        //trying to avoid strpos call as often as possible
        if ( $sFieldName[2] == $this->_sCoreTable[2] && strpos( $sFieldName, $this->_sCoreTable . '__' ) === 0 ) {
            return $sFieldName;
        }

        return $this->_sCoreTable . '__' . strtolower( $sFieldName );
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT )
    {

        $sLongFieldName = $this->_getFieldLongName( $sFieldName);
        //$sLongFieldName = $this->_sCoreTable . "__" . strtolower($sFieldName);

        //T2008-03-14
        //doing this because in lazy loaded lists on first load it is harmful to have initialised fields but not yet set
        //situation: only first article is loaded fully for "select oxid from oxarticles"
        /*
        if ($this->_blUseLazyLoading && !isset($this->$sLongFieldName))
            return;*/

        //in non lazy loading case we just add a field and do not care about it more
        if (!$this->_blUseLazyLoading && !isset( $this->$sLongFieldName )) {
            $aFields = $this->_getAllFields(true);
            if ( isset( $aFields[strtolower( $sFieldName )] ) ) {
                $this->_addField( $sFieldName, $this->_getFieldStatus( $sFieldName ) );
            }
        }
        // if we have a double field we replace "," with "." in case somebody enters it in european format
        if (isset($this->$sLongFieldName) && isset( $this->$sLongFieldName->fldtype ) && $this->$sLongFieldName->fldtype == 'double') {
            $sValue = str_replace( ',', '.', $sValue );
        }

        // isset is REQUIRED here not to use getter
        if ( isset( $this->$sLongFieldName ) && is_object( $this->$sLongFieldName ) ) {
            $this->$sLongFieldName->setValue( $sValue, $iDataType );
        } else {
            $this->$sLongFieldName = new oxField( $sValue, $iDataType );
        }

    }

    /**
     * check if db field can be null
     *
     * @param string $sFieldName db field name
     *
     * @return bool
     */
    protected function _canFieldBeNull( $sFieldName )
    {
        $aMetaData = $this->_getAllFields();
        foreach ( $aMetaData as $oMetaInfo ) {
            if ( strcasecmp( $oMetaInfo->name, $sFieldName ) == 0 ) {
                return !$oMetaInfo->not_null;
            }
        }
        return false;
    }

    /**
     * returns default field value
     *
     * @param string $sFieldName db field name
     *
     * @return mixed
     */
    protected function _getFieldDefaultValue( $sFieldName )
    {
        $aMetaData = $this->_getAllFields();
        foreach ( $aMetaData as $oMetaInfo ) {
            if ( strcasecmp( $oMetaInfo->name, $sFieldName ) == 0 ) {
                return $oMetaInfo->default_value;
            }
        }
        return false;
    }

    /**
     * returns quoted field value for using in update statement
     *
     * @param string  $sFieldName name of field
     * @param oxField $oField     field object
     *
     * @return string
     */
    protected function _getUpdateFieldValue( $sFieldName, $oField )
    {
        $mValue = null;
        if ( $oField instanceof oxField ) {
            $mValue = $oField->getRawValue();
        } elseif ( isset( $oField->value ) ) {
            $mValue = $oField->value;
        }

        $oDb = oxDb::getDb();
        //Check if this field value is null AND it can be null according if not returning default value
        if ( ( null === $mValue ) ) {
            if ( $this->_canFieldBeNull( $sFieldName ) ) {
                return 'null';
            } elseif ( $mValue = $this->_getFieldDefaultValue( $sFieldName ) ) {
                return $oDb->quote( $mValue );
            }
        }

        return $oDb->quote( $mValue );
    }

    /**
     * Get object fields sql part used for updates or inserts:
     * return e.g.  fldName1 = 'value1',fldName2 = 'value2'...
     *
     * @param bool $blUseSkipSaveFields forces usage of skip save fields array (default is true)
     *
     * @return string
     */
    protected function _getUpdateFields( $blUseSkipSaveFields = true )
    {
        $sSql = '';
        $blSep  = false;

        foreach ( array_keys( $this->_aFieldNames ) as $sKey ) {
            $sLongName = $this->_getFieldLongName( $sKey );
            $oField = $this->$sLongName;


            if ( !$blUseSkipSaveFields || ( $blUseSkipSaveFields && !in_array( strtolower( $sKey ), $this->_aSkipSaveFields ) ) ) {
                $sSql .= (( $blSep) ? ',' : '' ) . $sKey . ' = ' . $this->_getUpdateFieldValue( $sKey, $oField );
                $blSep = true;
            }
        }

        return $sSql;
    }

    /**
     * Update this Object into the database, this function only works on
     * the main table, it will not save any dependent tables, which might
     * be loaded through oxlist.
     *
     * @throws oxObjectException Throws on failure inserting
     *
     * @return bool
     */
    protected function _update()
    {
        //do not allow derived item update
        if ( !$this->allowDerivedUpdate() ) {
            return false;
        }


        if ( !$this->getId() ) {
            /**
             * @var oxObjectException $oEx
             */
            $oEx = oxNew( 'oxObjectException' );
            $oEx->setMessage( 'EXCEPTION_OBJECT_OXIDNOTSET' );
            $oEx->setObject($this);
            throw $oEx;
        }

        $sIDKey = oxRegistry::getUtils()->getArrFldName( $this->_sCoreTable . '.oxid' );
        $this->$sIDKey = new oxField($this->getId(), oxField::T_RAW);
        $oDb = oxDb::getDb();

        $sUpdate= "update {$this->_sCoreTable} set " . $this->_getUpdateFields()
                 ." where {$this->_sCoreTable}.oxid = " . $oDb->quote( $this->getId() );

        //trigger event
        $this->beforeUpdate();

        $blRet = (bool) $oDb->execute( $sUpdate );

        return $blRet;
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

        $oDb      = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $myConfig = $this->getConfig();
        $myUtils  = oxRegistry::getUtils();

        // let's get a new ID
        if ( !$this->getId()) {
            $this->setId();
        }

        $sIDKey = $myUtils->getArrFldName( $this->_sCoreTable . '.oxid' );
        $this->$sIDKey = new oxField( $this->getId(), oxField::T_RAW );
        $sInsert = "Insert into {$this->_sCoreTable} set ";

        //setting oxshopid
        $sShopField = $myUtils->getArrFldName( $this->_sCoreTable . '.oxshopid' );

        if ( isset( $this->$sShopField ) && !$this->$sShopField->value ) {
            $this->$sShopField = new oxField( $myConfig->getShopId(), oxField::T_RAW );
        }


        $sInsert .= $this->_getUpdateFields( $this->getUseSkipSaveFields() );

        $blRet = (bool) $oDb->execute( $sInsert );

        return $blRet;
    }

    /**
     * Checks if current class disables field caching.
     * This method is primary used in unit tests.
     *
     * @return bool
     */
    protected function _isDisabledFieldCache()
    {
        $sClass = get_class( $this );
        if ( isset( self::$_blDisableFieldCaching[$sClass] ) && self::$_blDisableFieldCaching[$sClass] ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if object ID's first two chars are 'o' and 'x'. Returns true or false
     *
     * @return bool
     */
    public function isOx()
    {
        $sOxId = $this->getId();
        if ( $sOxId[0] == 'o' && $sOxId[1] == 'x' ) {
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
     * @param bool $blReadOnly readonly flag
     *
     * @return null
     */
    public function setReadOnly( $blReadOnly )
    {
        $this->_blReadOnly = $blReadOnly;
    }

    /**
     * Returns array with object field names
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys( $this->_aFieldNames );
    }

    /**
     * Adds additional field name to meta structure
     *
     * @param string $sName Field name
     *
     * @return null;
     */
    public function addFieldName( $sName )
    {
        //preparation
        $sName = strtolower( $sName );
        $this->_aFieldNames[$sName] = 0;
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

}
