<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxRegistry;

/**
 * Shop news window.
 * Arranges news texts. OXID eShop -> (click on News box on left side).
 * @deprecated 6.5.6 "News" feature will be removed completely
 */
class NewsController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Newslist
     *
     * @var object
     */
    protected $_oNewsList = null;
    /**
     * Current class login template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/info/news.tpl';

    /**
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;


    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Number of possible pages.
     *
     * @var integer
     */
    protected $_iCntPages = null;

    /**
     * Template variable getter. Returns newslist
     *
     * @return object
     */
    public function getNews()
    {
        if ($this->_oNewsList === null) {
            $this->_oNewsList = false;

            $iPerPage = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
            $iPerPage = $iPerPage ? $iPerPage : 10;

            $oActNews = oxNew(\OxidEsales\Eshop\Application\Model\NewsList::class);

            if ($iCnt = $oActNews->getCount()) {
                $this->_iCntPages = ceil($iCnt / $iPerPage);
                $oActNews->loadNews($this->getActPage() * $iPerPage, $iPerPage);
                $this->_oNewsList = $oActNews;
            }
        }

        return $this->_oNewsList;
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

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iBaseLanguage = $oLang->getBaseLanguage();
        $sTranslatedString = $oLang->translateString('LATEST_NEWS_AND_UPDATES_AT', $iBaseLanguage, false);

        $aPath['title'] = $sTranslatedString . ' ' . $this->getConfig()->getActiveShop()->oxshops__oxname->value;
        $aPath['link'] = $this->getLink();

        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = false;
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
    }

    /**
     * Page title
     *
     * @return string
     */
    public function getTitle()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iBaseLanguage = $oLang->getBaseLanguage();
        $sTranslatedString = $oLang->translateString('LATEST_NEWS_AND_UPDATES_AT', $iBaseLanguage, false);

        return $sTranslatedString . ' ' . $this->getConfig()->getActiveShop()->oxshops__oxname->value;
    }
}
