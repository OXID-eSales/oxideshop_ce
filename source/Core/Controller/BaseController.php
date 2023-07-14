<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Core\ShopVersion;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterRequestProcessedEvent;
use OxidEsales\Facts\Facts;

/**
 * Base view class. Collects and passes data to template engine, sets some global
 * configuration parameters.
 */
class BaseController extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Array of data that is passed to template engine - array( "varName" => "varValue").
     *
     * @var array
     */
    protected $_aViewData = [];

    /**
     * View parameters array
     *
     * @var array
     */
    protected $_aViewParams = [];

    /**
     * Location of a executed class file.
     *
     * @var string
     */
    protected $_sClassLocation = null;

    /**
     * Name of running class method.
     *
     * @var string
     */
    protected $_sThisAction = null;

    /**
     * If this is a component we will have our parent view here.
     *
     * @var \OxidEsales\Eshop\Core\Controller\BaseController|null
     */
    protected $_oParent = null;

    /**
     * Flag if this object is a component or not
     *
     * @var bool|null
     */
    protected $_blIsComponent = false;

    /**
     * Name of template file to render.
     *
     * @var string
     */
    protected $_sThisTemplate = null;

    /**
     * ID of current view - generated php file.
     *
     * @var string
     */
    protected $_sViewId = null;

    /**
     * Current view class name
     *
     * @var string
     */
    protected $_sClass = null;

    /**
     * Current view class key
     *
     * @var string
     */
    protected $classKey = null;

    /**
     * Action function name
     *
     * @var string
     */
    protected $_sFnc = null;

    /**
     * Marker if user defined function was executed
     *
     * @var bool
     */
    protected static $_blExecuted = false;

    /**
     * Active charset
     *
     * @var string
     */
    protected $_sCharSet = null;

    /**
     * Shop version
     *
     * @var string
     */
    protected $_sVersion = null;

    /**
     * If current shop has demo version
     *
     * @var bool
     */
    protected $_blDemoVersion = null;

    /**
     * If current shop has demo shop
     *
     * @var bool
     */
    protected $_blDemoShop = null;

    /**
     * Display if newsletter must be displayed
     *
     * @var bool
     */
    protected $_iNewsStatus = null;

    /**
     * Shop logo
     *
     * @var string
     */
    protected $_sShopLogo = null;

    /**
     * Category ID
     *
     * @var string
     */
    protected $_sCategoryId = null;

    /**
     * Active category object.
     *
     * @var object
     */
    protected $_oClickCat = null;

    /**
     * Cache sign to enable/disable use of cache.
     *
     * @var bool
     */
    protected $_blIsCallForCache = false;

    /**
     * \OxidEsales\Eshop\Core\ViewConfig instance
     *
     * @var \OxidEsales\Eshop\Core\ViewConfig
     */
    protected $_oViewConf = null;

    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        // setting current view class name
        $this->_sThisAction = strtolower(get_class($this));

        if (!$this->_blIsComponent) {
            // assume that cached components does not affect this method ...
            $this->addGlobalParams();
        }
    }

    /**
     * Add parameters to controllers
     *
     * @param array $aParams view parameters array.
     */
    public function setViewParameters($aParams = null)
    {
        $this->_aViewParams = $aParams;
    }

    /**
     * Get parameters to controllers
     *
     * @param string $sKey parameter key
     *
     * @return string
     */
    public function getViewParameter($sKey)
    {
        return (isset($this->_aViewParams[$sKey])) ? $this->_aViewParams[$sKey] : Registry::getRequest()->getRequestEscapedParameter($sKey);
    }

    /**
     * Set cache sign to enable/disable use of cache
     *
     * @param bool $blIsCallForCache cache sign to enable/disable use of cache
     */
    public function setIsCallForCache($blIsCallForCache = null)
    {
        $this->_blIsCallForCache = $blIsCallForCache;
    }

    /**
     * Get cache sign to enable/disable use of cache
     *
     * @return bool
     */
    public function getIsCallForCache()
    {
        return $this->_blIsCallForCache;
    }

    /**
     * Returns view ID (currently it returns NULL)
     */
    public function getViewId()
    {
    }

    /**
     * Entry point to pass controller-specific data to the view.
     * @return string current view template file name
     */
    public function render()
    {
        return $this->getTemplateName();
    }

    /**
     * Sets and caches default parameters for shop object and returns it.
     *
     * Template variables:
     * <b>isdemoversion</b>, <b>shop</b>, <b>isdemoversion</b>,
     * <b>version</b>,
     * <b>urlsign</b>
     *
     * @param \OxidEsales\Eshop\Application\Model\Shop $oShop current shop object
     *
     * @return \OxidEsales\Eshop\Core\ViewConfig $oShop current shop object
     */
    public function addGlobalParams($oShop = null)
    {
        // by default we always display newsletter bar
        $this->_iNewsStatus = 1;

        // assigning shop to view config ..
        $oViewConf = $this->getViewConfig();
        if ($oShop) {
            $oViewConf->setViewShop($oShop, $this->_aViewData);
        }

        //sending all view to template engine
        $this->_aViewData['oView'] = $this;
        $this->_aViewData['oViewConf'] = $this->getViewConfig();

        return $oViewConf;
    }

    /**
     * Sets value to parameter used by template engine.
     *
     * @param string $sPara  name of parameter to pass
     * @param mixed  $sValue value of parameter
     */
    public function addTplParam($sPara, $sValue)
    {
        $this->_aViewData[$sPara] = $sValue;
    }

    /**
     * Returns belboon parameter
     *
     * @return string $sBelboon
     */
    public function getBelboonParam()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        if ($sBelboon = $session->getVariable('belboon')) {
            return $sBelboon;
        }
        if (($sBelboon = Registry::getRequest()->getRequestEscapedParameter('belboon'))) {
            $session->setVariable('belboon', $sBelboon);
        }

        return $sBelboon;
    }

    /**
     * Returns view config object
     *
     * @return \OxidEsales\Eshop\Core\ViewConfig
     */
    public function getViewConfig()
    {
        if ($this->_oViewConf === null) {
            $this->_oViewConf = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        }

        return $this->_oViewConf;
    }

    /**
     * Returns current view template file name
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->_sThisTemplate;
    }

    /**
     * Sets current view template file name
     *
     * @param string $sTemplate template name
     */
    public function setTemplateName($sTemplate)
    {
        $this->_sThisTemplate = $sTemplate;
    }

    /**
     * Current view class key setter.
     *
     * @param string $classKey current view class key
     */
    public function setClassKey($classKey)
    {
        $this->_sClass = $classKey;
        $this->classKey = $classKey;
    }

    /**
     * Returns class key of current view
     *
     * @return string
     */
    public function getClassKey()
    {
        return $this->classKey;
    }

    /**
     * Set current view action function name
     *
     * @param string $sFncName action function name
     */
    public function setFncName($sFncName)
    {
        $this->_sFnc = $sFncName;
    }

    /**
     * Returns name of current action function
     *
     * @return string
     */
    public function getFncName()
    {
        return $this->_sFnc;
    }

    /**
     * Set array of data that is passed to template engine - array( "varName" => "varValue")
     *
     * @param array $aViewData array of data that is passed to template engine
     */
    public function setViewData($aViewData = null)
    {
        $this->_aViewData = $aViewData;
    }

    /**
     * Get view data
     *
     * @return array
     */
    public function getViewData()
    {
        return $this->_aViewData;
    }

    /**
     * Get view data single array element
     *
     * @param string $sParamId view data array key
     *
     * @return mixed
     */
    public function getViewDataElement($sParamId = null)
    {
        $return = null;
        if ($sParamId && isset($this->_aViewData[$sParamId])) {
            $return = $this->_aViewData[$sParamId];
        }

        return $return;
    }

    /**
     * Set location of a executed class file
     *
     * @param string $sClassLocation location of a executed class file
     */
    public function setClassLocation($sClassLocation = null)
    {
        $this->_sClassLocation = $sClassLocation;
    }

    /**
     * Get location of a executed class file
     *
     * @return string
     */
    public function getClassLocation()
    {
        return $this->_sClassLocation;
    }

    /**
     * Set name of running class method
     *
     * @param string $sThisAction name of running class method
     */
    public function setThisAction($sThisAction = null)
    {
        $this->_sThisAction = $sThisAction;
    }

    /**
     * Get name of running class method
     *
     * @return string
     */
    public function getThisAction()
    {
        return $this->_sThisAction;
    }

    /**
     * Set parent object. If this is a component we will have our parent view here.
     * @param \OxidEsales\Eshop\Core\Controller\BaseController $oParent parent object
     */
    public function setParent($oParent = null)
    {
        $this->_oParent = $oParent;
    }

    /**
     * Get parent object
     *
     * @return \OxidEsales\Eshop\Core\Controller\BaseController|null
     */
    public function getParent()
    {
        return $this->_oParent;
    }

    /**
     * Set flag if this object is a component or not
     *
     * @param bool|null $blIsComponent flag if this object is a component
     */
    public function setIsComponent($blIsComponent = null)
    {
        $this->_blIsComponent = $blIsComponent;
    }

    /**
     * Get flag if this object is a component
     *
     * @return bool|null
     */
    public function getIsComponent()
    {
        return $this->_blIsComponent;
    }

    /**
     * Executes method (creates class and then executes). Returns executed
     * function result.
     *
     * @param string $sFunction name of function to execute
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException system component exception
     */
    public function executeFunction($sFunction)
    {
        // execute
        if ($sFunction && !self::$_blExecuted) {
            if (method_exists($this, $sFunction)) {
                $sNewAction = $this->$sFunction();
                self::$_blExecuted = true;
                ContainerFacade::dispatch(new AfterRequestProcessedEvent());

                if (isset($sNewAction)) {
                    $this->executeNewAction($sNewAction);
                }
            } elseif (!$this->_blIsComponent) {
                throw new \OxidEsales\Eshop\Core\Exception\RoutingException(
                    sprintf("Controller method is not accessible: %s::%s", self::class, $sFunction)
                );
            }
        }
    }

    /**
     * Formats header for new controller action
     *
     * Input example: "view_name?param1=val1&param2=val2" => "cl=view_name&param1=val1&param2=val2"
     *
     * @param string $sNewAction new action params
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException system component exception
     */
    protected function executeNewAction($sNewAction)
    {
        if ($sNewAction) {
            $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

            // page parameters is the part which goes after '?'
            $params = explode('?', $sNewAction);

            // action parameters is the part before '?'
            $pageParams = isset($params[1]) ? $params[1] : null;

            // looking for function name
            $params = explode('/', $params[0]);
            $className = $params[0];
            $resolvedClassName = \OxidEsales\Eshop\Core\Registry::getControllerClassNameResolver()->getClassNameById($className);
            $realClassName = $resolvedClassName ? \OxidEsales\Eshop\Core\Registry::getUtilsObject()->getClassName($resolvedClassName) : \OxidEsales\Eshop\Core\Registry::getUtilsObject()->getClassName($className);

            if (false === class_exists($realClassName)) {
                //If redirect tries to use a not existing class throw an exception.
                //we'll be redirected to start page directly.
                $exception =  new \OxidEsales\Eshop\Core\Exception\SystemComponentException();
                /** Use setMessage here instead of passing it in constructor in order to test exception message */
                $exception->setMessage('ERROR_MESSAGE_SYSTEMCOMPONENT_CLASSNOTFOUND' . ' ' . $className);
                $exception->setComponent($className);
                throw $exception;
            }

            $session = \OxidEsales\Eshop\Core\Registry::getSession();

            // building redirect path ...
            $header = ($className) ? "cl=$className&" : ''; // adding view name
            $header .= ($pageParams) ? "$pageParams&" : ''; // adding page params
            $header .= $session->sid(); // adding session Id

            $url = $myConfig->getCurrentShopUrl($this->isAdmin());

            $url = "{$url}index.php?{$header}";

            $url = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($url);

            if (\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive() && $seoUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($url)) {
                $url = $seoUrl;
            }

            $this->onExecuteNewAction();

            ContainerFacade::dispatch(new AfterRequestProcessedEvent());

            //#M341 do not add redirect parameter
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($url, (bool) Registry::getRequest()->getRequestEscapedParameter('redirected'), 302);
        }
    }

    /**
     * Method for overwriting if any additional actions on _executeNewAction is needed
     */
    protected function onExecuteNewAction()
    {
    }

    /**
     * Template variable getter. Returns additional params for url
     *
     * @return string
     */
    public function getAdditionalParams()
    {
        return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl('', false);
    }

    /**
     * Returns active charset
     *
     * @return string
     */
    public function getCharSet()
    {
        if ($this->_sCharSet == null) {
            $this->_sCharSet = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('charset');
        }

        return $this->_sCharSet;
    }

    /**
     * Returns shop version
     *
     * @return string
     */
    public function getShopVersion()
    {
        return ShopVersion::getVersion();
    }

    /**
     * Returns shop edition
     *
     * @return string
     */
    public function getShopEdition()
    {
        return (new Facts())->getEdition();
    }

    /**
     * Returns shop package info
     *
     * @return string
     */
    public function getPackageInfo()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getPackageInfo();
    }

    /**
     * Returns shop full edition
     *
     * @return string
     */
    public function getShopFullEdition()
    {
        $sEdition = $this->getShopEdition();
        $sFullEdition = "Community Edition";
        if ($sEdition == "PE") {
            $sFullEdition = "Professional Edition";
        }

        if ($sEdition == "EE") {
            $sFullEdition = "Enterprise Edition";
        }

        return $sFullEdition;
    }


    /**
     * Returns if current shop is demo version
     *
     * @return string
     */
    public function isDemoVersion()
    {
        if ($this->_blDemoVersion == null) {
            $this->_blDemoVersion = \OxidEsales\Eshop\Core\Registry::getConfig()->detectVersion() == 1;
        }

        return $this->_blDemoVersion;
    }

    /**
     * Returns if current shop is beta version.
     *
     * @return bool
     */
    public function isBetaVersion()
    {
        return (stripos($this->getShopVersion(), 'beta') !== false);
    }

    /**
     * Returns if current shop is release candidate version.
     *
     * @return bool
     */
    public function isRCVersion()
    {
        return (stripos($this->getShopVersion(), 'rc') !== false);
    }

    /**
     * Returns if current shop is demo shop
     *
     * @return string
     */
    public function isDemoShop()
    {
        if ($this->_blDemoShop == null) {
            $this->_blDemoShop = \OxidEsales\Eshop\Core\Registry::getConfig()->isDemoShop();
        }

        return $this->_blDemoShop;
    }

    /**
     * Template variable getter. Returns if newsletter can be displayed (for _right)
     *
     * @return integer
     */
    public function showNewsletter()
    {
        return $this->_iNewsStatus === null ? 1 : $this->_iNewsStatus;
    }

    /**
     * Sets if to show newsletter
     *
     * @param bool $blShow if TRUE - newsletter subscription box will be shown
     */
    public function setShowNewsletter($blShow)
    {
        $this->_iNewsStatus = $blShow;
    }

    /**
     * Returns active category set by categories component; if category is
     * not set by component - will create category object and will try to
     * load by id passed by request
     *
     * @return \OxidEsales\Eshop\Application\Model\Category
     */
    public function getActCategory()
    {
        // if active category is not set yet - trying to load it from request params
        // this may be usefull when category component was unable to load active category
        // and we still need some object to mount navigation info
        if ($this->_oClickCat === null) {
            $this->_oClickCat = false;
            $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
            if ($oCategory->load($this->getCategoryId())) {
                $this->_oClickCat = $oCategory;
            }
        }

        return $this->_oClickCat;
    }

    /**
     * Active category setter
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory active category
     */
    public function setActCategory($oCategory)
    {
        $this->_oClickCat = $oCategory;
    }

    /**
     * Get category ID
     *
     * @return string
     */
    public function getCategoryId()
    {
        if ($this->_sCategoryId == null && ($sCatId = Registry::getRequest()->getRequestEscapedParameter('cnid'))) {
            $this->_sCategoryId = $sCatId;
        }

        return $this->_sCategoryId;
    }

    /**
     * Category ID setter
     *
     * @param string $sCategoryId Id of category to cache
     */
    public function setCategoryId($sCategoryId)
    {
        $this->_sCategoryId = $sCategoryId;
    }

    /**
     * Returns a name of the view variable containing the error/exception messages
     */
    public function getErrorDestination()
    {
    }

    /**
     * Returns name of a view class, which will be active for an action
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getActionClassName()
    {
        return $this->getClassKey();
    }

    /**
     * Returns if shop is mall
     *
     * @return bool
     */
    public function isMall()
    {
        return false;
    }

    /**
     * Returns if page has rdfa
     *
     * @return bool
     */
    public function showRdfa()
    {
        return false;
    }

    /**
     * Returns session ID, but only in case it is needed to be included for widget calls.
     * This basically happens on session change,
     * when session cookie is not equals to the actual session ID.
     *
     * @return string|null
     */
    public function getSidForWidget()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $sid = null;
        if (!$session->isActualSidInCookie()) {
            $sid = $session->getId();
        }

        return $sid;
    }

    /**
     * Returns whether to show persistent parameter. Returns true as a default.
     *
     * @param string $persParamKey
     *
     * @return bool
     */
    public function showPersParam($persParamKey)
    {
        return true;
    }
}
