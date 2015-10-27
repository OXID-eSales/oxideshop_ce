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

namespace OxidEsales\Eshop\Core\GenericImport\ImportObject;

use Exception;
use oxBase;
use oxDb;
use oxI18n;
use OxidEsales\Eshop\Core\GenericImport\GenericImport;
use oxRegistry;

/**
 * Main import type superclass - includes methods abstraction and basic implementation
 * for all erp object types
 */
abstract class ImportObject
{
    /** @var string Database table name. */
    protected $_sTableName = null;

    /** @var array List of database fields, to which data should be imported. */
    protected $_aFieldList = null;

    /** @var array List of database key fields (i.e. oxid). */
    protected $_aKeyFieldList = null;

    /** @var string Shop object name. */
    protected $_sShopObjectName = null;

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
     * setter for field list
     *
     * @param array $aFieldList fields to set
     */
    public function setFieldList($aFieldList)
    {
        $this->_aFieldList = $aFieldList;
    }

    /**
     * Returns table or View name
     *
     * @return string
     */
    public function getTableName()
    {
        $iShopID = oxRegistry::getConfig()->getShopId();
        return getViewName($this->_sTableName, -1, $iShopID);
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
            throw new Exception(GenericImport::ERROR_USER_NO_RIGHTS);
        }
    }

    /**
     * Basic access check for creating new objects
     *
     * @param array $aData fields to be written
     *
     * @throws Exception on now access
     */
    public function checkCreateAccess($aData)
    {
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
            $aRParams[] = strtolower($this->_sTableName . '__' . $sField);
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

        if ($sObjectName) {
            $oShopObject = oxNew($sObjectName);
        } else {
            $oShopObject = oxNew('oxBase');
            $oShopObject->init($this->getTableName());
        }

        if ($oShopObject instanceof oxI18n) {
            $oShopObject->setLanguage(0);
            $oShopObject->setEnableMultilang(false);
        }

        $sViewName = $oShopObject->getViewName();
        $sFields = str_ireplace('`' . $sViewName . "`.", "", strtoupper($oShopObject->getSelectFields()));
        $sFields = str_ireplace(array(" ", "`"), array("", ""), $sFields);
        $this->_aFieldList = explode(",", $sFields);

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
        if (!is_array($this->getKeyFields())) {
            return null;
        }

        $oDb = oxDb::getDb();

        $aWhere = array();
        $blAllKeys = true;
        foreach ($this->getKeyFields() as $sKey) {
            if (array_key_exists($sKey, $aData)) {
                $aWhere[] = $sKey . '=' . $oDb->qstr($aData[$sKey]);
            } else {
                $blAllKeys = false;
            }
        }

        if ($blAllKeys) {
            $sSelect = 'SELECT OXID FROM ' . $this->getTableName() . ' WHERE ' . implode(' AND ', $aWhere);

            return $oDb->getOne($sSelect);
        }

        return null;
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
            $aData['OXSHOPID'] = oxRegistry::getConfig()->getShopId();
        }

        if (!isset($aData['OXID'])) {
            $aData['OXID'] = $this->getOxidFromKeyFields($aData);
        }

        // null values support
        foreach ($aData as $key => $val) {
            if (!strlen((string) $val)) {
                // oxbase will quote it as string if db does not support null for this field
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
     * Insert or Update a Row into database
     *
     * @param array $aData assoc. Array with fieldnames, values what should be stored in this table
     *
     * @return string | false
     */
    public function import($aData)
    {
        return $this->saveObject($aData, false);
    }

    /**
     * Checks if id field is valid
     *
     * @param string $sID field check id
     */
    protected function _checkIDField($sID)
    {
        if (!isset($sID) || !$sID) {
            throw new Exception("ERROR: Articlenumber/ID missing!");
        } elseif (strlen($sID) > 32) {
            throw new Exception("ERROR: Articlenumber/ID longer then allowed (32 chars max.)!");
        }
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
            $oShopObject = oxNew($sObjectName, 'core');
            if ($oShopObject instanceof oxI18n) {
                $oShopObject->setLanguage(0);
                $oShopObject->setEnableMultilang(false);
            }
        } else {
            $oShopObject = oxNew('oxbase', 'core');
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
            $blLoaded = $oShopObject->load($aData['OXID']);
        }

        $aData = $this->_preAssignObject($oShopObject, $aData, $blAllowCustomShopId);

        if ($blLoaded) {
            $this->checkWriteAccess($oShopObject, $aData);
        } else {
            $this->checkCreateAccess($aData);
        }

        $oShopObject->assign($aData);

        if ($blAllowCustomShopId) {
            $oShopObject->setIsDerived(false);
        }

        if ($this->_preSaveObject($oShopObject, $aData)) {
            // store
            if ($oShopObject->save()) {
                return $this->_postSaveObject($oShopObject, $aData);
            }
        }

        return false;
    }

    /**
     * post saving hook. can finish transactions if needed or ajust related data
     *
     * @param oxBase $oShopObject shop object
     * @param array  $aData       data to save
     *
     * @return mixed data to return
     */
    protected function _postSaveObject($oShopObject, $aData)
    {
        // returning ID on success
        return $oShopObject->getId();
    }
}
