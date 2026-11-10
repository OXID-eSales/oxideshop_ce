<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Article seo config class
 */
class ArticleSeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Chosen category id
     *
     * @var string
     */
    protected $_sActCatId = null;

    /**
     * Product selections (categories, vendors etc assigned)
     *
     * @var array
     */
    protected $_aSelectionList = null;

    /**
     * Returns active selection type - oxcategory, oxmanufacturer, oxvendor
     *
     * @return string
     */
    public function getActCatType()
    {
        $type = false;
        $seoData = Registry::getRequest()->getRequestEscapedParameter("aSeoData");
        if ($seoData && isset($seoData["oxparams"])) {
            $string = Str::getStr();
            $endPosition = $string->strpos($seoData["oxparams"], "#");
            $type = $string->substr($seoData["oxparams"], 0, $endPosition);
        } elseif ($selectionList = $this->getSelectionList()) {
            reset($selectionList);
            $type = key($selectionList);
        }

        return $type;
    }

    /**
     * Returns active category (manufacturer/vendor) language id
     *
     * @return int
     */
    public function getActCatLang()
    {
        if (Registry::getRequest()->getRequestEscapedParameter("editlanguage") !== null) {
            return $this->_iEditLang;
        }

        $languageId = false;
        $seoData = Registry::getRequest()->getRequestEscapedParameter("aSeoData");
        if ($seoData && isset($seoData["oxparams"])) {
            $string = Str::getStr();
            $startPosition = $string->strpos($seoData["oxparams"], "#");
            $endPosition = $string->strpos($seoData["oxparams"], "#", $startPosition + 1);
            $languageId = $string->substr($seoData["oxparams"], $endPosition + 1);
        } elseif ($selectionList = $this->getSelectionList()) {
            $selectionList = reset($selectionList);
            $languageId = key($selectionList);
        }

        return (int) $languageId;
    }

    /**
     * Returns active category (manufacturer/vendor) id
     *
     * @return false|string
     */
    public function getActCatId()
    {
        $id = false;
        $seoData = Registry::getRequest()->getRequestEscapedParameter("aSeoData");
        if ($seoData && isset($seoData["oxparams"])) {
            $string = Str::getStr();
            $startPosition = $string->strpos($seoData["oxparams"], "#");
            $endPosition = $string->strpos($seoData["oxparams"], "#", $startPosition + 1);
            $length = $string->strlen($seoData["oxparams"]);

            $id = $string->substr($seoData["oxparams"], $startPosition + 1, $endPosition - $length);
        } elseif ($selectionList = $this->getSelectionList()) {
            $item = reset($selectionList[$this->getActCatType()][$this->getActCatLang()]);

            $id = $item->getId();
        }

        return $id;
    }

    /**
     * Returns product selections array [type][language] (categories, vendors etc assigned)
     *
     * @return array
     */
    public function getSelectionList()
    {
        if ($this->_aSelectionList === null) {
            $this->_aSelectionList = [];

            $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $product->load($this->getEditObjectId());

            if ($categoryList = $this->getCategoryList($product)) {
                $this->_aSelectionList["oxcategory"][$this->_iEditLang] = $categoryList;
            }

            if ($vendorList = $this->getVendorList($product)) {
                $this->_aSelectionList["oxvendor"][$this->_iEditLang] = $vendorList;
            }

            if ($manufacturerList = $this->getManufacturerList($product)) {
                $this->_aSelectionList["oxmanufacturer"][$this->_iEditLang] = $manufacturerList;
            }
        }

        return $this->_aSelectionList;
    }

    /**
     * Returns array of product categories
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article object
     *
     * @return array
     */
    protected function getCategoryList($oArticle)
    {
        $mainCategoryId = false;
        if ($mainCategory = $oArticle->getCategory()) {
            $mainCategoryId = $mainCategory->getId();
        }

        $categories = [];
        $languageId = $this->getEditLang();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $viewName = $tableViewNameGenerator->getViewName('oxobject2category');
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $priceCategoriesSql = $oArticle->getSqlForPriceCategories('oxid');
        $query = "select oxobject2category.oxcatnid as oxid from {$viewName} as oxobject2category " .
              "where oxobject2category.oxobjectid = :oxobjectid union " . $priceCategoriesSql;

        $result = $database->select($query, [
            ':oxobjectid' => $oArticle->getId()
        ]);
        if ($result != false && $result->count() > 0) {
            while (!$result->EOF) {
                $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                if ($category->loadInLang($languageId, current($result->fields))) {
                    if ($mainCategoryId == $category->getId()) {
                        $suffix = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('(main category)', $this->getEditLang());
                        $titleField = 'oxcategories__oxtitle';
                        $title = $category->$titleField->getRawValue() . " " . $suffix;
                        $category->$titleField = new \OxidEsales\Eshop\Core\Field($title, \OxidEsales\Eshop\Core\Field::T_RAW);
                    }
                    $categories[] = $category;
                }
                $result->fetchRow();
            }
        }

        return $categories;
    }

    /**
     * Returns array containing product vendor object
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article object
     *
     * @return array
     */
    protected function getVendorList($oArticle)
    {
        if ($oArticle->oxarticles__oxvendorid->value) {
            $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
            if ($vendor->loadInLang($this->getEditLang(), $oArticle->oxarticles__oxvendorid->value)) {
                return [$vendor];
            }
        }
    }

    /**
     * Returns array containing product manufacturer object
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article object
     *
     * @return array
     */
    protected function getManufacturerList($oArticle)
    {
        if ($oArticle->oxarticles__oxmanufacturerid->value) {
            $manufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            if ($manufacturer->loadInLang($this->getEditLang(), $oArticle->oxarticles__oxmanufacturerid->value)) {
                return [$manufacturer];
            }
        }
    }

    /**
     * Returns active category object, used for seo url getter
     *
     * @return \OxidEsales\Eshop\Application\Model\Category|null
     */
    public function getActCategory()
    {
        $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        return ($category->load($this->getActCatId())) ? $category : null;
    }

    /**
     * Returns active vendor object if available
     *
     * @return \OxidEsales\Eshop\Application\Model\Vendor|null
     */
    public function getActVendor()
    {
        $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);

        return ($this->getActCatType() == 'oxvendor' && $vendor->load($this->getActCatId())) ? $vendor : null;
    }

    /**
     * Returns active manufacturer object if available
     *
     * @return \OxidEsales\Eshop\Application\Model\Manufacturer|null
     */
    public function getActManufacturer()
    {
        $manufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        $isLoaded = $this->getActCatType() == 'oxmanufacturer' && $manufacturer->load($this->getActCatId());

        return ($isLoaded) ? $manufacturer : null;
    }

    /**
     * Returns list type for current seo url
     *
     * @return string
     */
    public function getListType()
    {
        switch ($this->getActCatType()) {
            case 'oxvendor':
                return 'vendor';
            case 'oxmanufacturer':
                return 'manufacturer';
        }
    }

    /**
     * Returns editable object language id
     *
     * @return int
     */
    public function getEditLang()
    {
        return $this->getActCatLang();
    }

    /**
     * Returns alternative seo entry id
     *
     * @return null
     */
    protected function getAltSeoEntryId()
    {
        return $this->getEditObjectId();
    }

    protected function getStdUrl($sOxid)
    {
        $contextId = $this->getActCatId();
        $product = oxNew(Article::class);

        if (!$contextId || !$product->load($sOxid)) {
            return parent::getStdUrl($sOxid);
        }

        $seoEncoder = $this->getEncoder();
        $languageId = $this->getEditLang();

        return match ($this->getActCatType()) {
            'oxcategory' => $seoEncoder->getArticleCategoryStdUrl($product, $languageId, $contextId),
            'oxvendor' => $seoEncoder->getArticleVendorStdUrl($product, $languageId, $contextId, $this->getListType()),
            'oxmanufacturer' => $seoEncoder->getArticleManufacturerStdUrl(
                $product,
                $languageId,
                $contextId,
                $this->getListType()
            ),
            default => parent::getStdUrl($sOxid),
        };
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function getType()
    {
        return 'oxarticle';
    }

    /**
     * Processes parameter before writing to db
     *
     * @param string $sParam parameter to process
     *
     * @return string
     */
    public function processParam($sParam)
    {
        return $this->getActCatId();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return \OxidEsales\Eshop\Application\Model\SeoEncoderArticle
     */
    protected function getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderArticle::class);
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        if ($product->load($this->getEditObjectId())) {
            $seoEncoder = $this->getEncoder();

            switch ($this->getActCatType()) {
                case 'oxvendor':
                    return $seoEncoder->getArticleVendorUri($product, $this->getEditLang());
                case 'oxmanufacturer':
                    return $seoEncoder->getArticleManufacturerUri($product, $this->getEditLang());
                default:
                    if ($this->getActCatId()) {
                        return $seoEncoder->getArticleUri($product, $this->getEditLang());
                    } else {
                        return $seoEncoder->getArticleMainUri($product, $this->getEditLang());
                    }
            }
        }
    }

    /**
     * Returns TRUE, as this view support category selector
     *
     * @return bool
     */
    public function showCatSelect()
    {
        return true;
    }

    /**
     * Returns TRUE if current seo entry has fixed state
     *
     * @return bool
     */
    public function isEntryFixed()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $objectId = $this->getSaveObjectId();
        $languageId = (int) $this->getEditLang();
        $shopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();
        $parameter = $this->processParam($this->getActCatId());

        $query = "select oxfixed from oxseo where
                   oxseo.oxobjectid = :oxobjectid and
                   oxseo.oxshopid = :oxshopid and oxseo.oxlang = :oxlang and oxparams = :oxparams";

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        return (bool) \OxidEsales\Eshop\Core\DatabaseProvider::getMaster()->getOne($query, [
            ':oxobjectid' => $objectId,
            ':oxshopid' => $shopId,
            ':oxlang' => $languageId,
            ':oxparams' => $parameter
        ]);
    }
}
