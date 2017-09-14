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

use oxRegistry;
use oxDb;
use oxSysRequirements;

/**
 * Administrator GUI navigation manager class.
 */
class NavigationController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Allowed host url
     *
     * @var string
     */
    protected $_sAllowedHost = "http://admin.oxid-esales.com";

    /**
     * Executes parent method parent::render(), generates menu HTML code,
     * passes data to Smarty engine, returns name of template file "nav_frame.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $myUtilsServer = \OxidEsales\Eshop\Core\Registry::getUtilsServer();

        $sItem = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("item");
        $sItem = $sItem ? basename($sItem) : false;
        if (!$sItem) {
            $sItem = "nav_frame.tpl";
            $aFavorites = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("favorites");
            if (is_array($aFavorites)) {
                $myUtilsServer->setOxCookie('oxidadminfavorites', implode('|', $aFavorites));
            }
        } else {
            $oNavTree = $this->getNavigation();

            // set menu structure
            $this->_aViewData["menustructure"] = $oNavTree->getDomXml()->documentElement->childNodes;

            // version patch string
            $this->_aViewData["sVersion"] = $this->_sShopVersion;

            //checking requirements if this is not nav frame reload
            if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("navReload")) {
                // #661 execute stuff we run each time when we start admin once
                if ('home.tpl' == $sItem) {
                    $this->_aViewData['aMessage'] = $this->_doStartUpChecks();
                }
            } else {
                //removing reload param to force requirements checking next time
                \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("navReload");
            }

            // favorite navigation
            $aFavorites = explode('|', $myUtilsServer->getOxCookie('oxidadminfavorites'));

            if (is_array($aFavorites) && count($aFavorites)) {
                $this->_aViewData["menufavorites"] = $oNavTree->getListNodes($aFavorites);
                $this->_aViewData["aFavorites"] = $aFavorites;
            }

            // history navigation
            $aHistory = explode('|', $myUtilsServer->getOxCookie('oxidadminhistory'));
            if (is_array($aHistory) && count($aHistory)) {
                $this->_aViewData["menuhistory"] = $oNavTree->getListNodes($aHistory);
            }

            // open history node ?
            $this->_aViewData["blOpenHistory"] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('openHistory');
        }

        $blisMallAdmin = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('malladmin');
        $oShoplist = oxNew(\OxidEsales\Eshop\Application\Model\ShopList::class);
        if (!$blisMallAdmin) {
            // we only allow to see our shop
            $iShopId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("actshop");
            $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
            $oShop->load($iShopId);
            $oShoplist->add($oShop);
        } else {
            $oShoplist->getIdTitleList();
        }

        $this->_aViewData['shoplist'] = $oShoplist;
        return $sItem;
    }

    /**
     * Changing active shop
     */
    public function chshp()
    {
        parent::chshp();

        // informing about basefrm parameters
        $this->_aViewData['loadbasefrm'] = true;
        $this->_aViewData['listview'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('listview');
        $this->_aViewData['editview'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editview');
        $this->_aViewData['actedit'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('actedit');
    }

    /**
     * Destroy session, redirects to admin login and clears cache
     */
    public function logout()
    {
        $mySession = $this->getSession();
        $myConfig = $this->getConfig();

        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->logout();

        // kill session
        $mySession->destroy();

        //resetting content cache if needed
        if ($myConfig->getConfigParam('blClearCacheOnLogout')) {
            $this->resetContentCache(true);
        }

        \OxidEsales\Eshop\Core\Registry::getUtils()->redirect('index.php', true, 302);
    }

    /**
     * Caches external url file locally, adds <base> tag with original url to load images and other links correcly
     */
    public function exturl()
    {
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        if ($sUrl = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("url")) {
            // Limit external url's only allowed host
            $myConfig = $this->getConfig();
            if ($myConfig->getConfigParam('blSendTechnicalInformationToOxid') && strpos($sUrl, $this->_sAllowedHost) === 0) {
                $sPath = $myConfig->getConfigParam('sCompileDir') . "/" . md5($sUrl) . '.html';
                if ($myUtils->getRemoteCachePath($sUrl, $sPath)) {
                    $oStr = getStr();
                    $sVersion = $myConfig->getVersion();
                    $sEdition = $myConfig->getFullEdition();
                    $sCurYear = date("Y");

                    // Get ceontent
                    $sOutput = file_get_contents($sPath);

                    // Fix base path
                    $sOutput = $oStr->preg_replace("/<\/head>/i", "<base href=\"" . dirname($sUrl) . '/' . "\"></head>\n  <!-- OXID eShop {$sEdition}, Version {$sVersion}, Shopping Cart System (c) OXID eSales AG 2003 - {$sCurYear} - http://www.oxid-esales.com -->", $sOutput);

                    // Fix self url's
                    $myUtils->showMessageAndExit($oStr->preg_replace("/href=\"#\"/i", 'href="javascript::void();"', $sOutput));
                }
            } else {
                // Caching not allowed, redirecting
                $myUtils->redirect($sUrl, true, 302);
            }
        }

        $myUtils->showMessageAndExit("");
    }

    /**
     * Every Time Admin starts we perform these checks
     * returns some messages if there is something to display
     *
     * @return string
     */
    protected function _doStartUpChecks()
    {
        $aMessage = [];

        if ($this->getConfig()->getConfigParam('blCheckSysReq') !== false) {
            // check if system reguirements are ok
            $oSysReq = new \OxidEsales\Eshop\Core\SystemRequirements();
            if (!$oSysReq->getSysReqStatus()) {
                $aMessage['warning'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE');
                $aMessage['warning'] .= '<a href="?cl=sysreq&amp;stoken=' . $this->getSession()->getSessionChallengeToken() . '" target="basefrm">';
                $aMessage['warning'] .= \OxidEsales\Eshop\Core\Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2') . '</a>';
            }
        } else {
            $aMessage['message'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE_INACTIVE');
            $aMessage['message'] .= '<a href="?cl=sysreq&amp;stoken=' . $this->getSession()->getSessionChallengeToken() . '" target="basefrm">';
            $aMessage['message'] .= \OxidEsales\Eshop\Core\Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2') . '</a>';
        }

        // version check
        if ($this->getConfig()->getConfigParam('blCheckForUpdates')) {
            if ($sVersionNotice = $this->_checkVersion()) {
                $aMessage['message'] .= $sVersionNotice;
            }
        }


        // check if setup dir is deleted
        if (file_exists($this->getConfig()->getConfigParam('sShopDir') . '/Setup/index.php')) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . \OxidEsales\Eshop\Core\Registry::getLang()->translateString('SETUP_DIRNOTDELETED_WARNING');
        }

        // check if updateApp dir is deleted or empty
        $sUpdateDir = $this->getConfig()->getConfigParam('sShopDir') . '/updateApp/';
        if (file_exists($sUpdateDir) && !(count(glob("$sUpdateDir/*")) === 0)) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . \OxidEsales\Eshop\Core\Registry::getLang()->translateString('UPDATEAPP_DIRNOTDELETED_WARNING');
        }

        // check if config file is writable
        $sConfPath = $this->getConfig()->getConfigParam('sShopDir') . "/config.inc.php";
        if (!is_readable($sConfPath) || is_writable($sConfPath)) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . \OxidEsales\Eshop\Core\Registry::getLang()->translateString('SETUP_CONFIGPERMISSIONS_WARNING');
        }

        return $aMessage;
    }

    /**
     * Checks if newer shop version available. If true - returns message
     *
     * @return string
     */
    protected function _checkVersion()
    {
        $edition = $this->getConfig()->getEdition();
        $query = 'http://admin.oxid-esales.com/' . $edition . '/onlinecheck.php?getlatestversion';
        if ($version = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->readRemoteFileAsString($query)) {
            // current version is older ..
            if (version_compare($this->getConfig()->getVersion(), $version) == '-1') {
                return sprintf(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('NAVIGATION_NEWVERSIONAVAILABLE'), $version);
            }
        }
    }
}
