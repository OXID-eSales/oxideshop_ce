<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\ShopVersion;

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
        $this->getConfig()->setConfigParam('blAdmin', true);
        $this->_sThisAction = "login";
    }

    /**
     * Executes parent method parent::render(), creates shop object, sets template parameters
     * and returns name of template file "login.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        // automatically redirect to SSL login
        if (!$myConfig->isSsl() && strpos($myConfig->getConfigParam('sAdminSSLURL'), 'https://') === 0) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($myConfig->getConfigParam('sAdminSSLURL'), false, 302);
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

        $aLanguages = $this->_getAvailableLanguages();
        $this->addTplParam("aLanguages", $aLanguages);

        // setting templates language to selected language id
        foreach ($aLanguages as $iKey => $oLang) {
            if ($aLanguages[$iKey]->selected) {
                \OxidEsales\Eshop\Core\Registry::getLang()->setTplLanguage($iKey);
                break;
            }
        }

        return "login.tpl";
    }

    /**
     * Sets configuration parameters related to current shop.
     */
    protected function setShopConfigParameters()
    {
        $myConfig = $this->getConfig();

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

        $sUser = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('user', true);
        $sPass = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pwd', true);
        $sProfile = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('profile');

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
        } catch (UserException $oEx) {
            $myUtilsView->addErrorToDisplay('LOGIN_ERROR');
            $oStr = getStr();
            $this->addTplParam('user', $oStr->htmlspecialchars($sUser));
            $this->addTplParam('pwd', $oStr->htmlspecialchars($sPass));
            $this->addTplParam('profile', $oStr->htmlspecialchars($sProfile));

            return;
        } catch (\OxidEsales\Eshop\Core\Exception\CookieException $oEx) {
            $myUtilsView->addErrorToDisplay('LOGIN_NO_COOKIE_SUPPORT');
            $oStr = getStr();
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
        $iLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("chlanguage");
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
    protected function _authorize()
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
    protected function _getAvailableLanguages()
    {
        $sDefLang = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('oxidadminlanguage');
        $sDefLang = $sDefLang ? $sDefLang : $this->_getBrowserLanguage();

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
    protected function _getBrowserLanguage()
    {
        return strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    }
}
