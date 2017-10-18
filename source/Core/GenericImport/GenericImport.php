<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport;

use Exception;
use OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject;

/**
 * Class responsible for generic import functionality.
 */
class GenericImport
{
    const ERROR_USER_NO_RIGHTS = 'Not sufficient rights to perform operation!';
    const ERROR_NO_INIT = 'Init not executed, Access denied!';

    /** @var array Import objects types. */
    protected $objects = [
        'A' => 'Article',
        'K' => 'Category',
        'H' => 'Vendor',
        'C' => 'CrossSelling',
        'Z' => 'Accessories2Article',
        'T' => 'Article2Category',
        'I' => 'Article2Action',
        'P' => 'ScalePrice',
        'U' => 'User',
        'O' => 'Order',
        'R' => 'OrderArticle',
        'N' => 'Country',
        'Y' => 'ArticleExtends',
    ];

    /** @var string Imported data array. */
    protected $importType = null;

    /** @var array Imported id array */
    protected $importedIds = [];

    /** @var string Return message after import. */
    protected $returnMessage;

    /** @var string Csv file field terminator. */
    protected $defaultStringTerminator = ';';

    /** @var string Csv file field encloser. */
    protected $defaultStringEncloser = '"';

    /** @var bool CSV file contains header or not. */
    protected $csvContainsHeader = null;

    /** @var string Import file location. */
    protected $importFilePath;

    /** @var bool */
    protected $isInitialized = false;

    /** @var int */
    protected $userId = null;

    /** @var array */
    protected $statistics = [];

    /** @var bool Whether import was retried. */
    protected $retried = false;

    /** @var array CSV file fields array. */
    protected $csvFileFieldsOrder = [];

    /** @var int Maximum length of imported line. */
    protected $maxLineLength = 8192;

    /**
     * Init parameters needed for import.
     * Creates Objects, checks Rights etc.
     *
     * @throws Exception
     *
     * @return boolean
     */
    public function init()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->loadAdminUser();

        if (($user->oxuser__oxrights->value == 'malladmin' || $user->oxuser__oxrights->value == $config->getShopId())) {
            $this->isInitialized = true;
            $this->userId = $user->getId();
        } else {
            //user does not have sufficient rights for shop
            throw new Exception(self::ERROR_USER_NO_RIGHTS);
        }

