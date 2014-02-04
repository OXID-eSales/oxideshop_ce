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
 * main erp type superclass - includes methods abstraction and basic implementation
 * for all erp object types
 */
class oxERPType
{
    public static $ERROR_WRONG_SHOPID = "Wrong shop id, operation not allowed!";

    protected   $_sTableName        = null;
    protected   $_sFunctionSuffix   = null;
    protected   $_aFieldList        = null;
    protected   $_aKeyFieldList     = null;
    protected   $_sShopObjectName   = null;

    /**
     * If true a export will be restricted vias th oxshopid column of the table
     *
     * @var unknown_type
     */
    protected $_blRestrictedByShopId = false;

    /**
     * versioning support for db layers
     *
     * @var array
     */
    protected $_aFieldListVersions = null;

    /**
     * getter for _sFunctionSuffix
     *
     * @return string
     */
    public function getFunctionSuffix()
    {
        return $this->_sFunctionSuffix;
    }

    /**
     * getter for _sShopObjectName
     *
     * @return string
     */
    public function getShopObjectName()
    {
        return $this->_sShopObjectName;
    }

    /**
     * getter for _sTableName
     *
     * @return string
     */
    public function getBaseTableName()
    {
        return $this->_sTableName;
    }

    /**
     * class constructor
     *
     * @return null
     */
    public function __construct()
    {
        $this->_sFunctionSuffix = str_replace( "oxERPType_", "", get_class( $this));
    }

    /**
     * setter for the function prefix
     *
     * @param string $sNew new suffix
     *
     * @return null
     */
    public function setFunctionSuffix($sNew)
    {
        $this->_sFunctionSuffix = $sNew;
    }

    /**
     * setter for field list
     *
     * @param array $aFieldList fields to set
     *
     * @return null
     */
    public function setFieldList($aFieldList)
    {
        $this->_aFieldList = $aFieldList;
    }

    /**
     * Returns table or Viewname
     *
     * @param int $iShopID   shop id - default is the current shop id
     * @param int $iLanguage language id
     *
     * @return string
     */
    public function getTableName($iShopID=null, $iLanguage = 0)
    {
        if ($iShopID === null) {
            $iShopID = oxRegistry::getConfig()->getShopId();
        }

        return getViewName($this->_sTableName, -1, $iShopID);
    }

    /**
     * Creates Array with [iLanguage][sFieldName]
     *
     * @return array
     */
    private function _getMultilangualFields()
    {
        $aRet = array();

        $aData = oxDb::getInstance()->getTableDescription( $this->_sTableName);

        foreach ($aData as $key => $oADODBField) {
            $iLang = substr( $oADODBField->name, strlen( $oADODBField->name) - 1, 1);
            if ( is_numeric( $iLang) &&  substr( $oADODBField->name, strlen( $oADODBField->name) - 2, 1) == '_') {
                // multilangual field
                $sMainFld = str_replace( '_'.$iLang, "", $oADODBField->name);
                $aRet[$iLang][$sMainFld] = $oADODBField->name.' as '.$sMainFld;
            }
        }

        return $aRet;
    }

    /**
     * return sql column name of given table column
     *
     * @param string $sField    field to get
     * @param int    $iLanguage language id
     * @param int    $iShopID   shop id
     *
     * @return string
     */
    protected function _getSqlFieldName($sField, $iLanguage = 0, $iShopID = 1)
    {
        if ($iLanguage) {
            $aMultiLang = $this->_getMultilangualFields();
            // we need to load different fields
            if ( isset( $aMultiLang[$iLanguage][$sField])) {
                $sField = $aMultiLang[$iLanguage][$sField];
            }
        }

            switch ($sField) {
                case 'OXSHOPID':
                case 'OXSHOPINCL':
                    return "1 as $sField";
                case 'OXSHOPEXCL':
                    return "0 as $sField";
            }

        return $sField;
    }

