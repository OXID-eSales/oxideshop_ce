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
class PriceAlarm_Main extends oxAdminDetails
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

            // #1140 R - price must be checked from the object.
            $sql = "select oxarticles.oxid, oxpricealarm.oxprice from oxpricealarm, oxarticles where oxarticles.oxid = oxpricealarm.oxartid and oxpricealarm.oxsended = '000-00-00 00:00:00'";
            $rs = oxDb::getDb()->Execute( $sql);
            $iAllCnt = 0;

            if ($rs != false && $rs->recordCount() > 0) {
                while (!$rs->EOF) {
                    $oArticle = oxNew("oxarticle" );
                    $oArticle->load($rs->fields[0]);
                    if ($oArticle->getPrice()->getBruttoPrice() <= $rs->fields[1])
                        $iAllCnt++;
                    $rs->moveNext();
                }
            }
            $this->_aViewData['iAllCnt'] = $iAllCnt;

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oPricealarm = oxNew( "oxpricealarm" );
            $oPricealarm->load( $soxId);

            // customer info
            $oUser = null;
            if ( $oPricealarm->oxpricealarm__oxuserid->value) {
                $oUser = oxNew( "oxuser" );
                $oUser->load($oPricealarm->oxpricealarm__oxuserid->value);
                $oPricealarm->oUser = $oUser;
            }

            //
            $oShop = oxNew( "oxshop" );
            $oShop->load( $myConfig->getShopId());
            $oShop = $this->addGlobalParams( $oShop );

            if ( !( $iLang = $oPricealarm->oxpricealarm__oxlang->value ) ) {
                $iLang = 0;
            }

            $oLang = oxRegistry::getLang();
            $aLanguages = $oLang->getLanguageNames();
            $this->_aViewData["edit_lang"] = $aLanguages[$iLang];
            // rendering mail message text
            $oLetter = new stdClass();
            $aParams = oxConfig::getParameter( "editval");
            if ( isset( $aParams['oxpricealarm__oxlongdesc'] ) && $aParams['oxpricealarm__oxlongdesc'] ) {
                $oLetter->oxpricealarm__oxlongdesc = new oxField( stripslashes( $aParams['oxpricealarm__oxlongdesc'] ), oxField::T_RAW );
            } else {
/*
                $smarty = oxRegistry::get("oxUtilsView")->getSmarty();
                $smarty->assign( "shop", $oShop );
                $smarty->assign( "product", $oPricealarm->getArticle() );
                $smarty->assign( "bidprice", $oPricealarm->getFProposedPrice());
                $smarty->assign( "currency", $oPricealarm->getPriceAlarmCurrency() );
                $smarty->assign( "currency", $oPricealarm->getPriceAlarmCurrency() );
*/


                $oEmail = oxNew( "oxEmail" );
                $sDesc  = $oEmail->sendPricealarmToCustomer( $oPricealarm->oxpricealarm__oxemail->value, $oPricealarm, null, true );

                $iOldLang = $oLang->getTplLanguage();
                $oLang->setTplLanguage( $iLang );
                $oLetter->oxpricealarm__oxlongdesc = new oxField( $sDesc, oxField::T_RAW );
                $oLang->setTplLanguage( $iOldLang );
            }

            $this->_aViewData["editor"]  = $this->_generateTextEditor( "100%", 300, $oLetter, "oxpricealarm__oxlongdesc", "details.tpl.css");
            $this->_aViewData["edit"]    = $oPricealarm;
            $this->_aViewData["actshop"] = $myConfig->getShopId();
        }

        parent::render();

        return "pricealarm_main.tpl";
    }

    /**
     * Sending email to selected customer
     *
     * @return null
     */
    public function send()
    {
        $blError = true;

        // error
        if ( ( $sOxid = $this->getEditObjectId() ) ) {
            $oPricealarm = oxNew( "oxpricealarm" );
            $oPricealarm->load( $sOxid );

            $aParams = oxConfig::getParameter( "editval" );
            $sMailBody = isset( $aParams['oxpricealarm__oxlongdesc'] ) ? stripslashes( $aParams['oxpricealarm__oxlongdesc'] ) : '';
            if ( $sMailBody ) {
                $sMailBody = oxRegistry::get("oxUtilsView")->parseThroughSmarty( $sMailBody, $oPricealarm->getId() );
            }

            $sRecipient = $oPricealarm->oxpricealarm__oxemail->value;

            $oEmail = oxNew( 'oxemail' );
            $blSuccess = (int) $oEmail->sendPricealarmToCustomer( $sRecipient, $oPricealarm, $sMailBody );

            // setting result message
            if ( $blSuccess ) {
                $oPricealarm->oxpricealarm__oxsended->setValue( date( "Y-m-d H:i:s" ) );
                $oPricealarm->save();
                $blError = false;
            }
        }

        if ( !$blError ) {
            $this->_aViewData["mail_succ"] = 1;
        } else {
            $this->_aViewData["mail_err"] = 1;
        }
    }
}
