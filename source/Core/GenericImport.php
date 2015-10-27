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
use oxDb;
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

    /** @var array Import objects types */
    protected $objects = array(
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

    /** @var string Imported data array */
    protected $importTypePrefix = null;

    /** @var array Imported data array */
    protected $data = array();

    /** @var array Imported id array */
    protected $importedIds = array();

    /** @var string Return message after import */
    protected $returnMessage;

    /** @var string Csv file field terminator */
    protected $defaultStringTerminator = ";";

    /** @var string Csv file field encloser. */
    protected $defaultStringEncloser = '"';

    /** @var bool CSV file contains header or not. */
    protected $csvContainsHeader = null;

    /** @var string Import file location. */
    protected $importFilePath;

    /** @var bool */
    protected $isInitialized = false;

    /** @var int */
    protected $languageId = null;

    /** @var int */
    protected $userId = null;

    /** @var string */
    protected $sessionId = null;

    /** @var array */
    public $statistics = array();

    /** @var int */
    public $index = 0;

    /** @var int Count of import rows. */
    protected $retryRows = 0;

    /** @var array CSV file fields array. */
    protected $csvFileFieldsOrder = array();

    /** @var int Maximum length of imported line. */
    protected $maxLineLength = 8192;

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
        $config = oxRegistry::getConfig();
        $session = oxRegistry::getSession();
        $user = oxNew('oxUser');
        $user->loadAdminUser();

        if (($user->oxuser__oxrights->value == "malladmin" || $user->oxuser__oxrights->value == $config->getShopId())) {
            $this->sessionId = $session->getId();
            $this->isInitialized = true;
            $this->languageId = oxRegistry::getLang()->getBaseLanguage();
            $this->userId = $user->getId();
        } else {
            //user does not have sufficient rights for shop
            throw new Exception(self::ERROR_USER_NO_RIGHTS);
        }

        $this->resetIndex();

        return $this->isInitialized;
    }

    /**
     * Get import object according import type.
     *
     * @param string $type Import object type
     *
     * @return oxERPType
     */
    public function getImportObject($type)
    {
        $this->importTypePrefix = $type;
        $result = null;
        try {
            $importType = $this->_getImportType();
            $result = $this->getInstanceOfType($importType);
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * Set import object type prefix
     *
     * @param string $type import type prefix
     */
    public function setImportTypePrefix($type)
    {
        $this->importTypePrefix = $type;
    }

    /**
     * Set CSV file columns names
     *
     * @param array $csvFields CSV fields
     */
    public function setCsvFileFieldsOrder($csvFields)
    {
        $this->csvFileFieldsOrder = $csvFields;
    }

    /**
     * Set if CSV file contains header row
     *
     * @param bool $csvContainsHeader whether imported file has a header row
     */
    public function setCsvContainsHeader($csvContainsHeader)
    {
        $this->csvContainsHeader = $csvContainsHeader;
    }
    /**
     * Main import method, whole import of all types via a given csv file is done here
     *
     * @param string $importFilePath full path of the CSV file.
     *
     * @return string
     */
    public function doImport($importFilePath = null)
    {
        $this->returnMessage = "";
        $this->importFilePath = $importFilePath;

        //init with given data
        try {
            $this->init();
        } catch (Exception $ex) {
            return $this->returnMessage = 'ERPGENIMPORT_ERROR_USER_NO_RIGHTS';
        }

        $file = @fopen($this->importFilePath, "r");

        if (isset($file) && $file) {
            while (($row = fgetcsv($file, $this->maxLineLength, $this->getCsvFieldsTerminator(), $this->getCsvFieldsEncolser())) !== false) {
                $this->data[] = $row;
            }

            if ($this->csvContainsHeader) {
                //skipping first row - it's header
                array_shift($this->data);
            }

            try {
                $this->import();
            } catch (Exception $ex) {
                echo $ex->getMessage();
                $this->returnMessage = 'ERPGENIMPORT_ERROR_DURING_IMPORT';
            }
        } else {
            $this->returnMessage = 'ERPGENIMPORT_ERROR_WRONG_FILE';
        }

        @fclose($file);

        return $this->returnMessage;
    }

    /**
     * Performs import action
     */
    public function import()
    {
        $this->beforeImport();

        do {
            while ($this->importOne()) {
                // import data
            }
        } while (!$this->afterImport());
    }

    /**
     * Returns statistics information about import.
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
        return $this->data[$this->index];
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
        return count($this->importedIds);
    }

    /**
     * Get allowed for import objects list
     *
     * @return array
     */
    public function getImportObjectsList()
    {
        $importObjects = array();
        foreach ($this->objects as $sKey => $importType) {
            $type = $this->getInstanceOfType($importType);
            $importObjects[$sKey] = $type->getBaseTableName();
        }

        return $importObjects;
    }

    /**
     * Performs before import actions
     */
    protected function beforeImport()
    {
        if (!$this->retryRows) {
            //convert all text
            foreach ($this->data as $key => $value) {
                $this->data[$key] = $this->csvTextConvert($value, false);
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
    protected function importOne()
    {
        $result = false;

        // import one row/call/object...
        $importData = $this->getImportData();

        if ($importData) {
            $result = true;
            $success = false;

            $typeName = $this->_getImportType();
            $type = $this->getInstanceOfType($typeName);
            $importData = $this->modifyData($importData);
            $importData = $type->addImportData($importData);

            try {
                $this->checkAccess($type, true);

                $id = $type->import($importData);
                if (!$id) {
                    $success = false;
                } else {
                    $this->setImportedIds($id);
                    $success = true;
                }
                $errorMessage = '';
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }

            $this->statistics[$this->index] = array('r' => $success, 'm' => $errorMessage);

        }
        $this->nextIndex();

        return $result;
    }

    /**
     * Checks if user as sufficient rights
     *
     * @param oxErpType $type          Data type object
     * @param boolean   $isWriteAction Check for write permissions
     *
     * @throws Exception
     */
    protected function checkAccess($type, $isWriteAction)
    {
        $config = oxRegistry::getConfig();
        static $accessCache;

        if (!$this->isInitialized) {
            throw new Exception(self::ERROR_NO_INIT);
        }

    }

    /**
     * Performs after import actions
     *
     * @return bool
     */
    protected function afterImport()
    {
        //check if there have been no errors or failures
        $statistics = $this->getStatistics();
        $retryRows = 0;

        foreach ($statistics as $key => $value) {
            if ($value['r'] == false) {
                $retryRows++;
                $this->returnMessage .= "File[" . $this->importFilePath . "] - dataset number: $key - Error: " . $value['m'] . " ---<br> " . PHP_EOL;
            }
        }

        if ($retryRows != $this->retryRows && $retryRows > 0) {
            $this->resetIndex();
            $this->retryRows = $retryRows;
            $this->returnMessage = '';

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
        $type = $this->importTypePrefix;

        if (strlen($type) != 1 || !array_key_exists($type, $this->objects)) {
            throw new Exception("Error unknown command: " . $type);
        } else {
            return $this->objects[$type];
        }
    }

    /**
     * Modifies data before import. Calls method for object fields
     * and csv data mapping.
     *
     * @param array $data CSV data
     *
     * @return array
     */
    protected function modifyData($data)
    {
        return $this->mapFields($data);
    }

    /** adds true to $_aImportedIds where key is given
     *
     * @param mixed $key - given key
     */
    protected function setImportedIds($key)
    {
        if (!array_key_exists($key, $this->importedIds)) {
            $this->importedIds[$key] = true;
        }
    }

    /**
     * Increase import counter, if retry is detected, only failed imports are repeated
     */
    protected function nextIndex()
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
     * @param array $data numeric indices
     *
     * @return array assoc. indices
     */
    protected function mapFields($data)
    {
        $result = array();
        $index = 0;

        foreach ($this->csvFileFieldsOrder as $value) {
            if (!empty($value)) {
                if (strtolower($data[$index]) == "null") {
                    $result[$value] = null;
                } else {
                    $result[$value] = $data[$index];
                }
            }
            $index++;
        }

        return $result;
    }

    /**
     * parses and replaces special chars
     *
     * @param string $text input text
     * @param bool   $mode true = Text2CSV, false = CSV2Text
     *
     * @return string
     */
    protected function csvTextConvert($text, $mode)
    {
        $search = array(chr(13), chr(10), '\'', '"');
        $replace = array('&#13;', '&#10;', '&#39;', '&#34;');

        if ($mode) {
            $text = str_replace($search, $replace, $text);
        } else {
            $text = str_replace($replace, $search, $text);
        }

        return $text;
    }

    /**
     * Reset import counter, if retry is detected, only failed imports are repeated
     */
    protected function resetIndex()
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
    protected function getCsvFieldsTerminator()
    {
        $config = oxRegistry::getConfig();

        $fieldTerminator = $config->getConfigParam('sGiCsvFieldTerminator');

        if (!$fieldTerminator) {
            $fieldTerminator = $config->getConfigParam('sCSVSign');
        }
        if (!$fieldTerminator) {
            $fieldTerminator = $this->defaultStringTerminator;
        }

        return $fieldTerminator;
    }

    /**
     * Get csv field encloser symbol
     *
     * @return string
     */
    protected function getCsvFieldsEncolser()
    {
        $config = \oxRegistry::getConfig();

        if ($fieldEncloser = $config->getConfigParam('sGiCsvFieldEncloser')) {
            return $fieldEncloser;
        } else {
            return $this->defaultStringEncloser;
        }
    }

    /**
     * Factory for ERP types
     *
     * @param string $type type name in objects dir
     *
     * @throws Exception if no such import type prefix
     *
     * @return \oxErpType
     */
    protected function getInstanceOfType($type)
    {
        $className = 'oxerptype_' . $type;
        $fullPath = dirname(__FILE__) . '/objects/' . $className . '.php';

        if (!file_exists($fullPath)) {
            throw new Exception("Type $type not supported in ERP interface!");
        }

        include_once $fullPath;

        //return new $sClassName;
        return oxNew($className);
    }
}
