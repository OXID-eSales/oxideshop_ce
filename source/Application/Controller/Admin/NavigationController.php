<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReadyEvent;
use Symfony\Component\HttpFoundation\Response;

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

        $request = Registry::getRequest();
        $session = Registry::getSession();
        $utilsServer = Registry::getUtilsServer();

        $itemParam = $request->getRequestEscapedParameter('item');
        $item = $itemParam ? basename($itemParam) : false;

        if (!$item) {
            $item = 'nav_frame';
            $favoritesParam = $request->getRequestEscapedParameter('favorites');
            if (is_array($favoritesParam)) {
                $utilsServer->setOxCookie('oxidadminfavorites', implode('|', $favoritesParam));
            }
        } else {
            $navTree = $this->getNavigation();
            $this->_aViewData["menustructure"] = $navTree->getDomXml()->documentElement->childNodes;
            $this->_aViewData["sVersion"] = ShopVersion::getVersion();

            if (!$request->getRequestEscapedParameter("navReload")) {
                $templateExtension = ContainerFacade::getParameter('oxid_esales.templating.engine_template_extension');
                if ($item === "home.$templateExtension") {
                    $this->_aViewData['aMessage'] = $this->doStartUpChecks();
                }
            } else {
                $session->remove('navReload');
            }

            $favoritesCookie = $utilsServer->getOxCookie('oxidadminfavorites');
            $favorites = is_string($favoritesCookie) ? explode('|', $favoritesCookie) : [];
            if ($favorites) {
                $this->_aViewData["menufavorites"] = $navTree->getListNodes($favorites);
                $this->_aViewData["aFavorites"] = $favorites;
            }

            $historyCookie = $utilsServer->getOxCookie('oxidadminhistory');
            $history = is_string($historyCookie) ? explode('|', $historyCookie) : [];
            if ($history) {
                $this->_aViewData["menuhistory"] = $navTree->getListNodes($history);
            }

            $this->_aViewData["blOpenHistory"] = $request->getRequestEscapedParameter('openHistory');
        }

        $isMallAdmin = $session->getVariable('malladmin');
        $shopList = oxNew(\OxidEsales\Eshop\Application\Model\ShopList::class);
        if ($isMallAdmin) {
            $shopList->getIdTitleList();
        } else {
            $shopId = $session->getVariable('actshop');
            $shop = oxNew(Shop::class);
            $shop->load($shopId);
            $shopList->add($shop);
        }

        $this->_aViewData['shoplist'] = $shopList;
        $this->_aViewData["shopURL"] = Registry::getConfig()->getShopURL();

        return $item;
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

        ContainerFacade::dispatch(new ResponseReadyEvent(new Response('')));
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
                $messages['warning'] .= '<a href="?cl=sysreq&amp;stoken=' .
                    $session->getSessionChallengeToken() . '" target="basefrm">';
                $messages['warning'] .= Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2') . '</a>';
            }
        } else {
            $messages['message'] = Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE_INACTIVE');
            $messages['message'] .= '<a href="?cl=sysreq&amp;stoken=' .
                $session->getSessionChallengeToken() . '" target="basefrm">';
            $messages['message'] .= Registry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2') . '</a>';
        }

        // version check
        if (Registry::getConfig()->getConfigParam('blCheckForUpdates')) {
            if ($sVersionNotice = $this->checkVersion()) {
                $messages['message'] .= $sVersionNotice;
            }
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
        $query = 'https://admin.oxid-esales.com/' . $this->getShopEdition() . '/onlinecheck.php?getlatestversion';
        $latestVersion = Registry::getUtilsFile()->readRemoteFileAsString($query);
        if ($latestVersion) {
            $currentVersion = ShopVersion::getVersion();
            if (version_compare($currentVersion, $latestVersion, '<')) {
                return \sprintf(
                    Registry::getLang()->translateString('NAVIGATION_NEW_VERSION_AVAILABLE'),
                    $currentVersion,
                    $latestVersion
                );
            }
        }
    }
}
