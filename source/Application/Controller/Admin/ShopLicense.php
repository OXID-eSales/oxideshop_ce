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
use oxSystemComponentException;

/**
 * Admin shop license setting manager.
 * Collects shop license settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> License.
 */
class ShopLicense extends \Shop_Config
{
    /**
     * Current class template.
     *
     * @var string
     */
    protected $_sThisTemplate = "shop_license.tpl";

    /** @var string Current shop version links for edition. */
    private $versionCheckLink = 'http://admin.oxid-esales.com/CE/onlinecheck.php';

    /**
     * Executes parent method parent::render(), creates oxshop object, passes it's
     * data to Smarty engine and returns name of template file "shop_license.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        if ($myConfig->isDemoShop()) {
            /** @var oxSystemComponentException $oSystemComponentException */
            $oSystemComponentException = oxNew("oxSystemComponentException", "license");
            throw $oSystemComponentException;
        }

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ($soxId != "-1") {
            // load object
            $oShop = oxNew("oxshop");
            $oShop->load($soxId);
            $this->_aViewData["edit"] = $oShop;
        }

        $this->_aViewData["version"] = $myConfig->getVersion();

        $this->_aViewData['aCurVersionInfo'] = $this->_fetchCurVersionInfo($this->versionCheckLink);

        if (!$this->_canUpdate()) {
            $this->_aViewData['readonly'] = true;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Checks if the license key update is allowed.
     *
     * @return bool
     */
    protected function _canUpdate()
    {
        $myConfig = $this->getConfig();

        $blIsMallAdmin = oxRegistry::getSession()->getVariable('malladmin');
        if (!$blIsMallAdmin) {
            return false;
        }

        if ($myConfig->isDemoShop()) {
            return false;
        }

        return true;
    }

    /**
     * Fetch current shop version information from url
     *
     * @param string $sUrl current version info fetching url by edition
     *
     * @return string
     */
    protected function _fetchCurVersionInfo($sUrl)
    {
        $aParams = array("myversion" => $this->getConfig()->getVersion());
        $oLang = oxRegistry::getLang();
        $iLang = $oLang->getTplLanguage();
        $sLang = $oLang->getLanguageAbbr($iLang);

        $oCurl = oxNew('oxCurl');
        $oCurl->setMethod("POST");
        $oCurl->setUrl($sUrl . "/" . $sLang);
        $oCurl->setParameters($aParams);
        $sOutput = $oCurl->execute();

        $sOutput = strip_tags($sOutput, "<br>, <b>");
        $aResult = explode("<br>", $sOutput);
        if (strstr($aResult[5], "update")) {
            $sUpdateLink = oxRegistry::getLang()->translateString("VERSION_UPDATE_LINK");
            $aResult[5] = "<a id='linkToUpdate' href='$sUpdateLink' target='_blank'>" . $aResult[5] . "</a>";
        }

        return implode("<br>", $aResult);
    }
}
