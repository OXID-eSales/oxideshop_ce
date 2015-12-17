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

use OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
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

        include_once $basePath . '/Core/Edition/EditionSelector.php';

        $editionSelector = new EditionSelector();
        include_once $basePath . '/Core/SystemRequirements.php';
        if ($editionSelector->getEdition() === 'EE') {
            include_once $basePath . '/Edition/Professional/Core/SystemRequirements.php';
            include_once $basePath . '/Edition/Enterprise/Core/SystemRequirements.php';
            $systemRequirements = new \OxidEsales\EshopEnterprise\Core\SystemRequirements;
        } elseif ($editionSelector->getEdition() === 'PE') {
            include_once $basePath . '/Edition/Professional/Core/SystemRequirements.php';
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

require_once getShopBasePath() . '/Core/Edition/EditionSelector.php';
require_once getShopBasePath() . '/Core/Edition/EditionRootPathProvider.php';
require_once getShopBasePath() . '/Core/Edition/EditionPathProvider.php';


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

/**
 * Setup manager class
 */
class Setup extends Core
{

    /**
     * Current setup step title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Installation process status message
     *
     * @var string
     */
    protected $_sMessage = null;

    /**
     * Current setup step index
     *
     * @var int
     */
    protected $_iCurrStep = null;

    /**
     * Setup steps index array
     *
     * @var array
     */
    protected $_aSetupSteps = array(
        'STEP_SYSTEMREQ'   => 100, // 0
        'STEP_WELCOME'     => 200, // 1
        'STEP_LICENSE'     => 300, // 2
        'STEP_DB_INFO'     => 400, // 3
        'STEP_DB_CONNECT'  => 410, // 31
        'STEP_DB_CREATE'   => 420, // 32
        'STEP_DIRS_INFO'   => 500, // 4
        'STEP_DIRS_WRITE'  => 510, // 41
        'STEP_FINISH'      => 700, // 6
    );


    /**
     * Returns current setup step title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_sTitle;
    }

    /**
     * Current setup step title setter
     *
     * @param string $sTitle title
     */
    public function setTitle($sTitle)
    {
        $this->_sTitle = $sTitle;
    }

    /**
     * Returns installation process status message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_sMessage;
    }

    /**
     * Sets installation process status message
     *
     * @param string $sMsg status message
     */
    public function setMessage($sMsg)
    {
        $this->_sMessage = $sMsg;
    }

    /**
     * Returns current setup step index
     *
     * @return int
     */
    public function getCurrentStep()
    {
        if ($this->_iCurrStep === null) {
            if (($this->_iCurrStep = $this->getInstance("oxSetupUtils")->getRequestVar("istep")) === null) {
                $this->_iCurrStep = $this->getStep('STEP_SYSTEMREQ');
            }
            $this->_iCurrStep = (int) $this->_iCurrStep;
        }

        return $this->_iCurrStep;
    }

    /**
     * Returns next setup step ident
     *
     * @return int
     */
    public function getNextStep()
    {
        return $this->_iNextStep;
    }

    /**
     * Current setup step setter
     *
     * @param int $iStep current setup step index
     */
    public function setNextStep($iStep)
    {
        $this->_iNextStep = $iStep;
    }


    /**
     * Checks if config file is alleady filled with data
     *
     * @return bool
     */
    public function alreadySetUp()
    {
        $blSetUp = false;
        $sConfig = join("", file(getInstallPath() . "config.inc.php"));
        if (strpos($sConfig, "<dbHost>") === false) {
            $blSetUp = true;
        }

        return $blSetUp;
    }

    /**
     * Returns default shop id
     *
     * @return mixed
     */
    public function getShopId()
    {
        $sBaseShopId = 'oxbaseshop';


        return $sBaseShopId;
    }

    /**
     * Returns setup steps index array
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_aSetupSteps;
    }

    /**
     * Returns setup step index
     *
     * @param string $sStepId setup step identifier
     *
     * @return int
     */
    public function getStep($sStepId)
    {
        return isset($this->_aSetupSteps[$sStepId]) ? $this->_aSetupSteps[$sStepId] : null;
    }

    /**
     * Returns version prefix
     *
     * @return string
     */
    public function getVersionPrefix()
    {
        $sVerPrefix = '';

        return $sVerPrefix;
    }

    /**
     * $iModuleState - module status:
     * -1 - unable to datect, should not block
     *  0 - missing, blocks setup
     *  1 - fits min requirements
     *  2 - exists required or better
     *
     * @param int $iModuleState module state
     *
     * @return string
     */
    public function getModuleClass($iModuleState)
    {
        switch ($iModuleState) {
            case 2:
                $sClass = 'pass';
                break;
            case 1:
                $sClass = 'pmin';
                break;
            case -1:
                $sClass = 'null';
                break;
            default:
                $sClass = 'fail';
                break;
        }
        return $sClass;
    }
}
