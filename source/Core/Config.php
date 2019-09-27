<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;
use OxidEsales\Eshop\Application\Controller\OxidStartController;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\Module\ModuleTemplatePathCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use stdClass;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Event\ShopConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent;

//max integer
define('MAX_64BIT_INTEGER', '18446744073709551615');

/**
 * Main shop configuration class.
 *
 * @mixin \OxidEsales\EshopEnterprise\Core\Config
 * @mixin \OxidEsales\EshopProfessional\Core\Config
 */
class Config extends \OxidEsales\Eshop\Core\Base
{
    const DEFAULT_CONFIG_KEY = 'fq45QS09_fqyx09239QQ';

    // this column of params are defined in config.inc.php file,
    // so for backwards compatibility. names starts without underscore

    /**
     * Database host name
     *
     * @var string
     */
    protected $dbHost = null;

    /**
     * Database name
     *
     * @var string
     */
    protected $dbName = null;

    /**
     * Database user name
     *
     * @var string
     */
    protected $dbUser = null;

    /**
     * Database user password
     *
     * @var string
     */
    protected $dbPwd = null;

    /**
     * Database driver type
     *
     * @var string
     */
    protected $dbType = null;

    /**
     * Shop Url
     *
     * @var string
     */
    protected $sShopURL = null;

    /**
     * Shop SSL mode Url
     *
     * @var string
     */
    protected $sSSLShopURL = null;

    /**
     * Shops admin SSL mode Url
     *
     * @var string
     */
    protected $sAdminSSLURL = null;

    /**
     * Shops install directory
     *
     * @var string
     */
    protected $sShopDir = null;

    /**
     * Shops compile directory
     *
     * @var string
     */
    protected $sCompileDir = null;

    /**
     * Debug mode (default is set depending on if it is productive mode or not):
     *  -1 = Logger Messages internal use only
     *   0 = off
     *   1 = smarty
     *   2 = SQL
     *   3 = SQL + smarty
     *   4 = SQL + smarty + shop template data
     *   5 = Delivery Cost calculation info
     *   6 = SMTP Debug Messages
     *   7 = Slow SQL query indication
     *
     * @var int
     */
    protected $iDebug = null;

    /**
     * Administrator email address, used to send critical notices
     *
     * @var string
     */
    protected $sAdminEmail = null;

    /**
     * Use cookies
     *
     * @var bool
     */
    protected $blSessionUseCookies = null;

    /**
     * Default image loading location.
     * If $blNativeImages is set to true the shop loads images from current domain,
     * otherwise images are loaded from the domain specified in config.inc.php.
     * This is applicable for different domains depending on language or mall
     * if mall mode is available.
     *
     * @var bool
     */
    protected $blNativeImages = true;

    /**
     * Names of tables which are multi-shop
     *
     * @var array
     */
    protected $aMultiShopTables = ['oxarticles', 'oxdiscount', 'oxcategories', 'oxattribute',
                                        'oxlinks', 'oxvoucherseries', 'oxmanufacturers',
                                        // @deprecated since v.5.3.0 (2016-06-17); The Admin Menu: Customer Info -> News feature will be moved to a module in v6.0.0
                                        'oxnews',
                                        // END deprecated
                                        'oxselectlist', 'oxwrapping',
                                        'oxdeliveryset', 'oxdelivery', 'oxvendor', 'oxobject2category'];

    /**
     * Application starter instance
     *
     * @var OxidStartController
     */
    private $_oStart = null;

    /**
     * Active shop object.
     *
     * @var object
     */
    protected $_oActShop = null;

    /**
     * Active Views object array. Object has setters/getters for these properties:
     *   _sClass - name of current view class
     *   _sFnc   - name of current action function
     *
     * @var array
     */
    protected $_aActiveViews = [];

    /**
     * Array of global parameters.
     *
     * @var array
     */
    protected $_aGlobalParams = [];

    /**
     * Shop config parameters storage array
     *
     * @var array
     */
    protected $_aConfigParams = [];

    /**
     * Theme config parameters storage array
     *
     * @var array
     */
    protected $_aThemeConfigParams = [];

    /**
     * Current language Id
     *
     * @var int
     */
    protected $_iLanguageId = null;

    /**
     * Current shop Id
     *
     * @var int
     */
    protected $_iShopId = null;

    /**
     * Out dir name
     *
     * @var string
     */
    protected $_sOutDir = 'out';

    /**
     * Image dir name
     *
     * @var string
     */
    protected $_sImageDir = 'img';

    /**
     * Dyn Image dir name
     *
     * @var string
     */
    protected $_sPictureDir = 'pictures';

    /**
     * Master pictures dir name
     *
     * @var string
     */
    protected $_sMasterPictureDir = 'master';

    /**
     * Template dir name
     *
     * @var string
     */
    protected $_sTemplateDir = 'tpl';

    /**
     * Resource dir name
     *
     * @var string
     */
    protected $_sResourceDir = 'src';

    /**
     * Modules dir name
     *
     * @var string
     */
    protected $_sModulesDir = 'modules';

    /**
     * Whether shop is in SSL mode
     *
     * @var bool
     */
    protected $_blIsSsl = null;

    /**
     * Absolute image dirs for each shops
     *
     * @var array
     */
    protected $_aAbsDynImageDir = [];

    /**
     * Active currency object
     *
     * @var array
     */
    protected $_oActCurrencyObject = null;

    /**
     * Indicates if Config::init() method has been already run.
     * Is checked for loading config variables on demand.
     * Used in Config::getConfigParam() method
     *
     * @var bool
     */
    protected $_blInit = false;

    /** @var string Default configuration encryption key for database values. */
    protected $sConfigKey = self::DEFAULT_CONFIG_KEY;

    /**
     * prefix for oxModule field for themes in oxConfig and oxConfigDisplay tables
     *
     * @var string
     */
    const OXMODULE_THEME_PREFIX = 'theme:';

    /**
     * prefix for oxModule field for modules in oxConfig and oxConfigDisplay tables
     *
     * @var string
     */
    const OXMODULE_MODULE_PREFIX = 'module:';

    /**
     * Returns config parameter value if such parameter exists
     *
     * @param string $name    config parameter name
     * @param mixed  $default default value if no config var is found default null
     *
     * @return mixed
     */
    public function getConfigParam($name, $default = null)
    {
        $this->init();

        if (isset($this->_aConfigParams[$name])) {
            $value = $this->_aConfigParams[$name];
        } elseif (isset($this->$name)) {
            $value = $this->$name;
        } else {
            $value = $default;
        }

        return $value;
    }

    /**
     * Stores config parameter value in config
     *
     * @param string $name  config parameter name
     * @param string $value config parameter value
     */
    public function setConfigParam($name, $value)
    {
        if (isset($this->_aConfigParams[$name])) {
            $this->_aConfigParams[$name] = $value;
        } elseif (isset($this->$name)) {
            $this->$name = $value;
        } else {
            $this->_aConfigParams[$name] = $value;
        }
    }

    /**
     * Parse SEO url parameters.
     */
    protected function _processSeoCall()
    {
        // TODO: refactor shop bootstrap and parse url params as soon as possible
        if (isSearchEngineUrl()) {
            oxNew(\OxidEsales\Eshop\Core\SeoDecoder::class)->processSeoCall();
        }
    }

