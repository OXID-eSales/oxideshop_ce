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

use oxRegistry;
use oxDb;
use oxField;
use stdClass;

/**
 * Admin article main pricealarm manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer Info -> pricealarm -> Main.
 */
class PriceAlarmMain extends \oxAdminDetails
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
        $config = $this->getConfig();

        $this->_aViewData['iAllCnt'] = $this->getActivePriceAlarmsCount();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oPricealarm = oxNew("oxpricealarm");
            $oPricealarm->load($soxId);

            // customer info
            $oUser = null;
            if ($oPricealarm->oxpricealarm__oxuserid->value) {
                $oUser = oxNew("oxuser");
                $oUser->load($oPricealarm->oxpricealarm__oxuserid->value);
                $oPricealarm->oUser = $oUser;
            }

            //
            $oShop = oxNew("oxshop");
            $oShop->load($config->getShopId());
            $this->addGlobalParams($oShop);

            if (!($iLang = $oPricealarm->oxpricealarm__oxlang->value)) {
                $iLang = 0;
            }

            $oLang = oxRegistry::getLang();
            $aLanguages = $oLang->getLanguageNames();
            $this->_aViewData["edit_lang"] = $aLanguages[$iLang];
            // rendering mail message text
            $oLetter = new stdClass();
            $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
            if (isset($aParams['oxpricealarm__oxlongdesc']) && $aParams['oxpricealarm__oxlongdesc']) {
                $oLetter->oxpricealarm__oxlongdesc = new oxField(stripslashes($aParams['oxpricealarm__oxlongdesc']), oxField::T_RAW);
            } else {
                $oEmail = oxNew("oxEmail");
                $sDesc = $oEmail->sendPricealarmToCustomer($oPricealarm->oxpricealarm__oxemail->value, $oPricealarm, null, true);

                $iOldLang = $oLang->getTplLanguage();
                $oLang->setTplLanguage($iLang);
                $oLetter->oxpricealarm__oxlongdesc = new oxField($sDesc, oxField::T_RAW);
                $oLang->setTplLanguage($iOldLang);
            }

            $this->_aViewData["editor"] = $this->_generateTextEditor("100%", 300, $oLetter, "oxpricealarm__oxlongdesc", "details.tpl.css");
            $this->_aViewData["edit"] = $oPricealarm;
            $this->_aViewData["actshop"] = $config->getShopId();
        }

        parent::render();

        return "pricealarm_main.tpl";
    }

    /**
     * Sending email to selected customer
     */
    public function send()
    {
        $blError = true;

        // error
        if (($sOxid = $this->getEditObjectId())) {
            $oPricealarm = oxNew("oxpricealarm");
            $oPricealarm->load($sOxid);

            $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
            $sMailBody = isset($aParams['oxpricealarm__oxlongdesc']) ? stripslashes($aParams['oxpricealarm__oxlongdesc']) : '';
            if ($sMailBody) {
                $sMailBody = oxRegistry::get("oxUtilsView")->parseThroughSmarty($sMailBody, $oPricealarm->getId());
            }

            $sRecipient = $oPricealarm->oxpricealarm__oxemail->value;

            $oEmail = oxNew('oxemail');
            $blSuccess = (int) $oEmail->sendPricealarmToCustomer($sRecipient, $oPricealarm, $sMailBody);

            // setting result message
            if ($blSuccess) {
                $oPricealarm->oxpricealarm__oxsended->setValue(date("Y-m-d H:i:s"));
                $oPricealarm->save();
                $blError = false;
            }
        }

        if (!$blError) {
            $this->_aViewData["mail_succ"] = 1;
        } else {
            $this->_aViewData["mail_err"] = 1;
        }
    }

    /**
     * Returns number of active price alarms.
     *
     * @return int
     */
    protected function getActivePriceAlarmsCount()
    {
        // #1140 R - price must be checked from the object.
        $query = "
            SELECT oxarticles.oxid, oxpricealarm.oxprice
            FROM oxpricealarm, oxarticles
            WHERE oxarticles.oxid = oxpricealarm.oxartid AND oxpricealarm.oxsended = '000-00-00 00:00:00'";
        $result = oxDb::getDb()->select($query);
        $count = 0;

        if ($result != false && $result->count() > 0) {
            while (!$result->EOF) {
                $article = oxNew("oxArticle");
                $article->load($result->fields[0]);
                if ($article->getPrice()->getBruttoPrice() <= $result->fields[1]) {
                    $count++;
                }
                $result->fetchRow();
            }
        }

        return $count;
    }
}