        return $this->isInitialized;
    }

    /**
     * Get import object according import type.
     *
     * @param string $type Import object type.
     *
     * @return ImportObject
     */
    public function getImportObject($type)
    {
        $this->importType = $type;
        $result = null;
        try {
            $importType = $this->getImportType();
            $result = $this->createImportObject($importType);
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * Set import object type prefix.
     *
     * @param string $type Import type prefix.
     */
    public function setImportType($type)
    {
        $this->importType = $type;
    }

    /**
     * Set CSV file columns names.
     *
     * @param array $csvFields CSV fields.
     */
    public function setCsvFileFieldsOrder($csvFields)
    {
        $this->csvFileFieldsOrder = $csvFields;
    }

    /**
     * Set if CSV file contains header row.
     *
     * @param bool $csvContainsHeader Whether imported file has a header row.
     */
    public function setCsvContainsHeader($csvContainsHeader)
    {
        $this->csvContainsHeader = $csvContainsHeader;
    }
    /**
     * Main import method, whole import of all types via a given csv file is done here.
     *
     * @param string $importFilePath Full path of the CSV file.
     *
     * @return string
     */
    public function importFile($importFilePath = null)
    {
        $this->returnMessage = '';
        $this->importFilePath = $importFilePath;

        //init with given data
        try {
            $this->init();
        } catch (Exception $ex) {
            return $this->returnMessage = 'ERPGENIMPORT_ERROR_USER_NO_RIGHTS';
        }

        $file = @fopen($this->importFilePath, 'r');

        if (isset($file) && $file) {
            $data = [];
            while (($row = fgetcsv($file, $this->maxLineLength, $this->getCsvFieldsTerminator(), $this->getCsvFieldsEncolser())) !== false) {
                $data[] = $this->csvTextConvert($row, false);
            }

            if ($this->csvContainsHeader) {
                array_shift($data);
            }

            try {
                $this->importData($data);
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
     * Performs import action.
     *
     * @param array $data
     */
    public function importData($data)
    {
        foreach ($data as $key => $row) {
            if ($row) {
                try {
                    $success = $this->importOne($row);
                    $errorMessage = '';
                } catch (Exception $e) {
                    $success = false;
                    $errorMessage = $e->getMessage();
                }

                $this->statistics[$key] = ['r' => $success, 'm' => $errorMessage];
            }
        }

        $this->afterImport($data);
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
     * Returns count of imported rows, total, during import.
     *
     * @return int $_iImportedRowCount
     */
    public function getImportedRowCount()
    {
        return count($this->importedIds);
    }

    /**
     * Returns allowed for import objects list.
     *
     * @return array
     */
    public function getImportObjectsList()
    {
        $importObjects = [];
        foreach ($this->objects as $sKey => $importType) {
            $type = $this->createImportObject($importType);
            $importObjects[$sKey] = $type->getBaseTableName();
        }

        return $importObjects;
    }

    /**
     * Main Import Handler, imports one row/call/object...
     * returns true if there were any data processed, and
     * master loop should run import again.
     *
     * after importing, fills $this->_aStatistics[$this->_iIdx] with array
     * of r=>(boolean)result, m=>(string)error message
     *
     * @param array $data
     *
     * @return bool
     */
    protected function importOne($data)
    {
        $type = $this->getImportType();
        $importObject = $this->createImportObject($type);
        $data = $this->mapFields($data);

        $this->checkAccess($importObject, true);

        $id = $importObject->import($data);
        if ($id) {
            $this->addImportedId($id);
        }

        return (bool) $id;
    }

    /**
     * Performs after import actions.
     * If any error occurred during import tries to run import again and marks retried as true.
     * If after running import second time all of the records failed, stops.
     *
     * @param array $data
     */
    protected function afterImport($data)
    {
        $statistics = $this->getStatistics();

        $dataForRetry = [];
        foreach ($statistics as $key => $value) {
            if ($value['r'] == false) {
                $this->returnMessage .= "File[" . $this->importFilePath . "] - dataset number: $key - Error: " . $value['m'] . " ---<br> " . PHP_EOL;
                $dataForRetry[$key] = $data[$key];
            }
        }

        if (!empty($dataForRetry) && (!$this->retried || count($dataForRetry) != count($data))) {
            $this->retried = true;
            $this->returnMessage = '';
            $this->importData($dataForRetry);
        }
    }

    /**
     * Gets import object type according type prefix.
     *
     * @throws Exception if no such import type prefix
     *
     * @return string
     */
    protected function getImportType()
    {
        $type = $this->importType;

        if (strlen($type) != 1 || !array_key_exists($type, $this->objects)) {
            throw new Exception('Error unknown command: ' . $type);
        } else {
            return $this->objects[$type];
        }
    }

    /** Adds true to $_aImportedIds where key is given.
     *
     * @param mixed $id - given key
     */
    protected function addImportedId($id)
    {
        if (!array_key_exists($id, $this->importedIds)) {
            $this->importedIds[$id] = true;
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
        $result = [];
        $index = 0;

        foreach ($this->csvFileFieldsOrder as $value) {
            if (!empty($value)) {
                if (strtolower($data[$index]) == 'null') {
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
     * Parses and replaces special chars.
     *
     * @param string $text input text
     * @param bool   $mode true = Text2CSV, false = CSV2Text
     *
     * @return string
     */
    protected function csvTextConvert($text, $mode)
    {
        $search = [chr(13), chr(10), '\'', '"'];
        $replace = ['&#13;', '&#10;', '&#39;', '&#34;'];

        if ($mode) {
            $text = str_replace($search, $replace, $text);
        } else {
            $text = str_replace($replace, $search, $text);
        }

        return $text;
    }

    /**
     * Set csv field terminator symbol.
     *
     * @return string
     */
    protected function getCsvFieldsTerminator()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

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
     * Get csv field encloser symbol.
     *
     * @return string
     */
    protected function getCsvFieldsEncolser()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        if ($fieldEncloser = $config->getConfigParam('sGiCsvFieldEncloser')) {
            return $fieldEncloser;
        }

        return $this->defaultStringEncloser;
    }

    /**
     * Checks if user has sufficient rights.
     *
     * @param ImportObject $importObject  Data type object
     * @param boolean      $isWriteAction Check for write permissions
     *
     * @throws Exception
     */
    protected function checkAccess($importObject, $isWriteAction)
    {
        if (!$this->isInitialized) {
            throw new Exception(self::ERROR_NO_INIT);
        }
    }

    /**
     * Creates and returns import object.
     *
     * @param string $type Type name in objects dir.
     *
     * @return ImportObject
     */
    protected function createImportObject($type)
    {
        $className = __NAMESPACE__ . "\\ImportObject\\".$type;

        return oxNew($className);
    }
}