    /**
     * Initialize configuration variables
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseException
     * @param int $shopID
     */
    public function initVars($shopID)
    {
        $this->_loadVarsFromFile();

        $this->_setDefaults();

        $configLoaded = $this->_loadVarsFromDb($shopID);
        // loading shop config
        if (empty($shopID) || !$configLoaded) {
            // if no config values where loaded (some problems with DB), throwing an exception
            $exception = new \OxidEsales\Eshop\Core\Exception\DatabaseException(
                "Unable to load shop config values from database",
                0,
                new \Exception()
            );
            throw $exception;
        }

        // loading theme config options
        $this->_loadVarsFromDb($shopID, null, Config::OXMODULE_THEME_PREFIX . $this->getConfigParam('sTheme'));

        // checking if custom theme (which has defined parent theme) config options should be loaded over parent theme (#3362)
        if ($this->getConfigParam('sCustomTheme')) {
            $this->_loadVarsFromDb($shopID, null, Config::OXMODULE_THEME_PREFIX . $this->getConfigParam('sCustomTheme'));
        }

        // loading modules config
        $this->_loadVarsFromDb($shopID, null, Config::OXMODULE_MODULE_PREFIX);

        $this->loadAdditionalConfiguration();

        // Admin handling
        $this->setConfigParam('blAdmin', isAdmin());

        if (defined('OX_ADMIN_DIR')) {
            $this->setConfigParam('sAdminDir', OX_ADMIN_DIR);
        }

        $this->_loadVarsFromFile();
    }

    /**
     * Starts session manager
     *
     * @return null
     */
    public function init()
    {
        // Duplicated init protection
        if ($this->_blInit) {
            return;
        }
        $this->_blInit = true;

        try {
            // config params initialization
            $this->initVars($this->getShopId());

            // application initialization
            $this->initializeShop();
            $this->_oStart = oxNew(\OxidEsales\Eshop\Application\Controller\OxidStartController::class);
            $this->_oStart->appInit();
        } catch (\OxidEsales\Eshop\Core\Exception\DatabaseException $exception) {
            $this->_handleDbConnectionException($exception);
        } catch (\OxidEsales\Eshop\Core\Exception\CookieException $exception) {
            $this->_handleCookieException($exception);
        }
    }

    /**
     * Reloads all configuration.
     */
    public function reinitialize()
    {
        $this->_blInit = false;
        $this->init();
    }

    /**
     * Load any additional configuration on Config::init.
     */
    protected function loadAdditionalConfiguration()
    {
    }

    /**
     * Initializes main shop tasks - processing of SEO calls, starting of session.
     */
    protected function initializeShop()
    {
        $this->_processSeoCall();
        $this->getSession()->start();
    }

    /**
     * Loads vars from default config file
     */
    protected function _loadVarsFromFile()
    {
        //config variables from config.inc.php takes priority over the ones loaded from db
        include getShopBasePath() . '/config.inc.php';

        //adding trailing slashes
        $fileUtils = Registry::getUtilsFile();
        $this->sShopDir = $fileUtils->normalizeDir($this->sShopDir);
        $this->sCompileDir = $fileUtils->normalizeDir($this->sCompileDir);
        $this->sShopURL = $fileUtils->normalizeDir($this->sShopURL);
        $this->sSSLShopURL = $fileUtils->normalizeDir($this->sSSLShopURL);
        $this->sAdminSSLURL = $fileUtils->normalizeDir($this->sAdminSSLURL);

        $this->_loadCustomConfig();
    }

    /**
     * Set important defaults.
     */
    protected function _setDefaults()
    {
        $this->setConfigParam('sTheme', 'azure');

        if (is_null($this->getConfigParam('sDefaultLang'))) {
            $this->setConfigParam('sDefaultLang', 0);
        }

        if (is_null($this->getConfigParam('blLogChangesInAdmin'))) {
            $this->setConfigParam('blLogChangesInAdmin', false);
        }

        if (is_null($this->getConfigParam('blCheckTemplates'))) {
            $this->setConfigParam('blCheckTemplates', false);
        }

        if (is_null($this->getConfigParam('blAllowArticlesubclass'))) {
            $this->setConfigParam('blAllowArticlesubclass', false);
        }

        if (is_null($this->getConfigParam('iAdminListSize'))) {
            $this->setConfigParam('iAdminListSize', 9);
        }

        // #1173M  for EE - not all pic are deleted
        if (is_null($this->getConfigParam('iPicCount'))) {
            $this->setConfigParam('iPicCount', 12);
        }

        if (is_null($this->getConfigParam('iZoomPicCount'))) {
            $this->setConfigParam('iZoomPicCount', 4);
        }

        if (is_null($this->getConfigParam('iDebug'))) {
            $this->setConfigParam('iDebug', $this->isProductiveMode() ? 0 : -1);
        }

        $this->setConfigParam('sCoreDir', __DIR__ . DIRECTORY_SEPARATOR);
    }

    /**
     * Loads vars from custom config file
     */
    protected function _loadCustomConfig()
    {
        $custConfig = getShopBasePath() . '/cust_config.inc.php';
        if (is_readable($custConfig)) {
            include $custConfig;
        }
    }

    /**
     * Load config values from DB
     *
     * @param string $shopID   shop ID to load parameters
     * @param array  $onlyVars array of params to load (optional)
     * @param string $module   module vars to load, empty for base options
     *
     * @return bool
     */
    protected function _loadVarsFromDb($shopID, $onlyVars = null, $module = '')
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $params = [
          ':oxshopid' => $shopID
        ];
        $select = "select
                        oxvarname, oxvartype, " . $this->getDecodeValueQuery() . " as oxvarvalue
                    from oxconfig
                    where oxshopid = :oxshopid and ";

        if ($module) {
            $select .= " oxmodule LIKE :oxmodule";
            $params[':oxmodule'] = $module . "%";
        } else {
            $select .= "oxmodule = ''";
        }

        $select .= $this->_getConfigParamsSelectSnippet($onlyVars);

        $result = $db->getAll($select, $params);

        foreach ($result as $value) {
            $varName = $value[0];
            $varType = $value[1];
            $varVal = $value[2];

            $this->_setConfVarFromDb($varName, $varType, $varVal);

            //setting theme options array
            if ($module) {
                $this->_aThemeConfigParams[$varName] = $module;
            }
        }

