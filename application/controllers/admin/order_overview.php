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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Admin order overview manager.
 * Collects order overview information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Overview.
 */
class Order_Overview extends oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates oxOrder, passes
     * it's data to Smarty engine and returns name of template file
     * "order_overview.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        parent::render();

        $oOrder = oxNew("oxOrder");
        $oCur = $myConfig->getActShopCurrencyObject();
        $oLang = oxRegistry::getLang();

        $soxId = $this->getEditObjectId();
        if ($soxId != "-1" && isset($soxId)) {
            // load object
            $oOrder->load($soxId);

            $this->_aViewData["edit"] = $oOrder;
            $this->_aViewData["aProductVats"] = $oOrder->getProductVats();
            $this->_aViewData["orderArticles"] = $oOrder->getOrderArticles();
            $this->_aViewData["giftCard"] = $oOrder->getGiftCard();
            $this->_aViewData["paymentType"] = $this->_getPaymentType($oOrder);
            $this->_aViewData["deliveryType"] = $oOrder->getDelSet();
            $sTsProtectsField = 'oxorder__oxtsprotectcosts';
            if ($oOrder->$sTsProtectsField->value) {
                $this->_aViewData["tsprotectcosts"] = $oLang->formatCurrency($oOrder->$sTsProtectsField->value, $oCur);
            }
        }

        // orders today
        $dSum = $oOrder->getOrderSum(true);
        $this->_aViewData["ordersum"] = $oLang->formatCurrency($dSum, $oCur);
        $this->_aViewData["ordercnt"] = $oOrder->getOrderCnt(true);

        // ALL orders
        $dSum = $oOrder->getOrderSum();
        $this->_aViewData["ordertotalsum"] = $oLang->formatCurrency($dSum, $oCur);
        $this->_aViewData["ordertotalcnt"] = $oOrder->getOrderCnt();
        $this->_aViewData["afolder"] = $myConfig->getConfigParam('aOrderfolder');
        $this->_aViewData["alangs"] = $oLang->getLanguageNames();

        $this->_aViewData["currency"] = $oCur;

        return "order_overview.tpl";
    }

    /**
     * Returns user payment used for current order. In case current order was executed using
     * credit card and user payment info is not stored in db (if oxConfig::blStoreCreditCardInfo = false),
     * just for preview user payment is set from oxPayment
     *
     * @param object $oOrder Order object
     *
     * @return oxUserPayment
     */
    protected function _getPaymentType($oOrder)
    {
        if (!($oUserPayment = $oOrder->getPaymentType()) && $oOrder->oxorder__oxpaymenttype->value) {
            $oPayment = oxNew("oxPayment");
            if ($oPayment->load($oOrder->oxorder__oxpaymenttype->value)) {
                // in case due to security reasons payment info was not kept in db
                $oUserPayment = oxNew("oxUserPayment");
                $oUserPayment->oxpayments__oxdesc = new oxField($oPayment->oxpayments__oxdesc->value);
            }
        }

        return $oUserPayment;
    }

    /**
     * Performs Lexware export to user (outputs file to save).
     */
    public function exportlex()
    {
        $sOrderNr = oxRegistry::getConfig()->getRequestParameter("ordernr");
        $sToOrderNr = oxRegistry::getConfig()->getRequestParameter("toordernr");
        $oImex = oxNew("oximex");
        if (($sLexware = $oImex->exportLexwareOrders($sOrderNr, $sToOrderNr))) {
            $oUtils = oxRegistry::getUtils();
            $oUtils->setHeader("Pragma: public");
            $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            $oUtils->setHeader("Expires: 0");
            $oUtils->setHeader("Content-type: application/x-download");
            $oUtils->setHeader("Content-Length: " . strlen($sLexware));
            $oUtils->setHeader("Content-Disposition: attachment; filename=intern.xml");
            $oUtils->showMessageAndExit($sLexware);
        }
    }

    /**
     * Gets proper file name
     *
     * @param string $sFilename file name
     *
     * @return string
     */
    public function makeValidFileName($sFilename)
    {
        $sFilename = preg_replace('/[\s]+/', '_', $sFilename);
        $sFilename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $sFilename);

        return str_replace(' ', '_', $sFilename);
    }

    /**
     * Performs PDF export to user (outputs file to save).
     *
     * @deprecated since v5.2.0 (2014-03-27); Moved to invoicepdf module's InvoicepdfOrder_Overview class
     */
    public function createPDF()
    {
        $soxId = $this->getEditObjectId();
        if ($soxId != "-1" && isset($soxId)) {
            // load object
            $oOrder = oxNew("oxorder");
            if ($oOrder->load($soxId)) {
                $oUtils = oxRegistry::getUtils();
                $sTrimmedBillName = trim($oOrder->oxorder__oxbilllname->getRawValue());
                $sFilename = $oOrder->oxorder__oxordernr->value . "_" . $sTrimmedBillName . ".pdf";
                $sFilename = $this->makeValidFileName($sFilename);
                ob_start();
                $oOrder->genPDF($sFilename, oxRegistry::getConfig()->getRequestParameter("pdflanguage"));
                $sPDF = ob_get_contents();
                ob_end_clean();
                $oUtils->setHeader("Pragma: public");
                $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                $oUtils->setHeader("Expires: 0");
                $oUtils->setHeader("Content-type: application/pdf");
                $oUtils->setHeader("Content-Disposition: attachment; filename=" . $sFilename);
                oxRegistry::getUtils()->showMessageAndExit($sPDF);
            }
        }
    }


    /**
     * Sends order.
     */
    public function sendorder()
    {
        $oOrder = oxNew("oxorder");
        if ($oOrder->load($this->getEditObjectId())) {
            $oOrder->oxorder__oxsenddate = new oxField(date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime()));
            $oOrder->save();

            // #1071C
            $oOrderArticles = $oOrder->getOrderArticles();
            foreach ($oOrderArticles as $sOxid => $oArticle) {
                // remove canceled articles from list
                if ($oArticle->oxorderarticles__oxstorno->value == 1) {
                    $oOrderArticles->offsetUnset($sOxid);
                }
            }

            if (($blMail = oxRegistry::getConfig()->getRequestParameter("sendmail"))) {
                // send eMail
                $oEmail = oxNew("oxemail");
                $oEmail->sendSendedNowMail($oOrder);
            }
        }
    }

    /**
     * Resets order shipping date.
     */
    public function resetorder()
    {
        $oOrder = oxNew("oxorder");
        if ($oOrder->load($this->getEditObjectId())) {
            $oOrder->oxorder__oxsenddate = new oxField("0000-00-00 00:00:00");
            $oOrder->save();
        }
    }

    /**
     * Returns pdf export state - can export or not
     *
     * @return bool
     */
    public function canExport()
    {
        $blCan = false;
        //V #529: check if PDF invoice module is active
        $oModule = oxNew('oxmodule');
        $oModule->load('invoicepdf');
        if ($oModule->isActive()) {
            $oDb = oxDb::getDb();
            $sOrderId = $this->getEditObjectId();
            $sTable = getViewName("oxorderarticles");
            $sQ = "select count(oxid) from {$sTable} where oxorderid = " . $oDb->quote($sOrderId) . " and oxstorno = 0";
            $blCan = (bool) $oDb->getOne($sQ, false, false);
        }

        return $blCan;
    }

    /**
     * Get information about shipping status
     *
     * @return bool
     */
    public function canResetShippingDate()
    {
        $oOrder = oxNew("oxorder");
        $blCan = false;
        if ($oOrder->load($this->getEditObjectId())) {
            $blCan = $oOrder->oxorder__oxstorno->value == "0" &&
                     !($oOrder->oxorder__oxsenddate->value == "0000-00-00 00:00:00" || $oOrder->oxorder__oxsenddate->value == "-");
        }

        return $blCan;
    }
}
