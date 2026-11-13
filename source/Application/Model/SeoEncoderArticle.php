<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderRecomm;
use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use oxView;
use oxRegistry;
use oxUBase;
use oxDb;
use oxCategory;

/**
 * Seo encoder for articles
 */
class SeoEncoderArticle extends \OxidEsales\Eshop\Core\SeoEncoder
{
    /**
     * Product parent title cache
     *
     * @var array
     */
    protected static $_aTitleCache = [];

    /**
     * Returns target "extension" (.html)
     *
     * @return string
     */
    protected function getUrlExtension()
    {
        return '.html';
    }

    /**
     * Checks if current article is in same language as preferred (language id passed by param).
     * In case languages are not the same - reloads article object in different language
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article to check language
     * @param int                                         $iLang    user defined language id
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function getProductForLang($oArticle, $iLang)
    {
        if (isset($iLang) && $iLang != $oArticle->getLanguage()) {
            $articleId = $oArticle->getId();
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->setSkipAssign(true);
            $oArticle->loadInLang($iLang, $articleId);
        }

        return $oArticle;
    }

    /**
     * Returns SEO uri for passed article and active tag
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     * @param int                                         $iLang    language id
     *
     * @return string
     */
    public function getArticleRecommUri($oArticle, $iLang)
    {
        $seoUri = null;
        if (
            ($recommendationList = $this->_getRecomm($oArticle, $iLang))
            && !($seoUri = $this->loadFromDb(
                'oxarticle',
                $oArticle->getId(),
                $iLang,
                null,
                $recommendationList->getId(),
                true
            ))
        ) {
            $oArticle = $this->getProductForLang($oArticle, $iLang);

            $title = $this->prepareArticleTitle($oArticle);

            $seoUri = Registry::get(SeoEncoderRecomm::class)->getRecommUri($recommendationList, $iLang);
            $seoUri = $this->processSeoUrl($seoUri . $title, $oArticle->getId(), $iLang);

            $stdParams = ['recommid' => $recommendationList->getId(), 'listtype' => $this->getListType()];
            $this->saveToDb(
                'oxarticle',
                $oArticle->getId(),
                Registry::getUtilsUrl()->appendUrl(
                    $oArticle->getBaseStdLink($iLang),
                    $stdParams
                ),
                $seoUri,
                $iLang,
                null,
                0,
                $recommendationList->getId()
            );
        }

        return $seoUri;
    }

    /**
     * Returns active recommendation list object if available
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle product
     * @param int                                         $iLang    language id
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return \OxidEsales\Eshop\Application\Model\RecommendationList|null
     */
    protected function _getRecomm($oArticle, $iLang) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $recommendationList = null;
        $view = Registry::getConfig()->getActiveView();
        if ($view instanceof FrontendController) {
            $recommendationList = $view->getActiveRecommList();
        }

