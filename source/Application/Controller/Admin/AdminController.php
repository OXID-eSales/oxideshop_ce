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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use oxRegistry;
use oxDb;
use oxNavigationTree;
use oxShop;

/**
 * @internal This class should not be directly extended, instead of it oxAdminView class should be used.
 */
class AdminController extends \oxView
{
    /**
     * Fixed types - enums in database.
     *
     * @var array
     */
    protected $_aSumType = array(
        0 => 'abs',
        1 => '%',
        2 => 'itm'
    );

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
            $this->_sShopVersion = $oShop->oxshops__oxversion->value;
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
                $oEditShop = oxNew('oxShop');
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
        $myConfig = $this->getConfig();

        // authorization check
        if (!$this->_authorize()) {
            oxRegistry::getUtils()->redirect('index.php?cl=login', true, 302);
            exit;
        }

        $oLang = oxRegistry::getLang();

        // language handling
        $this->_iEditLang = $oLang->getEditLanguage();
        $oLang->setBaseLanguage();

        parent::init();

        $this->_aViewData['malladmin'] = oxRegistry::getSession()->getVariable('malladmin');
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
        $mySession = $this->getSession();
        $myConfig = $this->getConfig();
        $oLang = oxRegistry::getLang();

        $oShop = parent::addGlobalParams($oShop);

        // override cause of admin dir
        $sURL = $myConfig->getConfigParam('sShopURL') . $myConfig->getConfigParam('sAdminDir') . "/";

        if ($myConfig->getConfigParam('sAdminSSLURL')) {
            $sURL = $myConfig->getConfigParam('sAdminSSLURL');
        }

        $oViewConf = $this->getViewConfig();
        $oViewConf->setViewConfigParam('selflink', oxRegistry::get("oxUtilsUrl")->processUrl($sURL . 'index.php?editlanguage=' . $this->_iEditLang, false));
        $oViewConf->setViewConfigParam('ajaxlink', str_replace('&amp;', '&', oxRegistry::get("oxUtilsUrl")->processUrl($sURL . 'oxajax.php?editlanguage=' . $this->_iEditLang, false)));
        $oViewConf->setViewConfigParam('sServiceUrl', $this->getServiceUrl());
        $oViewConf->setViewConfigParam('blLoadDynContents', $myConfig->getConfigParam('blLoadDynContents'));
        $oViewConf->setViewConfigParam('sShopCountry', $myConfig->getConfigParam('sShopCountry'));

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

            $sCountry = $this->_getCountryByCode($this->getConfig()->getConfigParam('sShopCountry'));

            if (!$sLangAbbr) {
                $oLang = oxRegistry::getLang();
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
     * @return string
     */
    protected function _getShopVersionNr()
    {
        $myConfig = $this->getConfig();

        if ($sShopID = $myConfig->getShopId()) {
            $sQ = "select oxversion from oxshops where oxid = '$sShopID' ";
            // Value does not change that often, reading from slave is ok here (see ESDEV-3804 and ESDEV-3822).
            $sVersion = oxDb::getDb()->getOne($sQ);
        }

        return trim(preg_replace("/(^[^0-9]+)(.+)$/", "$2", $sVersion));
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
            $iActTab = oxRegistry::getConfig()->getRequestParameter('actedit');
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
        $myUtilsServer = oxRegistry::get("oxUtilsServer");

        // store navigation history
        $aHistory = explode('|', $myUtilsServer->getOxCookie('oxidadminhistory'));
        if (!is_array($aHistory)) {
            $aHistory = array();
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
        $oLang = oxRegistry::getLang();

        // sets up navigation data
        $this->_setupNavigation(oxRegistry::getConfig()->getRequestParameter('cl'));

        // active object id
        $sOxId = $this->getEditObjectId();
        $this->_aViewData['oxid'] = (!$sOxId) ? -1 : $sOxId;
        // add Sumtype to all templates
        $this->_aViewData['sumtype'] = $this->_aSumType;

        // active shop title
        $this->_aViewData['actshop'] = $this->_sShopTitle;
        $this->_aViewData["shopid"] = $myConfig->getShopId();

        // loading active shop
        if ($sActShopId = oxRegistry::getSession()->getVariable('actshop')) {
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
            $this->_aViewData['updatelist'] = oxRegistry::getConfig()->getRequestParameter('updatelist');
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
        $aMarkers = array('KB', 'MB', 'GB');
        $sFormattedMaxSize = '';

        $iSize = floor($iMaxFileSize / 1024);
        while ($iSize && current($aMarkers)) {
            $sFormattedMaxSize = $iSize . " " . current($aMarkers);
            $iSize = floor($iSize / 1024);
            next($aMarkers);
        }

        return array($iMaxFileSize, $sFormattedMaxSize);
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
            oxRegistry::getUtils()->oxResetFileCache();
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
        $myUtilsCount = oxRegistry::get("oxUtilsCount");

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
        $myConfig = $this->getConfig();

        //default country
        $sCountry = 'international';

        if (!empty($sCountryCode)) {
            $aLangIds = oxRegistry::getLang()->getLanguageIds();
            $iEnglishId = array_search("en", $aLangIds);
            if (false !== $iEnglishId) {
                $sViewName = getViewName("oxcountry", $iEnglishId);
                $sQ = "select oxtitle from {$sViewName} where oxisoalpha2 = " . oxDb::getDb()->quote($sCountryCode);
                // Value does not change that often, reading from slave is ok here (see ESDEV-3804 and ESDEV-3822).
                $sCountryName = oxDb::getDb()->getOne($sQ);
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
            && count(oxRegistry::get("oxUtilsServer")->getOxCookie())
            && oxRegistry::getUtils()->checkAccessRights()
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
            self::$_oNaviTree = oxNew('oxnavigationtree');
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
        $sClassName = is_null($this->viewId) ? strtolower(get_class($this)) : $this->viewId;

        return $this->getNavigation()->getClassId($sClassName);
    }

    /**
     * Changing active shop
     */
    public function chshp()
    {
        $sActShop = oxRegistry::getConfig()->getRequestParameter('shp');
        oxRegistry::getSession()->setVariable("shp", $sActShop);
        oxRegistry::getSession()->setVariable('currentadminshop', $sActShop);
    }

    /**
     * Marks seo entires as expired.
     *
     * @param string $sShopId Shop id
     */
    public function resetSeoData($sShopId)
    {
        $aTypes = array('oxarticle', 'oxcategory', 'oxvendor', 'oxcontent', 'dynamic', 'oxmanufacturer');
        $oEncoder = oxRegistry::get("oxSeoEncoder");
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
        return oxRegistry::getUtils()->getPreviewId();
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function getEditObjectId()
    {
        if (null === ($sId = $this->_sEditObjectId)) {
            if (null === ($sId = oxRegistry::getConfig()->getRequestParameter("oxid"))) {
                $sId = oxRegistry::getSession()->getVariable("saved_oxid");
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
}
