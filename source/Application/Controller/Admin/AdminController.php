<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\Eshop\Core\ShopVersion;
use oxDb;
use oxNavigationTree;
use oxShop;

/**
 * Main Controller class for admin area.
 */
class AdminController extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    /**
     * Fixed types - enums in database.
     *
     * @var array
     */
    protected $_aSumType = [
        0 => 'abs',
        1 => '%',
        2 => 'itm'
    ];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = null;

    /**
     * Override this in list class to show other tab from beginning
     * (default 0 - the first tab).
     *
     * @var int
     */
    protected $_iDefEdit = 0;

    /**
     * Navigation tree object
     *
     * @var oxnavigationtree
     */
    protected static $_oNaviTree = null;

    /**
     * Objects editing language (default 0).
     *
     * @var integer
     */
    protected $_iEditLang = 0;

    /**
     * Active shop title
     *
     * @var string
     */
    protected $_sShopTitle = " - ";

    /**
     * Shop Version
     *
     * @deprecated since v6.0.0-rc.2 (2017-08-23); Use  OxidEsales\Eshop\Core\ShopVersion::getVersion() instead.
     *
     * @var string
     */
    protected $_sShopVersion = null;

    /**
     * Shop dynamic pages url
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     *
     * @var string
     */
    protected $_sServiceUrl = null;

    /**
     * Session user rights
     *
     * @var string
     */
    protected static $_sAuthUserRights = null;

    /**
     * Active shop object
     *
     * @return
     */
    protected $_oEditShop = null;

    /**
     * Editable object id
     *
     * @var string
     */
    protected $_sEditObjectId = null;

    /**
     * Optional view id.
     *
     * @var string
     */
    protected $viewId = null;

    /**
     * Creates oxshop object and loads shop data, sets title of shop
     */
    public function __construct()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('blAdmin', true);
        $this->setAdminMode(true);

        if ($oShop = $this->_getEditShop($myConfig->getShopId())) {
            // passing shop info
            $this->_sShopTitle = $oShop->oxshops__oxname->getRawValue();
            $this->_sShopVersion = oxNew(ShopVersion::class)->getVersion();
        }
    }

    /**
     * Returns (cached) shop object
     *
     * @param object $sShopId shop id
     *
     * @return oxshop
     */
    protected function _getEditShop($sShopId)
    {
        if (!$this->_oEditShop) {
            $this->_oEditShop = $this->getConfig()->getActiveShop();
            if ($this->_oEditShop->getId() != $sShopId) {
                $oEditShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
                if ($oEditShop->load($sShopId)) {
                    $this->_oEditShop = $oEditShop;
                }
            }
        }

        return $this->_oEditShop;
    }

    /**
     * Sets some shop configuration parameters (such as language),
     * creates some list object (depends on subclass) and executes
     * parent method parent::Init().
     */
    public function init()
    {
        // authorization check
        if (!$this->_authorize()) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect('index.php?cl=login', true, 302);
            exit;
        }

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        // language handling
        $this->_iEditLang = $oLang->getEditLanguage();
        $oLang->setBaseLanguage();

        parent::init();

        $this->_aViewData['malladmin'] = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('malladmin');
    }

    /**
     * Sets some global parameters to Smarty engine (such as self link, etc.), returns
     * modified shop object.
     *
     * @param object $oShop Object to modify some parameters
     *
     * @return object
     */
    public function addGlobalParams($oShop = null)
    {
        $myConfig = $this->getConfig();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        $oShop = parent::addGlobalParams($oShop);

        // override cause of admin dir
        $sURL = $myConfig->getConfigParam('sShopURL') . $myConfig->getConfigParam('sAdminDir') . "/";

        if ($myConfig->getConfigParam('sAdminSSLURL')) {
            $sURL = $myConfig->getConfigParam('sAdminSSLURL');
        }

        $oViewConf = $this->getViewConfig();
        $oViewConf->setViewConfigParam('selflink', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($sURL . 'index.php?editlanguage=' . $this->_iEditLang, false));
        $oViewConf->setViewConfigParam('ajaxlink', str_replace('&amp;', '&', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($sURL . 'oxajax.php?editlanguage=' . $this->_iEditLang, false)));
        $oViewConf->setViewConfigParam('sServiceUrl', $this->getServiceUrl());

        // set langugae in admin
        $iDynInterfaceLanguage = $myConfig->getConfigParam('iDynInterfaceLanguage');
        //$this->_aViewData['adminlang'] = isset( $iDynInterfaceLanguage )?$iDynInterfaceLanguage:$myConfig->getConfigParam( 'iAdminLanguage' );
        $this->_aViewData['adminlang'] = isset($iDynInterfaceLanguage) ? $iDynInterfaceLanguage : $oLang->getTplLanguage();
        $this->_aViewData['charset'] = $this->getCharSet();

        //setting active currency object
        $this->_aViewData["oActCur"] = $myConfig->getActShopCurrencyObject();

        return $oShop;
    }

    /**
     * Returns service url protocol: "https" is admin works in ssl mode, "http" if no ssl
     *
     * @return string
     */
    protected function _getServiceProtocol()
    {
        return $this->getConfig()->isSsl() ? 'https' : 'http';
    }

    /**
     * Returns service URL
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     *
     * @param string $sLangAbbr language abbr.
     *
     * @return string
     */
    public function getServiceUrl($sLangAbbr = null)
    {
        if ($this->_sServiceUrl === null) {
            $sProtocol = $this->_getServiceProtocol();

            $editionSelector = new EditionSelector();
            $sUrl = $sProtocol . '://admin.oxid-esales.com/' . $editionSelector->getEdition() . '/';

            $sCountry = 'international';

            if (!$sLangAbbr) {
                $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
                $sLangAbbr = $oLang->getLanguageAbbr($oLang->getTplLanguage());
            }

            if ($sLangAbbr != "de") {
                $sLangAbbr = "en";
            }

            $this->_sServiceUrl = $sUrl . $this->_getShopVersionNr() . "/{$sCountry}/{$sLangAbbr}/";
        }

        return $this->_sServiceUrl;
    }

    /**
     * Returns shop version
     *
     * @deprecated since v6.0.0-rc.2 (2017-08-23); Use  OxidEsales\Eshop\Core\ShopVersion::getVersion() instead.
     *
     * @return string
     */
    protected function _getShopVersionNr()
    {
        return oxNew(ShopVersion::class)->getVersion();
    }

    /**
     * Sets-up navigation parameters
     *
     * @param string $sNode active view id
     */
    protected function _setupNavigation($sNode)
    {
        // navigation according to class
        if ($sNode) {
            $myAdminNavig = $this->getNavigation();

            // active tab
            $iActTab = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('actedit');
            $iActTab = $iActTab ? $iActTab : $this->_iDefEdit;

            $sActTab = $iActTab ? "&actedit=$iActTab" : '';

            // store navigation history
            $this->_addNavigationHistory($sNode);

            // list url
            $this->_aViewData['listurl'] = $myAdminNavig->getListUrl($sNode) . $sActTab;

            // edit url
            $this->_aViewData['editurl'] = $myAdminNavig->getEditUrl($sNode, $iActTab) . $sActTab;
        }
    }

    /**
     * Store navigation history parameters to cookie
     *
     * @param string $sNode active view id
     */
    protected function _addNavigationHistory($sNode)
    {
        $myUtilsServer = \OxidEsales\Eshop\Core\Registry::getUtilsServer();

        // store navigation history
        $aHistory = explode('|', $myUtilsServer->getOxCookie('oxidadminhistory'));
        if (!is_array($aHistory)) {
            $aHistory = [];
        }

        if (!in_array($sNode, $aHistory)) {
            $aHistory[] = $sNode;
        }

        $myUtilsServer->setOxCookie('oxidadminhistory', implode('|', $aHistory));
    }

    /**
     * Executes parent method parent::render(), passes configuration data to
     * Smarty engine.
     *
     * @return string
     */
    public function render()
    {
        $sReturn = parent::render();

        $myConfig = $this->getConfig();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        // sets up navigation data
        $this->_setupNavigation(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestControllerId());

        // active object id
        $sOxId = $this->getEditObjectId();
        $this->_aViewData['oxid'] = (!$sOxId) ? -1 : $sOxId;
        // add Sumtype to all templates
        $this->_aViewData['sumtype'] = $this->_aSumType;

        // active shop title
        $this->_aViewData['actshop'] = $this->_sShopTitle;
        $this->_aViewData["shopid"] = $myConfig->getShopId();

        // loading active shop
        if ($sActShopId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('actshop')) {
            // load object
            $this->_aViewData['actshopobj'] = $this->_getEditShop($sActShopId);
        }

        // add language data to all templates
        $this->_aViewData['actlang'] = $iLanguage = $oLang->getBaseLanguage();
        $this->_aViewData['editlanguage'] = $this->_iEditLang;
        $this->_aViewData['languages'] = $oLang->getLanguageArray($iLanguage);

        // setting maximum upload size
        list($this->_aViewData['iMaxUploadFileSize'], $this->_aViewData['sMaxFormattedFileSize']) = $this->_getMaxUploadFileInfo(@ini_get("upload_max_filesize"));

        // "save-on-tab"
        if (!isset($this->_aViewData['updatelist'])) {
            $this->_aViewData['updatelist'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('updatelist');
        }

        return $sReturn;
    }

    /**
     * Returns maximum allowed size of upload file and formatted size equivalent
     *
     * @param int  $iMaxFileSize recommended maximum size of file (normalu value is taken from php ini, otherwise sets 2MB)
     * @param bool $blFormatted  Return formated
     *
     * @return array
     */
    protected function _getMaxUploadFileInfo($iMaxFileSize, $blFormatted = false)
    {
        $iMaxFileSize = $iMaxFileSize ? $iMaxFileSize : '2M';

        // processing config
        $iMaxFileSize = trim($iMaxFileSize);
        $sParam = strtolower($iMaxFileSize{strlen($iMaxFileSize) - 1});
        switch ($sParam) {
            case 'g':
                $iMaxFileSize *= 1024;
            // no break
            case 'm':
                $iMaxFileSize *= 1024;
            // no break
            case 'k':
                $iMaxFileSize *= 1024;
        }

        // formatting
        $aMarkers = ['KB', 'MB', 'GB'];
        $sFormattedMaxSize = '';

        $iSize = floor($iMaxFileSize / 1024);
        while ($iSize && current($aMarkers)) {
            $sFormattedMaxSize = $iSize . " " . current($aMarkers);
            $iSize = floor($iSize / 1024);
            next($aMarkers);
        }

        return [$iMaxFileSize, $sFormattedMaxSize];
    }

    /**
     * Clears cache
     */
    public function save()
    {
        $this->resetContentCache();
    }

    /**
     * Reset output cache
     *
     * @param bool $blForceReset if true, forces reset
     */
    public function resetContentCache($blForceReset = null)
    {
        $blDeleteCacheOnLogout = $this->getConfig()->getConfigParam('blClearCacheOnLogout');
        if (!$blDeleteCacheOnLogout || $blForceReset) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->oxResetFileCache();
        }
    }

    /**
     * Resets counters values from cache. Resets price category articles, category articles,
     * vendor articles, manufacturer articles count.
     *
     * @param string $sCounterType counter type
     * @param string $sValue       reset value
     */
    public function resetCounter($sCounterType, $sValue = null)
    {
        $blDeleteCacheOnLogout = $this->getConfig()->getConfigParam('blClearCacheOnLogout');
        $myUtilsCount = \OxidEsales\Eshop\Core\Registry::getUtilsCount();

        if (!$blDeleteCacheOnLogout) {
            switch ($sCounterType) {
                case 'priceCatArticle':
                    $myUtilsCount->resetPriceCatArticleCount($sValue);
                    break;
                case 'catArticle':
                    $myUtilsCount->resetCatArticleCount($sValue);
                    break;
                case 'vendorArticle':
                    $myUtilsCount->resetVendorArticleCount($sValue);
                    break;
                case 'manufacturerArticle':
                    $myUtilsCount->resetManufacturerArticleCount($sValue);
                    break;
            }
            $this->_resetContentCache();
        }
    }

    /**
     * Resets cache.
     */
    protected function _resetContentCache()
    {
    }

    /**
     * Checks if current $sUserId user is not an admin and checks if user is able to be edited by logged in user.
     * This method does not perform full rights check.
     *
     * @param string $sUserId user id
     *
     * @return bool
     */
    protected function _allowAdminEdit($sUserId)
    {
        return true;
    }

    /**
     * Get english country name by country iso alpha 2 code
     *
     * @param string $sCountryCode Country code
     *
     * @return boolean
     */
    protected function _getCountryByCode($sCountryCode)
    {
        //default country
        $sCountry = 'international';

        if (!empty($sCountryCode)) {
            $aLangIds = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageIds();
            $iEnglishId = array_search("en", $aLangIds);
            if (false !== $iEnglishId) {
                $sViewName = getViewName("oxcountry", $iEnglishId);
                $sQ = "select oxtitle from {$sViewName} where oxisoalpha2 = :oxisoalpha2";
                // Value does not change that often, reading from slave is ok here (see ESDEV-3804 and ESDEV-3822).
                $sCountryName = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sQ, [
                    ':oxisoalpha2' => $sCountryCode
                ]);
                if ($sCountryName) {
                    $sCountry = $sCountryName;
                }
            } else {
                // handling when english language is deleted
                switch ($sCountryCode) {
                    case 'de':
                        return 'germany';
                    default:
                        return 'international';
                }
            }
        }

        return strtolower($sCountry);
    }

    /**
     * performs authorization of admin user
     *
     * @return boolean
     */
    protected function _authorize()
    {
        return ( bool ) (
            $this->getSession()->checkSessionChallenge()
            && count(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie())
            && \OxidEsales\Eshop\Core\Registry::getUtils()->checkAccessRights()
        );
    }

    /**
     * Returns navigation object
     *
     * @return oxnavigationtree
     */
    public function getNavigation()
    {
        if (self::$_oNaviTree == null) {
            self::$_oNaviTree = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class);
        }

        return self::$_oNaviTree;
    }

    /**
     * Current view ID getter helps to identify navigation position
     *
     * @return string
     */
    public function getViewId()
    {
        $viewId = is_null($this->viewId) ? strtolower($this->getControllerKey()) : $this->viewId;
        return $this->getNavigation()->getClassId($viewId);
    }

    /**
     * Changing active shop
     */
    public function chshp()
    {
        $sActShop = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('shp');
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("shp", $sActShop);
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('currentadminshop', $sActShop);
    }

    /**
     * Marks seo entires as expired.
     *
     * @param string $sShopId Shop id
     */
    public function resetSeoData($sShopId)
    {
        $aTypes = ['oxarticle', 'oxcategory', 'oxvendor', 'oxcontent', 'dynamic', 'oxmanufacturer'];
        $oEncoder = \OxidEsales\Eshop\Core\Registry::getSeoEncoder();
        foreach ($aTypes as $sType) {
            $oEncoder->markAsExpired(null, $sShopId, 1, null, "oxtype = '{$sType}'");
        }
    }

    /**
     * Returns id which is used for product preview in shop during administration
     *
     * @return string
     */
    public function getPreviewId()
    {
        return \OxidEsales\Eshop\Core\Registry::getUtils()->getPreviewId();
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function getEditObjectId()
    {
        if (null === ($sId = $this->_sEditObjectId)) {
            if (null === ($sId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid"))) {
                $sId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("saved_oxid");
            }
        }

        return $sId;
    }

    /**
     * Sets editable object id
     *
     * @param string $sId object id
     */
    public function setEditObjectId($sId)
    {
        $this->_sEditObjectId = $sId;
        $this->_aViewData["updatelist"] = 1;
    }

    /**
     * Returns true if editable object is new.
     *
     * @return bool
     */
    protected function isNewEditObject()
    {
        return '-1' === (string) $this->getEditObjectId();
    }

    /**
     * Get controller key also for chain extended class.
     *
     * @return null|string
     */
    protected function getControllerKey()
    {
        $actualClass = get_class($this);
        $controllerKey = \OxidEsales\Eshop\Core\Registry::getControllerClassNameResolver()->getIdByClassName($actualClass);
        if (is_null($controllerKey)) {
            //we might not have found a class key because class is a module chain extended class
            $controllerKey = \OxidEsales\Eshop\Core\Registry::getControllerClassNameResolver()->getIdByClassName($this->getShopParentClass());
        }
        return $controllerKey;
    }

    /**
     * Method to figure out \OxidEsales\Eshop class.
     *
     * @return string
     */
    protected function getShopParentClass()
    {
        $className = get_class($this); //actual class, might be shop class chain extended by module
        while ($className && !\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace($className)) {
            $className = get_parent_class($className);
        }
        return $className;
    }
}
