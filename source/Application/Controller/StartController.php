<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * Starting shop page.
 * Shop starter, manages starting visible articles, etc.
 */
class StartController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * List display type
     *
     * @var string
     */
    protected $_sListDisplayType = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/shop/start';

    /**
     * Start page meta description CMS ident
     *
     * @var string
     */
    protected $_sMetaDescriptionIdent = 'oxstartmetadescription';

    /**
     * Start page meta keywords CMS ident
     *
     * @var string
     */
    protected $_sMetaKeywordsIdent = 'oxstartmetakeywords';

    /**
     * Are actions on
     *
     * @var bool
     */
    protected $_blLoadActions = null;

    /**
     * Newest article list
     *
     * @var array
     */
    protected $_aNewArticleList = null;

    /**
     * Sign if to load and show top5articles action
     *
     * @var bool
     */
    protected $_blTop5Action = true;

    /**
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Executes parent::render(), loads action articles
     * (oxarticlelist::loadActionArticles()). Returns name of
     * template file to render.
     *
     * @return  string  cuurent template file name
     */
    public function render()
    {
        if (Registry::getRequest()->getRequestEscapedParameter('showexceptionpage') == '1') {
            return 'message/exception';
        }
        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns if actions are ON
     *
     * @return string
     */
    protected function getLoadActionsParam()
    {
        if ($this->_blLoadActions === null) {
            $this->_blLoadActions = false;
            if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadAktion')) {
                $this->_blLoadActions = true;
            }
        }

        return $this->_blLoadActions;
    }

    /**
     * Template variable getter. Returns newest article list
     *
     * @return array
     */
    public function getNewestArticles()
    {
        if ($this->_aNewArticleList === null) {
            $this->_aNewArticleList = [];
            if ($this->getLoadActionsParam()) {
                // newest articles
                $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $oArtList->loadNewestArticles();
                if ($oArtList->count()) {
                    $this->_aNewArticleList = $oArtList;
                }
            }
        }

        return $this->_aNewArticleList;
    }

    /**
     * Returns SEO suffix for page title
     *
     * @return string
     */
    public function getTitleSuffix()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveShop()->oxshops__oxstarttitle->value;
    }

    /**
     * Returns view canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        if (\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive() && ($oViewConf = $this->getViewConfig())) {
            return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareCanonicalUrl($oViewConf->getHomeLink());
        }
    }

    /**
     * Returns active banner list
     *
     * @return \OxidEsales\Eshop\Application\Model\ActionList|null
     */
    public function getBanners()
    {
        $oBannerList = null;

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadAktion')) {
            $oBannerList = oxNew(\OxidEsales\Eshop\Application\Model\ActionList::class);
            $oBannerList->loadBanners();
        }

        return $oBannerList;
    }

    /**
     * Returns manufacturer list for manufacturer slider
     *
     * @return array|null
     */
    public function getManufacturerForSlider()
    {
        $oList = null;

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadManufacturerTree')) {
            $oList = $this->getManufacturerlist();
        }

        return $oList;
    }
}
