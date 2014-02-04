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
 * Admin article main pricealarm manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer News -> pricealarm -> Main.
 * @package admin
 */
class PriceAlarm_Mail extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxpricealarm object
     * and passes it's data to Smarty engine. Returns name of template file
     * "pricealarm_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();
        // #889C - Netto prices in Admin
        $sIndex="";
        if ( $myConfig->getConfigParam( 'blEnterNetPrice' ) )
            $sIndex = " * ". (1+$myConfig->getConfigParam( 'dDefaultVAT' )/100);

        $sShopID = $myConfig->getShopId();
        //articles price in subshop and baseshop can be different
        $this->_aViewData['iAllCnt'] = 0;
        $sQ = "select oxprice, oxartid from oxpricealarm where oxsended = '000-00-00 00:00:00' and oxshopid = '$sShopID' ";
        $rs = oxDb::getDb()->execute($sQ);
        if ($rs != false && $rs->recordCount() > 0) {
            $aSimpleCache = array();
            while (!$rs->EOF) {
                $sPrice = $rs->fields[0];
                $sArtID = $rs->fields[1];
                if (isset($aSimpleCache[$sArtID])) {
                    if ($aSimpleCache[$sArtID] <= $sPrice) {
                        $this->_aViewData['iAllCnt'] += 1;
                    }
                } else {
                    $oArticle = oxNew( "oxarticle" );
                    if ( $oArticle->load($sArtID)) {
                        $dArtPrice = $aSimpleCache[$sArtID] = $oArticle->getPrice()->getBruttoPrice();
                        if ($dArtPrice <= $sPrice) {
                            $this->_aViewData['iAllCnt'] += 1;
                        }
                    }
                }
                $rs->moveNext();
            }
        }
        return "pricealarm_mail.tpl";
    }
}
