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

use Exception;
use oxERPType;
use oxRegistry;

/**
 * Class GenericImport
 * @package OxidEsales\Eshop\Core
 */
class GenericImport
{
    const ERROR_USER_NO_RIGHTS = "Not sufficient rights to perform operation!";
    const ERROR_NO_INIT = "Init not executed, Access denied!";

    /**
     * Import objects types
     *
     * @var array
     */
    protected $_aObjects = array(
        'A' => 'article',
        'K' => 'category',
        'H' => 'vendor',
        'C' => 'crossselling',
        'Z' => 'accessoire',
        'T' => 'article2category',
        'I' => 'article2action',
        'P' => 'scaleprice',
        'U' => 'user',
        'O' => 'order',
        'R' => 'orderarticle',
        'N' => 'country',
        'Y' => 'artextends',
    );

    /**
     * Imported data array
     *
     * @var string
     */
    protected $_sImportTypePrefix = null;

    /**
     * Imported data array
     *
     * @var array
     */
    protected $_aData = array();

    /**
     * Imported id array
     *
     * @var array
     */
    protected $_aImportedIds = array();

    /**
     * Return message after import
     *
     * @var string
     */
    protected $_sReturn;

    /**
     * Csv file field terminator
     *
     * @var string
     */
    protected $_sDefaultStringTerminator = ";";

    /**
     * Csv file field encloser
     *
     * @var string
     */
    protected $_sDefaultStringEncloser = '"';

    /**
     * CSV file contains header or not.
     *
     * @var bool
     */
    protected $_blCsvContainsHeader = null;

    /**
     * Import file location
     *
     * @var string
     */
    protected $_sPath;

    /** @var bool */
    protected $_blInit = false;

    protected $_iLanguage = null;
    protected $_sUserID = null;
    //session id
    protected $_sSID = null;

    /** @var array */
    public $statistics = array();

    /** @var int */
    public $index = 0;

    /**
     * count of import rows
     *
     * @var int
     */
    protected $_iRetryRows = 0;

    /**
     * CSV file fields array.
     *
     * @var array
     */
    protected $_aCsvFileFieldsOrder = array();

    /**
     * Init ERP Framework parameters
     * Creates Objects, checks Rights etc.
     *
     * @throws Exception
     *
     * @return boolean
     */
    public function init()
    {
        $myConfig = oxRegistry::getConfig();
        $mySession = oxRegistry::getSession();
        $oUser = oxNew('oxUser');
        $oUser->loadAdminUser();

        if (($oUser->oxuser__oxrights->value == "malladmin" || $oUser->oxuser__oxrights->value == $myConfig->getShopID())) {
            $this->_sSID = $mySession->getId();
            $this->_blInit = true;
            $this->_iLanguage = oxRegistry::getLang()->getBaseLanguage();
            $this->_sUserID = $oUser->getId();
        } else {
            //user does not have sufficient rights for shop
            throw new Exception(self::ERROR_USER_NO_RIGHTS);
        }

        $this->_resetIdx();

        return $this->_blInit;
    }