        return $recommendationList;
    }

    /**
     * Returns active list type
     *
     * @return string
     */
    protected function getListType()
    {
        return Registry::getConfig()->getActiveView()->getListType();
    }

    public function generateArticleCategoryUri($article, $category, $languageId)
    {
        return $this->createArticleCategoryUri($article, $category, $languageId);
    }

    /**
     * create article uri for given category and save it
     *
     * @param \OxidEsales\Eshop\Application\Model\Article  $oArticle  article object
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory category object
     * @param int                                          $iLang     language to generate uri for
     *
     * @deprecated since v7.6.0, will be removed in v8.0, use generateArticleCategoryUri() instead
     *
     * @return string
     */
    protected function createArticleCategoryUri($oArticle, $oCategory, $iLang)
    {
        startProfile(__FUNCTION__);
        $article = $this->getProductForLang($oArticle, $iLang);

        $seoUri = $this->processSeoUrl(
            Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class)
                ->getCategoryUri($oCategory, $iLang) . $this->prepareArticleTitle($article),
            $article->getId(),
            $iLang
        );
        $categoryId = $oCategory->getId();
        $this->saveToDb(
            'oxarticle',
            $article->getId(),
            Registry::getUtilsUrl()->appendUrl(
                $article->getBaseStdLink($iLang),
                ['cnid' => $categoryId]
            ),
            $seoUri,
            $iLang,
            null,
            0,
            $categoryId
        );

        stopProfile(__FUNCTION__);

        return $seoUri;
    }

    /**
     * Returns SEO uri for passed article
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle     article object
     * @param int                                         $iLang        language id
     * @param bool                                        $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleUri($oArticle, $iLang, $blRegenerate = false)
    {
        startProfile(__FUNCTION__);

        $activeCategoryId = '';

        $activeCategory = $this->getCategory($oArticle, $iLang);

        if (!$activeCategory instanceof \OxidEsales\Eshop\Application\Model\Category) {
            $activeCategory = $this->getMainCategory($oArticle);
        }
        if ($activeCategory) {
            $activeCategoryId = $activeCategory->getId();
        }

        if (
            (
                $blRegenerate
                || !($seoUri = $this->loadFromDb(
                    'oxarticle',
                    $oArticle->getId(),
                    $iLang,
                    null,
                    $activeCategoryId,
                    true
                ))
            )
            && $activeCategory
        ) {
            $isInCategory = $activeCategory->isPriceCategory()
                ? $oArticle->inPriceCategory($activeCategoryId)
                : $oArticle->inCategory($activeCategoryId);

            if ($isInCategory) {
                $seoUri = $this->createArticleCategoryUri($oArticle, $activeCategory, $iLang);
            }
        }

        stopProfile(__FUNCTION__);

        return $seoUri;
    }

    /**
     * Returns active category if available
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle product
     * @param int                                         $iLang    language id
     *
     * @return \OxidEsales\Eshop\Application\Model\Category|null
     */
    protected function getCategory($oArticle, $iLang)
    {
        $category = null;
        $view = Registry::getConfig()->getActiveView();
        if ($view instanceof FrontendController) {
            $category = $view->getActiveCategory();
        } elseif ($view instanceof \OxidEsales\Eshop\Core\Controller\BaseController) {
            $category = $view->getActCategory();
        }

        return $category;
    }

    /**
     * Returns products main category id
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle product
     *
     * @return string
     */
    protected function getMainCategory($oArticle)
    {
        $oMainCat = null;

        // if variant parent id must be used
        $sArtId = $oArticle->getId();
        if (isset($oArticle->oxarticles__oxparentid->value) && $oArticle->oxarticles__oxparentid->value) {
            $sArtId = $oArticle->oxarticles__oxparentid->value;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $categoryViewName = $tableViewNameGenerator->getViewName("oxobject2category");

        // add main category caching;
        $sQ = "select oxcatnid from " . $categoryViewName . " where oxobjectid = :oxobjectid order by oxtime";
        $sIdent = md5($categoryViewName . $sArtId);

        if (($sMainCatId = $this->loadFromCache($sIdent, "oxarticle")) === false) {
            $sMainCatId = $oDb->getOne($sQ, [
                ':oxobjectid' => $sArtId
            ]);
            // storing in cache
            $this->saveInCache($sIdent, $sMainCatId, "oxarticle");
        }

        if ($sMainCatId) {
            $oMainCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
            if (!$oMainCat->load($sMainCatId)) {
                $oMainCat = null;
            }
        }

        return $oMainCat;
    }

    /**
     * Returns SEO uri for passed article
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     * @param int                                         $iLang    language id
     *
     * @return string
     */
    public function getArticleMainUri($oArticle, $iLang)
    {
        startProfile(__FUNCTION__);

        $mainCategory = $this->getMainCategory($oArticle);
        $mainCategoryId = $mainCategory ? $mainCategory->getId() : '';

        if (!($seoUri = $this->loadFromDb('oxarticle', $oArticle->getId(), $iLang, null, $mainCategoryId, true))) {
            if ($mainCategory) {
                $seoUri = $this->createArticleCategoryUri($oArticle, $mainCategory, $iLang);
            } else {
                $oArticle = $this->getProductForLang($oArticle, $iLang);
                $seoUri = $this->processSeoUrl($this->prepareArticleTitle($oArticle), $oArticle->getId(), $iLang);

                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    $oArticle->getBaseStdLink($iLang),
                    $seoUri,
                    $iLang,
                    null,
                    0,
                    ''
                );
            }
        }

        stopProfile(__FUNCTION__);

        return $seoUri;
    }

    /**
     * Returns seo title for current article (if oxTitle field is empty, oxArtnum is used).
     * Additionally - if oxVarSelect is set - title is appended with its value
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     *
     * @return string
     */
    protected function prepareArticleTitle($oArticle)
    {
        if (
            !($title = $oArticle->oxarticles__oxtitle->value)
            && $parentId = $oArticle->oxarticles__oxparentid->value
        ) {
            if (!isset(self::$_aTitleCache[$parentId])) {
                $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
                $query = "select oxtitle from " . $oArticle->getViewName() . " where oxid = :oxid";
                self::$_aTitleCache[$parentId] = $database->getOne($query, [
                    ':oxid' => $parentId
                ]);
            }
            $title = self::$_aTitleCache[$parentId];
        }

        if ($oArticle->oxarticles__oxvarselect->value) {
            $title .= ($title ? ' ' : '') . $oArticle->oxarticles__oxvarselect->value . ' ';
        } elseif (!$title || ($oArticle->oxarticles__oxparentid->value)) {
            // in case nothing was found - looking for the number
            $title .= ($title ? ' ' : '') . $oArticle->oxarticles__oxartnum->value;
        }

        return $this->prepareTitle($title, false, $oArticle->getLanguage()) . $this->getUrlExtension();
    }

    /**
     * Returns vendor seo uri for current article
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle     article object
     * @param int                                         $iLang        language id
     * @param bool                                        $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleVendorUri($oArticle, $iLang, $blRegenerate = false)
    {
        startProfile(__FUNCTION__);

        $seoUri = null;
        if ($vendor = $this->getVendor($oArticle, $iLang)) {
            if (
                $blRegenerate
                || !($seoUri = $this->loadFromDb(
                    'oxarticle',
                    $oArticle->getId(),
                    $iLang,
                    null,
                    $vendor->getId(),
                    true
                ))
            ) {
                $oArticle = $this->getProductForLang($oArticle, $iLang);

                $title = $this->prepareArticleTitle($oArticle);

                $seoUri = Registry::get(SeoEncoderVendor::class)->getVendorUri($vendor, $iLang);
                $seoUri = $this->processSeoUrl($seoUri . $title, $oArticle->getId(), $iLang);

                $stdParams = ['cnid' => "v_" . $vendor->getId(), 'listtype' => $this->getListType()];
                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    Registry::getUtilsUrl()->appendUrl(
                        $oArticle->getBaseStdLink($iLang),
                        $stdParams
                    ),
                    $seoUri,
                    $iLang,
                    null,
                    0,
                    $vendor->getId()
                );
            }

            stopProfile(__FUNCTION__);
        }

        return $seoUri;
    }

    /**
     * Returns active vendor if available
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle product
     * @param int                                         $iLang    language id
     *
     * @return \OxidEsales\Eshop\Application\Model\Vendor|null
     */
    protected function getVendor($oArticle, $iLang)
    {
        $view = Registry::getConfig()->getActiveView();

        $vendor = null;
        if ($activeVendorId = $oArticle->oxarticles__oxvendorid->value) {
            if ($view instanceof FrontendController && ($activeVendor = $view->getActVendor())) {
                $vendor = $activeVendor;
            } else {
                $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
            }
            if ($vendor->getId() !== $activeVendorId) {
                $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
                if (!$vendor->loadInLang($iLang, $activeVendorId)) {
                    $vendor = null;
                }
            }
        }

        return $vendor;
    }

    /**
     * Returns manufacturer seo uri for current article
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle     article object
     * @param int                                         $iLang        language id
     * @param bool                                        $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleManufacturerUri($oArticle, $iLang, $blRegenerate = false)
    {
        $seoUri = null;
        startProfile(__FUNCTION__);
        if ($manufacturer = $this->getManufacturer($oArticle, $iLang)) {
            if (
                $blRegenerate
                || !($seoUri = $this->loadFromDb(
                    'oxarticle',
                    $oArticle->getId(),
                    $iLang,
                    null,
                    $manufacturer->getId(),
                    true
                ))
            ) {
                $oArticle = $this->getProductForLang($oArticle, $iLang);

                $title = $this->prepareArticleTitle($oArticle);

                $seoUri = Registry::get(SeoEncoderManufacturer::class)->getManufacturerUri($manufacturer, $iLang);
                $seoUri = $this->processSeoUrl($seoUri . $title, $oArticle->getId(), $iLang);

                $stdParams = ['mnid' => $manufacturer->getId(), 'listtype' => $this->getListType()];
                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    Registry::getUtilsUrl()->appendUrl(
                        $oArticle->getBaseStdLink($iLang),
                        $stdParams
                    ),
                    $seoUri,
                    $iLang,
                    null,
                    0,
                    $manufacturer->getId()
                );
            }

            stopProfile(__FUNCTION__);
        }

        return $seoUri;
    }

    /**
     * Returns active manufacturer if available
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle product
     * @param int                                         $iLang    language id
     *
     * @return \OxidEsales\Eshop\Application\Model\Manufacturer|null
     */
    protected function getManufacturer($oArticle, $iLang)
    {
        $manufacturer = null;
        if ($activeManufacturerId = $oArticle->oxarticles__oxmanufacturerid->value) {
            $view = Registry::getConfig()->getActiveView();

            if ($view instanceof FrontendController && ($activeManufacturer = $view->getActManufacturer())) {
                $manufacturer = $activeManufacturer;
            } else {
                $manufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            }

            if ($manufacturer->getId() !== $activeManufacturerId || $manufacturer->getLanguage() != $iLang) {
                $manufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
                if (!$manufacturer->loadInLang($iLang, $activeManufacturerId)) {
                    $manufacturer = null;
                }
            }
        }

        return $manufacturer;
    }

    /**
     * return article main url, with path of its default category
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle product
     * @param int                                         $iLang    language id
     *
     * @return string
     */
    public function getArticleMainUrl($oArticle, $iLang = null)
    {
        if (!isset($iLang)) {
            $iLang = $oArticle->getLanguage();
        }

        return $this->getFullUrl($this->getArticleMainUri($oArticle, $iLang), $iLang);
    }

    /**
     * Encodes article URLs into SEO format
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article object
     * @param int                                         $iLang    language
     * @param int                                         $iType    type
     *
     * @return string
     */
    public function getArticleUrl($oArticle, $iLang = null, $iType = 0)
    {
        if (!isset($iLang)) {
            $iLang = $oArticle->getLanguage();
        }

        $uri = null;
        switch ($iType) {
            case OXARTICLE_LINKTYPE_VENDOR:
                $uri = $this->getArticleVendorUri($oArticle, $iLang);
                break;
            case OXARTICLE_LINKTYPE_MANUFACTURER:
                $uri = $this->getArticleManufacturerUri($oArticle, $iLang);
                break;
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            case OXARTICLE_LINKTYPE_RECOMM:
                $uri = $this->getArticleRecommUri($oArticle, $iLang);
                break;
            // END deprecated
            case OXARTICLE_LINKTYPE_PRICECATEGORY: // goes price category urls to default (category urls)
            default:
                $uri = $this->getArticleUri($oArticle, $iLang);
                break;
        }

        if (!$uri) {
            $uri = $this->getArticleMainUri($oArticle, $iLang);
        }

        return $this->getFullUrl($uri, $iLang);
    }

    /**
     * deletes article seo entries
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article to remove
     */
    public function onDeleteArticle($oArticle)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->execute("delete from oxseo where oxobjectid = :oxobjectid and oxtype = 'oxarticle'", [
            ':oxobjectid' => $oArticle->getId()
        ]);
        $database->execute("delete from oxobject2seodata where oxobjectid = :oxobjectid", [
            ':oxobjectid' => $oArticle->getId()
        ]);
        $database->execute("delete from oxseohistory where oxobjectid = :oxobjectid", [
            ':oxobjectid' => $oArticle->getId()
        ]);
    }

    /**
     * Returns alternative uri used while updating seo
     *
     * @param string $sObjectId object id
     * @param int    $iLang     language id
     *
     * @return string
     */
    protected function getAltUri($sObjectId, $iLang)
    {
        $seoUrl = null;
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setSkipAssign(true);
        if ($oArticle->loadInLang($iLang, $sObjectId)) {
            switch ($this->getListType()) {
                case 'vendor':
                    $seoUrl = $this->getArticleVendorUri($oArticle, $iLang, true);
                    break;
                case 'manufacturer':
                    $seoUrl = $this->getArticleManufacturerUri($oArticle, $iLang, true);
                    break;
                default:
                    $seoUrl = $this->getArticleUri($oArticle, $iLang, true);
                    break;
            }
        }

        return $seoUrl;
    }
}