        return (bool) count($result);
    }

    /**
     * Allow loading from some vars only from baseshop
     *
     * @param array $vars
     *
     * @return string
     */
    protected function _getConfigParamsSelectSnippet($vars)
    {
        $select = '';
        if (is_array($vars) && !empty($vars)) {
            foreach ($vars as &$field) {
                $field = '"' . $field . '"';
            }
            $select = ' and oxvarname in ( ' . implode(', ', $vars) . ' ) ';
        }

        return $select;
    }

    /**
     * Sets config variable to config object, first unserializing it by given type.
     * sShopURL and sSSLShopURL are skipped for admin or when URL values are not set
     *
     * @param string $varName variable name
     * @param string $varType variable type - arr, aarr, bool or str
     * @param string $varVal  serialized by type value
     *
     * @return null
     */
    protected function _setConfVarFromDb($varName, $varType, $varVal)
    {
        if (($varName == 'sShopURL' || $varName == 'sSSLShopURL') &&
            (!$varVal || $this->isAdmin() === true)
        ) {
            return;
        }

        switch ($varType) {
            case 'arr':
            case 'aarr':
                $this->setConfigParam($varName, unserialize($varVal));
                break;
            case 'bool':
                $this->setConfigParam($varName, ($varVal == 'true' || $varVal == '1'));
                break;
            default:
                $this->setConfigParam($varName, $varVal);
                break;
        }
    }

    /**
     * Unsets all session data.
     *
     * @return null
     */
    public function pageClose()
    {
        if ($this->hasActiveViewsChain()) {
            // do not commit session until active views chain exists
            return;
        }

        return $this->_oStart->pageClose();
    }

    /**
     * Returns value of parameter stored in POST,GET.
     * For security reasons performed Config->checkParamSpecialChars().
     * use $raw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $name Name of parameter.
     * @param bool   $raw  Get unescaped parameter.
     *
     * @deprecated on b-dev (2015-06-10); Use Request::getRequestEscapedParameter().
     *
     * @return mixed
     */
    public function getRequestParameter($name, $raw = false)
    {
        $request = Registry::get(\OxidEsales\Eshop\Core\Request::class);
        return $raw ? $request->getRequestParameter($name) : $request->getRequestEscapedParameter($name);
    }

    /**
     * Returns escaped value of parameter stored in POST,GET.
     *
     * @param string $name         Name of parameter.
     * @param string $defaultValue Default value if no value provided.
     *
     * @deprecated on 6.0.0 (2016-05-16); use OxidEsales\Eshop\Core\Request::getRequestEscapedParameter()
     *
     * @return mixed
     */
    public function getRequestEscapedParameter($name, $defaultValue = null)
    {
        return Registry::get(\OxidEsales\Eshop\Core\Request::class)->getRequestEscapedParameter($name, $defaultValue);
    }

    /**
     * Returns raw value of parameter stored in POST,GET.
     *
     * @param string $name         Name of parameter.
     * @param string $defaultValue Default value if no value provided.
     *
     * @deprecated on 6.0.0 (2016-05-16); use OxidEsales\Eshop\Core\Request::getRequestEscapedParameter()
     *
     * @return mixed
     */
    public function getRequestRawParameter($name, $defaultValue = null)
    {
        return Registry::get(\OxidEsales\Eshop\Core\Request::class)->getRequestParameter($name, $defaultValue);
    }

    /**
     * Get request 'cl' parameter which is the controller id.
     *
     * @return string|null
     */
    public function getRequestControllerId()
    {
        return $this->getRequestParameter('cl');
    }

    /**
     * Use this function to get the controller class hidden behind the request's 'cl' parameter.
     *
     * @return mixed
     */
    public function getRequestControllerClass()
    {
        $controllerId = $this->getRequestControllerId();
        $controllerClass = Registry::getControllerClassNameResolver()->getClassNameById($controllerId);

        return $controllerClass;
    }

    /**
     * Returns uploaded file parameter
     *
     * @param string $paramName param name
     *
     * @return null
     */
    public function getUploadedFile($paramName)
    {
        return $_FILES[$paramName];
    }

    /**
     * Sets global parameter value
     *
     * @param string $name  name of parameter
     * @param mixed  $value value to store
     */
    public function setGlobalParameter($name, $value)
    {
        $this->_aGlobalParams[$name] = $value;
    }

    /**
     * Returns global parameter value
     *
     * @param string $name name of cached parameter
     *
     * @return mixed
     */
    public function getGlobalParameter($name)
    {
        if (isset($this->_aGlobalParams[$name])) {
            return $this->_aGlobalParams[$name];
        } else {
            return null;
        }
    }

    /**
     * Checks if passed parameter has special chars and replaces them.
     * Returns checked value.
     *
     * @param mixed $value value to process escaping
     * @param array $raw   keys of unescaped values
     *
     * @return mixed
     */
    public function checkParamSpecialChars(&$value, $raw = null)
    {
        return Registry::get(\OxidEsales\Eshop\Core\Request::class)->checkParamSpecialChars($value, $raw);
    }

    /**
     * Active Shop id setter
     *
     * @param string $shopId shop id
     */
    public function setShopId($shopId)
    {
        $this->getSession()->setVariable('actshop', $shopId);
        $this->_iShopId = $shopId;
    }

    /**
     * Returns active shop ID.
     *
     * @return int
     */
    public function getShopId()
    {
        if (is_null($this->_iShopId)) {
            $shopId = $this->calculateActiveShopId();
            $this->setShopId($shopId);

            if (!$this->_isValidShopId($shopId)) {
                $shopId = $this->getBaseShopId();
            }
            $this->setShopId($shopId);
        }

        return $this->_iShopId;
    }

    /**
     * Set is shop url
     *
     * @param bool $isSsl - state bool value
     */
    public function setIsSsl($isSsl = false)
    {
        $this->_blIsSsl = $isSsl;
    }

    /**
     * Checks if WEB session is SSL.
     */
    protected function _checkSsl()
    {
        $myUtilsServer = Registry::getUtilsServer();
        $serverVars = $myUtilsServer->getServerVar();
        $httpsServerVar = $myUtilsServer->getServerVar('HTTPS');

        $this->setIsSsl();
        if (isset($httpsServerVar) && ($httpsServerVar === 'on' || $httpsServerVar === 'ON' || $httpsServerVar == '1')) {
            // "1&1" hoster provides "1"
            $this->setIsSsl($this->getConfigParam('sSSLShopURL') || $this->getConfigParam('sMallSSLShopURL'));
            if ($this->isAdmin() && !$this->_blIsSsl) {
                //#4026
                $this->setIsSsl(!is_null($this->getConfigParam('sAdminSSLURL')));
            }
        }

        //additional special handling for profihost customers
        if (isset($serverVars['HTTP_X_FORWARDED_SERVER']) &&
            (strpos($serverVars['HTTP_X_FORWARDED_SERVER'], 'ssl') !== false ||
             strpos($serverVars['HTTP_X_FORWARDED_SERVER'], 'secure-online-shopping.de') !== false)
        ) {
            $this->setIsSsl(true);
        }
    }


    /**
     * Checks if WEB session is SSL. Returns true if yes.
     *
     * @return bool
     */
    public function isSsl()
    {
        if (is_null($this->_blIsSsl)) {
            $this->_checkSsl();
        }

        return $this->_blIsSsl;
    }

    /**
     * Checks if shop runs in https only mode
     * https only mode means there is no http url but only a https url
     *
     * @return bool
     */
    public function isHttpsOnly()
    {
        return $this->isSsl() && $this->getSslShopUrl() == $this->getShopUrl();
    }

    /**
     * Compares current URL to supplied string
     *
     * @param string $url URL
     *
     * @return bool true if $url is equal to current page URL
     */
    public function isCurrentUrl($url)
    {
        /** @var UtilsServer $utilsServer */
        $utilsServer = Registry::getUtilsServer();
        return $utilsServer->isCurrentUrl($url);
    }

    /**
     * Compares current protocol to supplied url string
     *
     * @param string $url URL
     *
     * @return bool true if $url is equal to current page URL
     */
    public function isCurrentProtocol($url)
    {
        // Missing protocol, cannot proceed, assuming true.
        if (!$url || (strpos($url, "http") !== 0)) {
            return true;
        }

        return (strpos($url, "https:") === 0) == $this->isSsl();
    }

    /**
     * Returns config sShopURL or sMallShopURL if secondary shop
     *
     * @param int  $lang  language
     * @param bool $admin if set true, function returns shop url without checking language/subshops for different url.
     *
     * @return string
     */
    public function getShopUrl($lang = null, $admin = null)
    {
        $url = null;
        $admin = isset($admin) ? $admin : $this->isAdmin();

        if (!$admin) {
            $url = $this->getShopUrlByLanguage($lang);
            if (!$url) {
                $url = $this->getMallShopUrl();
            }
        }

        if (!$url) {
            $url = $this->getConfigParam('sShopURL');
        }

        return $url;
    }

    /**
     * Returns config sSSLShopURL or sMallSSLShopURL if secondary shop
     *
     * @param int $lang language (default is null)
     *
     * @return string
     */
    public function getSslShopUrl($lang = null)
    {
        $url = $this->getShopUrlByLanguage($lang, true);

        if (!$url) {
            $url = $this->getMallShopUrl(true);
        }

        if (!$url) {
            $url = $this->getMallShopUrl();
        }

        //normal section
        if (!$url) {
            $url = $this->getConfigParam('sSSLShopURL');
        }

        if (!$url) {
            $url = $this->getShopUrl($lang);
        }

        return $url;
    }

    /**
     * Returns utils dir URL
     *
     * @return string
     */
    public function getCoreUtilsUrl()
    {
        return $this->getCurrentShopUrl() . 'Core/utils/';
    }

    /**
     * Returns SSL or non SSL shop URL without index.php depending on Mall
     * affecting environment is admin mode and current ssl usage status
     *
     * @param bool $admin if admin
     *
     * @return string
     */
    public function getCurrentShopUrl($admin = null)
    {
        if ($admin === null) {
            $admin = $this->isAdmin();
        }
        if ($admin) {
            if ($this->isSsl()) {
                $url = $this->getConfigParam('sAdminSSLURL');
                if (!$url) {
                    return $this->getSslShopUrl() . $this->getConfigParam('sAdminDir') . '/';
                }

                return $url;
            } else {
                return $this->getShopUrl() . $this->getConfigParam('sAdminDir') . '/';
            }
        } else {
            return $this->isSsl() ? $this->getSslShopUrl() : $this->getShopUrl();
        }
    }

    /**
     * Returns SSL or not SSL shop URL with index.php and sid
     *
     * @param int $lang language (optional)
     *
     * @return string
     */
    public function getShopCurrentUrl($lang = null)
    {
        if ($this->isSsl()) {
            $url = $this->getSSLShopURL($lang);
        } else {
            $url = $this->getShopURL($lang);
        }

        return Registry::getUtilsUrl()->processUrl($url . 'index.php', false);
    }

    /**
     * Returns shop non SSL URL including index.php and sid.
     *
     * @param int  $lang  language
     * @param bool $admin if admin
     *
     * @return string
     */
    public function getShopHomeUrl($lang = null, $admin = null)
    {
        return Registry::getUtilsUrl()->processUrl($this->getShopUrl($lang, $admin) . 'index.php', false);
    }

    /**
     * Returns widget start non SSL URL including widget.php and sid.
     *
     * @param int   $languageId    language
     * @param bool  $inAdmin       if admin
     * @param array $urlParameters parameters which should be added to URL.
     *
     * @return string
     */
    public function getWidgetUrl($languageId = null, $inAdmin = null, $urlParameters = [])
    {
        $utilsUrl = Registry::getUtilsUrl();
        $widgetUrl = $this->isSsl() ? $this->getSslShopUrl($languageId) : $this->getShopUrl($languageId, $inAdmin);
        $widgetUrl = $utilsUrl->processUrl($widgetUrl . 'widget.php', false);

        if (!isset($languageId)) {
            $language = Registry::getLang();
            $languageId = $language->getBaseLanguage();
        }
        $urlLang = $utilsUrl->getUrlLanguageParameter($languageId);
        $widgetUrl = $utilsUrl->appendUrl($widgetUrl, $urlLang, true);

        $widgetUrl = $utilsUrl->appendUrl($widgetUrl, $urlParameters, true, true);

        return $widgetUrl;
    }

    /**
     * Returns shop SSL URL with index.php and sid.
     *
     * @return string
     */
    public function getShopSecureHomeUrl()
    {
        return Registry::getUtilsUrl()->processUrl($this->getSslShopUrl() . 'index.php', false);
    }

    /**
     * Returns active shop currency.
     *
     * @return string
     */
    public function getShopCurrency()
    {
        if ((null === ($curr = $this->getRequestParameter('cur')))) {
            if (null === ($curr = $this->getRequestParameter('currency'))) {
                $curr = $this->getSession()->getVariable('currency');
            }
        }

        return (int) $curr;
    }

    /**
     * Returns active shop currency object.
     *
     * @return object
     */
    public function getActShopCurrencyObject()
    {
        if ($this->_oActCurrencyObject === null) {
            $cur = $this->getShopCurrency();
            $currencies = $this->getCurrencyArray();
            if (!isset($currencies[$cur])) {
                $this->_oActCurrencyObject = reset($currencies); // reset() returns the first element
            } else {
                $this->_oActCurrencyObject = $currencies[$cur];
            }
        }

        return $this->_oActCurrencyObject;
    }

    /**
     * Sets the actual currency
     *
     * @param int $cur 0 = EUR, 1 = GBP, 2 = CHF
     */
    public function setActShopCurrency($cur)
    {
        $currencies = $this->getCurrencyArray();
        if (isset($currencies[$cur])) {
            $this->getSession()->setVariable('currency', $cur);
            $this->_oActCurrencyObject = null;
        }
    }

    /**
     * Returns path to out dir
     *
     * @param bool $absolute mode - absolute/relative path
     *
     * @return string
     */
    public function getOutDir($absolute = true)
    {
        if ($absolute) {
            return $this->getConfigParam('sShopDir') . $this->_sOutDir . '/';
        } else {
            return $this->_sOutDir . '/';
        }
    }

    /**
     * Returns path to out dir
     *
     * @param bool $absolute mode - absolute/relative path
     *
     * @return string
     */
    public function getViewsDir($absolute = true)
    {
        if ($absolute) {
            return $this->getConfigParam('sShopDir') . 'Application/views/';
        } else {
            return 'Application/views/';
        }
    }

    /**
     * Returns path to translations dir
     *
     * @param string $file     File name
     * @param string $dir      Directory name
     * @param bool   $absolute mode - absolute/relative path
     *
     * @return string
     */
    public function getTranslationsDir($file, $dir, $absolute = true)
    {
        $path = $absolute ? $this->getConfigParam('sShopDir') : '';
        $path .= 'Application/translations/';
        if (is_readable($path . $dir . '/' . $file)) {
            return $path . $dir . '/' . $file;
        }

        return false;
    }

    /**
     * Returns path to out dir
     *
     * @param bool $absolute mode - absolute/relative path
     *
     * @return string
     */
    public function getAppDir($absolute = true)
    {
        if ($absolute) {
            return $this->getConfigParam('sShopDir') . 'Application/';
        } else {
            return 'Application/';
        }
    }

    /**
     * Returns url to out dir
     *
     * @param bool $ssl       Whether to force ssl
     * @param bool $admin     Whether to force admin
     * @param bool $nativeImg Whether to force native image dirs
     *
     * @return string
     */
    public function getOutUrl($ssl = null, $admin = null, $nativeImg = false)
    {
        $ssl = is_null($ssl) ? $this->isSsl() : $ssl;
        $admin = is_null($admin) ? $this->isAdmin() : $admin;

        if ($ssl) {
            if ($nativeImg && !$admin) {
                $url = $this->getSslShopUrl();
            } else {
                $url = $this->getConfigParam('sSSLShopURL');
                if (!$url && $admin) {
                    $url = $this->getConfigParam('sAdminSSLURL') . '../';
                }
            }
        } else {
            $url = ($nativeImg && !$admin) ? $this->getShopUrl() : $this->getConfigParam('sShopURL');
        }

        return $url . $this->_sOutDir . '/';
    }

    /**
     * Finds and returns files or folders path in out dir
     *
     * @param string $file       File name
     * @param string $dir        Directory name
     * @param bool   $admin      Whether to force admin
     * @param int    $lang       Language id
     * @param int    $shop       Shop id
     * @param string $theme      Theme name
     * @param bool   $absolute   mode - absolute/relative path
     * @param bool   $ignoreCust Ignore custom theme
     *
     * @return string
     */
    public function getDir($file, $dir, $admin, $lang = null, $shop = null, $theme = null, $absolute = true, $ignoreCust = false)
    {
        if (is_null($theme)) {
            $theme = $this->getConfigParam('sTheme');
        }

        if ($admin) {
            $theme = $this->getContainer()->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        }

        if ($dir != $this->_sTemplateDir) {
            $base = $this->getOutDir($absolute);
            $absBase = $this->getOutDir();
        } else {
            $base = $this->getViewsDir($absolute);
            $absBase = $this->getViewsDir();
        }

        $langAbbr = '-';
        // false means skip language folder check
        if ($lang !== false) {
            $language = Registry::getLang();

            if (is_null($lang)) {
                $lang = $language->getEditLanguage();
            }

            $langAbbr = $language->getLanguageAbbr($lang);
        }

        if (is_null($shop)) {
            $shop = $this->getShopId();
        }

        //Load from
        $path = "{$theme}/{$shop}/{$langAbbr}/{$dir}/{$file}";
        $cacheKey = $path . "_{$ignoreCust}{$absolute}";

        if (($return = Registry::getUtils()->fromStaticCache($cacheKey)) !== null) {
            return $return;
        }

        $return = $this->getEditionTemplate("{$theme}/{$dir}/{$file}");

        // Check for custom template
        $customTheme = $this->getConfigParam('sCustomTheme');
        if (!$return && !$admin && !$ignoreCust && $customTheme && $customTheme != $theme) {
            $return = $this->getDir($file, $dir, $admin, $lang, $shop, $customTheme, $absolute);
        }

        //test lang level ..
        if (!$return && !$admin && is_readable($absBase . $path)) {
            $return = $base . $path;
        }

        //test shop level ..
        if (!$return && !$admin) {
            $return = $this->getShopLevelDir($base, $absBase, $file, $dir, $admin, $lang, $shop, $theme, $absolute, $ignoreCust);
        }

        //test theme language level ..
        $path = "$theme/$langAbbr/$dir/$file";
        if (!$return && $lang !== false && is_readable($absBase . $path)) {
            $return = $base . $path;
        }

        //test theme level ..
        $path = "$theme/$dir/$file";
        if (!$return && is_readable($absBase . $path)) {
            $return = $base . $path;
        }

        //test out language level ..
        $path = "$langAbbr/$dir/$file";
        if (!$return && $lang !== false && is_readable($absBase . $path)) {
            $return = $base . $path;
        }

        //test out level ..
        $path = "$dir/$file";
        if (!$return && is_readable($absBase . $path)) {
            $return = $base . $path;
        }

        // TODO: implement logic to log missing paths

        // to cache
        Registry::getUtils()->toStaticCache($cacheKey, $return);

        return $return;
    }

    /**
     * @param string $base
     * @param string $absBase
     * @param string $file
     * @param string $dir
     * @param bool   $admin
     * @param int    $lang
     * @param int    $shop
     * @param string $theme
     * @param bool   $absolute
     * @param bool   $ignoreCust
     *
     * @return bool|string
     */
    protected function getShopLevelDir($base, $absBase, $file, $dir, $admin, $lang, $shop, $theme, $absolute, $ignoreCust)
    {
        $return = false;

        $path = "$theme/$shop/$dir/$file";
        if (is_readable($absBase . $path)) {
            $return = $base . $path;
        }

        return $return;
    }

    /**
     * Finds and returns file or folder url in out dir
     *
     * @param string $file      File name
     * @param string $dir       Directory name
     * @param bool   $admin     Whether to force admin
     * @param bool   $ssl       Whether to force ssl
     * @param bool   $nativeImg Whether to force native image dirs
     * @param int    $lang      Language id
     * @param int    $shop      Shop id
     * @param string $theme     Theme name
     *
     * @return string
     */
    public function getUrl($file, $dir, $admin = null, $ssl = null, $nativeImg = false, $lang = null, $shop = null, $theme = null)
    {
        return str_replace(
            $this->getOutDir(),
            $this->getOutUrl($ssl, $admin, $nativeImg),
            $this->getDir($file, $dir, $admin, $lang, $shop, $theme)
        );
    }

    /**
     * Finds and returns image files or folders path
     *
     * @param string $file  File name
     * @param bool   $admin Whether to force admin
     *
     * @return string
     */
    public function getImagePath($file, $admin = false)
    {
        return $this->getDir($file, $this->_sImageDir, $admin);
    }

    /**
     * Finds and returns image folder url
     *
     * @param bool   $admin     Whether to force admin
     * @param bool   $ssl       Whether to force ssl
     * @param bool   $nativeImg Whether to force native image dirs
     * @param string $file      Image file name
     *
     * @return string
     */
    public function getImageUrl($admin = false, $ssl = null, $nativeImg = null, $file = null)
    {
        $nativeImg = is_null($nativeImg) ? $this->getConfigParam('blNativeImages') : $nativeImg;

        return $this->getUrl($file, $this->_sImageDir, $admin, $ssl, $nativeImg);
    }

    /**
     * Finds and returns image folders path
     *
     * @param bool $admin Whether to force admin
     *
     * @return string
     */
    public function getImageDir($admin = false)
    {
        return $this->getDir(null, $this->_sImageDir, $admin);
    }

    /**
     * Finds and returns product pictures files or folders path
     *
     * @param string $file  File name
     * @param bool   $admin Whether to force admin
     * @param int    $lang  Language
     * @param int    $shop  Shop id
     * @param string $theme theme name
     *
     * @return string
     */
    public function getPicturePath($file, $admin = false, $lang = null, $shop = null, $theme = null)
    {
        return $this->getDir($file, $this->_sPictureDir, $admin, $lang, $shop, $theme);
    }

    /**
     * Finds and returns master pictures folder path
     *
     * @param bool $admin Whether to force admin
     *
     * @return string
     */
    public function getMasterPictureDir($admin = false)
    {
        return $this->getDir(null, $this->_sPictureDir . "/" . $this->_sMasterPictureDir, $admin);
    }

    /**
     * Finds and returns master picture path
     *
     * @param string $file  File name
     * @param bool   $admin Whether to force admin
     *
     * @return string
     */
    public function getMasterPicturePath($file, $admin = false)
    {
        return $this->getDir($file, $this->_sPictureDir . "/" . $this->_sMasterPictureDir, $admin);
    }

    /**
     * Finds and returns product picture file or folder url
     *
     * @param string $file   File name
     * @param bool   $admin  Whether to force admin
     * @param bool   $ssl    Whether to force ssl
     * @param int    $lang   Language
     * @param int    $shopId Shop id
     * @param string $defPic Default (nopic) image path ["0/nopic.jpg"]
     *
     * @return string
     */
    public function getPictureUrl($file, $admin = false, $ssl = null, $lang = null, $shopId = null, $defPic = "master/nopic.jpg")
    {
        if ($altUrl = Registry::getPictureHandler()->getAltImageUrl('', $file, $ssl)) {
            return $altUrl;
        }

        $nativeImg = $this->getConfigParam('blNativeImages');
        $url = $this->getUrl($file, $this->_sPictureDir, $admin, $ssl, $nativeImg, $lang, $shopId);

        //anything is better than empty name, because <img src=""> calls shop once more = x2 SLOW.
        if (!$url && $defPic) {
            $url = $this->getUrl($defPic, $this->_sPictureDir, $admin, $ssl, $nativeImg, $lang, $shopId);
        }

        return $url;
    }

    /**
     * Finds and returns product pictures folders path
     *
     * @param bool $admin Whether to force admin
     *
     * @return string
     */
    public function getPictureDir($admin)
    {
        return $this->getDir(null, $this->_sPictureDir, $admin);
    }

    /**
     * Calculates and returns full path to template.
     *
     * @param string $templateName Template name
     * @param bool   $isAdmin      Whether to force admin
     *
     * @return string
     */
    public function getTemplatePath($templateName, $isAdmin)
    {
        $finalTemplatePath = $this->getDir($templateName, $this->_sTemplateDir, $isAdmin);

        if (!$finalTemplatePath) {
            $templatePathCalculator = $this->getModuleTemplatePathCalculator();
            $templatePathCalculator->setModulesPath($this->getConfig()->getModulesDir());
            try {
                $finalTemplatePath = $templatePathCalculator->calculateModuleTemplatePath($templateName);
            } catch (Exception $e) {
                $finalTemplatePath = '';
            }
        }

        return $finalTemplatePath;
    }

    /**
     * Get module template calculator object
     *
     * @return ModuleTemplatePathCalculator
     */
    protected function getModuleTemplatePathCalculator()
    {
        return oxNew(ModuleTemplatePathCalculator::class);
    }

    /**
     * Finds and returns templates folders path
     *
     * @param bool $admin Whether to force admin
     *
     * @return string
     */
    public function getTemplateDir($admin = false)
    {
        return $this->getDir(null, $this->_sTemplateDir, $admin);
    }

    /**
     * Finds and returns template file or folder url
     *
     * @param string $file  File name
     * @param bool   $admin Whether to force admin
     * @param bool   $ssl   Whether to force ssl
     * @param int    $lang  Language id
     *
     * @return string
     */
    public function getTemplateUrl($file = null, $admin = false, $ssl = null, $lang = null)
    {
        return $this->getShopMainUrl() . $this->getDir($file, $this->_sTemplateDir, $admin, $lang, null, null, false);
    }

    /**
     * Finds and returns base template folder url
     *
     * @param bool $admin Whether to force admin
     *
     * @return string
     */
    public function getTemplateBase($admin = false)
    {
        // Base template dir is the parent dir of template dir
        return str_replace($this->_sTemplateDir . '/', '', $this->getDir(null, $this->_sTemplateDir, $admin, null, null, null, false));
    }

    /**
     * Finds and returns resource (css, js, etc..) files or folders path
     *
     * @param string $file  File name
     * @param bool   $admin Whether to force admin
     *
     * @return string
     */
    public function getResourcePath($file = '', $admin = false)
    {
        return $this->getDir($file, $this->_sResourceDir, $admin);
    }

    /**
     * Returns path to modules dir
     *
     * @param bool $absolute mode - absolute/relative path
     *
     * @return string
     */
    public function getModulesDir($absolute = true)
    {
        if ($absolute) {
            return $this->getConfigParam('sShopDir') . $this->_sModulesDir . '/';
        } else {
            return $this->_sModulesDir . '/';
        }
    }

    /**
     * Finds and returns resource (css, js, etc..) file or folder url
     *
     * @param string $file  File name
     * @param bool   $admin Whether to force admin
     * @param bool   $ssl   Whether to force ssl
     * @param int    $lang  Language id
     *
     * @return string
     */
    public function getResourceUrl($file = '', $admin = false, $ssl = null, $lang = null)
    {
        $nativeImg = $this->getConfigParam('blNativeImages');

        return $this->getUrl($file, $this->_sResourceDir, $admin, $ssl, $nativeImg, $lang);
    }

    /**
     * Finds and returns resource (css, js, etc..) folders path
     *
     * @param bool $admin Whether to force admin
     *
     * @return string
     */
    public function getResourceDir($admin)
    {
        return $this->getDir(null, $this->_sResourceDir, $admin);
    }

    /**
     * Returns array of available currencies
     *
     * @param integer $currency Active currency number (default null)
     *
     * @return array
     */
    public function getCurrencyArray($currency = null)
    {
        $confCurrencies = $this->getConfigParam('aCurrencies');
        if (!is_array($confCurrencies)) {
            return [];
        }

        // processing currency configuration data
        $currencies = [];
        reset($confCurrencies);
        foreach ($confCurrencies as $key => $val) {
            if ($val) {
                $cur = new stdClass();
                $cur->id = $key;
                $curValues = explode('@', $val);
                $cur->name = trim($curValues[0]);
                $cur->rate = trim($curValues[1]);
                $cur->dec = trim($curValues[2]);
                $cur->thousand = trim($curValues[3]);
                $cur->sign = trim($curValues[4]);
                $cur->decimal = trim($curValues[5]);

                // change for US version
                if (isset($curValues[6])) {
                    $cur->side = trim($curValues[6]);
                }

                if (isset($currency) && $key == $currency) {
                    $cur->selected = 1;
                } else {
                    $cur->selected = 0;
                }
                $currencies[$key] = $cur;
            }

            // #861C -  performance, do not load other currencies
            if (!$this->getConfigParam('bl_perfLoadCurrency')) {
                break;
            }
        }

        return $currencies;
    }

    /**
     * Returns currency object.
     *
     * @param string $name Name of active currency
     *
     * @return object
     */
    public function getCurrencyObject($name)
    {
        $search = $this->getCurrencyArray();
        foreach ($search as $cur) {
            if ($cur->name == $name) {
                return $cur;
            }
        }
    }

    /**
     * Checks if the shop is in demo mode.
     *
     * @return bool
     */
    public function isDemoShop()
    {
        return $this->getConfigParam('blDemoShop');
    }

    /**
     * Returns OXID eShop edition.
     *
     * @deprecated since v6.0.0-rc.2 (2017-08-24); Use \OxidEsales\Facts\Facts::getEdition() instead.
     *
     * @return string
     */
    public function getEdition()
    {
        return "CE";
    }

    /**
     * Returns full eShop edition name
     *
     * @return string
     */
    public function getFullEdition()
    {
        $edition = $this->getEdition();
        if ($edition == "CE") {
            $edition = "Community Edition";
        }

        return $edition;
    }

    /**
     * Returns shops version number (eg. '4.4.2')
     *
     * @deprecated since v6.0.0-rc.2 (2017-08-23); Use  OxidEsales\Eshop\Core\ShopVersion::getVersion() instead.
     *
     * @return string
     */
    public function getVersion()
    {
        return oxNew(\OxidEsales\Eshop\Core\ShopVersion::class)->getVersion();
    }

    /**
     * Returns build revision number or false on read error.
     *
     * @deprecated since v6.0.0 (2017-12-04); This functionality will be removed completely
     *
     * @return bool|string
     */
    public function getRevision()
    {
        $fileName = $this->getConfigParam('sShopDir') . "/pkg.rev";

        $rev = false;

        if (file_exists($fileName) && is_readable($fileName)) {
            $rev = trim(file_get_contents($fileName));
        }

        if (!$rev) {
            return false;
        }

        return $rev;
    }

    /**
     * Returns build package info file content.
     *
     * @return bool|string
     */
    public function getPackageInfo()
    {
        $fileName = $this->getConfigParam('sShopDir') . "/pkg.info";
        $rev = @file_get_contents($fileName);
        $rev = str_replace("\n", "<br>", $rev);

        if (!$rev) {
            return false;
        }

        return $rev;
    }

    /**
     * Counts OXID mandates
     *
     * @return int
     */
    public function getMandateCount()
    {
        return 1;
    }

    /**
     * Checks if shop is MALL. Returns true on success.
     *
     * @return bool
     */
    public function isMall()
    {
        return false;
    }

    /**
     * Checks version of shop, returns:
     *  0 - version is bellow 2.2
     *  1 - Demo or unlicensed
     *  2 - Pro
     *  3 - Enterprise
     */
    public function detectVersion()
    {
    }

    /**
     * Updates or adds new shop configuration parameters to DB.
     * Arrays must be passed not serialized, serialized values are supported just for backward compatibility.
     *
     * @param string $varType Variable Type
     * @param string $varName Variable name
     * @param mixed  $varVal  Variable value (can be string, integer or array)
     * @param string $shopId  Shop ID, default is current shop
     * @param string $module  Module name (empty for base options)
     */
    public function saveShopConfVar($varType, $varName, $varVal, $shopId = null, $module = '')
    {
        switch ($varType) {
            case 'arr':
            case 'aarr':
                $value = serialize($varVal);
                break;
            case 'bool':
                //config param
                $varVal = (($varVal == 'true' || $varVal) && $varVal && strcasecmp($varVal, "false"));
                //db value
                $value = $varVal ? "1" : "";
                break;
            case 'num':
                //config param
                $varVal = $varVal != '' ? Registry::getUtils()->string2Float($varVal) : '';
                $value = $varVal;
                break;
            default:
                $value = $varVal;
                break;
        }

        if (!$shopId) {
            $shopId = $this->getShopId();
        }

        // Update value only for current shop
        if ($shopId == $this->getShopId()) {
            $this->setConfigParam($varName, $varVal);
        }

        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $newOXID = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID();

        $query = "delete from oxconfig where oxshopid = :oxshopid and oxvarname = :oxvarname and oxmodule = :oxmodule";
        $db->execute($query, [
            ':oxshopid' => $shopId,
            ':oxvarname' => $varName,
            ':oxmodule' => $module ?: ''
        ]);

        $query = "insert into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue)
                  values (:oxid, :oxshopid, :oxmodule, :oxvarname, :oxvartype, ENCODE(:value, :key))";
        $db->execute($query, [
            ':oxid' => $newOXID,
            ':oxshopid' => $shopId,
            ':oxmodule' => $module ?: '',
            ':oxvarname' => $varName,
            ':oxvartype' => $varType,
            ':value' => $value ?? '',
            ':key' => $this->getConfigParam('sConfigKey'),
        ]);

        $this->informServicesAfterConfigurationChanged($varName, $shopId, $module);
    }

    /**
     * Retrieves shop configuration parameters from DB.
     *
     * @param string $varName Variable name
     * @param string $shopId  Shop ID
     * @param string $module  module identifier
     *
     * @return object - raw configuration value in DB
     */
    public function getShopConfVar($varName, $shopId = null, $module = '')
    {
        if (!$shopId) {
            $shopId = $this->getShopId();
        }

        if ($shopId == $this->getShopId() && (!$module || $module == Config::OXMODULE_THEME_PREFIX . $this->getConfigParam('sTheme'))) {
            $varValue = $this->getConfigParam($varName);
            if ($varValue !== null) {
                return $varValue;
            }
        }

        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

        $query = "select oxvartype, " . $this->getDecodeValueQuery() . " as oxvarvalue from oxconfig where oxshopid = :oxshopid and oxmodule = :oxmodule and oxvarname = :oxvarname";
        $rs = $db->select($query, [
            ':oxshopid' => $shopId,
            ':oxmodule' => $module,
            ':oxvarname' => $varName
        ]);

        if ($rs != false && $rs->count() > 0) {
            return $this->decodeValue($rs->fields['oxvartype'], $rs->fields['oxvarvalue']);
        }
    }

    /**
     * Decodes and returns database value
     *
     * @param string $type       parameter type
     * @param mixed  $mOrigValue parameter db value
     *
     * @return mixed
     */
    public function decodeValue($type, $mOrigValue)
    {
        $value = $mOrigValue;
        switch ($type) {
            case 'arr':
            case 'aarr':
                $value = unserialize($mOrigValue);
                break;
            case 'bool':
                $value = ($mOrigValue == 'true' || $mOrigValue == '1');
                break;
        }

        return $value;
    }

    /**
     * Returns decode query part user to decode config field value
     *
     * @param string $fieldName field name, default "oxvarvalue" [optional]
     *
     * @return string
     */
    public function getDecodeValueQuery($fieldName = "oxvarvalue")
    {
        return " DECODE( {$fieldName}, '" . $this->getConfigParam('sConfigKey') . "') ";
    }

    /**
     * Returns true if current active shop is in productive mode or false if not
     *
     * @return bool
     */
    public function isProductiveMode()
    {
        $productive = $this->getConfigParam('blProductive');
        if (!isset($productive)) {
            $query = 'select oxproductive from oxshops where oxid = :oxid';
            $productive = ( bool ) \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($query, [
                ':oxid' => $this->getShopId()
            ]);
            $this->setConfigParam('blProductive', $productive);
        }

        return $productive;
    }

    /**
     * Function returns default shop ID
     *
     * @return string
     */
    public function getBaseShopId()
    {
        return \OxidEsales\Eshop\Core\ShopIdCalculator::BASE_SHOP_ID;
    }

    /**
     * Loads and returns active shop object
     *
     * @return Shop
     */
    public function getActiveShop()
    {
        if ($this->_oActShop && $this->_iShopId == $this->_oActShop->getId() &&
            $this->_oActShop->getLanguage() == Registry::getLang()->getBaseLanguage()
        ) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        $this->_oActShop->load($this->getShopId());

        return $this->_oActShop;
    }

    /**
     * Returns active view object. If this object was not defined - returns oxubase object
     *
     * @return FrontendController
     */
    public function getActiveView()
    {
        if (count($this->_aActiveViews)) {
            $actView = end($this->_aActiveViews);
        }
        if (!isset($actView) || $actView == null) {
            $actView = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
            $this->_aActiveViews[] = $actView;
        }

        return $actView;
    }

    /**
     * Returns top active view object from views chain.
     *
     * @return FrontendController
     */
    public function getTopActiveView()
    {
        if (count($this->_aActiveViews)) {
            return reset($this->_aActiveViews);
        } else {
            return $this->getActiveView();
        }
    }

    /**
     * Returns all active views objects list.
     *
     * @return array
     */
    public function getActiveViewsList()
    {
        return $this->_aActiveViews;
    }

    /**
     * View object setter
     *
     * @param object $view view object
     */
    public function setActiveView($view)
    {
        $this->_aActiveViews[] = $view;
    }

    /**
     * Drop last active view object
     */
    public function dropLastActiveView()
    {
        array_pop($this->_aActiveViews);
    }

    /**
     * Check if there is more than one active view
     *
     * @return null
     */
    public function hasActiveViewsChain()
    {
        return (count($this->_aActiveViews) > 1);
    }

    /**
     * @deprecated since v6.0 (2017-02-3). Use Config::getActiveViewsIds() instead.
     *
     * NOTE: according to current logic, this returns the view ids.
     *
     * Get active views names list
     *
     * @return array
     */
    public function getActiveViewsNames()
    {
        $names = [];

        if (is_array($this->getActiveViewsList())) {
            foreach ($this->getActiveViewsList() as $view) {
                $names[] = $view->getClassName();
            }
        }

        return $names;
    }

    /**
     * Get active views class id list
     *
     * @return array
     */
    public function getActiveViewsIds()
    {
        $ids = [];

        if (is_array($this->getActiveViewsList())) {
            foreach ($this->getActiveViewsList() as $view) {
                $ids[] = $view->getClassKey();
            }
        }

        return $ids;
    }

    /**
     * Returns true if current installation works in utf-8 mode, or false if not.
     *
     * @deprecated since 6.0 (2016-12-07) As the shop installation is utf-8, this method will be removed.
     *
     * CAUTION: if you use this deprecated feature, you have to be sure, that there is a iUtfMode in your config.inc.php,
     *          otherwise the result of this method is always true.
     *
     * @return bool
     */
    public function isUtf()
    {
        if ($this->getConfigParam('iUtfMode') !== null) {
            return (bool) $this->getConfigParam('iUtfMode');
        }
        return true;
    }

    /**
     * Returns log files storage path
     *
     * @return string
     */
    public function getLogsDir()
    {
        return $this->getConfigParam('sShopDir') . 'log/';
    }

    /**
     * Returns true if option is theme option
     *
     * @param string $name option name
     *
     * @return bool
     */
    public function isThemeOption($name)
    {
        return (bool) isset($this->_aThemeConfigParams[$name]);
    }

    /**
     * Returns  SSL or non SSL shop main URL without index.php
     *
     * @return string
     */
    public function getShopMainUrl()
    {
        return $this->_blIsSsl ? $this->getConfigParam('sSSLShopURL') : $this->getConfigParam('sShopURL');
    }

    /**
     * Get parsed modules
     *
     * @return array
     */
    public function getModulesWithExtendedClass()
    {
        return $this->parseModuleChains($this->getConfigParam('aModules'));
    }

    /**
     * Parse array of module chains to nested array
     *
     * @deprecated on 6.0.0 (2017-03-09); use OxidEsales\Eshop\Core\ModuleList::parseModuleChains() instead.
     *
     * @param array $modules Module array (config format)
     *
     * @return array
     */
    public function parseModuleChains($modules)
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        return $moduleList->parseModuleChains($modules);
    }

    /**
     * Return active shop ids
     *
     * @return array
     */
    public function getShopIds()
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getCol("SELECT `oxid` FROM `oxshops`");
    }

    /**
     * Function returns shop url by given language.
     * #680 per language another URL
     *
     * @param integer $lang Language id.
     * @param bool    $ssl  Whether to use ssl.
     *
     * @return null|string
     */
    public function getShopUrlByLanguage($lang, $ssl = false)
    {
        $configParameter = $ssl ? 'aLanguageSSLURLs' : 'aLanguageURLs';
        $lang = isset($lang) ? $lang : Registry::getLang()->getBaseLanguage();
        $languageURLs = $this->getConfigParam($configParameter);
        if (isset($lang) && isset($languageURLs[$lang]) && !empty($languageURLs[$lang])) {
            $languageURLs[$lang] = Registry::getUtils()->checkUrlEndingSlash($languageURLs[$lang]);
            return $languageURLs[$lang];
        }
    }

    /**
     * Function returns mall shop url.
     *
     * @param bool $ssl
     *
     * @return null|string
     */
    public function getMallShopUrl($ssl = false)
    {
        $configParameter = $ssl ? 'sMallSSLShopURL' : 'sMallShopURL';
        $mallShopUrl = $this->getConfigParam($configParameter);
        if ($mallShopUrl) {
            return Registry::getUtils()->checkUrlEndingSlash($mallShopUrl);
        }
    }

    /**
     * Handle database exception.
     * At this point everything has crashed already and not much of shop business logic is left to call.
     * So just go straight and call the ExceptionHandler.
     *
     * @param \OxidEsales\Eshop\Core\Exception\DatabaseException $exception
     */
    protected function _handleDbConnectionException(\OxidEsales\Eshop\Core\Exception\DatabaseException $exception)
    {
        $exceptionHandler = $this->getExceptionHandler();
        $exceptionHandler->handleDatabaseException($exception);
    }

    /**
     * Redirect to start page and display the error
     *
     * @param \OxidEsales\Eshop\Core\Exception\StandardException $ex message to show on exit
     */
    protected function _handleCookieException($ex)
    {
        $this->_processSeoCall();

        //starting up the session
        $this->getSession()->start();

        // redirect to start page and display the error
        Registry::getUtilsView()->addErrorToDisplay($ex);
        Registry::getUtils()->redirect($this->getShopHomeUrl() . 'cl=start', true, 302);
    }

    /**
     * Save system configuration parameters, which is the same for sub-shops.
     *
     * @param string $parameterType  Type
     * @param string $parameterName  Name
     * @param mixed  $parameterValue Value (can be string, integer or array)
     */
    public function saveSystemConfigParameter($parameterType, $parameterName, $parameterValue)
    {
        $this->saveShopConfVar($parameterType, $parameterName, $parameterValue, $this->getBaseShopId());
    }

    /**
     * Retrieves system configuration parameters, which is the same for sub-shops.
     *
     * @param string $parameterName Variable name
     *
     * @return mixed
     */
    public function getSystemConfigParameter($parameterName)
    {
        return $this->getShopConfVar($parameterName, $this->getBaseShopId());
    }

    /**
     * Returns whether given shop id is valid.
     *
     * @param string $shopId
     *
     * @return bool
     */
    protected function _isValidShopId($shopId)
    {
        return !empty($shopId);
    }

    /**
     * Returns active shop id.
     *
     * @return string
     */
    protected function calculateActiveShopId()
    {
        return $this->getBaseShopId();
    }

    /**
     * Check and get template path by Edition if exists
     *
     * @param string $templateName
     *
     * @return false|string
     */
    protected function getEditionTemplate($templateName)
    {
        return false;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Exception\ExceptionHandler
     */
    protected function getExceptionHandler()
    {
        $exceptionHandler = new \OxidEsales\Eshop\Core\Exception\ExceptionHandler();

        return $exceptionHandler;
    }

    /**
     * Inform respective services if shop/module/theme related configuration data was changed in database.
     *
     * @param string  $varName   Variable name
     * @param integer $shopId    Shop id
     * @param string  $extension Module or theme name in case of extension config change
     */
    protected function informServicesAfterConfigurationChanged($varName, $shopId, $extension = '')
    {
        if (empty($extension)) {
            $this->dispatchEvent(new ShopConfigurationChangedEvent($varName, (int) $shopId));
        } elseif (false !== strpos($extension, self::OXMODULE_MODULE_PREFIX)) {
            $moduleId = str_replace(self::OXMODULE_MODULE_PREFIX, '', $extension);
            $this->dispatchEvent(new SettingChangedEvent($varName, (int) $shopId, $moduleId));
        } elseif (false !== strpos($extension, self::OXMODULE_THEME_PREFIX)) {
            $this->dispatchEvent(new ThemeSettingChangedEvent($varName, (int) $shopId, $extension));
        }
    }
}