    /**
     * returns SQL string for this type
     *
     * @param string $sWhere    where part of sql
     * @param int    $iLanguage language id
     * @param int    $iShopId   shop id
     *
     * @return string
     */
    public function getSQL( $sWhere, $iLanguage = 0, $iShopId = 1)
    {
        if ( !$this->_aFieldList) {
            return;
        }

        $sSQL    = 'select ';
        $blSep = false;

        foreach ( $this->_aFieldList as $sField) {
            if ( $blSep) {
                $sSQL .= ',';
            }

            $sSQL .= $this->_getSqlFieldName($sField, $iLanguage, $iShopId);
            $blSep = true;
        }


        $sSQL .= ' from '.$this->getTableName($iShopId, $iLanguage).' '.$sWhere;

        return $sSQL;
    }

    /**
     * returns the "order by " string for  a sql query
     *
     * @param string $sFieldName order by that field
     * @param string $sType      allowed values ASC and DESC
     *
     * @return string
     */
    public function getSortString($sFieldName = null, $sType = null)
    {
        $sRes = " order by ";
        if ($sFieldName) {
            $sRes .= $sFieldName;
        } else {
            $sRes .= "oxid";
        }
        if ($sType && ($sType == "ASC" || $sType == "DESC")) {
            $sRes .= " ". $sType;
        }
        return $sRes;
    }

    /**
     * Basic access check for writing data, checks for same shopid, should be overridden if field oxshopid does not exist
     *
     * @param oxBase $oObj  loaded shop object
     * @param array  $aData fields to be written, null for default
     *
     * @throws Exception on now access
     *
     * @return null
     */
    public function checkWriteAccess($oObj, $aData = null)
    {
            return;

        if ($oObj->isDerived()) {
            throw new Exception( oxERPBase::$ERROR_USER_NO_RIGHTS);
        }
    }

    /**
     * Basic access check for creating new objects
     *
     * @param array $aData fields to be written
     *
     * @throws Exception on now access
     *
     * @return null
     */
    public function checkCreateAccess($aData)
    {
    }

    /**
     * checks done to make sure deletion is possible and allowed
     *
     * @param string $sId id of object
     *
     * @throws Exception on error
     *
     * @return object
     */
    public function getObjectForDeletion( $sId)
    {
        $myConfig = oxRegistry::getConfig();

        if (!isset($sId)) {
            throw new Exception( "Missing ID!");
        }

        $sName = $this->getShopObjectName();
        if ($sName) {
            $oObj = oxNew( $sName, "core");
        } else {
            $oObj = oxNew( 'oxbase', 'core');
            $oObj->init($this->getBaseTableName());
        }

        if (!$oObj->exists($sId)) {
            throw new Exception( $this->getShopObjectName(). " " . $sId. " does not exists!");
        }

        //We must load the object here, to check shopid and return it for further checks
        if (!$oObj->Load($sId)) {
            //its possible that access is restricted allready
            throw new Exception( "No right to delete object {$sId} !");
        }

        if (!$this->_isAllowedToEdit($oObj->getShopId())) {
            throw new Exception( "No right to delete object {$sId} !");
        }

        return $oObj;
    }

    /**
     * checks if user is allowed to edit in this shop
     *
     * @param int $iShopId shop id
     *
     * @return bool
     */
    protected function _isAllowedToEdit($iShopId)
    {
        $oUsr = oxNew('oxUser');
        $oUsr->loadAdminUser();

        if ($oUsr->oxuser__oxrights->value == "malladmin") {
            return true;
        } elseif ($oUsr->oxuser__oxrights->value == (int) $iShopId) {
            return true;
        }

        return false;
    }

