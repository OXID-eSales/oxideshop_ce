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
 * Order pdf generator class
 */
class InvoicepdfOxOrder extends InvoicepdfOxOrder_parent
{

    /**
     * PDF language
     *
     * @var int
     */
    protected $_iSelectedLang = 0;

    /**
     * Cached active shop object
     *
     * @var object
     */
    protected $_oActShop = null;

    /**
     * Order arctiles VAT's
     *
     * @var array
     */
    protected $_aVATs = array();

    /**
     * Order currency object
     *
     * @var object
     */
    protected $_oCur = null;


    /**
     * Set language for pdf generation.
     *
     * @param integer $iLang Language id.
     */
    public function setSelectedLang($iLang)
    {
        $this->_iSelectedLang = $iLang;
    }

    /**
     * Returns active shop object.
     *
     * @return oxshop $oUser
     */
    protected function _getActShop()
    {
        // shop is allready loaded
        if ($this->_oActShop !== null) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew('oxshop');
        $this->_oActShop->load($this->getConfig()->getShopId());

        return $this->_oActShop;
    }

    /**
     * Returns translated string.
     *
     * @param string $sString string to translate
     *
     * @return string
     */
    public function translate($sString)
    {
        return oxRegistry::getLang()->translateString($sString, $this->getSelectedLang());
    }

    /**
     * Formats pdf page footer.
     *
     * @param object $oPdf pdf document object
     */
    public function pdfFooter($oPdf)
    {

        $oShop = $this->_getActShop();

        $oPdf->line(15, 272, 195, 272);

        $oPdfBlock = new InvoicepdfBlock();
        /* column 1 - company name, shop owner info, shop address */
        $oPdf->setFont($oPdfBlock->getFont(), '', 7);
        $oPdf->text(15, 275, strip_tags($oShop->oxshops__oxcompany->getRawValue()));
        $oPdf->text(15, 278, strip_tags($oShop->oxshops__oxfname->getRawValue()) . ' ' . strip_tags($oShop->oxshops__oxlname->getRawValue()));
        $oPdf->text(15, 281, strip_tags($oShop->oxshops__oxstreet->getRawValue()));
        $oPdf->text(15, 284, strip_tags($oShop->oxshops__oxzip->value) . ' ' . strip_tags($oShop->oxshops__oxcity->getRawValue()));
        $oPdf->text(15, 287, strip_tags($oShop->oxshops__oxcountry->getRawValue()));

        /* column 2 - phone, fax, url, email address */
        $oPdf->text(85, 275, $this->translate('ORDER_OVERVIEW_PDF_PHONE') . strip_tags($oShop->oxshops__oxtelefon->value));
        $oPdf->text(85, 278, $this->translate('ORDER_OVERVIEW_PDF_FAX') . strip_tags($oShop->oxshops__oxtelefax->value));
        $oPdf->text(85, 281, strip_tags($oShop->oxshops__oxurl->value));
        $oPdf->text(85, 284, strip_tags($oShop->oxshops__oxorderemail->value));

        /* column 3 - bank information */
        $oPdf->text(150, 275, strip_tags($oShop->oxshops__oxbankname->getRawValue()));
        $oPdf->text(150, 278, $this->translate('ORDER_OVERVIEW_PDF_ACCOUNTNR') . strip_tags($oShop->oxshops__oxbanknumber->value));
        $oPdf->text(150, 281, $this->translate('ORDER_OVERVIEW_PDF_BANKCODE') . strip_tags($oShop->oxshops__oxbankcode->value));
        $oPdf->text(150, 284, $this->translate('ORDER_OVERVIEW_PDF_VATID') . strip_tags($oShop->oxshops__oxvatnumber->value));
        $oPdf->text(150, 287, $this->translate('ORDER_OVERVIEW_PDF_TAXID') . strip_tags($oShop->oxshops__oxtaxnumber->value));
    }

