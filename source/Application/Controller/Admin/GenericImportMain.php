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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxStr;
use oxRegistry;
use oxAdminDetails;

/**
 * Admin general export manager.
 */
class GenericImportMain extends oxAdminDetails
{

    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "genImport_do";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = "genImport_main";

    /**
     * Csv file path
     *
     * @var string
     */
    protected $_sCsvFilePath = null;

    /**
     * Csv file field terminator
     *
     * @var string
     */
    protected $_sStringTerminator = null;

    /**
     * Csv file field encloser
     *
     * @var string
     */
    protected $_sStringEncloser = null;

    /**
     * Default Csv file field terminator
     *
     * @var string
     */
    protected $_sDefaultStringTerminator = ";";

    /**
     * Default Csv file field encloser
     *
     * @var string
     */
    protected $_sDefaultStringEncloser = '"';

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "genimport_main.tpl";

    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file
     *
     * @return string
     */
    public function render()
    {
        $config = $this->getConfig();

        $genericImport = oxNew('OxidEsales\EshopCommunity\Core\GenericImport\GenericImport');
        $this->_sCsvFilePath = null;

        $navigationStep = $config->getRequestParameter('sNavStep');

        if (!$navigationStep) {
            $navigationStep = 1;
        } else {
            $navigationStep++;
        }

        $navigationStep = $this->_checkErrors($navigationStep);

        if ($navigationStep == 1) {
            $this->_aViewData['sGiCsvFieldTerminator'] = oxStr::getStr()->htmlentities($this->_getCsvFieldsTerminator());
            $this->_aViewData['sGiCsvFieldEncloser'] = oxStr::getStr()->htmlentities($this->_getCsvFieldsEncolser());
        }

        if ($navigationStep == 2) {
            $noJsValidator = oxNew('oxNoJsValidator');
            //saving csv field terminator and encloser to config
            $terminator = $config->getRequestParameter('sGiCsvFieldTerminator');
            if ($terminator && !$noJsValidator->isValid($terminator)) {
                $this->setErrorToView($terminator);
            } else {
                $this->_sStringTerminator = $terminator;
                $config->saveShopConfVar('str', 'sGiCsvFieldTerminator', $terminator);
            }

            $encloser = $config->getRequestParameter('sGiCsvFieldEncloser');
            if ($encloser && !$noJsValidator->isValid($encloser)) {
                $this->setErrorToView($encloser);
            } else {
                $this->_sStringEncloser = $encloser;
                $config->saveShopConfVar('str', 'sGiCsvFieldEncloser', $encloser);
            }

            $type = $config->getRequestParameter('sType');
            $importObject = $genericImport->getImportObject($type);
            $this->_aViewData['sType'] = $type;
            $this->_aViewData['sImportTable'] = $importObject->getBaseTableName();
            $this->_aViewData['aCsvFieldsList'] = $this->_getCsvFieldsNames();
            $this->_aViewData['aDbFieldsList'] = $importObject->getFieldList();
        }

        if ($navigationStep == 3) {
            $csvFields = $config->getRequestParameter('aCsvFields');
            $type = $config->getRequestParameter('sType');

            $genericImport = oxNew('OxidEsales\EshopCommunity\Core\GenericImport\GenericImport');
            $genericImport->setImportType($type);
            $genericImport->setCsvFileFieldsOrder($csvFields);
            $genericImport->setCsvContainsHeader(oxRegistry::getSession()->getVariable('blCsvContainsHeader'));

            $genericImport->importFile($this->_getUploadedCsvFilePath());
            $this->_aViewData['iTotalRows'] = $genericImport->getImportedRowCount();

            //checking if errors occured during import
            $this->_checkImportErrors($genericImport);

            //deleting uploaded csv file from temp dir
            $this->_deleteCsvFile();

            //check if repeating import - then forsing first step
            if ($config->getRequestParameter('iRepeatImport')) {
                $this->_aViewData['iRepeatImport'] = 1;
                $navigationStep = 1;
            }
        }

        if ($navigationStep == 1) {
            $this->_aViewData['aImportTables'] = $genericImport->getImportObjectsList();
            asort($this->_aViewData['aImportTables']);
            $this->_resetUploadedCsvData();
        }

        $this->_aViewData['sNavStep'] = $navigationStep;

        return parent::render();
    }

    /**
     * Deletes uploaded csv file from temp directory
     */
    protected function _deleteCsvFile()
    {
        $sPath = $this->_getUploadedCsvFilePath();
        if (is_file($sPath)) {
            @unlink($sPath);
        }
    }

    /**
     * Get columns names from CSV file header. If file has no header
     * returns default columns names Column 1, Column 2..
     *
     * @return array
     */
    protected function _getCsvFieldsNames()
    {
        $blCsvContainsHeader = $this->getConfig()->getRequestParameter('blContainsHeader');
        oxRegistry::getSession()->setVariable('blCsvContainsHeader', $blCsvContainsHeader);
        $sCsvPath = $this->_getUploadedCsvFilePath();

        $aFirstRow = $this->_getCsvFirstRow();

        if (!$blCsvContainsHeader) {
            $iIndex = 1;
            foreach ($aFirstRow as $sValue) {
                $aCsvFields[$iIndex] = 'Column ' . $iIndex++;
            }
        } else {
            foreach ($aFirstRow as $sKey => $sValue) {
                $aFirstRow[$sKey] = oxStr::getStr()->htmlentities($sValue);
            }

            $aCsvFields = $aFirstRow;
        }

        return $aCsvFields;
    }

