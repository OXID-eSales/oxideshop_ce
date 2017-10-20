<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxSystemComponentException;

/**
 * Admin shop license setting manager.
 * Collects shop license settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> License.
 */
class ShopLicense extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
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
            /** @var \OxidEsales\Eshop\Core\Exception\SystemComponentException $oSystemComponentException */
            $oSystemComponentException = oxNew(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class, "license");
            throw $oSystemComponentException;
        }

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ($soxId != "-1") {
            // load object
            $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
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

        $blIsMallAdmin = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('malladmin');
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
        $aParams = ["myversion" => $this->getConfig()->getVersion()];
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iLang = $oLang->getTplLanguage();
        $sLang = $oLang->getLanguageAbbr($iLang);

        $oCurl = oxNew(\OxidEsales\Eshop\Core\Curl::class);
        $oCurl->setMethod("POST");
        $oCurl->setUrl($sUrl . "/" . $sLang);
        $oCurl->setParameters($aParams);
        $sOutput = $oCurl->execute();

        $sOutput = strip_tags($sOutput, "<br>, <b>");
        $aResult = explode("<br>", $sOutput);
        if (strstr($aResult[5], "update")) {
            $sUpdateLink = \OxidEsales\Eshop\Core\Registry::getLang()->translateString("VERSION_UPDATE_LINK");
            $aResult[5] = "<a id='linkToUpdate' href='$sUpdateLink' target='_blank'>" . $aResult[5] . "</a>";
        }

        return implode("<br>", $aResult);
    }
}