    /**
     * Adds shop logo to page header. Returns position for next texts in pdf.
     *
     * @param object $oPdf pdf document object
     *
     * @return int
     */
    public function pdfHeaderPlus($oPdf)
    {

        // new page with shop logo
        $this->pdfHeader($oPdf);

        $oPdfBlock = new InvoicepdfBlock();
        // column names
        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
        $oPdf->text(15, 50, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdf->text(30, 50, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdf->text(45, 50, $this->translate('ORDER_OVERVIEW_PDF_DESC'));
        $oPdf->text(160, 50, $this->translate('ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdf->text(195 - $oPdf->getStringWidth($sText), 50, $sText);

        // line separator
        $oPdf->line(15, 52, 195, 52);

        return 56;
    }

    /**
     * Creating new page with shop logo. Returning position to continue data writing.
     *
     * @param object $oPdf pdf document object
     *
     * @return int
     */
    public function pdfHeader($oPdf)
    {
        // adding new page ...
        $oPdf->addPage();

        // loading active shop
        $oShop = $this->_getActShop();

        //logo
        $myConfig = $this->getConfig();
        $aSize = getimagesize($myConfig->getImageDir() . '/pdf_logo.jpg');
        $iMargin = 195 - $aSize[0] * 0.2;
        $oPdf->setLink($oShop->oxshops__oxurl->value);
        $oPdf->image($myConfig->getImageDir() . '/pdf_logo.jpg', $iMargin, 10, $aSize[0] * 0.2, $aSize[1] * 0.2, '', $oShop->oxshops__oxurl->value);

        return 14 + $aSize[1] * 0.2;
    }

    /**
     * Generates order pdf report file.
     *
     * @param string $sFilename name of report file
     * @param int    $iSelLang  active language
     */
    public function genPdf($sFilename, $iSelLang = 0)
    {
        // setting pdf language
        $this->setSelectedLang($iSelLang);

        $blIsNewOrder = 0;
        // setting invoice number
        if (!$this->oxorder__oxbillnr->value) {
            $this->oxorder__oxbillnr->setValue($this->getNextBillNum());
            $blIsNewOrder = 1;
        }
        // setting invoice date
        if ($this->oxorder__oxbilldate->value == '0000-00-00') {
            $this->oxorder__oxbilldate->setValue(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))));
            $blIsNewOrder = 1;
        }
        // saving order if new number or date
        if ($blIsNewOrder) {
            $this->save();
        }

        // initiating pdf engine
        $oPdf = oxNew('oxPDF');
        $oPdf->setPrintHeader(false);
        $oPdf->open();

        // adding header
        $this->pdfHeader($oPdf);

        // adding info data
        switch (oxRegistry::getConfig()->getRequestParameter('pdftype')) {
            case 'dnote':
                $this->exportDeliveryNote($oPdf);
                break;
            default:
                $this->exportStandart($oPdf);
        }

        // adding footer
        $this->pdfFooter($oPdf);

        // outputting file to browser
        $oPdf->output($sFilename, 'I');
    }


    /**
     * Set billing address info to pdf.
     *
     * @param object $oPdf pdf document object
     */
    protected function _setBillingAddressToPdf($oPdf)
    {
        $oLang = oxRegistry::getLang();
        $sSal = $this->oxorder__oxbillsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxbillsal->value, $this->getSelectedLang());
        } catch (Exception $e) {
        }
        $oPdfBlock = new InvoicepdfBlock();
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(15, 59, $sSal);
        $oPdf->text(15, 63, $this->oxorder__oxbillfname->getRawValue() . ' ' . $this->oxorder__oxbilllname->getRawValue());
        $oPdf->text(15, 67, $this->oxorder__oxbillcompany->getRawValue());
        $oPdf->text(15, 71, $this->oxorder__oxbillstreet->getRawValue() . ' ' . $this->oxorder__oxbillstreetnr->value);
        $oPdf->setFont($oPdfBlock->getFont(), 'B', 10);
        $oPdf->text(15, 75, $this->oxorder__oxbillzip->value . ' ' . $this->oxorder__oxbillcity->getRawValue());
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(15, 79, $this->oxorder__oxbillcountry->getRawValue());
    }

    /**
     * Set delivery address info to pdf.
     *
     * @param object $oPdf pdf document object
     */
    protected function _setDeliveryAddressToPdf($oPdf)
    {
        $oLang = oxRegistry::getLang();
        $sSal = $this->oxorder__oxdelsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxdelsal->value, $this->getSelectedLang());
        } catch (Exception $e) {
        }
        $oPdfBlock = new InvoicepdfBlock();
        $oPdf->setFont($oPdfBlock->getFont(), '', 6);
        $oPdf->text(15, 87, $this->translate('ORDER_OVERVIEW_PDF_DELIVERYADDRESS'));
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(15, 91, $sSal);
        $oPdf->text(15, 95, $this->oxorder__oxdellname->getRawValue() . ' ' . $this->oxorder__oxdelfname->getRawValue());
        $oPdf->text(15, 99, $this->oxorder__oxdelcompany->getRawValue());
        $oPdf->text(15, 103, $this->oxorder__oxdelstreet->getRawValue() . ' ' . $this->oxorder__oxdelstreetnr->value);
        $oPdf->setFont($oPdfBlock->getFont(), 'B', 10);
        $oPdf->text(15, 107, $this->oxorder__oxdelzip->value . ' ' . $this->oxorder__oxdelcity->getRawValue());
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(15, 111, $this->oxorder__oxdelcountry->getRawValue());
    }

    /**
     * Set order articles info and articles VAT's to pdf.
     *
     * @param object $oPdf        pdf document object
     * @param int    &$iStartPos  text start position from top
     * @param bool   $blShowPrice show articles prices / VAT info or not
     */
    protected function _setOrderArticlesToPdf($oPdf, &$iStartPos, $blShowPrice = true)
    {
        if (!$this->_oArticles) {
            $this->_oArticles = $this->getOrderArticles(true);
        }

        $oCurr = $this->getCurrency();
        $oPdfBlock = new InvoicepdfBlock();
        // product list
        foreach ($this->_oArticles as $key => $oOrderArt) {

            // starting a new page ...
            if ($iStartPos > 243) {
                $this->pdffooter($oPdf);
                $iStartPos = $this->pdfheaderplus($oPdf);
                $oPdf->setFont($oPdfBlock->getFont(), '', 10);
            } else {
                $iStartPos = $iStartPos + 4;
            }

            // sold amount
            $oPdf->text(20 - $oPdf->getStringWidth($oOrderArt->oxorderarticles__oxamount->value), $iStartPos, $oOrderArt->oxorderarticles__oxamount->value);

            // product number
            $oPdf->setFont($oPdfBlock->getFont(), '', 8);
            $oPdf->text(28, $iStartPos, $oOrderArt->oxorderarticles__oxartnum->value);

            // product title
            $oPdf->setFont($oPdfBlock->getFont(), '', 10);
            $oPdf->text(45, $iStartPos, substr(strip_tags($this->_replaceExtendedChars($oOrderArt->oxorderarticles__oxtitle->getRawValue(), true)), 0, 58));

            if ($blShowPrice) {
                $oLang = oxRegistry::getLang();

                // product VAT percent
                $oPdf->text(140 - $oPdf->getStringWidth($oOrderArt->oxorderarticles__oxvat->value), $iStartPos, $oOrderArt->oxorderarticles__oxvat->value);

                // product price

                $dUnitPrice = ($this->isNettoMode()) ? $oOrderArt->oxorderarticles__oxnprice->value : $oOrderArt->oxorderarticles__oxbprice->value;
                $dTotalPrice = ($this->isNettoMode()) ? $oOrderArt->oxorderarticles__oxnetprice->value : $oOrderArt->oxorderarticles__oxbrutprice->value;

                $sText = $oLang->formatCurrency($dUnitPrice, $this->_oCur) . ' ' . $this->_oCur->name;
                $oPdf->text(163 - $oPdf->getStringWidth($sText), $iStartPos, $sText);

                // total product price
                $sText = $oLang->formatCurrency($dTotalPrice, $this->_oCur) . ' ' . $this->_oCur->name;
                $oPdf->text(195 - $oPdf->getStringWidth($sText), $iStartPos, $sText);

            }

            // additional variant info
            if ($oOrderArt->oxorderarticles__oxselvariant->value) {
                $iStartPos = $iStartPos + 4;
                $oPdf->text(45, $iStartPos, substr($oOrderArt->oxorderarticles__oxselvariant->value, 0, 58));
            }

        }
    }

    /**
     * Exporting standard invoice pdf
     *
     * @param object $oPdf pdf document object
     */
    public function exportStandart($oPdf)
    {
        // preparing order curency info
        $myConfig = $this->getConfig();
        $oPdfBlock = new InvoicepdfBlock();

        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!$this->_oCur) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // loading active shop
        $oShop = $this->_getActShop();

        // shop information
        $oPdf->setFont($oPdfBlock->getFont(), '', 6);
        $oPdf->text(15, 55, $oShop->oxshops__oxname->getRawValue() . ' - ' . $oShop->oxshops__oxstreet->getRawValue() . ' - ' . $oShop->oxshops__oxzip->value . ' - ' . $oShop->oxshops__oxcity->getRawValue());

        // billing address
        $this->_setBillingAddressToPdf($oPdf);

        // delivery address
        if ($this->oxorder__oxdelsal->value) {
            $this->_setDeliveryAddressToPdf($oPdf);
        }

        // loading user
        $oUser = oxNew('oxuser');
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdf->setFont($oPdfBlock->getFont(), '', 5);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), 55, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR') . ' ' . $oUser->oxuser__oxcustnr->value;
        $oPdf->setFont($oPdfBlock->getFont(), '', 7);
        $oPdf->text(195 - $oPdf->getStringWidth($sCustNr), 59, $sCustNr);

        // setting position if delivery address is used
        if ($this->oxorder__oxdelsal->value) {
            $iTop = 115;
        } else {
            $iTop = 91;
        }

        // shop city
        $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y', strtotime($this->oxorder__oxbilldate->value));
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR') . ' ' . $oShop->oxshops__oxvatnumber->value;
            $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 12, $sText);
            $iTop += 8;
        } else {
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR') . ' ' . $this->oxorder__oxbillnr->value;
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // marking if order is canceled
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   ' . $this->translate('ORDER_OVERVIEW_PDF_STORNO'), oxField::T_RAW);
        }

        // order number
        $oPdf->setFont($oPdfBlock->getFont(), '', 12);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_PURCHASENR') . ' ' . $this->oxorder__oxordernr->value);

        // order date
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $aOrderDate = explode(' ', $this->oxorder__oxorderdate->value);
        $sOrderDate = oxRegistry::get("oxUtilsDate")->formatDBDate($aOrderDate[0]);
        $oPdf->text(15, $iTop + 8, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM') . $sOrderDate . $this->translate('ORDER_OVERVIEW_PDF_ORDERSAT') . $oShop->oxshops__oxurl->value);
        $iTop += 16;

        // product info header
        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdf->text(30, $iTop, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdf->text(45, $iTop, $this->translate('ORDER_OVERVIEW_PDF_DESC'));
        $oPdf->text(135, $iTop, $this->translate('ORDER_OVERVIEW_PDF_VAT'));
        $oPdf->text(148, $iTop, $this->translate('ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop, $sText);

        // separator line
        $iTop += 2;
        $oPdf->line(15, $iTop, 195, $iTop);

        // #345
        $siteH = $iTop;
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);

        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $siteH, true);

        // generating pdf file
        $oArtSumm = new InvoicepdfArticleSummary($this, $oPdf);
        $iHeight = $oArtSumm->generate($siteH);
        if ($siteH + $iHeight > 258) {
            $this->pdfFooter($oPdf);
            $iTop = $this->pdfHeader($oPdf);
            $oArtSumm->ajustHeight($iTop - $siteH);
            $siteH = $iTop;
        }

        $oArtSumm->run($oPdf);
        $siteH += $iHeight + 8;

        $oPdf->text(15, $siteH, $this->translate('ORDER_OVERVIEW_PDF_GREETINGS'));
    }

    /**
     * Generating delivery note pdf.
     *
     * @param object $oPdf pdf document object
     */
    public function exportDeliveryNote($oPdf)
    {
        $myConfig = $this->getConfig();
        $oShop = $this->_getActShop();
        $oPdfBlock = new InvoicepdfBlock();

        $oLang = oxRegistry::getLang();
        $sSal = $this->oxorder__oxdelsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxdelsal->value, $this->getSelectedLang());
        } catch (Exception $e) {
        }

        // loading order currency info
        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!isset($this->_oCur)) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // shop info
        $oPdf->setFont($oPdfBlock->getFont(), '', 6);
        $oPdf->text(15, 55, $oShop->oxshops__oxname->getRawValue() . ' - ' . $oShop->oxshops__oxstreet->getRawValue() . ' - ' . $oShop->oxshops__oxzip->value . ' - ' . $oShop->oxshops__oxcity->getRawValue());

        // delivery address
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        if ($this->oxorder__oxdelsal->value) {
            $oPdf->text(15, 59, $sSal);
            $oPdf->text(15, 63, $this->oxorder__oxdellname->getRawValue() . ' ' . $this->oxorder__oxdelfname->getRawValue());
            $oPdf->text(15, 67, $this->oxorder__oxdelcompany->getRawValue());
            $oPdf->text(15, 71, $this->oxorder__oxdelstreet->getRawValue() . ' ' . $this->oxorder__oxdelstreetnr->value);
            $oPdf->setFont($oPdfBlock->getFont(), 'B', 10);
            $oPdf->text(15, 75, $this->oxorder__oxdelzip->value . ' ' . $this->oxorder__oxdelcity->getRawValue());
            $oPdf->setFont($oPdfBlock->getFont(), '', 10);
            $oPdf->text(15, 79, $this->oxorder__oxdelcountry->getRawValue());
        } else {
            // no delivery address - billing address is used for delivery
            $this->_setBillingAddressToPdf($oPdf);
        }

        // loading user info
        $oUser = oxNew('oxuser');
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdf->setFont($oPdfBlock->getFont(), '', 5);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), 70, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR') . ' ' . $oUser->oxuser__oxcustnr->value;
        $oPdf->setFont($oPdfBlock->getFont(), '', 7);
        $oPdf->text(195 - $oPdf->getStringWidth($sCustNr), 73, $sCustNr);

        // shops city
        $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y');
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), 95, $sText);

        $iTop = 99;
        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR') . ' ' . $oShop->oxshops__oxvatnumber->value;
            $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop, $sText);
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR') . ' ' . $this->oxorder__oxbillnr->value;
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop, $sText);

        // canceled order marker
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   ' . $this->translate('ORDER_OVERVIEW_PDF_STORNO'), oxField::T_RAW);
        }

        // order number
        $oPdf->setFont($oPdfBlock->getFont(), '', 12);
        $oPdf->text(15, 108, $this->translate('ORDER_OVERVIEW_PDF_DELIVNOTE') . ' ' . $this->oxorder__oxordernr->value);

        // order date
        $aOrderDate = explode(' ', $this->oxorder__oxorderdate->value);
        $sOrderDate = oxRegistry::get("oxUtilsDate")->formatDBDate($aOrderDate[0]);
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(15, 119, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM') . $sOrderDate . $this->translate('ORDER_OVERVIEW_PDF_ORDERSAT') . $oShop->oxshops__oxurl->value);

        // product info header
        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
        $oPdf->text(15, 128, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdf->text(30, 128, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdf->text(45, 128, $this->translate('ORDER_OVERVIEW_PDF_DESC'));

        // line separator
        $oPdf->line(15, 130, 195, 130);

        // product list
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $siteH = 130;

        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $siteH, false);

        // sine separator
        $oPdf->line(15, $siteH + 2, 195, $siteH + 2);
        $siteH += 4;

        // payment date
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $text = $this->translate('ORDER_OVERVIEW_PDF_PAYUPTO') . date('d.m.Y', strtotime('+' . $this->getPaymentTerm() . ' day', strtotime($this->oxorder__oxbilldate->value)));
        $oPdf->text(15, $siteH + 4, $text);
    }

    /**
     * Replaces some special characters to HTML compatible symbol codes.
     * SWITCHED OFF NOW ( 2.2 )
     *
     * @param string $sValue    initial value
     * @param bool   $blReverse (default false) if false - checks if we do have already htmlentities inside
     *
     * @return string
     */
    protected function _replaceExtendedChars($sValue, $blReverse = false)
    {
        // we need to replace this for compatibility with XHTML
        // as this function causes a lot of trouble with editor
        // we switch it off, even if this means that fields do not validate through xhtml
        // return $sValue;

        // we need to replace this for compatibility with XHTML
        $aReplace = array(chr(169) => "&copy;", chr(128) => "&euro;", "\"" => "&quot;", "'" => "&#039;");

        // #899C reverse html entities and references transformation is used in invoicepdf module
        // so this part must be enabled. Now it works with html references like &#123;
        if ($blReverse) {
            // replace now
            if (version_compare(PHP_VERSION, '5.3.4') >= 0) {
                $aTransTbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'ISO-8859-1');
            } else {
                $aTransTbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT);
            }

            $aTransTbl = array_flip($aTransTbl) + array_flip($aReplace);
            $sValue = strtr($sValue, $aTransTbl);
            $sValue = getStr()->preg_replace_callback('/\&\#([0-9]+)\;/m', create_function('$matches', "return chr(\$matches[1]);") , $sValue);
        }

        return $sValue;
    }

    /**
     * Returns order articles VATS's.
     *
     * @return array
     */
    public function getVats()
    {
        // for older orders
        return $this->getProductVats(false);
    }

    /**
     * Returns order currency object.
     *
     * @return object
     */
    public function getCurrency()
    {
        return $this->_oCur;
    }

    /**
     * Returns order currency object.
     *
     * @return object
     */
    public function getSelectedLang()
    {
        return $this->_iSelectedLang;
    }

    /**
     * Method returns config param iPaymentTerm, default value is 7;
     *
     * @return int
     */
    public function getPaymentTerm()
    {
        if (null === $iPaymentTerm = $this->getConfig()->getConfigParam('iPaymentTerm')) {
            $iPaymentTerm = 7;
        }

        return $iPaymentTerm;
    }
}
