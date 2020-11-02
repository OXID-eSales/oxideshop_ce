<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin general export manager.
 */
class GenericImportMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Export class name.
     *
     * @var string
     */
    public $sClassDo = 'genImport_do';

    /**
     * Export ui class name.
     *
     * @var string
     */
    public $sClassMain = 'genImport_main';

    /**
     * Csv file path.
     *
     * @var string
     */
    protected $_sCsvFilePath = null;

    /**
     * Csv file field terminator.
     *
     * @var string
     */
    protected $_sStringTerminator = null;

    /**
     * Csv file field encloser.
     *
     * @var string
     */
    protected $_sStringEncloser = null;

    /**
     * Default Csv file field terminator.
     *
     * @var string
     */
    protected $_sDefaultStringTerminator = ';';

    /**
     * Default Csv file field encloser.
     *
     * @var string
     */
    protected $_sDefaultStringEncloser = '"';

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'genimport_main.tpl';

    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file.
     *
     * @return string
     */
    public function render()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $genericImport = oxNew(\OxidEsales\Eshop\Core\GenericImport\GenericImport::class);
        $this->_sCsvFilePath = null;

        $navigationStep = $config->getRequestParameter('sNavStep');

        if (!$navigationStep) {
            $navigationStep = 1;
        } else {
            ++$navigationStep;
        }

        $navigationStep = $this->_checkErrors($navigationStep);

        if (1 === $navigationStep) {
            $this->_aViewData['sGiCsvFieldTerminator'] = \OxidEsales\Eshop\Core\Str::getStr()->htmlentities($this->_getCsvFieldsTerminator());
            $this->_aViewData['sGiCsvFieldEncloser'] = \OxidEsales\Eshop\Core\Str::getStr()->htmlentities($this->_getCsvFieldsEncolser());
        }

        if (2 === $navigationStep) {
            $noJsValidator = oxNew(\OxidEsales\Eshop\Core\NoJsValidator::class);
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

        if (3 === $navigationStep) {
            $csvFields = $config->getRequestParameter('aCsvFields');
            $type = $config->getRequestParameter('sType');

            $genericImport = oxNew(\OxidEsales\Eshop\Core\GenericImport\GenericImport::class);
            $genericImport->setImportType($type);
            $genericImport->setCsvFileFieldsOrder($csvFields);
            $genericImport->setCsvContainsHeader(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('blCsvContainsHeader'));

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

        if (1 === $navigationStep) {
            $this->_aViewData['aImportTables'] = $genericImport->getImportObjectsList();
            asort($this->_aViewData['aImportTables']);
            $this->_resetUploadedCsvData();
        }

        $this->_aViewData['sNavStep'] = $navigationStep;

        return parent::render();
    }

    /**
     * Deletes uploaded csv file from temp directory.
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "deleteCsvFile" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _deleteCsvFile(): void
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
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCsvFieldsNames" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getCsvFieldsNames()
    {
        $blCsvContainsHeader = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('blContainsHeader');
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('blCsvContainsHeader', $blCsvContainsHeader);
        $this->_getUploadedCsvFilePath();

        $aFirstRow = $this->_getCsvFirstRow();

        if (!$blCsvContainsHeader) {
            $iIndex = 1;
            foreach ($aFirstRow as $sValue) {
                $aCsvFields[$iIndex] = 'Column ' . $iIndex++;
            }
        } else {
            foreach ($aFirstRow as $sKey => $sValue) {
                $aFirstRow[$sKey] = \OxidEsales\Eshop\Core\Str::getStr()->htmlentities($sValue);
            }

            $aCsvFields = $aFirstRow;
        }

        return $aCsvFields;
    }

    /**
     * Get first row from uploaded CSV file.
     *
     * @return array
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCsvFirstRow" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getCsvFirstRow()
    {
        $sPath = $this->_getUploadedCsvFilePath();
        $iMaxLineLength = 8192;

        //getting first row
        if (false !== ($rFile = @fopen($sPath, 'r'))) {
            $aRow = fgetcsv($rFile, $iMaxLineLength, $this->_getCsvFieldsTerminator(), $this->_getCsvFieldsEncolser());
            fclose($rFile);
        }

        return $aRow;
    }

    /**
     * Resets CSV parameters stored in session.
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "resetUploadedCsvData" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _resetUploadedCsvData(): void
    {
        $this->_sCsvFilePath = null;
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('sCsvFilePath', null);
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('blCsvContainsHeader', null);
    }

    /**
     * Checks current import navigation step errors.
     * Returns step id in which error occured.
     *
     * @param int $iNavStep Navigation step id
     *
     * @return int
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "checkErrors" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _checkErrors($iNavStep)
    {
        if (2 === $iNavStep) {
            if (!$this->_getUploadedCsvFilePath()) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('GENIMPORT_ERRORUPLOADINGFILE');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'genimport');

                return 1;
            }
        }

        if (3 === $iNavStep) {
            $blIsEmpty = true;
            $aCsvFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aCsvFields');
            foreach ($aCsvFields as $sValue) {
                if ($sValue) {
                    $blIsEmpty = false;
                    break;
                }
            }

            if ($blIsEmpty) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('GENIMPORT_ERRORASSIGNINGFIELDS');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'genimport');

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
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getUploadedCsvFilePath" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getUploadedCsvFilePath()
    {
        //try to get uploaded csv file path
        if (null !== $this->_sCsvFilePath) {
            return $this->_sCsvFilePath;
        } elseif ($this->_sCsvFilePath = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('sCsvFilePath')) {
            return $this->_sCsvFilePath;
        }

        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aFile = $oConfig->getUploadedFile('csvfile');
        if (isset($aFile['name']) && $aFile['name']) {
            $this->_sCsvFilePath = $oConfig->getConfigParam('sCompileDir') . basename($aFile['tmp_name']);
            move_uploaded_file($aFile['tmp_name'], $this->_sCsvFilePath);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('sCsvFilePath', $this->_sCsvFilePath);

            return $this->_sCsvFilePath;
        }
    }

    /**
     * Checks if any error occured during import and displays them.
     *
     * @param object $oErpImport Import object
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "checkImportErrors" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _checkImportErrors($oErpImport): void
    {
        foreach ($oErpImport->getStatistics() as $aValue) {
            if (!$aValue['r']) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage($aValue['m']);
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'genimport');
            }
        }
    }

    /**
     * Get csv field terminator symbol.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCsvFieldsTerminator" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getCsvFieldsTerminator()
    {
        if (null === $this->_sStringTerminator) {
            $this->_sStringTerminator = $this->_sDefaultStringTerminator;
            if ($char = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sGiCsvFieldTerminator')) {
                $this->_sStringTerminator = $char;
            }
        }

        return $this->_sStringTerminator;
    }

    /**
     * Get csv field encloser symbol.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCsvFieldsEncolser" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getCsvFieldsEncolser()
    {
        if (null === $this->_sStringEncloser) {
            $this->_sStringEncloser = $this->_sDefaultStringEncloser;
            if ($char = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sGiCsvFieldEncloser')) {
                $this->_sStringEncloser = $char;
            }
        }

        return $this->_sStringEncloser;
    }

    /**
     * @param string $invalidData
     */
    private function setErrorToView($invalidData): void
    {
        $error = oxNew(\OxidEsales\Eshop\Core\DisplayError::class);
        $error->setFormatParameters(htmlspecialchars($invalidData));
        $error->setMessage('SHOP_CONFIG_ERROR_INVALID_VALUE');
        \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($error);
    }
}
