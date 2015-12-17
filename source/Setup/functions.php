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

if (!function_exists('isAdmin')) {
    /**
     * Returns false, marking non admin state
     *
     * @return bool
     */
    function isAdmin()
    {
        return false;
    }
}

if (!function_exists('getShopBasePath')) {
    /**
     * Returns class responsible for system requirements check
     *
     * @return string
     */
    function getShopBasePath()
    {
        return dirname(__FILE__) . '/../';
    }
}

if (!function_exists('getInstallPath')) {
    /**
     * Returns shop installation directory
     *
     * @return string
     */
    function getInstallPath()
    {
        if (defined('OXID_PHP_UNIT')) {
            return getShopBasePath();
        } else {
            return "../";
        }
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
        $basePath = defined('OXID_PHP_UNIT') ? getShopBasePath() : getInstallPath();

        $editionSelector = new EditionSelector();
        if ($editionSelector->getEdition() === 'EE') {
            $systemRequirements = new \OxidEsales\EshopEnterprise\Core\SystemRequirements;
        } elseif ($editionSelector->getEdition() === 'PE') {
            $systemRequirements = new OxidEsales\EshopProfessional\Core\SystemRequirements;
        } else {
            $systemRequirements = new OxidEsales\Eshop\Core\SystemRequirements;
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
        if (defined('OXID_PHP_UNIT')) {
            include getShopBasePath() . "Application/Controller/Admin/shop_countries.php";
        } else {
            include getInstallPath() . "Application/Controller/Admin/shop_countries.php";
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
        if (defined('OXID_PHP_UNIT')) {
            include getShopBasePath() . "Application/Controller/Admin/shop_countries.php";
        } else {
            include getInstallPath() . "Application/Controller/Admin/shop_countries.php";
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
        if (defined('OXID_PHP_UNIT')) {
            include getShopBasePath() . "Application/Controller/Admin/shop_countries.php";
        } else {
            include getInstallPath() . "Application/Controller/Admin/shop_countries.php";
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


if (!class_exists("Config")) {
    /**
     * Config file loader class
     */
    class Config
    {

        /**
         * Class constructor, loads config file data
         *
         * @return null
         */
        public function __construct()
        {
            include getInstallPath() . "config.inc.php";
        }
    }
}

if (!class_exists("Conf")) {
    /**
     * Config key loader class
     */
    class Conf
    {

        /**
         * Class constructor, loads config key
         *
         * @return null
         */
        public function __construct()
        {
            if (defined('OXID_PHP_UNIT')) {
                include getShopBasePath() . "Core/oxconfk.php";
            } else {
                include getInstallPath() . "Core/oxconfk.php";
            }
        }
    }
}