    /**
     * Get first row from uploaded CSV file
     *
     * @return array
     */
    protected function _getCsvFirstRow()
    {
        $sPath = $this->_getUploadedCsvFilePath();
        $iMaxLineLength = 8192;

        //getting first row
        if (($rFile = @fopen($sPath, "r")) !== false) {
            $aRow = fgetcsv($rFile, $iMaxLineLength, $this->_getCsvFieldsTerminator(), $this->_getCsvFieldsEncolser());
            fclose($rFile);
        }

        return $aRow;
    }

    /**
     * Resets CSV parameters stored in session
     */
    protected function _resetUploadedCsvData()
    {
        $this->_sCsvFilePath = null;
        oxRegistry::getSession()->setVariable('sCsvFilePath', null);
        oxRegistry::getSession()->setVariable('blCsvContainsHeader', null);
    }

    /**
     * Checks current import navigation step errors.
     * Returns step id in which error occured.
     *
     * @param int $iNavStep Navigation step id
     *
     * @return int
     */
    protected function _checkErrors($iNavStep)
    {
        if ($iNavStep == 2) {
            if (!$this->_getUploadedCsvFilePath()) {
                $oEx = oxNew("oxExceptionToDisplay");
                $oEx->setMessage('GENIMPORT_ERRORUPLOADINGFILE');
                oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true, 'genimport');

                return 1;
            }
        }

        if ($iNavStep == 3) {
            $blIsEmpty = true;
            $aCsvFields = $this->getConfig()->getRequestParameter('aCsvFields');
            foreach ($aCsvFields as $sValue) {
                if ($sValue) {
                    $blIsEmpty = false;
                    break;
                }
            }

            if ($blIsEmpty) {
                $oEx = oxNew("oxExceptionToDisplay");
                $oEx->setMessage('GENIMPORT_ERRORASSIGNINGFIELDS');
                oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true, 'genimport');

                return 2;
            }
        }

        return $iNavStep;
    }

    /**
     * Checks if CSV file was uploaded. If uploaded - moves it to temp dir
     * and stores path to file in session. Return path to uploaded file.
     *
     * @return string
     */
    protected function _getUploadedCsvFilePath()
    {
        //try to get uploaded csv file path
        if ($this->_sCsvFilePath !== null) {
            return $this->_sCsvFilePath;
        } elseif ($this->_sCsvFilePath = oxRegistry::getSession()->getVariable('sCsvFilePath')) {
            return $this->_sCsvFilePath;
        }

        $oConfig = $this->getConfig();
        $aFile = $oConfig->getUploadedFile('csvfile');
        if (isset($aFile['name']) && $aFile['name']) {
            $this->_sCsvFilePath = $oConfig->getConfigParam('sCompileDir') . basename($aFile['tmp_name']);
            move_uploaded_file($aFile['tmp_name'], $this->_sCsvFilePath);
            oxRegistry::getSession()->setVariable('sCsvFilePath', $this->_sCsvFilePath);

            return $this->_sCsvFilePath;
        }
    }

    /**
     * Checks if any error occured during import and displays them
     *
     * @param object $oErpImport Import object
     */
    protected function _checkImportErrors($oErpImport)
    {
        foreach ($oErpImport->getStatistics() as $aValue) {
            if (!$aValue ['r']) {
                $oEx = oxNew("oxExceptionToDisplay");
                $oEx->setMessage($aValue ['m']);
                oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true, 'genimport');
            }
        }

    }

    /**
     * Get csv field terminator symbol
     *
     * @return string
     */
    protected function _getCsvFieldsTerminator()
    {
        if ($this->_sStringTerminator === null) {
            $this->_sStringTerminator = $this->_sDefaultStringTerminator;
            if ($char = $this->getConfig()->getConfigParam('sGiCsvFieldTerminator')) {
                $this->_sStringTerminator = $char;
            }
        }

        return $this->_sStringTerminator;
    }

    /**
     * Get csv field encloser symbol
     *
     * @return string
     */
    protected function _getCsvFieldsEncolser()
    {
        if ($this->_sStringEncloser === null) {
            $this->_sStringEncloser = $this->_sDefaultStringEncloser;
            if ($char = $this->getConfig()->getConfigParam('sGiCsvFieldEncloser')) {
                $this->_sStringEncloser = $char;
            }
        }

        return $this->_sStringEncloser;
    }

    /**
     * @param string $invalidData
     */
    private function setErrorToView($invalidData)
    {
        $error = oxNew('oxDisplayError');
        $error->setFormatParameters(htmlspecialchars($invalidData));
        $error->setMessage("SHOP_CONFIG_ERROR_INVALID_VALUE");
        oxRegistry::get("oxUtilsView")->addErrorToDisplay($error);
    }
}
