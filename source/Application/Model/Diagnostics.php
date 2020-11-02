<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Diagnostic tool model
 * Stores configuration and public diagnostic methods for shop diagnostics.
 */
class Diagnostics
{
    /**
     * Edition of THIS OXID eShop.
     *
     * @var string
     */
    protected $_sEdition = '';

    /**
     * Version of THIS OXID eShop.
     *
     * @var string
     */
    protected $_sVersion = '';

    /**
     * Revision of THIS OXID eShop.
     *
     * @var string
     */
    protected $_sShopLink = '';

    /**
     * Version setter.
     *
     * @param string $sVersion version
     */
    public function setVersion($sVersion): void
    {
        if (!empty($sVersion)) {
            $this->_sVersion = $sVersion;
        }
    }

    /**
     * Version getter.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_sVersion;
    }

    /**
     * Edition setter.
     *
     * @param string $sEdition Edition
     */
    public function setEdition($sEdition): void
    {
        if (!empty($sEdition)) {
            $this->_sEdition = $sEdition;
        }
    }

    /**
     * Edition getter.
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->_sEdition;
    }

    /**
     * ShopLink setter.
     *
     * @param string $sShopLink shop link
     */
    public function setShopLink($sShopLink): void
    {
        if (!empty($sShopLink)) {
            $this->_sShopLink = $sShopLink;
        }
    }

    /**
     * ShopLink getter.
     *
     * @return string
     */
    public function getShopLink()
    {
        return $this->_sShopLink;
    }

    /**
     * Collects information on the shop, like amount of categories, articles, users.
     *
     * @return array
     */
    public function getShopDetails()
    {
        return [
            'Date' => date(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('fullDateFormat'), time()),
            'URL' => $this->getShopLink(),
            'Edition' => $this->getEdition(),
            'Version' => $this->getVersion(),
            'Subshops (Total)' => $this->_countRows('oxshops', true),
            'Subshops (Active)' => $this->_countRows('oxshops', false),
            'Categories (Total)' => $this->_countRows('oxcategories', true),
            'Categories (Active)' => $this->_countRows('oxcategories', false),
            'Articles (Total)' => $this->_countRows('oxarticles', true),
            'Articles (Active)' => $this->_countRows('oxarticles', false),
            'Users (Total)' => $this->_countRows('oxuser', true),
        ];
    }

    /**
     * counts result Rows.
     *
     * @param string $sTable table
     * @param bool   $blMode mode
     *
     * @return int
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "countRows" in next major
     */
    protected function _countRows($sTable, $blMode) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sRequest = 'SELECT COUNT(*) FROM ' . $sTable;

        if (false === $blMode) {
            $sRequest .= ' WHERE oxactive = 1';
        }

