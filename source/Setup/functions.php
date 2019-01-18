<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use \OxidEsales\Facts\Facts;
use \OxidEsales\EshopProfessional\Core\Serial;

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
        $facts = new Facts();
        if ($facts->isEnterprise()) {
            $systemRequirements = new \OxidEsales\EshopEnterprise\Core\SystemRequirements;
        } elseif ($facts->isProfessional()) {
            $systemRequirements = new \OxidEsales\EshopProfessional\Core\SystemRequirements;
        } else {
            $systemRequirements = new \OxidEsales\EshopCommunity\Core\SystemRequirements;
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
        $aCountries = [];
        $relativePath = 'Application/Controller/Admin/ShopCountries.php';
        if (file_exists(getVendorDirectory() . "oxid-esales/oxideshop-ce/source/$relativePath")) {
            include getVendorDirectory() . "oxid-esales/oxideshop-ce/source/$relativePath";
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
        $aLocationCountries = [];
        $relativePath = 'Application/Controller/Admin/ShopCountries.php';
        if (file_exists(getVendorDirectory() . "oxid-esales/oxideshop-ce/source/$relativePath")) {
            include getVendorDirectory() . "oxid-esales/oxideshop-ce/source/$relativePath";
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
        $aLanguages = [];
        $relativePath = 'Application/Controller/Admin/ShopCountries.php';
        if (file_exists(getVendorDirectory() . "oxid-esales/oxideshop-ce/source/$relativePath")) {
            include getVendorDirectory() . "oxid-esales/oxideshop-ce/source/$relativePath";
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
        return VENDOR_PATH;
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
