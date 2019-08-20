<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * General export class.
 */
class VoucherSerieExport extends \OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieMain
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "voucherserie_export";

    /**
     * Export file extension
     *
     * @var string
     */
    public $sExportFileType = "csv";

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "voucherserie_export.tpl";

    /**
     * Number of records to export per tick
     *
     * @var int
     */
    public $iExportPerTick = 1000;

    /**
     * Calls parent costructor and initializes $this->_sFilePath parameter
     */
    public function __construct()
    {
        parent::__construct();

        // export file name
        $this->sExportFileName = $this->_getExportFileName();

        // set generic frame template
        $this->_sFilePath = $this->_getExportFilePath();
    }

    /**
     * Returns export file download url
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        $myConfig = $this->getConfig();

        // override cause of admin dir
        $sUrl = $myConfig->getConfigParam('sShopURL') . $myConfig->getConfigParam('sAdminDir');
        if ($myConfig->getConfigParam('sAdminSSLURL')) {
            $sUrl = $myConfig->getConfigParam('sAdminSSLURL');
        }

        $sUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($sUrl . '/index.php');

        return $sUrl . '&amp;cl=' . $this->sClassDo . '&amp;fnc=download';
    }

    /**
     * Return export file name
     *
     * @return string
     */
    protected function _getExportFileName()
    {
        $sSessionFileName = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("sExportFileName");
        if (!$sSessionFileName) {
            $sSessionFileName = md5($this->getSession()->getId() . \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId());
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportFileName", $sSessionFileName);
        }

        return $sSessionFileName;
    }

    /**
     * Return export file path
     *
     * @return string
     */
    protected function _getExportFilePath()
    {
        return $this->getConfig()->getConfigParam('sShopDir') . "/export/" . $this->_getExportFileName();
    }

    /**
     * Performs Voucherserie export to export file.
     */
    public function download()
    {
        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        $oUtils->setHeader("Pragma: public");
        $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        $oUtils->setHeader("Expires: 0");
        $oUtils->setHeader("Content-Disposition: attachment; filename=vouchers.csv");
        $oUtils->setHeader("Content-Type: application/csv");
        $sFile = $this->_getExportFilePath();
        if (file_exists($sFile) && is_readable($sFile)) {
            readfile($sFile);
        }
        $oUtils->showMessageAndExit("");
    }

    /**
     * Does Export
     */
    public function run()
    {
        $blContinue = true;

        $this->fpFile = @fopen($this->_sFilePath, "a");
        if (!isset($this->fpFile) || !$this->fpFile) {
            // we do have an error !
            $this->stop(ERR_FILEIO);
        } else {
            // file is open
            $iStart = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("iStart");
            if (!$iStart) {
                ftruncate($this->fpFile, 0);
            }

            if (($iExportedItems = $this->exportVouchers($iStart)) === false) {
                // end reached
                $this->stop(ERR_SUCCESS);
                $blContinue = false;
            }

            if ($blContinue) {
                // make ticker continue
                $this->_aViewData['refresh'] = 0;
                $this->_aViewData['iStart'] = $iStart + $iExportedItems;
                $this->_aViewData['iExpItems'] = $iStart + $iExportedItems;
            }
            fclose($this->fpFile);
        }
    }

    /**
     * Writes voucher number information to export file and returns number of written records info
     *
     * @param int $iStart start exporting from
     *
     * @return int
     */
    public function exportVouchers($iStart)
    {
        $iExported = false;

        if ($oSerie = $this->_getVoucherSerie()) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

            $sSelect = "select oxvouchernr from oxvouchers where oxvoucherserieid = :oxvoucherserieid";
            $rs = $oDb->selectLimit($sSelect, $this->iExportPerTick, $iStart, [
                ':oxvoucherserieid' => $oSerie->getId()
            ]);

            if (!$rs->EOF) {
                $iExported = 0;

                // writing header text
                if ($iStart == 0) {
                    $this->write(\OxidEsales\Eshop\Core\Registry::getLang()->translateString("VOUCHERSERIE_MAIN_VOUCHERSTATISTICS", \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage(), true));
                }
            }

            // writing vouchers..
            while (!$rs->EOF) {
                $this->write(current($rs->fields));
                $iExported++;
                $rs->fetchRow();
            }
        }

        return $iExported;
    }

    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     */
    public function write($sLine)
    {
        if ($sLine) {
            fwrite($this->fpFile, $sLine . "\n");
        }
    }
}
