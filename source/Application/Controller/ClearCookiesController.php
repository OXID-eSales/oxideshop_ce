<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use \OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AllCookiesRemovedEvent;

/**
 * CMS - loads pages and displays it
 */
class ClearCookiesController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current view template
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/info/clearcookies.tpl';

    /**
     * Executes parent::render(), passes template variables to
     * template engine and generates content. Returns the name
     * of template to render content::_sThisTemplate
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        parent::render();

        $this->_removeCookies();

        return $this->_sThisTemplate;
    }

    /**
     * Clears all cookies
     */
    protected function _removeCookies()
    {
        $oUtilsServer = Registry::getUtilsServer();
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $aCookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($aCookies as $sCookie) {
                $sRawCookie = explode('=', $sCookie);
                $oUtilsServer->setOxCookie(trim($sRawCookie[0]), '', time() - 10000, '/');
            }
        }
        $oUtilsServer->setOxCookie('language', '', time() - 10000, '/');
        $oUtilsServer->setOxCookie('displayedCookiesNotification', '', time() - 10000, '/');

        $this->dispatchEvent(new AllCookiesRemovedEvent());
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $aPath['title'] = Registry::getLang()->translateString('INFO_ABOUT_COOKIES', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
