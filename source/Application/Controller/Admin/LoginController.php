<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Exception\CookieException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\Str;

/**
 * Administrator login form.
 * Performs administrator login form data collection.
 */
class LoginController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /** Login page view id. */
    const VIEW_ID = 'login';

    /**
     * Sets value for _sThisAction to "login".
     */
    public function __construct()
    {
        \OxidEsales\Eshop\Core\Registry::getConfig()->setConfigParam('blAdmin', true);
        $this->_sThisAction = "login";
    }

    /**
     * Executes parent method parent::render(), creates shop object, sets template parameters
     * and returns name of template file "login".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        // automatically redirect to SSL login
        if (!$myConfig->isSsl() && strpos((string)$myConfig->getConfigParam('sAdminSSLURL'), 'https://') === 0) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect((string)$myConfig->getConfigParam('sAdminSSLURL'), false, 302);
        }

        //resets user once on this screen.
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->logout();

        \OxidEsales\Eshop\Core\Controller\BaseController::render();

        $this->setShopConfigParameters();

        if ($myConfig->isDemoShop()) {
            // demo
            $this->addTplParam("user", "admin");
            $this->addTplParam("pwd", "admin");
        }
        //#533 user profile
        $this->addTplParam("profiles", \OxidEsales\Eshop\Core\Registry::getUtils()->loadAdminProfile($myConfig->getConfigParam('aInterfaceProfiles')));

        $aLanguages = $this->getAvailableLanguages();
        $this->addTplParam("aLanguages", $aLanguages);

        // setting templates language to selected language id
        foreach ($aLanguages as $iKey => $oLang) {
            if ($aLanguages[$iKey]->selected) {
                \OxidEsales\Eshop\Core\Registry::getLang()->setTplLanguage($iKey);
                break;
            }
        }

        return "login";
    }

    /**
     * Sets configuration parameters related to current shop.
     */
    protected function setShopConfigParameters()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $oBaseShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        $oBaseShop->load($myConfig->getBaseShopId());
        $this->getViewConfig()->setViewConfigParam('sShopVersion', oxNew(ShopVersion::class)->getVersion());
    }

    /**
     * Checks user login data, on success returns "admin_start".
     *
     * @return mixed
     */
    public function checklogin()
    {
        $myUtilsServer = \OxidEsales\Eshop\Core\Registry::getUtilsServer();
        $myUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();

        $sUser = Registry::getRequest()->getRequestParameter('user');
        $sPass = Registry::getRequest()->getRequestParameter('pwd');
        $sProfile = Registry::getRequest()->getRequestEscapedParameter('profile');

        try { // trying to login
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $adminProfiles = $session->getVariable("aAdminProfiles");
            $session->initNewSession();
            $session->setVariable("aAdminProfiles", $adminProfiles);

            /** @var \OxidEsales\Eshop\Application\Model\User $oUser */
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->login($sUser, $sPass);

            if ($oUser->oxuser__oxrights->value === 'user') {
                throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOVALIDLOGIN');
            }

            $iSubshop = (int) $oUser->oxuser__oxrights->value;
            if ($iSubshop) {
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("shp", $iSubshop);
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('currentadminshop', $iSubshop);
                \OxidEsales\Eshop\Core\Registry::getConfig()->setShopId($iSubshop);
            }
        } catch (UserException|CookieException $oEx) {
            $myUtilsView->addErrorToDisplay($oEx);
            $oStr = Str::getStr();
            $this->addTplParam('user', $oStr->htmlspecialchars($sUser));
            $this->addTplParam('pwd', $oStr->htmlspecialchars($sPass));
            $this->addTplParam('profile', $oStr->htmlspecialchars($sProfile));

            return;
        } catch (\OxidEsales\Eshop\Core\Exception\ConnectionException $oEx) {
            $myUtilsView->addErrorToDisplay($oEx);
        }

        //execute onAdminLogin() event
        $oEvenHandler = oxNew(\OxidEsales\Eshop\Core\SystemEventHandler::class);
        $oEvenHandler->onAdminLogin(\OxidEsales\Eshop\Core\Registry::getConfig()->getShopId());

        // #533
        if (isset($sProfile)) {
            $aProfiles = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("aAdminProfiles");
            if ($aProfiles && isset($aProfiles[$sProfile])) {
                // setting cookie to store last locally used profile
                $myUtilsServer->setOxCookie("oxidadminprofile", $sProfile . "@" . implode("@", $aProfiles[$sProfile]), time() + 31536000, "/");
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("profile", $aProfiles[$sProfile]);
            }
        } else {
            //deleting cookie info, as setting profile to default
            $myUtilsServer->setOxCookie("oxidadminprofile", "", time() - 3600, "/");
        }

        // languages
        $iLang = Registry::getRequest()->getRequestEscapedParameter("chlanguage");
        $aLanguages = \OxidEsales\Eshop\Core\Registry::getLang()->getAdminTplLanguageArray();
        if (!isset($aLanguages[$iLang])) {
            $iLang = key($aLanguages);
        }

        $myUtilsServer->setOxCookie("oxidadminlanguage", $aLanguages[$iLang]->abbr, time() + 31536000, "/");

        //P
        //\OxidEsales\Eshop\Core\Registry::getSession()->setVariable( "blAdminTemplateLanguage", $iLang );
        \OxidEsales\Eshop\Core\Registry::getLang()->setTplLanguage($iLang);

        return "admin_start";
    }

    /**
     * Users are always authorized to use login page.
     * Rewrites authorization method.
     *
     * @return boolean
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * Current view ID getter
     *
     * @return string
     */
    public function getViewId()
    {
        return self::VIEW_ID;
    }

    /**
     * Get available admin interface languages
     *
     * @return array
     */
    protected function getAvailableLanguages()
    {
        $sDefLang = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('oxidadminlanguage');
        $sDefLang = $sDefLang ? $sDefLang : $this->getBrowserLanguage();

        $aLanguages = \OxidEsales\Eshop\Core\Registry::getLang()->getAdminTplLanguageArray();
        foreach ($aLanguages as $oLang) {
            $oLang->selected = ($sDefLang == $oLang->abbr) ? 1 : 0;
        }

        return $aLanguages;
    }

    /**
     * Get detected user browser language abbervation
     *
     * @return string
     */
    protected function getBrowserLanguage()
    {
        return strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    }
}
