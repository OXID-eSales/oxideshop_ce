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

use OxidEsales\Eshop\Application\Model\User;
use oxRegistry;
use oxView;
use oxCookieException;
use oxUserException;
use oxConnectionException;

/**
 * Administrator login form.
 * Performs administrator login form data collection.
 */
class LoginController extends \oxAdminView
{
    /** Login page view id. */
    CONST VIEW_ID = 'login';

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
            oxRegistry::getUtils()->redirect($myConfig->getConfigParam('sAdminSSLURL'), false, 302);
        }

        //resets user once on this screen.
        $oUser = oxNew("oxUser");
        $oUser->logout();

        oxView::render();

        $this->setShopConfigParameters();

        if ($myConfig->isDemoShop()) {
            // demo
            $this->addTplParam("user", "admin");
            $this->addTplParam("pwd", "admin");
        }
        //#533 user profile
        $this->addTplParam("profiles", oxRegistry::getUtils()->loadAdminProfile($myConfig->getConfigParam('aInterfaceProfiles')));

        $aLanguages = $this->_getAvailableLanguages();
        $this->addTplParam("aLanguages", $aLanguages);

        // setting templates language to selected language id
        foreach ($aLanguages as $iKey => $oLang) {
            if ($aLanguages[$iKey]->selected) {
                oxRegistry::getLang()->setTplLanguage($iKey);
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

        $oBaseShop = oxNew("oxShop");
        $oBaseShop->load($myConfig->getBaseShopId());
        $sVersion = $oBaseShop->oxshops__oxversion->value;
        $this->getViewConfig()->setViewConfigParam('sShopVersion', $sVersion);
    }

    /**
     * Checks user login data, on success returns "admin_start".
     *
     * @return mixed
     */
    public function checklogin()
    {
        $myUtilsServer = oxRegistry::get("oxUtilsServer");
        $myUtilsView = oxRegistry::get("oxUtilsView");

        $sUser = oxRegistry::getConfig()->getRequestParameter('user', true);
        $sPass = oxRegistry::getConfig()->getRequestParameter('pwd', true);
        $sProfile = oxRegistry::getConfig()->getRequestParameter('profile');

        try { // trying to login
            /** @var oxUser $oUser */
            $oUser = oxNew(User::class);
            $oUser->login($sUser, $sPass);
            $iSubshop = (int) $oUser->oxuser__oxrights->value;
            if ($iSubshop) {
                oxRegistry::getSession()->setVariable("shp", $iSubshop);
                oxRegistry::getSession()->setVariable('currentadminshop', $iSubshop);
                oxRegistry::getConfig()->setShopId($iSubshop);
            }
        } catch (oxUserException $oEx) {
            $myUtilsView->addErrorToDisplay('LOGIN_ERROR');
            $oStr = getStr();
            $this->addTplParam('user', $oStr->htmlspecialchars($sUser));
            $this->addTplParam('pwd', $oStr->htmlspecialchars($sPass));
            $this->addTplParam('profile', $oStr->htmlspecialchars($sProfile));

            return;
        } catch (oxCookieException $oEx) {
            $myUtilsView->addErrorToDisplay('LOGIN_NO_COOKIE_SUPPORT');
            $oStr = getStr();
            $this->addTplParam('user', $oStr->htmlspecialchars($sUser));
            $this->addTplParam('pwd', $oStr->htmlspecialchars($sPass));
            $this->addTplParam('profile', $oStr->htmlspecialchars($sProfile));

            return;
        } catch (oxConnectionException $oEx) {
            $myUtilsView->addErrorToDisplay($oEx);
        }

        // success
        oxRegistry::getUtils()->logger("login successful");

        //execute onAdminLogin() event
        $oEvenHandler = oxNew("oxSystemEventHandler");
        $oEvenHandler->onAdminLogin(oxRegistry::getConfig()->getShopId());

        // #533
        if (isset($sProfile)) {
            $aProfiles = oxRegistry::getSession()->getVariable("aAdminProfiles");
            if ($aProfiles && isset($aProfiles[$sProfile])) {
                // setting cookie to store last locally used profile
                $myUtilsServer->setOxCookie("oxidadminprofile", $sProfile . "@" . implode("@", $aProfiles[$sProfile]), time() + 31536000, "/");
                oxRegistry::getSession()->setVariable("profile", $aProfiles[$sProfile]);
            }
        } else {
            //deleting cookie info, as setting profile to default
            $myUtilsServer->setOxCookie("oxidadminprofile", "", time() - 3600, "/");
        }

        // languages
        $iLang = oxRegistry::getConfig()->getRequestParameter("chlanguage");
        $aLanguages = oxRegistry::getLang()->getAdminTplLanguageArray();
        if (!isset($aLanguages[$iLang])) {
            $iLang = key($aLanguages);
        }

        $myUtilsServer->setOxCookie("oxidadminlanguage", $aLanguages[$iLang]->abbr, time() + 31536000, "/");

        //P
        //oxRegistry::getSession()->setVariable( "blAdminTemplateLanguage", $iLang );
        oxRegistry::getLang()->setTplLanguage($iLang);

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
        $sDefLang = oxRegistry::get("oxUtilsServer")->getOxCookie('oxidadminlanguage');
        $sDefLang = $sDefLang ? $sDefLang : $this->_getBrowserLanguage();

        $aLanguages = oxRegistry::getLang()->getAdminTplLanguageArray();
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
