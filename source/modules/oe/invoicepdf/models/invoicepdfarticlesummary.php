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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Order summary class
 */
class InvoicepdfArticleSummary extends InvoicepdfBlock
{

    /**
     * order object
     *
     * @var object
     */
    protected $_oData = null;

    /**
     * pdf object
     *
     * @var object
     */
    protected $_oPdf = null;

    /**
     * Constructor
     *
     * @param object $oData order object
     * @param object $oPdf  pdf object
     */
    public function __construct($oData, $oPdf)
    {
        $this->_oData = $oData;
        $this->_oPdf = $oPdf;
    }

    /**
     * Sets total costs values using order without discount.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setTotalCostsWithoutDiscount(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();

        // products netto price
        $this->line(15, $iStartPos + 1, 195, $iStartPos + 1);
        $sNetSum = $oLang->formatCurrency($this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
        $this->text(45, $iStartPos + 4, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICENETTO'));
        $this->text(195 - $this->_oPdf->getStringWidth($sNetSum), $iStartPos + 4, $sNetSum);

        // #345 - product VAT info
        $iCtr = 0;
        foreach ($this->_oData->getVats() as $iVat => $dVatPrice) {
            $iStartPos += 4 * $iCtr;
            $sVATSum = $oLang->formatCurrency($dVatPrice, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 8, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $iVat . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
            $this->text(195 - $this->_oPdf->getStringWidth($sVATSum), $iStartPos + 8, $sVATSum);
            $iCtr++;
        }

        // products brutto price
        $sBrutPrice = $oLang->formatCurrency($this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
        $this->text(45, $iStartPos + 12, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO'));
        $this->text(195 - $this->_oPdf->getStringWidth($sBrutPrice), $iStartPos + 12, $sBrutPrice);
        $iStartPos++;

        // line separator
        $this->line(45, $iStartPos + 13, 195, $iStartPos + 13);
        $iStartPos += 5;
    }

    /**
     * Sets total costs values using order with discount.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setTotalCostsWithDiscount(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();

        // line separator
        $this->line(15, $iStartPos + 1, 195, $iStartPos + 1);

        if ($this->_oData->isNettoMode()) {

            // products netto price
            $sNetSum = $oLang->formatCurrency($this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 4, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICENETTO'));
            $this->text(195 - $this->_oPdf->getStringWidth($sNetSum), $iStartPos + 4, $sNetSum);

            // discount
            $dDiscountVal = $this->_oData->oxorder__oxdiscount->value;
            if ($dDiscountVal > 0) {
                $dDiscountVal *= -1;
            }
            $sDiscount = $oLang->formatCurrency($dDiscountVal, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 8, $this->_oData->translate('ORDER_OVERVIEW_PDF_DISCOUNT'));
            $this->text(195 - $this->_oPdf->getStringWidth($sDiscount), $iStartPos + 8, $sDiscount);
            $iStartPos++;

            // line separator
            $this->line(45, $iStartPos + 8, 195, $iStartPos + 8);

            $iCtr = 0;
            foreach ($this->_oData->getVats() as $iVat => $dVatPrice) {
                $iStartPos += 4 * $iCtr;
                $sVATSum = $oLang->formatCurrency($dVatPrice, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(45, $iStartPos + 12, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $iVat . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
                $this->text(195 - $this->_oPdf->getStringWidth($sVATSum), $iStartPos + 12, $sVATSum);
                $iCtr++;
            }
            $iStartPos += 4;

            // products brutto price
            $sBrutPrice = $oLang->formatCurrency($this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 12, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO'));
            $this->text(195 - $this->_oPdf->getStringWidth($sBrutPrice), $iStartPos + 12, $sBrutPrice);
            $iStartPos += 4;

        } else {
            // products brutto price
            $sBrutPrice = $oLang->formatCurrency($this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 4, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO'));
            $this->text(195 - $this->_oPdf->getStringWidth($sBrutPrice), $iStartPos + 4, $sBrutPrice);

            // line separator
            $this->line(45, $iStartPos + 5, 195, $iStartPos + 5);

            // discount
            $dDiscountVal = $this->_oData->oxorder__oxdiscount->value;
            if ($dDiscountVal > 0) {
                $dDiscountVal *= -1;
            }
            $sDiscount = $oLang->formatCurrency($dDiscountVal, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 8, $this->_oData->translate('ORDER_OVERVIEW_PDF_DISCOUNT'));
            $this->text(195 - $this->_oPdf->getStringWidth($sDiscount), $iStartPos + 8, $sDiscount);
            $iStartPos++;

            // line separator
            $this->line(45, $iStartPos + 8, 195, $iStartPos + 8);
            $iStartPos += 4;

            // products netto price
            $sNetSum = $oLang->formatCurrency($this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos + 8, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICENETTO'));
            $this->text(195 - $this->_oPdf->getStringWidth($sNetSum), $iStartPos + 8, $sNetSum);

            // #345 - product VAT info
            $iCtr = 0;
            foreach ($this->_oData->getVats() as $iVat => $dVatPrice) {
                $iStartPos += 4 * $iCtr;
                $sVATSum = $oLang->formatCurrency($dVatPrice, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(45, $iStartPos + 12, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $iVat . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
                $this->text(195 - $this->_oPdf->getStringWidth($sVATSum), $iStartPos + 12, $sVATSum);
                $iCtr++;
            }
            $iStartPos += 4;
        }
    }

    /**
     * Sets voucher values to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setVoucherInfo(&$iStartPos)
    {
        $dVoucher = 0;
        if ($this->_oData->oxorder__oxvoucherdiscount->value) {
            $dDiscountVal = $this->_oData->oxorder__oxvoucherdiscount->value;
            if ($dDiscountVal > 0) {
                $dDiscountVal *= -1;
            }
            $sPayCost = oxRegistry::getLang()->formatCurrency($dDiscountVal, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_VOUCHER'));
            $this->text(195 - $this->_oPdf->getStringWidth($sPayCost), $iStartPos, $sPayCost);
            $iStartPos += 4;
        }

        $iStartPos++;
    }

    /**
     * Sets delivery info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setDeliveryInfo(&$iStartPos)
    {
        $sAddString = '';
        $oLang = oxRegistry::getLang();
        $oConfig = oxRegistry::getConfig();

        if ($oConfig->getConfigParam('blShowVATForDelivery')) {
            // delivery netto
            $sDelCostNetto = $oLang->formatCurrency($this->_oData->getOrderDeliveryPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_SHIPCOST') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
            $this->text(195 - $this->_oPdf->getStringWidth($sDelCostNetto), $iStartPos, $sDelCostNetto);
            $iStartPos += 4;

            if ($oConfig->getConfigParam('sAdditionalServVATCalcMethod') != 'proportional') {
                $sVatValueText = $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $this->_oData->oxorder__oxdelvat->value . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM');
            } else {
                $sVatValueText = $this->_oData->translate('TOTAL_PLUS_PROPORTIONAL_VAT');
            }

            // delivery VAT
            $sDelCostVAT = $oLang->formatCurrency($this->_oData->getOrderDeliveryPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $sVatValueText);
            $this->text(195 - $this->_oPdf->getStringWidth($sDelCostVAT), $iStartPos, $sDelCostVAT);
            //$iStartPos += 4;

            $sAddString = ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_BRUTTO');
        } else {
            // if canceled order, reset value
            if ($this->_oData->oxorder__oxstorno->value) {
                $this->_oData->oxorder__oxdelcost->setValue(0);
            }

            $sDelCost = $oLang->formatCurrency($this->_oData->oxorder__oxdelcost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_SHIPCOST') . $sAddString);
            $this->text(195 - $this->_oPdf->getStringWidth($sDelCost), $iStartPos, $sDelCost);
        }
    }

    /**
     * Sets wrapping info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setWrappingInfo(&$iStartPos)
    {
        if ($this->_oData->oxorder__oxwrapcost->value || $this->_oData->oxorder__oxgiftcardcost->value) {
            $oLang = oxRegistry::getLang();
            $oConfig = oxRegistry::getConfig();

            //displaying wrapping VAT info
            if ($oConfig->getConfigParam('blShowVATForWrapping')) {

                if ($this->_oData->oxorder__oxwrapcost->value) {
                    // wrapping netto
                    $iStartPos += 4;
                    $sWrapCostNetto = $oLang->formatCurrency($this->_oData->getOrderWrappingPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(45, $iStartPos, $this->_oData->translate('WRAPPING_COSTS') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
                    $this->text(195 - $this->_oPdf->getStringWidth($sWrapCostNetto), $iStartPos, $sWrapCostNetto);
                    //$iStartPos++;

                    //wrapping VAT
                    $iStartPos += 4;
                    $sWrapCostVAT = $oLang->formatCurrency($this->_oData->getOrderWrappingPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT'));
                    $this->text(195 - $this->_oPdf->getStringWidth($sWrapCostVAT), $iStartPos, $sWrapCostVAT);
                    // $iStartPos++;
                }

                if ($this->_oData->oxorder__oxgiftcardcost->value) {
                    // wrapping netto
                    $iStartPos += 4;
                    $sWrapCostNetto = $oLang->formatCurrency($this->_oData->getOrderGiftCardPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(45, $iStartPos, $this->_oData->translate('GIFTCARD_COSTS') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
                    $this->text(195 - $this->_oPdf->getStringWidth($sWrapCostNetto), $iStartPos, $sWrapCostNetto);
                    //$iStartPos++;

                    //wrapping VAT
                    $iStartPos += 4;
                    $sWrapCostVAT = $oLang->formatCurrency($this->_oData->getOrderGiftCardPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;

                    if ($oConfig->getConfigParam('sAdditionalServVATCalcMethod') != 'proportional') {
                        $sVatValueText = $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $this->_oData->oxorder__oxgiftcardvat->value . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM');
                    } else {
                        $sVatValueText = $this->_oData->translate('TOTAL_PLUS_PROPORTIONAL_VAT');
                    }

                    $this->text(45, $iStartPos, $sVatValueText);
                    $this->text(195 - $this->_oPdf->getStringWidth($sWrapCostVAT), $iStartPos, $sWrapCostVAT);
                    $iStartPos++;
                }

            } else {
                $sAddString = ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_BRUTTO');

                if ($this->_oData->oxorder__oxwrapcost->value) {
                    $iStartPos += 4;
                    // wrapping cost
                    $sWrapCost = $oLang->formatCurrency($this->_oData->oxorder__oxwrapcost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(45, $iStartPos, $this->_oData->translate('WRAPPING_COSTS' /*'ORDER_OVERVIEW_PDF_WRAPPING'*/) . $sAddString);
                    $this->text(195 - $this->_oPdf->getStringWidth($sWrapCost), $iStartPos, $sWrapCost);
                    $iStartPos++;
                }

                if ($this->_oData->oxorder__oxgiftcardcost->value) {
                    $iStartPos += 4;
                    // gift card cost
                    $sWrapCost = $oLang->formatCurrency($this->_oData->oxorder__oxgiftcardcost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(45, $iStartPos, $this->_oData->translate('GIFTCARD_COSTS') . $sAddString);
                    $this->text(195 - $this->_oPdf->getStringWidth($sWrapCost), $iStartPos, $sWrapCost);
                    $iStartPos++;
                }
            }
        }
    }

    /**
     * Sets payment info to pdf
     *
     * @param int &$iStartPos text start position
     */
    protected function _setPaymentInfo(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();
        $oConfig = oxRegistry::getConfig();

        if ($this->_oData->oxorder__oxstorno->value) {
            $this->_oData->oxorder__oxpaycost->setValue(0);
        }

        if ($oConfig->getConfigParam('blShowVATForDelivery')) {
            if ($this->_oData->oxorder__oxpayvat->value) {
                // payment netto
                $iStartPos += 4;
                $sPayCostNetto = $oLang->formatCurrency($this->_oData->getOrderPaymentPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYMENTIMPACT') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
                $this->text(195 - $this->_oPdf->getStringWidth($sPayCostNetto), $iStartPos, $sPayCostNetto);

                if ($oConfig->getConfigParam('sAdditionalServVATCalcMethod') != 'proportional') {
                    $sVatValueText = $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $this->_oData->oxorder__oxpayvat->value . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM');
                } else {
                    $sVatValueText = $this->_oData->translate('TOTAL_PLUS_PROPORTIONAL_VAT');
                }

                // payment VAT
                $iStartPos += 4;
                $sPayCostVAT = $oLang->formatCurrency($this->_oData->getOrderPaymentPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(45, $iStartPos, $sVatValueText);
                $this->text(195 - $this->_oPdf->getStringWidth($sPayCostVAT), $iStartPos, $sPayCostVAT);

            }

            // if canceled order, reset value

        } else {

            // payment costs
            if ($this->_oData->oxorder__oxpaycost->value) {
                $iStartPos += 4;
                $sPayCost = $oLang->formatCurrency($this->_oData->oxorder__oxpaycost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYMENTIMPACT'));
                $this->text(195 - $this->_oPdf->getStringWidth($sPayCost), $iStartPos, $sPayCost);
            }

            $iStartPos++;
        }
    }

    /**
     * Sets payment info to pdf.
     *
     * @deprecated since 5.3.0 (2016.04.19); Use Trusted Shops Module instead.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setTsProtection(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();
        $oConfig = oxRegistry::getConfig();
        if ($this->_oData->oxorder__oxtsprotectcosts->value && $oConfig->getConfigParam('blShowVATForPayCharge')) {

            // payment netto
            $iStartPos += 4;
            $sPayCostNetto = $oLang->formatCurrency($this->_oData->getOrderTsProtectionPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_TSPROTECTION') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
            $this->text(195 - $this->_oPdf->getStringWidth($sPayCostNetto), $iStartPos, $sPayCostNetto);

            // payment VAT
            $iStartPos += 4;
            $sPayCostVAT = $oLang->formatCurrency($this->_oData->getOrderTsProtectionPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $oConfig->getConfigParam('dDefaultVAT') . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
            $this->text(195 - $this->_oPdf->getStringWidth($sPayCostVAT), $iStartPos, $sPayCostVAT);

            $iStartPos++;

        } elseif ($this->_oData->oxorder__oxtsprotectcosts->value) {

            $iStartPos += 4;
            $sPayCost = $oLang->formatCurrency($this->_oData->oxorder__oxtsprotectcosts->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_TSPROTECTION'));
            $this->text(195 - $this->_oPdf->getStringWidth($sPayCost), $iStartPos, $sPayCost);

            $iStartPos++;
        }
    }

    /**
     * Sets grand total order price to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setGrandTotalPriceInfo(&$iStartPos)
    {
        $this->font($this->getFont(), 'B', 10);

        // total order sum
        $sTotalOrderSum = $this->_oData->getFormattedTotalOrderSum() . ' ' . $this->_oData->getCurrency()->name;
        $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLSUM'));
        $this->text(195 - $this->_oPdf->getStringWidth($sTotalOrderSum), $iStartPos, $sTotalOrderSum);
        $iStartPos += 2;

        if ($this->_oData->oxorder__oxdelvat->value || $this->_oData->oxorder__oxwrapvat->value || $this->_oData->oxorder__oxpayvat->value) {
            $iStartPos += 2;
        }
    }

    /**
     * Sets payment method info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setPaymentMethodInfo(&$iStartPos)
    {
        $oPayment = oxNew('oxpayment');
        $oPayment->loadInLang($this->_oData->getSelectedLang(), $this->_oData->oxorder__oxpaymenttype->value);

        $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_SELPAYMENT') . $oPayment->oxpayments__oxdesc->value;
        $this->font($this->getFont(), '', 10);
        $this->text(15, $iStartPos + 4, $text);
        $iStartPos += 4;
    }

    /**
     * Sets pay until date to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setPayUntilInfo(&$iStartPos)
    {
        $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYUPTO') . date('d.m.Y', strtotime('+' . $this->_oData->getPaymentTerm() . ' day', strtotime($this->_oData->oxorder__oxbilldate->value)));
        $this->font($this->getFont(), '', 10);
        $this->text(15, $iStartPos + 4, $text);
        $iStartPos += 4;
    }

    /**
     * Generates order info block (prices, VATs, etc ).
     *
     * @param int $iStartPos text start position
     *
     * @return int
     */
    public function generate($iStartPos)
    {

        $this->font($this->getFont(), '', 10);
        $siteH = $iStartPos;

        // #1147 discount for vat must be displayed
        if (!$this->_oData->oxorder__oxdiscount->value) {
            $this->_setTotalCostsWithoutDiscount($siteH);
        } else {
            $this->_setTotalCostsWithDiscount($siteH);
        }

        $siteH += 12;

        // voucher info
        $this->_setVoucherInfo($siteH);

        // additional line separator
        if ($this->_oData->oxorder__oxdiscount->value || $this->_oData->oxorder__oxvoucherdiscount->value) {
            $this->line(45, $siteH - 3, 195, $siteH - 3);
        }

        // delivery info
        $this->_setDeliveryInfo($siteH);

        // payment info
        $this->_setPaymentInfo($siteH);

        // wrapping info
        $this->_setWrappingInfo($siteH);

        // TS protection info
        $this->_setTsProtection($siteH);

        // separating line
        $this->line(15, $siteH, 195, $siteH);
        $siteH += 4;

        // total order sum
        $this->_setGrandTotalPriceInfo($siteH);

        // separating line
        $this->line(15, $siteH, 195, $siteH);
        $siteH += 4;

        // payment method
        $this->_setPaymentMethodInfo($siteH);

        // pay until ...
        $this->_setPayUntilInfo($siteH);

        return $siteH - $iStartPos;
    }
}