    /**
     * direct sql check if it is allowed to delete the OXID of the current table
     *
     * @param string $sId object id
     *
     * @throws Exception on no access
     *
     * @return null
     */
    protected function _directSqlCheckForDeletion($sId)
    {
        $oDb = oxDb::getDb();
        $sSql = "select oxshopid from ".$this->_sTableName." where oxid = " . $oDb->quote( $sId );
        try {
            $iShopId = $oDb->getOne($sSql);
        } catch (Exception $e) {
            // no shopid was found
            return;
        }
        if (!$this->_isAllowedToEdit($iShopId)) {
            throw new Exception( "No right to delete object {$sId} !");
        }
    }

    /**
     * default check if it is allowed to delete the OXID of the current table
     *
     * @param string $sId object id
     *
     * @throws Exception on no access
     *
     * @return null
     */
    public function checkForDeletion($sId)
    {

        if ( !isset($sId)) {
            throw new Exception( "Missing ID!");
        }
        // malladmin can do it
        $oUsr = oxNew('oxUser');
        $oUsr->loadAdminUser();
        if ($oUsr->oxuser__oxrights->value == "malladmin") {
            return;
        }
        try {
            $this->getObjectForDeletion($sId);
        } catch (oxSystemComponentException $e) {
            if ($e->getMessage() == 'EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND') {
                $this->_directSqlCheckForDeletion($sId);
            } else {
                throw $e;
            }
        }
    }

    /**
     * default deletion of the given OXID in the current table
     *
     * @param string $sID object id
     *
     * @return bool
     */
    public function delete($sID)
    {
        $myConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb();
        $sSql = "delete from ".$this->_sTableName." where oxid = " . $oDb->quote( $sID );

        return $oDb->Execute($sSql);
    }

    /**
     * default delete call to the given object
     *
     * @param object $oObj object
     * @param string $sID  object id
     *
     * @return bool
     */
    public function deleteObject($oObj, $sID)
    {
        return $oObj->delete($sID);
    }

    /**
     * We have the possibility to add some data
     *
     * @param array $aFields initial data
     *
     * @return array
     */
    public function addExportData( $aFields)
    {
        return $aFields;
    }

    /**
     * allows to modify data before import
     *
     * @param array $aFields initial data
     *
     * @see _preAssignObject
     *
     * @return array
     */
    public function addImportData($aFields)
    {
        return $aFields;
    }

    /**
     * used for the RR implementation, right now not really used
     *
     * @return array
     */
    public function getRightFields()
    {
        $aRParams = array();
        if (!$this->_aFieldList) {
            $this->getFieldList();
        }

        foreach ($this->_aFieldList as $sField) {
            $aRParams[] = strtolower($this->_sTableName.'__'.$sField);
        }
        return $aRParams;
    }

    /**
     * returns the predefined field list
     *
     * @return array
     */
    public function getFieldList()
    {
        $sObjectName = $this->getShopObjectName();

        if ( $sObjectName ) {
            $oShopObject = oxNew( $sObjectName );
        } else {
            $oShopObject = oxNew( 'oxbase' );
            $oShopObject->init( $this->getTableName() );
        }

        if ($oShopObject instanceof oxI18n) {
            $oShopObject->setLanguage( 0 );
            $oShopObject->setEnableMultilang(false);
        }

        $sViewName = $oShopObject->getViewName();
        $sFields = str_ireplace( '`' . $sViewName . "`.", "", strtoupper($oShopObject->getSelectFields()) );
        $sFields = str_ireplace( array(" ", "`"), array("", ""), $sFields );
        $this->_aFieldList = explode( ",", $sFields );

        return $this->_aFieldList;
    }

    /**
     * returns the keylist array
     *
     * @return array
     */
    public function getKeyFields()
    {
        return $this->_aKeyFieldList;
    }

