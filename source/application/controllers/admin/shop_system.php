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
 * Admin shop system setting manager.
 * Collects shop system settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> System.
 * @package admin
 */
class Shop_System extends Shop_Config
{
    /**
     * Current class template name.
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
        $myConfig  = $this->getConfig();
        parent::render();

        $aConfArrs = array();

        $oLang = oxRegistry::getLang();

        $aLanguages = $oLang->getLanguageArray();
        $sLangAbbr  = $aLanguages[$oLang->getObjectTplLanguage()]->abbr;

        // loading shop location countries list (defines in which country shop exists)
        include "shop_countries.php";

        $soxId = $this->getEditObjectId();
        if ( !$soxId)
            $soxId = $myConfig->getShopId();

        $oDb = oxDb::getDb();
        $sShopCountry = $oDb->getOne("select DECODE( oxvarvalue, ".$oDb->quote( $myConfig->getConfigParam( 'sConfigKey' ) ).") as oxvarvalue from oxconfig where oxshopid = '$soxId' and oxvarname = 'sShopCountry'", false, false);

        $this->_aViewData["shop_countries"] = $aLocationCountries[$sLangAbbr];
        $this->_aViewData["confstrs"]["sShopCountry"] = $sShopCountry;

        return $this->_sThisTemplate;
    }
}
