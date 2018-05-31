<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * Diagnostic tool model
 * Stores configuration and public diagnostic methods for shop diagnostics
 *
 */
class Diagnostics
{
    /**
     * Edition of THIS OXID eShop
     *
     * @var string
     */
    protected $_sEdition = "";

    /**
     * Version of THIS OXID eShop
     *
     * @var string
     */
    protected $_sVersion = "";

    /**
     * Revision of THIS OXID eShop
     *
     * @var string
     */
    protected $_sShopLink = "";

    /**
     * Array of all files and folders in shop root folder which are to be checked
     *
     * @var array
     */
    protected $_aFileCheckerPathList = [
        'bootstrap.php',
        'index.php',
        'oxid.php',
        'oxseo.php',
        'admin/',
        'Application/',
        'bin/',
        'Core/',
        'modules/',
    ];

    /**
     * Array of file extensions which are to be checked
     *
     * @var array
     */
    protected $_aFileCheckerExtensionList = ['php', 'tpl'];

    /**
     * Setter for list of files and folders to check
     *
     * @param array $aPathList Path list.
     */
    public function setFileCheckerPathList($aPathList)
    {
        $this->_aFileCheckerPathList = $aPathList;
    }

    /**
     * getter for list of files and folders to check
     *
     * @return $this->_aFileCheckerPathList array
     */
    public function getFileCheckerPathList()
    {
        return $this->_aFileCheckerPathList;
    }

    /**
     * Setter for extensions of files to check
     *
     * @param array $aExtList List of extensions.
     */
    public function setFileCheckerExtensionList($aExtList)
    {
        $this->_aFileCheckerExtensionList = $aExtList;
    }

    /**
     * getter for extensions of files to check
     *
     * @return $this->_aFileCheckerExtensionList array
     */
    public function getFileCheckerExtensionList()
    {
        return $this->_aFileCheckerExtensionList;
    }


    /**
     * Version setter
     *
     * @param string $sVersion Version.
     */
    public function setVersion($sVersion)
    {
        if (!empty($sVersion)) {
            $this->_sVersion = $sVersion;
        }
    }

    /**
     * Version getter
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_sVersion;
    }

    /**
     * Edition setter
     *
     * @param string $sEdition Edition
     */
    public function setEdition($sEdition)
    {
        if (!empty($sEdition)) {
            $this->_sEdition = $sEdition;
        }
    }

    /**
     * Edition getter
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->_sEdition;
    }

    /**
     * ShopLink setter
     *
     * @param string $sShopLink Shop link.
     */
    public function setShopLink($sShopLink)
    {
        if (!empty($sShopLink)) {
            $this->_sShopLink = $sShopLink;
        }
    }

    /**
     * ShopLink getter
     *
     * @return string
     */
    public function getShopLink()
    {
        return $this->_sShopLink;
    }

    /**
     * Collects information on the shop, like amount of categories, articles, users
     *
     * @return array
     */
    public function getShopDetails()
    {
        $aShopDetails = [
            'Date'                => date(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('fullDateFormat'), time()),
            'URL'                 => $this->getShopLink(),
            'Edition'             => $this->getEdition(),
            'Version'             => $this->getVersion(),
            'Subshops (Total)'    => $this->_countRows('oxshops', true),
            'Subshops (Active)'   => $this->_countRows('oxshops', false),
            'Categories (Total)'  => $this->_countRows('oxcategories', true),
            'Categories (Active)' => $this->_countRows('oxcategories', false),
            'Articles (Total)'    => $this->_countRows('oxarticles', true),
            'Articles (Active)'   => $this->_countRows('oxarticles', false),
            'Users (Total)'       => $this->_countRows('oxuser', true),
        ];

        return $aShopDetails;
    }

    /**
     * counts result Rows
     *
     * @param string  $sTable table
     * @param boolean $blMode mode
     *
     * @return integer
     */
    protected function _countRows($sTable, $blMode)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sRequest = 'SELECT COUNT(*) FROM ' . $sTable;

        if ($blMode == false) {
            $sRequest .= ' WHERE oxactive = 1';
        }

         $aRes = $oDb->select($sRequest)->fields[0];

