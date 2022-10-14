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
     * Top article list (OXTOPSTART)
     *
     * @var array
     */
    protected $_aTopArticleList = null;

    /**
     * Newest article list
     *
     * @var array
     */
    protected $_aNewArticleList = null;

    /**
     * First article (OXFIRSTSTART)
     *
     * @var object
     */
    protected $_oFirstArticle = null;

    /**
     * Category offer article (OXCATOFFER)
     *
     * @var object
     */
    protected $_oCatOfferArticle = null;

    /**
     * Category offer article list (OXCATOFFER)
     *
     * @var array
     */
    protected $_oCatOfferArtList = null;

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
     * Returns current view meta data
     * If $sMeta parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sMeta     category path
     * @param int    $iLength   max length of result, -1 for no truncation
     * @param bool   $blDescTag if true - performs additional duplicate cleaning
     *
     * @return string
     */
    protected function prepareMetaDescription($sMeta, $iLength = 1024, $blDescTag = false)
    {
        if (
            !$sMeta &&
            \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadAktion') &&
            $oArt = $this->getFirstArticle()
        ) {
            $oDescField = $oArt->getLongDescription();
            $sMeta = $oArt->oxarticles__oxtitle->value . ' - ' . $oDescField->value;
        }

        return parent::prepareMetaDescription($sMeta, $iLength, $blDescTag);
    }

    /**
     * Returns current view keywords seperated by comma
     * If $sKeywords parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sKeywords               data to use as keywords
     * @param bool   $blRemoveDuplicatedWords remove duplicated words
     *
     * @return string
     */
    protected function prepareMetaKeyword($sKeywords, $blRemoveDuplicatedWords = true)
    {
        if (
            !$sKeywords &&
            \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadAktion') &&
            $oArt = $this->getFirstArticle()
        ) {
            $oDescField = $oArt->getLongDescription();
            $sKeywords = $oDescField->value;
        }

        return parent::prepareMetaKeyword($sKeywords, $blRemoveDuplicatedWords);
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
     * Template variable getter. Returns Top article list (OXTOPSTART)
     *
     * @return array
     */
    public function getTopArticleList()
    {
        if ($this->_aTopArticleList === null) {
            $this->_aTopArticleList = false;
            if ($this->getLoadActionsParam()) {
                // start list
                $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $oArtList->loadActionArticles('OXTOPSTART');
                if ($oArtList->count()) {
                    $this->_aTopArticleList = $oArtList;
                }
            }
        }

        return $this->_aTopArticleList;
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
     * Template variable getter. Returns first article
     *
     * @return object
     */
    public function getFirstArticle()
    {
        if ($this->_oFirstArticle === null) {
            $this->_oFirstArticle = false;
            if ($this->getLoadActionsParam()) {
                // top articles ( big one )
                $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $oArtList->loadActionArticles('OXFIRSTSTART');
                if ($oArtList->count()) {
                    $this->_oFirstArticle = $oArtList->current();
                }
            }
        }

        return $this->_oFirstArticle;
    }

    /**
     * Template variable getter. Returns category offer article (OXCATOFFER)
     *
     * @return object
     */
    public function getCatOfferArticle()
    {
        if ($this->_oCatOfferArticle === null) {
            $this->_oCatOfferArticle = false;
            if ($oArtList = $this->getCatOfferArticleList()) {
                $this->_oCatOfferArticle = $oArtList->current();
            }
        }

        return $this->_oCatOfferArticle;
    }

    /**
     * Template variable getter. Returns category offer article list (OXCATOFFER)
     *
     * @return array
     */
    public function getCatOfferArticleList()
    {
        if ($this->_oCatOfferArtList === null) {
            $this->_oCatOfferArtList = [];
            if ($this->getLoadActionsParam()) {
                // "category offer" articles
                $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $oArtList->loadActionArticles('OXCATOFFER');
                if ($oArtList->count()) {
                    $this->_oCatOfferArtList = $oArtList;
                }
            }
        }

        return $this->_oCatOfferArtList;
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
