<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
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
            $sId = $oArticle->getId();
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->setSkipAssign(true);
            $oArticle->loadInLang($iLang, $sId);
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
        $sSeoUri = null;
        if ($oRecomm = $this->_getRecomm($oArticle, $iLang)) {
            //load details link from DB
            if (!($sSeoUri = $this->loadFromDb('oxarticle', $oArticle->getId(), $iLang, null, $oRecomm->getId(), true))) {
                $oArticle = $this->getProductForLang($oArticle, $iLang);

                // create title part for uri
                $sTitle = $this->prepareArticleTitle($oArticle);

                // create uri for all categories
                $sSeoUri = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderRecomm::class)->getRecommUri($oRecomm, $iLang);
                $sSeoUri = $this->processSeoUrl($sSeoUri . $sTitle, $oArticle->getId(), $iLang);

                $aStdParams = ['recommid' => $oRecomm->getId(), 'listtype' => $this->getListType()];
                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendUrl(
                        $oArticle->getBaseStdLink($iLang),
                        $aStdParams
                    ),
                    $sSeoUri,
                    $iLang,
                    null,
                    0,
                    $oRecomm->getId()
                );
            }
        }

        return $sSeoUri;
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
        $oList = null;
        $oView = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView();
        if ($oView instanceof \OxidEsales\Eshop\Application\Controller\FrontendController) {
            $oList = $oView->getActiveRecommList();
        }

        return $oList;
    }

    /**
     * Returns active list type
     *
     * @return string
     */
    protected function getListType()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getListType();
    }

    /**
     * create article uri for given category and save it
     *
     * @param \OxidEsales\Eshop\Application\Model\Article  $oArticle  article object
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory category object
     * @param int                                          $iLang     language to generate uri for
     *
     * @return string
     */
    protected function createArticleCategoryUri($oArticle, $oCategory, $iLang)
    {
        startProfile(__FUNCTION__);
        $oArticle = $this->getProductForLang($oArticle, $iLang);

        // create title part for uri
        $sTitle = $this->prepareArticleTitle($oArticle);

        // writing category path
        $sSeoUri = $this->processSeoUrl(
            \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class)->getCategoryUri($oCategory, $iLang) . $sTitle,
            $oArticle->getId(),
            $iLang
        );
        $sCatId = $oCategory->getId();
        $this->saveToDb(
            'oxarticle',
            $oArticle->getId(),
            \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendUrl(
                $oArticle->getBaseStdLink($iLang),
                ['cnid' => $sCatId]
            ),
            $sSeoUri,
            $iLang,
            null,
            0,
            $sCatId
        );

        stopProfile(__FUNCTION__);

        return $sSeoUri;
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

        $sActCatId = '';

        $oActCat = $this->getCategory($oArticle, $iLang);

        if ($oActCat instanceof \OxidEsales\Eshop\Application\Model\Category) {
            $sActCatId = $oActCat->getId();
        } elseif ($oActCat = $this->getMainCategory($oArticle)) {
            $sActCatId = $oActCat->getId();
        }

        //load details link from DB
        if ($blRegenerate || !($sSeoUri = $this->loadFromDb('oxarticle', $oArticle->getId(), $iLang, null, $sActCatId, true))) {
            if ($oActCat) {
                $blInCat = $oActCat->isPriceCategory()
                    ? $oArticle->inPriceCategory($sActCatId)
                    : $oArticle->inCategory($sActCatId);

                if ($blInCat) {
                    $sSeoUri = $this->createArticleCategoryUri($oArticle, $oActCat, $iLang);
                }
            }
        }

        stopProfile(__FUNCTION__);

        return $sSeoUri;
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
        $oCat = null;
        $oView = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView();
        if ($oView instanceof \OxidEsales\Eshop\Application\Controller\FrontendController) {
            $oCat = $oView->getActiveCategory();
        } elseif ($oView instanceof \OxidEsales\Eshop\Core\Controller\BaseController) {
            $oCat = $oView->getActCategory();
        }

        return $oCat;
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

        $oMainCat = $this->getMainCategory($oArticle);
        $sMainCatId = $oMainCat ? $oMainCat->getId() : '';

        //load default article url from DB
        if (!($sSeoUri = $this->loadFromDb('oxarticle', $oArticle->getId(), $iLang, null, $sMainCatId, true))) {
            // save for main category
            if ($oMainCat) {
                $sSeoUri = $this->createArticleCategoryUri($oArticle, $oMainCat, $iLang);
            } else {
                // get default article url
                $oArticle = $this->getProductForLang($oArticle, $iLang);
                $sSeoUri = $this->processSeoUrl($this->prepareArticleTitle($oArticle), $oArticle->getId(), $iLang);

                // save default article url
                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    $oArticle->getBaseStdLink($iLang),
                    $sSeoUri,
                    $iLang,
                    null,
                    0,
                    ''
                );
            }
        }

        stopProfile(__FUNCTION__);

        return $sSeoUri;
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
        // create title part for uri
        if (!($sTitle = $oArticle->oxarticles__oxtitle->value)) {
            // taking parent article title
            if (($sParentId = $oArticle->oxarticles__oxparentid->value)) {
                // looking in cache ..
                if (!isset(self::$_aTitleCache[$sParentId])) {
                    $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
                    $sQ = "select oxtitle from " . $oArticle->getViewName() . " where oxid = :oxid";
                    self::$_aTitleCache[$sParentId] = $oDb->getOne($sQ, [
                        ':oxid' => $sParentId
                    ]);
                }
                $sTitle = self::$_aTitleCache[$sParentId];
            }
        }

        // variant has varselect value
        if ($oArticle->oxarticles__oxvarselect->value) {
            $sTitle .= ($sTitle ? ' ' : '') . $oArticle->oxarticles__oxvarselect->value . ' ';
        } elseif (!$sTitle || ($oArticle->oxarticles__oxparentid->value)) {
            // in case nothing was found - looking for number
            $sTitle .= ($sTitle ? ' ' : '') . $oArticle->oxarticles__oxartnum->value;
        }

        return $this->prepareTitle($sTitle, false, $oArticle->getLanguage()) . $this->getUrlExtension();
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

        $sSeoUri = null;
        if ($oVendor = $this->getVendor($oArticle, $iLang)) {
            //load details link from DB
            if ($blRegenerate || !($sSeoUri = $this->loadFromDb('oxarticle', $oArticle->getId(), $iLang, null, $oVendor->getId(), true))) {
                $oArticle = $this->getProductForLang($oArticle, $iLang);

                // create title part for uri
                $sTitle = $this->prepareArticleTitle($oArticle);

                // create uri for all categories
                $sSeoUri = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class)->getVendorUri($oVendor, $iLang);
                $sSeoUri = $this->processSeoUrl($sSeoUri . $sTitle, $oArticle->getId(), $iLang);

                $aStdParams = ['cnid' => "v_" . $oVendor->getId(), 'listtype' => $this->getListType()];
                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendUrl(
                        $oArticle->getBaseStdLink($iLang),
                        $aStdParams
                    ),
                    $sSeoUri,
                    $iLang,
                    null,
                    0,
                    $oVendor->getId()
                );
            }

            stopProfile(__FUNCTION__);
        }

        return $sSeoUri;
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
        $oView = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView();

        $oVendor = null;
        if ($sActVendorId = $oArticle->oxarticles__oxvendorid->value) {
            if ($oView instanceof \OxidEsales\Eshop\Application\Controller\FrontendController && ($oActVendor = $oView->getActVendor())) {
                $oVendor = $oActVendor;
            } else {
                $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
            }
            if ($oVendor->getId() !== $sActVendorId) {
                $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
                if (!$oVendor->loadInLang($iLang, $sActVendorId)) {
                    $oVendor = null;
                }
            }
        }

        return $oVendor;
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
        $sSeoUri = null;
        startProfile(__FUNCTION__);
        if ($oManufacturer = $this->getManufacturer($oArticle, $iLang)) {
            //load details link from DB
            if ($blRegenerate || !($sSeoUri = $this->loadFromDb('oxarticle', $oArticle->getId(), $iLang, null, $oManufacturer->getId(), true))) {
                $oArticle = $this->getProductForLang($oArticle, $iLang);

                // create title part for uri
                $sTitle = $this->prepareArticleTitle($oArticle);

                // create uri for all categories
                $sSeoUri = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class)->getManufacturerUri($oManufacturer, $iLang);
                $sSeoUri = $this->processSeoUrl($sSeoUri . $sTitle, $oArticle->getId(), $iLang);

                $aStdParams = ['mnid' => $oManufacturer->getId(), 'listtype' => $this->getListType()];
                $this->saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendUrl(
                        $oArticle->getBaseStdLink($iLang),
                        $aStdParams
                    ),
                    $sSeoUri,
                    $iLang,
                    null,
                    0,
                    $oManufacturer->getId()
                );
            }

            stopProfile(__FUNCTION__);
        }

        return $sSeoUri;
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
        $oManufacturer = null;
        if ($sActManufacturerId = $oArticle->oxarticles__oxmanufacturerid->value) {
            $oView = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView();

            if ($oView instanceof \OxidEsales\Eshop\Application\Controller\FrontendController && ($oActManufacturer = $oView->getActManufacturer())) {
                $oManufacturer = $oActManufacturer;
            } else {
                $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            }

            if ($oManufacturer->getId() !== $sActManufacturerId || $oManufacturer->getLanguage() != $iLang) {
                $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
                if (!$oManufacturer->loadInLang($iLang, $sActManufacturerId)) {
                    $oManufacturer = null;
                }
            }
        }

        return $oManufacturer;
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

        $sUri = null;
        switch ($iType) {
            case OXARTICLE_LINKTYPE_VENDOR:
                $sUri = $this->getArticleVendorUri($oArticle, $iLang);
                break;
            case OXARTICLE_LINKTYPE_MANUFACTURER:
                $sUri = $this->getArticleManufacturerUri($oArticle, $iLang);
                break;
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            case OXARTICLE_LINKTYPE_RECOMM:
                $sUri = $this->getArticleRecommUri($oArticle, $iLang);
                break;
            // END deprecated
            case OXARTICLE_LINKTYPE_PRICECATEGORY: // goes price category urls to default (category urls)
            default:
                $sUri = $this->getArticleUri($oArticle, $iLang);
                break;
        }

        // if was unable to fetch type uri - returning main
        if (!$sUri) {
            $sUri = $this->getArticleMainUri($oArticle, $iLang);
        }

        return $this->getFullUrl($sUri, $iLang);
    }

    /**
     * deletes article seo entries
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article to remove
     */
    public function onDeleteArticle($oArticle)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oDb->execute("delete from oxseo where oxobjectid = :oxobjectid and oxtype = 'oxarticle'", [
            ':oxobjectid' => $oArticle->getId()
        ]);
        $oDb->execute("delete from oxobject2seodata where oxobjectid = :oxobjectid", [
            ':oxobjectid' => $oArticle->getId()
        ]);
        $oDb->execute("delete from oxseohistory where oxobjectid = :oxobjectid", [
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
        $sSeoUrl = null;
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setSkipAssign(true);
        if ($oArticle->loadInLang($iLang, $sObjectId)) {
            // choosing URI type to generate
            switch ($this->getListType()) {
                case 'vendor':
                    $sSeoUrl = $this->getArticleVendorUri($oArticle, $iLang, true);
                    break;
                case 'manufacturer':
                    $sSeoUrl = $this->getArticleManufacturerUri($oArticle, $iLang, true);
                    break;
                default:
                    $sSeoUrl = $this->getArticleUri($oArticle, $iLang, true);
                    break;
            }
        }

        return $sSeoUrl;
    }
}
