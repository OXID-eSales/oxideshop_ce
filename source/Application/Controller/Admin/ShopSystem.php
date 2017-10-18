<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Admin shop system setting manager.
 * Collects shop system settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> System.
 */
class ShopSystem extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'shop_system.tpl';

    /**
     * Executes parent method parent::render(), passes shop configuration parameters
     * to Smarty and returns name of template file "shop_system.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        parent::render();

        $aConfArrs = [];

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        $aLanguages = $oLang->getLanguageArray();
        $sLangAbbr = $aLanguages[$oLang->getObjectTplLanguage()]->abbr;

        // loading shop location countries list (defines in which country shop exists)
        include "ShopCountries.php";

        $soxId = $this->getEditObjectId();
        if (!$soxId) {
            $soxId = $myConfig->getShopId();
        }

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $sShopCountry = $masterDb->getOne("select DECODE( oxvarvalue, " . $masterDb->quote($myConfig->getConfigParam('sConfigKey')) . ") as oxvarvalue from oxconfig where oxshopid = '$soxId' and oxvarname = 'sShopCountry'");

        $this->_aViewData["shop_countries"] = $aLocationCountries[$sLangAbbr];
        $this->_aViewData["confstrs"]["sShopCountry"] = $sShopCountry;

        return $this->_sThisTemplate;
    }
}