    /**
     * returns oxid of this data type from key fields
     *
     * @param array $aData data for object
     *
     * @return string
     */
    public function getOxidFromKeyFields($aData)
    {
        $myConfig = oxRegistry::getConfig();

        if (!is_array($this->getKeyFields())) {
            return null;
        }

        $oDb = oxDb::getDb();

        $aWhere = array();
        $blAllKeys = true;
        foreach ($this->getKeyFields() as $sKey) {
            if (array_key_exists($sKey, $aData)) {
                $aWhere[] = $sKey.'='.$oDb->qstr($aData[$sKey]);
            } else {
                $blAllKeys = false;
            }
        }

        if ($blAllKeys) {
            $sSelect = 'SELECT OXID FROM '.$this->getTableName().' WHERE '.implode(' AND ', $aWhere);
            return $oDb->getOne( $sSelect );
        }

        return null;
    }

    /**
     * returns try if type has key fields array
     *
     * @return bool
     */
    public function hasKeyFields()
    {
        if (isset($this->_aKeyFieldList) && is_array($this->_aKeyFieldList)) {
            return true;
        }
        return false;
    }

    /**
     * issued before saving an object. can modify aData for saving
     *
     * @param oxBase $oShopObject         shop object
     * @param array  $aData               data to prepare
     * @param bool   $blAllowCustomShopId if allow custom shop id
     *
     * @return array
     */
    protected function _preAssignObject($oShopObject, $aData, $blAllowCustomShopId)
    {
            if (isset($aData['OXSHOPID'])) {
                $aData['OXSHOPID'] = 'oxbaseshop';
            }


        if (!isset($aData['OXID'])) {
            $aData['OXID'] = $this->getOxidFromKeyFields($aData);
        }

        // null values support
        foreach ($aData as $key => $val) {
            if (!strlen((string) $val)) {
                // oxbase whill quote it as string if db does not support null for this field
                $aData[$key] = null;
            }
        }
        return $aData;
    }

    /**
     * prepares object for saving in shop
     * returns true if save can proceed further
     *
     * @param oxBase $oShopObject shop object
     * @param array  $aData       data for importing
     *
     * @return boolean
     */
    protected function _preSaveObject($oShopObject, $aData)
    {
        return true;
    }

    /**
     * saves data by calling object saving
     *
     * @param array $aData               data for saving
     * @param bool  $blAllowCustomShopId allow custom shop id
     *
     * @return string | false
     */
    public function saveObject($aData, $blAllowCustomShopId)
    {
        $sObjectName = $this->getShopObjectName();
        if ($sObjectName) {
            $oShopObject = oxNew( $sObjectName, 'core');
            if ($oShopObject instanceof oxI18n) {
                $oShopObject->setLanguage( 0 );
                $oShopObject->setEnableMultilang(false);
            }
        } else {
            $oShopObject = oxNew( 'oxbase', 'core');
            $oShopObject->init($this->getBaseTableName());
        }

        foreach ($aData as $key => $value) {
            // change case to UPPER
            $sUPKey = strtoupper($key);
            if (!isset($aData[$sUPKey])) {
                unset($aData[$key]);
                $aData[$sUPKey] = $value;
            }
        }


        $blLoaded = false;
        if ($aData['OXID']) {
            $blLoaded = $oShopObject->load( $aData['OXID']);
        }

        $aData = $this->_preAssignObject( $oShopObject, $aData, $blAllowCustomShopId );

        if ($blLoaded) {
            $this->checkWriteAccess($oShopObject, $aData);
        } else {
            $this->checkCreateAccess($aData);
        }

        $oShopObject->assign( $aData );

        if ($blAllowCustomShopId) {
            $oShopObject->setIsDerived(false);
        }

        if ($this->_preSaveObject($oShopObject, $aData)) {
            // store
            if ( $oShopObject->save()) {
                return $this->_postSaveObject($oShopObject, $aData);
            }
        }

        return false;
    }

    /**
     * post saving hook. can finish transactions if needed or ajust related data
     *
     * @param oxBase $oShopObject shop object
     * @param data   $aData       data to save
     *
     * @return mixed data to return
     */
    protected function _postSaveObject($oShopObject, $aData)
    {
        // returning ID on success
        return $oShopObject->getId();
    }
}

