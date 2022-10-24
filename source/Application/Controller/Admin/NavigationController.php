<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Facts\Facts;

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

    /** @inheritdoc */
    public function render()
    {
        parent::render();
        $myUtilsServer = Registry::getUtilsServer();

        $sItem = Registry::getRequest()->getRequestEscapedParameter("item");
        $sItem = $sItem ? basename($sItem) : false;
        if (!$sItem) {
            $sItem = "nav_frame";
            $aFavorites = Registry::getRequest()->getRequestEscapedParameter("favorites");
            if (is_array($aFavorites)) {
                $myUtilsServer->setOxCookie('oxidadminfavorites', implode('|', $aFavorites));
            }
        } else {
            $oNavTree = $this->getNavigation();

            // set menu structure
            $this->_aViewData["menustructure"] = $oNavTree->getDomXml()->documentElement->childNodes;

            // version patch string
            $this->_aViewData["sVersion"] = ShopVersion::getVersion();

            //checking requirements if this is not nav frame reload
            if (!Registry::getRequest()->getRequestEscapedParameter("navReload")) {
                // #661 execute stuff we run each time when we start admin once
                if ('home' == $sItem) {
                    $this->_aViewData['aMessage'] = $this->doStartUpChecks();
                }
            } else {
                //removing reload param to force requirements checking next time
                Registry::getSession()->deleteVariable("navReload");
            }

            // favorite navigation
            $aFavorites = explode('|', (string)$myUtilsServer->getOxCookie('oxidadminfavorites'));

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
            $this->_aViewData["blOpenHistory"] = Registry::getRequest()->getRequestEscapedParameter('openHistory');
        }

        $blisMallAdmin = Registry::getSession()->getVariable('malladmin');
        $oShoplist = oxNew(\OxidEsales\Eshop\Application\Model\ShopList::class);
        if (!$blisMallAdmin) {
            // we only allow to see our shop
            $iShopId = Registry::getSession()->getVariable("actshop");
            $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
            $oShop->load($iShopId);
            $oShoplist->add($oShop);
        } else {
            $oShoplist->getIdTitleList();
        }

        $this->_aViewData['shoplist'] = $oShoplist;
        $this->_aViewData["shopURL"] = Registry::getConfig()->getShopURL();

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
        $this->_aViewData['listview'] = Registry::getRequest()->getRequestEscapedParameter('listview');
        $this->_aViewData['editview'] = Registry::getRequest()->getRequestEscapedParameter('editview');
        $this->_aViewData['actedit'] = Registry::getRequest()->getRequestEscapedParameter('actedit');
    }

    /**
     * Destroy session, redirects to admin login and clears cache
     */
    public function logout()
    {
        $session = Registry::getSession();
        $myConfig = Registry::getConfig();

        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->logout();

        // kill session
        $session->destroy();

        //resetting content cache if needed
        if ($myConfig->getConfigParam('blClearCacheOnLogout')) {
            $this->resetContentCache(true);
        }

        Registry::getUtils()->redirect('index.php', true, 302);
    }

    /**
     * Caches external url file locally, adds <base> tag with original url to load images and other links correcly
     */
    public function exturl()
    {
        $myUtils = Registry::getUtils();
        if ($sUrl = Registry::getRequest()->getRequestEscapedParameter("url")) {
            // Caching not allowed, redirecting
            $myUtils->redirect($sUrl, true, 302);
        }

        $myUtils->showMessageAndExit("");
    }

    /**
     * Every Time Admin starts we perform these checks
     * returns some messages if there is something to display
     *
     * @return array
     */
    protected function doStartUpChecks()
    {
        $messages = [];
        $session = Registry::getSession();

        if (Registry::getConfig()->getConfigParam('blCheckSysReq') !== false) {
            // check if system requirements are ok
            $oSysReq = oxNew(\OxidEsales\Eshop\Core\SystemRequirements::class);
            if (!$oSysReq->getSysReqStatus()) {
                $messages['warning'] = Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE');
                $messages['warning'] .= '<a href="?cl=sysreq&amp;stoken=' . $session->getSessionChallengeToken() . '" target="basefrm">';
                $messages['warning'] .= Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2') . '</a>';
            }
        } else {
            $messages['message'] = Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE_INACTIVE');
            $messages['message'] .= '<a href="?cl=sysreq&amp;stoken=' . $session->getSessionChallengeToken() . '" target="basefrm">';
            $messages['message'] .= Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2') . '</a>';
        }

        // version check
        if (Registry::getConfig()->getConfigParam('blCheckForUpdates')) {
            if ($sVersionNotice = $this->checkVersion()) {
                $messages['message'] .= $sVersionNotice;
            }
        }


        // check if setup dir is deleted
        if (file_exists(Registry::getConfig()->getConfigParam('sShopDir') . '/Setup/index.php')) {
            $messages['warning'] .= ((!empty($messages['warning'])) ? "<br>" : '') . Registry::getLang()->translateString('SETUP_DIRNOTDELETED_WARNING');
        }

        // check if config file is writable
        $sConfPath = Registry::getConfig()->getConfigParam('sShopDir') . "/config.inc.php";
        if (!is_readable($sConfPath) || is_writable($sConfPath)) {
            $messages['warning'] .= ((!empty($messages['warning'])) ? "<br>" : '') . Registry::getLang()->translateString('SETUP_CONFIGPERMISSIONS_WARNING');
        }

        return $messages;
    }

    /**
     * Checks if newer shop version available. If true - returns message
     *
     * @return string
     */
    protected function checkVersion()
    {
        $edition = (new Facts())->getEdition();
        $query = 'https://admin.oxid-esales.com/' . $edition . '/onlinecheck.php?getlatestversion';
        $latestVersion = Registry::getUtilsFile()->readRemoteFileAsString($query);
        if ($latestVersion) {
            $currentVersion = ShopVersion::getVersion();
            if (version_compare($currentVersion, $latestVersion, '<')) {
                return sprintf(
                    Registry::getLang()->translateString('NAVIGATION_NEW_VERSION_AVAILABLE'),
                    $currentVersion,
                    $latestVersion
                );
            }
        }
    }
}