        return $oDb->select($sRequest)->fields[0];
    }

    /**
     * Picks some pre-selected PHP configuration settings and returns them.
     *
     * @return array
     */
    public function getPhpSelection()
    {
        $aPhpIniParams = [
            'allow_url_fopen',
            'display_errors',
            'file_uploads',
            'max_execution_time',
            'memory_limit',
            'post_max_size',
            'register_globals',
            'upload_max_filesize',
        ];

        $aPhpIniConf = [];

        foreach ($aPhpIniParams as $sParam) {
            $sValue = ini_get($sParam);
            $aPhpIniConf[$sParam] = $sValue;
        }

        return $aPhpIniConf;
    }

    /**
     * Returns the installed PHP devoder (like Zend Optimizer, Guard Loader).
     *
     * @return string
     */
    public function getPhpDecoder()
    {
        $sReturn = 'Zend ';

        if (\function_exists('zend_optimizer_version')) {
            $sReturn .= 'Optimizer';
        }

        if (\function_exists('zend_loader_enabled')) {
            $sReturn .= 'Guard Loader';
        }

        return $sReturn;
    }

    /**
     * General server information
     * We will use the exec command here several times. In order tro prevent stop on failure, use $this->isExecAllowed().
     *
     * @return array
     */
    public function getServerInfo()
    {
        // init empty variables (can be filled if exec is allowed)
        $iMemTotal = $iMemFree = $sCpuModelName = $sCpuModel = $sCpuFreq = $iCpuCores = null;

        // fill, if exec is allowed
        if ($this->isExecAllowed()) {
            $iCpuAmnt = $this->_getCpuAmount();
            $iCpuMhz = $this->_getCpuMhz();
            $iBogo = $this->_getBogoMips();
            $iMemTotal = $this->_getMemoryTotal();
            $iMemFree = $this->_getMemoryFree();
            $sCpuModelName = $this->_getCpuModel();
            $sCpuModel = $iCpuAmnt . 'x ' . $sCpuModelName;
            $sCpuFreq = $iCpuMhz . ' MHz';

            // prevent "division by zero" error
            if ($iBogo && $iCpuMhz) {
                $iCpuCores = $iBogo / $iCpuMhz;
            }
        }

        return [
            'Server OS' => @php_uname('s'),
            'VM' => $this->_getVirtualizationSystem(),
            'PHP' => $this->_getPhpVersion(),
            'MySQL' => $this->_getMySqlServerInfo(),
            'Apache' => $this->_getApacheVersion(),
            'Disk total' => $this->_getDiskTotalSpace(),
            'Disk free' => $this->_getDiskFreeSpace(),
            'Memory total' => $iMemTotal,
            'Memory free' => $iMemFree,
            'CPU Model' => $sCpuModel,
            'CPU frequency' => $sCpuFreq,
            'CPU cores' => round($iCpuCores, 0),
        ];
    }

    /**
     * Returns Apache version.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getApacheVersion" in next major
     */
    protected function _getApacheVersion() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if (\function_exists('apache_get_version')) {
            $sReturn = apache_get_version();
        } else {
            $sReturn = $_SERVER['SERVER_SOFTWARE'];
        }

        return $sReturn;
    }

    /**
     * Tries to find out which VM is used.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getVirtualizationSystem" in next major
     */
    protected function _getVirtualizationSystem() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sSystemType = '';

        if ($this->isExecAllowed()) {
            //VMWare
            @$sDeviceList = $this->_getDeviceList('vmware');
            if ($sDeviceList) {
                $sSystemType = 'VMWare';
                unset($sDeviceList);
            }

            //VirtualBox
            @$sDeviceList = $this->_getDeviceList('VirtualBox');
            if ($sDeviceList) {
                $sSystemType = 'VirtualBox';
                unset($sDeviceList);
            }
        }

        return $sSystemType;
    }

    /**
     * Determines, whether the exec() command is allowed or not.
     *
     * @return bool
     */
    public function isExecAllowed()
    {
        return \function_exists('exec');
    }

    /**
     * Finds the list of system devices for given system type.
     *
     * @param string $sSystemType system type
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getDeviceList" in next major
     */
    protected function _getDeviceList($sSystemType) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return exec('lspci | grep -i ' . $sSystemType);
    }

    /**
     * Returns amount of CPU units.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCpuAmount" in next major
     */
    protected function _getCpuAmount() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        // cat /proc/cpuinfo | grep "processor" | sort -u | cut -d: -f2');
        return exec('cat /proc/cpuinfo | grep "physical id" | sort | uniq | wc -l');
    }

    /**
     * Returns CPU speed in Mhz.
     *
     * @return float
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCpuMhz" in next major
     */
    protected function _getCpuMhz() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return round(exec('cat /proc/cpuinfo | grep "MHz" | sort -u | cut -d: -f2'), 0);
    }

    /**
     * Returns BogoMIPS evaluation of processor.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getBogoMips" in next major
     */
    protected function _getBogoMips() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return exec('cat /proc/cpuinfo | grep "bogomips" | sort -u | cut -d: -f2');
    }

    /**
     * Returns total amount of memory.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getMemoryTotal" in next major
     */
    protected function _getMemoryTotal() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return exec('cat /proc/meminfo | grep "MemTotal" | sort -u | cut -d: -f2');
    }

    /**
     * Returns amount of free memory.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getMemoryFree" in next major
     */
    protected function _getMemoryFree() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return exec('cat /proc/meminfo | grep "MemFree" | sort -u | cut -d: -f2');
    }

    /**
     * Returns CPU model information.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCpuModel" in next major
     */
    protected function _getCpuModel() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return exec('cat /proc/cpuinfo | grep "model name" | sort -u | cut -d: -f2');
    }

    /**
     * Returns total disk space.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getDiskTotalSpace" in next major
     */
    protected function _getDiskTotalSpace() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return round(disk_total_space('/') / 1024 / 1024, 0) . ' GiB';
    }

    /**
     * Returns free disk space.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getDiskFreeSpace" in next major
     */
    protected function _getDiskFreeSpace() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return round(disk_free_space('/') / 1024 / 1024, 0) . ' GiB';
    }

    /**
     * Returns PHP version.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getPhpVersion" in next major
     */
    protected function _getPhpVersion() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return PHP_VERSION;
    }

    /**
     * Returns MySQL server Information.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getMySqlServerInfo" in next major
     */
    protected function _getMySqlServerInfo() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aResult = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getRow("SHOW VARIABLES LIKE 'version'");

        return $aResult['Value'];
    }
}
