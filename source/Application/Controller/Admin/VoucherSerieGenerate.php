<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;

/**
 * Voucher Serie generator class
 *
 */
class VoucherSerieGenerate extends \OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieMain
{
    /**
     * Voucher generator class name
     *
     * @var string
     */
    public $sClassDo = "voucherserie_generate";

    /**
     * Number of vouchers to generate per tick
     *
     * @var int
     */
    public $iGeneratePerTick = 100;

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = "voucherserie_generate.tpl";

    /**
     * Voucher serie object
     *
     * @var oxvoucherserie
     */
    protected $_oVoucherSerie = null;

    /**
     * Generated vouchers count
     *
     * @var int
     */
    protected $_iGenerated = false;

    /**
     * Generates vouchers by offset iCnt
     *
     * @param integer $iCnt voucher offset
     *
     * @return bool
     */
    public function nextTick($iCnt)
    {
        if ($iGeneratedItems = $this->generateVoucher($iCnt)) {
            return $iGeneratedItems;
        }

        return false;
    }

    /**
     * Generates and saves vouchers. Returns number of saved records
     *
     * @param int $iCnt voucher counter offset
     *
     * @return int saved record count
     */
    public function generateVoucher($iCnt)
    {
        $iAmount = abs((int) \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("voucherAmount"));

        // creating new vouchers
        if ($iCnt < $iAmount && ($oVoucherSerie = $this->_getVoucherSerie())) {
            if (!$this->_iGenerated) {
                $this->_iGenerated = $iCnt;
            }

            $blRandomNr = ( bool ) \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("randomVoucherNr");
            $sVoucherNr = $blRandomNr ? \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID() : \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("voucherNr");

            $oNewVoucher = oxNew(\OxidEsales\Eshop\Application\Model\Voucher::class);
            $oNewVoucher->oxvouchers__oxvoucherserieid = new \OxidEsales\Eshop\Core\Field($oVoucherSerie->getId());
            $oNewVoucher->oxvouchers__oxvouchernr = new \OxidEsales\Eshop\Core\Field($sVoucherNr);
            $oNewVoucher->save();

            $this->_iGenerated++;
        }

        return $this->_iGenerated;
    }

    /**
     * Runs voucher generation
     */
    public function run()
    {
        $blContinue = true;
        $iExportedItems = 0;

        // file is open
        $iStart = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("iStart");

        for ($i = $iStart; $i < $iStart + $this->iGeneratePerTick; $i++) {
            if (($iExportedItems = $this->nextTick($i)) === false) {
                // end reached
                $this->stop(ERR_SUCCESS);
                $blContinue = false;
                break;
            }
        }

        if ($blContinue) {
            // make ticker continue
            $this->_aViewData['refresh'] = 0;
            $this->_aViewData['iStart'] = $i;
            $this->_aViewData['iExpItems'] = $iExportedItems;
        }
    }
}
