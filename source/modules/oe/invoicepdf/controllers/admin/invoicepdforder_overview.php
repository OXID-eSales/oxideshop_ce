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
 * Class InvoicepdfOrder_Overview extends order_overview.
 */
class InvoicepdfOrder_Overview extends InvoicepdfOrder_Overview_parent
{

    /**
     * Add Languages to parameters.
     *
     * @return string
     */
    public function render()
    {
        $return = parent::render();

        $oLang = oxRegistry::getLang();
        $this->_aViewData["alangs"] = $oLang->getLanguageNames();

        return $return;
    }

    /**
     * Performs PDF export to user (outputs file to save).
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
}
