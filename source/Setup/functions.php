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

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\EshopProfessional\Core\Serial;

if (!function_exists('getInstallPath')) {
    /**
     * Returns shop installation directory
     *
     * @return string
     */
    function getInstallPath()
    {
        return "../";
    }
}

if (!function_exists('getSystemReqCheck')) {
    /**
     * Returns class responsible for system requirements check
     *
     * @return oxSysRequirements
     */
    function getSystemReqCheck()
    {
        $editionSelector = new EditionSelector();
        if ($editionSelector->isEnterprise()) {
            $systemRequirements = new \OxidEsales\EshopEnterprise\Core\SystemRequirements;
        } elseif ($editionSelector->isProfessional()) {
            $systemRequirements = new OxidEsales\EshopProfessional\Core\SystemRequirements;
        } else {
            $systemRequirements = new OxidEsales\EshopCommunity\Core\SystemRequirements;
        }

        return $systemRequirements;
    }
}

if (!function_exists('getCountryList')) {
    /**
     * Includes country list for setup
     *
     * @return null
     */
    function getCountryList()
    {
        $aCountries = array();
        $relativePath = 'Application/Controller/Admin/ShopCountries.php';
        if (file_exists(getVendorDirectory() . "/oxid-esales/oxideshop-ce/source/$relativePath")) {
            include getVendorDirectory() . "/oxid-esales/oxideshop-ce/source/$relativePath";
        } else {
            include __DIR__ . "/../$relativePath";
        }

        return $aCountries;
    }
}

if (!function_exists('getLocation')) {
    /**
     * Includes country list for setup
     *
     * @return null
     */
    function getLocation()
    {
        $aLocationCountries = array();
        $relativePath = 'Application/Controller/Admin/ShopCountries.php';
        if (file_exists(getVendorDirectory() . "/oxid-esales/oxideshop-ce/source/$relativePath")) {
            include getVendorDirectory() . "/oxid-esales/oxideshop-ce/source/$relativePath";
        } else {
            include __DIR__ . "/../$relativePath";
        }
        return $aLocationCountries;
    }
}

if (!function_exists('getLanguages')) {
    /**
     * Includes country list for setup
     *
     * @return null
     */
    function getLanguages()
    {
        $aLanguages = array();
        $relativePath = 'Application/Controller/Admin/ShopCountries.php';
        if (file_exists(getVendorDirectory() . "/oxid-esales/oxideshop-ce/source/$relativePath")) {
            include getVendorDirectory() . "/oxid-esales/oxideshop-ce/source/$relativePath";
        } else {
            include __DIR__ . "/../$relativePath";
        }

        return $aLanguages;
    }
}

if (!function_exists('getDefaultFileMode')) {
    /**
     * Returns mode which must be set for files or folders
     *
     * @return int
     */
    function getDefaultFileMode()
    {
        return 0755;
    }
}

if (!function_exists('getDefaultConfigFileMode')) {
    /**
     * Returns mode which must be set for config file
     *
     * @return int
     */
    function getDefaultConfigFileMode()
    {
        return 0444;
    }
}

if (!function_exists('getSerial') && class_exists(Serial::class)) {
    /**
     * Creates and returns oxSerial object
     *
     * @return Serial
     */
    function getSerial()
    {
        return new Serial();
    }
}

if (!function_exists('getVendorDirectory')) {
    /**
     * Returns vendors directory
     *
     * @return string
     */
    function getVendorDirectory()
    {
        $oConfigFile = new OxidEsales\EshopCommunity\Core\ConfigFile(OX_BASE_PATH . "config.inc.php");
        return $oConfigFile->vendorDirectory;
    }
}

if (!class_exists("Conf", false)) {
    /**
     * Config key loader class
     */
    class Conf
    {
        /**
         * Conf constructor.
         */
        public function __construct()
        {
            $config = new \OxidEsales\EshopCommunity\Core\ConfigFile(getShopBasePath() . "/config.inc.php");
            $this->sConfigKey = $config->getVar('sConfigKey') ?: \OxidEsales\EshopCommunity\Core\Config::DEFAULT_CONFIG_KEY;
        }
    }
}