    /**
     * Get imort object according import type
     *
     * @param string $sType import object type
     *
     * @return object
     */
    public function getImportObject($sType)
    {
        $this->_sImportTypePrefix = $sType;
        $result = null;
        try {
            $sImportType = $this->_getImportType();
            $result = $this->_getInstanceOfType($sImportType);
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * Set import object type prefix
     *
     * @param string $sType import type prefix
     */
    public function setImportTypePrefix($sType)
    {
        $this->_sImportTypePrefix = $sType;
    }

    /**
     * Set CSV file columns names
     *
     * @param array $aCsvFields CSV fields
     */
    public function setCsvFileFieldsOrder($aCsvFields)
    {
        $this->_aCsvFileFieldsOrder = $aCsvFields;
    }

    /**
     * Set if CSV file contains header row
     *
     * @param bool $blCsvContainsHeader has or not file header
     */
    public function setCsvContainsHeader($blCsvContainsHeader)
    {
        $this->_blCsvContainsHeader = $blCsvContainsHeader;
    }
    /**
     * Main import method, whole import of all types via a given csv file is done here
     *
     * @param string $sPath full path of the CSV file.
     *
     * @return string
     *
     */
    public function doImport($sPath = null)
    {
        $this->_sReturn = "";
        $iMaxLineLength = 8192;

        $this->_sPath = $sPath;

        //init with given data
        try {
            $this->init();
        } catch (Exception $ex) {
            return $this->_sReturn = 'ERPGENIMPORT_ERROR_USER_NO_RIGHTS';
        }

        $file = @fopen($this->_sPath, "r");

        if (isset($file) && $file) {
            while (($aRow = fgetcsv($file, $iMaxLineLength, $this->_getCsvFieldsTerminator(), $this->_getCsvFieldsEncolser())) !== false) {
                $this->_aData[] = $aRow;
            }

            if ($this->_blCsvContainsHeader) {
                //skipping first row - it's header
                array_shift($this->_aData);
            }

            try {
                $this->import();
            } catch (Exception $ex) {
                echo $ex->getMessage();
                $this->_sReturn = 'ERPGENIMPORT_ERROR_DURING_IMPORT';
            }

        } else {
            $this->_sReturn = 'ERPGENIMPORT_ERROR_WRONG_FILE';
        }

        @fclose($file);

        return $this->_sReturn;
    }

    /**
     * Performs import action
     */
    public function import()
    {
        $this->_beforeImport();

        do {
            while ($this->_importOne()) {
            }
        } while (!$this->_afterImport());
    }

    /**
     * _aStatistics getter
     *
     * @return array
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * Returns import data cor current index
     *
     * @return mixed
     */
    public function getImportData()
    {
        return $this->_aData[$this->index];
    }

    /**
     * Get successfully imported rows number
     *
     * @return int
     */
    public function getTotalImportedRowsNumber()
    {
        return $this->getImportedRowCount();
    }

    /** gets count of imported rows, total, during import
     *
     * @return int $_iImportedRowCount
     */
    public function getImportedRowCount()
    {
        return count($this->_aImportedIds);
    }

    /**
     * Get allowed for import objects list
     *
     * @return array
     */
    public function getImportObjectsList()
    {
        $aList = array();
        foreach ($this->_aObjects as $sKey => $sImportType) {
            $oType = $this->_getInstanceOfType($sImportType);
            $aList[$sKey] = $oType->getBaseTableName();
        }

        return $aList;
    }

    /**
     * Performs before import actions
     */
    protected function _beforeImport()
    {
        if (!$this->_iRetryRows) {
            //convert all text
            foreach ($this->_aData as $key => $value) {
                $this->_aData[$key] = $this->_csvTextConvert($value, false);
            }
        }
    }

    /**
     * Main Import Handler, imports one row/call/object...
     * returns true if there were any data processed, and
     * master loop should run import again.
     *
     * after importing, fills $this->_aStatistics[$this->_iIdx] with array
     * of r=>(boolean)result, m=>(string)error message
     *
     * @return boolean
     */
    protected function _importOne()
    {
        $blRet = false;

        // import one row/call/object...
        $aData = $this->getImportData();

        if ($aData) {
            $blRet = true;
            $blImport = false;

            $sType = $this->_getImportType();
            $oType = $this->_getInstanceOfType($sType);
            $aData = $this->_modifyData($aData);
            $aData = $oType->addImportData($aData);

            try {
                $this->_checkAccess($oType, true);

                $iId = $oType->import($aData);
                if (!$iId) {
                    $blImport = false;
                } else {
                    $this->setImportedIds($iId);
                    $blImport = true;
                }
                $sMessage = '';
            } catch (Exception $e) {
                $sMessage = $e->getMessage();
            }

            $this->statistics[$this->index] = array('r' => $blImport, 'm' => $sMessage);

        }
        $this->_nextIdx();

        return $blRet;
    }

    /**
     * Checks if user as sufficient rights
     *
     * @param oxErpType $oType   data type object
     * @param boolean   $blWrite check also for write access
     */
    protected function _checkAccess($oType, $blWrite)
    {
        $myConfig = oxRegistry::getConfig();
        static $aAccessCache;

        if (!$this->_blInit) {
            throw new Exception(self::ERROR_NO_INIT);
        }

    }

    /**
     * Performs after import actions
     *
     * @return bool
     */
    protected function _afterImport()
    {
        //check if there have been no errors or failures
        $aStatistics = $this->getStatistics();
        $iRetryRows = 0;

        foreach ($aStatistics as $key => $value) {
            if ($value['r'] == false) {
                $iRetryRows++;
                $this->_sReturn .= "File[" . $this->_sPath . "] - dataset number: $key - Error: " . $value['m'] . " ---<br> " . PHP_EOL;
            }
        }

        if ($iRetryRows != $this->_iRetryRows && $iRetryRows > 0) {
            $this->_resetIdx();
            $this->_iRetryRows = $iRetryRows;
            $this->_sReturn = '';

            return false;
        }

        return true;
    }

    /**
     * Gets import object type according type prefix
     *
     * @throws Exception if no such import type prefix
     *
     * @return string
     */
    protected function _getImportType()
    {
        $sType = $this->_sImportTypePrefix;

        if (strlen($sType) != 1 || !array_key_exists($sType, $this->_aObjects)) {
            throw new Exception("Error unknown command: " . $sType);
        } else {
            return $this->_aObjects[$sType];
        }
    }

    /**
     * Modyfies data before import. Calls method for object fields
     * and csv data mapping.
     *
     * @param array $aData CSV data
     *
     * @return array
     */
    protected function _modifyData($aData)
    {
        return $this->_mapFields($aData);
    }

    /** adds true to $_aImportedIds where key is given
     *
     * @param mixed $key - given key
     */
    protected function setImportedIds($key)
    {
        if (!array_key_exists($key, $this->_aImportedIds)) {
            $this->_aImportedIds[$key] = true;
        }
    }

    /**
     * Increase import counter, if retry is detected, only failed imports are repeated
     */
    protected function _nextIdx()
    {
        $this->index++;

        if (count($this->statistics) && isset($this->statistics[$this->index])) {
            while (isset($this->statistics[$this->index]) && $this->statistics[$this->index]['r']) {
                $this->index++;
            }
        }
    }

    /**
     * Maps numeric array to assoc. Array
     *
     * @param array $aData numeric indices
     *
     * @return array assoc. indices
     */
    protected function _mapFields($aData)
    {
        $aRet = array();
        $iIndex = 0;

        foreach ($this->_aCsvFileFieldsOrder as $sValue) {
            if (!empty($sValue)) {
                if (strtolower($aData[$iIndex]) == "null") {
                    $aRet[$sValue] = null;
                } else {
                    $aRet[$sValue] = $aData[$iIndex];
                }
            }
            $iIndex++;
        }

        return $aRet;
    }

    /**
     * parses and replaces special chars
     *
     * @param string $sText  input text
     * @param bool   $blMode true = Text2CSV, false = CSV2Text
     *
     * @return string
     */
    protected function _csvTextConvert($sText, $blMode)
    {
        $aSearch = array(chr(13), chr(10), '\'', '"');
        $aReplace = array('&#13;', '&#10;', '&#39;', '&#34;');

        if ($blMode) {
            $sText = str_replace($aSearch, $aReplace, $sText);
        } else {
            $sText = str_replace($aReplace, $aSearch, $sText);
        }

        return $sText;
    }

    /**
     * Reset import counter, if retry is detected, only failed imports are repeated
     */
    protected function _resetIdx()
    {
        $this->index = 0;

        if (count($this->statistics) && isset($this->statistics[$this->index])) {
            while (isset($this->statistics[$this->index]) && $this->statistics[$this->index]['r']) {
                $this->index++;
            }
        }
    }

    /**
     * Set csv field terminator symbol
     *
     * @return string
     */
    protected function _getCsvFieldsTerminator()
    {
        $myConfig = oxRegistry::getConfig();

        $sChar = $myConfig->getConfigParam('sGiCsvFieldTerminator');

        if (!$sChar) {
            $sChar = $myConfig->getConfigParam('sCSVSign');
        }
        if (!$sChar) {
            $sChar = $this->_sDefaultStringTerminator;
        }

        return $sChar;
    }

    /**
     * Get csv field encloser symbol
     *
     * @return string
     */
    protected function _getCsvFieldsEncolser()
    {
        $myConfig = \oxRegistry::getConfig();

        if ($sChar = $myConfig->getConfigParam('sGiCsvFieldEncloser')) {
            return $sChar;
        } else {
            return $this->_sDefaultStringEncloser;
        }
    }

    /**
     * Factory for ERP types
     *
     * @param string $sType type name in objects dir
     *
     * @throws Exception if no such import type prefix
     *
     * @return \oxErpType
     */
    protected function _getInstanceOfType($sType)
    {
        $sClassName = 'oxerptype_' . $sType;
        $sFullPath = dirname(__FILE__) . '/objects/' . $sClassName . '.php';

        if (!file_exists($sFullPath)) {
            throw new Exception("Type $sType not supported in ERP interface!");
        }

        include_once $sFullPath;

        //return new $sClassName;
        return oxNew($sClassName);
    }
}