        return $aRes;
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
     * Returns the installed PHP devoder (like Zend Optimizer, Guard Loader)
     *
     * @return string
     */
    public function getPhpDecoder()
    {
        $sReturn = 'Zend ';

        if (function_exists('zend_optimizer_version')) {
            $sReturn .= 'Optimizer';
        }

        if (function_exists('zend_loader_enabled')) {
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
        $iCpuAmnt = $iCpuMhz = $iBogo = $iMemTotal = $iMemFree = $sCpuModelName = $sCpuModel = $sCpuFreq = $iCpuCores = null;

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

        $aServerInfo = [
            'Server OS'     => @php_uname('s'),
            'VM'            => $this->_getVirtualizationSystem(),
            'PHP'           => $this->_getPhpVersion(),
            'MySQL'         => $this->_getMySqlServerInfo(),
            'Apache'        => $this->_getApacheVersion(),
            'Disk total'    => $this->_getDiskTotalSpace(),
            'Disk free'     => $this->_getDiskFreeSpace(),
            'Memory total'  => $iMemTotal,
            'Memory free'   => $iMemFree,
            'CPU Model'     => $sCpuModel,
            'CPU frequency' => $sCpuFreq,
            'CPU cores'     => round($iCpuCores, 0),
        ];

        return $aServerInfo;
    }

    /**
     * Returns Apache version
     *
     * @return string
     */
    protected function _getApacheVersion()
    {
        if (function_exists('apache_get_version')) {
            $sReturn = apache_get_version();
        } else {
            $sReturn = $_SERVER['SERVER_SOFTWARE'];
        }

        return $sReturn;
    }

    /**
     * Tries to find out which VM is used
     *
     * @return string
     */
    protected function _getVirtualizationSystem()
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
     * @return boolean
     */
    public function isExecAllowed()
    {
        return function_exists('exec');
    }

    /**
     * Finds the list of system devices for given system type
     *
     * @param string $sSystemType System type.
     *
     * @return string
     */
    protected function _getDeviceList($sSystemType)
    {
        return exec('lspci | grep -i ' . $sSystemType);
    }

    /**
     * Returns amount of CPU units.
     *
     * @return string
     */
    protected function _getCpuAmount()
    {
        // cat /proc/cpuinfo | grep "processor" | sort -u | cut -d: -f2');
        return exec('cat /proc/cpuinfo | grep "physical id" | sort | uniq | wc -l');
    }

    /**
     * Returns CPU speed in Mhz
     *
     * @return float
     */
    protected function _getCpuMhz()
    {
        return round(exec('cat /proc/cpuinfo | grep "MHz" | sort -u | cut -d: -f2'), 0);
    }

    /**
     * Returns BogoMIPS evaluation of processor
     *
     * @return string
     */
    protected function _getBogoMips()
    {
        return exec('cat /proc/cpuinfo | grep "bogomips" | sort -u | cut -d: -f2');
    }

    /**
     * Returns total amount of memory
     *
     * @return string
     */
    protected function _getMemoryTotal()
    {
        return exec('cat /proc/meminfo | grep "MemTotal" | sort -u | cut -d: -f2');
    }

    /**
     * Returns amount of free memory
     *
     * @return string
     */
    protected function _getMemoryFree()
    {
        return exec('cat /proc/meminfo | grep "MemFree" | sort -u | cut -d: -f2');
    }

    /**
     * Returns CPU model information
     *
     * @return string
     */
    protected function _getCpuModel()
    {
        return exec('cat /proc/cpuinfo | grep "model name" | sort -u | cut -d: -f2');
    }

    /**
     * Returns total disk space
     *
     * @return string
     */
    protected function _getDiskTotalSpace()
    {
        return round(disk_total_space('/') / 1024 / 1024, 0) . ' GiB';
    }

    /**
     * Returns free disk space
     *
     * @return string
     */
    protected function _getDiskFreeSpace()
    {
        return round(disk_free_space('/') / 1024 / 1024, 0) . ' GiB';
    }

    /**
     * Returns PHP version
     *
     * @return string
     */
    protected function _getPhpVersion()
    {
        return phpversion();
    }

    /**
     * Returns MySQL server Information
     *
     * @return string
     */
    protected function _getMySqlServerInfo()
    {
        $aResult = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getRow("SHOW VARIABLES LIKE 'version'");

        return $aResult['Value'];
    }
}
