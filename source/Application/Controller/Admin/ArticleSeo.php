<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;

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
        $sType = false;
        $aData = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aSeoData");
        if ($aData && isset($aData["oxparams"])) {
            $oStr = getStr();
            $iEndPos = $oStr->strpos($aData["oxparams"], "#");
            $sType = $oStr->substr($aData["oxparams"], 0, $iEndPos);
        } elseif ($aList = $this->getSelectionList()) {
            reset($aList);
            $sType = key($aList);
        }

        return $sType;
    }

    /**
     * Returns active category (manufacturer/vendor) language id
     *
     * @return int
     */
    public function getActCatLang()
    {
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editlanguage") !== null) {
            return $this->_iEditLang;
        }

        $iLang = false;
        $aData = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aSeoData");
        if ($aData && isset($aData["oxparams"])) {
            $oStr = getStr();
            $iStartPos = $oStr->strpos($aData["oxparams"], "#");
            $iEndPos = $oStr->strpos($aData["oxparams"], "#", $iStartPos + 1);
            $iLang = $oStr->substr($aData["oxparams"], $iEndPos + 1);
        } elseif ($aList = $this->getSelectionList()) {
            $aList = reset($aList);
            $iLang = key($aList);
        }

        return (int) $iLang;
    }

    /**
     * Returns active category (manufacturer/vendor) id
     *
     * @return false|string
     */
    public function getActCatId()
    {
        $sId = false;
        $aData = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aSeoData");
        if ($aData && isset($aData["oxparams"])) {
            $oStr = getStr();
            $iStartPos = $oStr->strpos($aData["oxparams"], "#");
            $iEndPos = $oStr->strpos($aData["oxparams"], "#", $iStartPos + 1);
            $iLen = $oStr->strlen($aData["oxparams"]);

            $sId = $oStr->substr($aData["oxparams"], $iStartPos + 1, $iEndPos - $iLen);
        } elseif ($aList = $this->getSelectionList()) {
            $oItem = reset($aList[$this->getActCatType()][$this->getActCatLang()]);

            $sId = $oItem->getId();
        }

        return $sId;
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

            $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oProduct->load($this->getEditObjectId());

            if ($oCatList = $this->_getCategoryList($oProduct)) {
                $this->_aSelectionList["oxcategory"][$this->_iEditLang] = $oCatList;
            }

            if ($oVndList = $this->_getVendorList($oProduct)) {
                $this->_aSelectionList["oxvendor"][$this->_iEditLang] = $oVndList;
            }

            if ($oManList = $this->_getManufacturerList($oProduct)) {
                $this->_aSelectionList["oxmanufacturer"][$this->_iEditLang] = $oManList;
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
    protected function _getCategoryList($oArticle)
    {
        $sMainCatId = false;
        if ($oMainCat = $oArticle->getCategory()) {
            $sMainCatId = $oMainCat->getId();
        }

        $aCatList = [];
        $iLang = $this->getEditLang();

        // adding categories
        $sView = getViewName('oxobject2category');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $sSqlForPriceCategories = $oArticle->getSqlForPriceCategories('oxid');
        $sQ = "select oxobject2category.oxcatnid as oxid from {$sView} as oxobject2category " .
              "where oxobject2category.oxobjectid = :oxobjectid union " . $sSqlForPriceCategories;

        $oRs = $oDb->select($sQ, [
            ':oxobjectid' => $oArticle->getId()
        ]);
        if ($oRs != false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                if ($oCat->loadInLang($iLang, current($oRs->fields))) {
                    if ($sMainCatId == $oCat->getId()) {
                        $sSuffix = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('(main category)', $this->getEditLang());
                        $sTitleField = 'oxcategories__oxtitle';
                        $sTitle = $oCat->$sTitleField->getRawValue() . " " . $sSuffix;
                        $oCat->$sTitleField = new \OxidEsales\Eshop\Core\Field($sTitle, \OxidEsales\Eshop\Core\Field::T_RAW);
                    }
                    $aCatList[] = $oCat;
                }
                $oRs->fetchRow();
            }
        }

        return $aCatList;
    }

    /**
     * Returns array containing product vendor object
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article object
     *
     * @return array
     */
    protected function _getVendorList($oArticle)
    {
        if ($oArticle->oxarticles__oxvendorid->value) {
            $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
            if ($oVendor->loadInLang($this->getEditLang(), $oArticle->oxarticles__oxvendorid->value)) {
                return [$oVendor];
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
    protected function _getManufacturerList($oArticle)
    {
        if ($oArticle->oxarticles__oxmanufacturerid->value) {
            $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            if ($oManufacturer->loadInLang($this->getEditLang(), $oArticle->oxarticles__oxmanufacturerid->value)) {
                return [$oManufacturer];
            }
        }
    }

    /**
     * Returns active category object, used for seo url getter
     *
     * @return \OxidEsales\Eshop\Application\Model\Category | null
     */
    public function getActCategory()
    {
        $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        return ($oCat->load($this->getActCatId())) ? $oCat : null;
    }

    /**
     * Returns active vendor object if available
     *
     * @return \OxidEsales\Eshop\Application\Model\Vendor | null
     */
    public function getActVendor()
    {
        $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);

        return ($this->getActCatType() == 'oxvendor' && $oVendor->load($this->getActCatId())) ? $oVendor : null;
    }

    /**
     * Returns active manufacturer object if available
     *
     * @return \OxidEsales\Eshop\Application\Model\Manufacturer | null
     */
    public function getActManufacturer()
    {
        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        $blLoaded = $this->getActCatType() == 'oxmanufacturer' && $oManufacturer->load($this->getActCatId());

        return ($blLoaded) ? $oManufacturer : null;
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
    protected function _getAltSeoEntryId()
    {
        return $this->getEditObjectId();
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
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
     * @return oxSeoEncoderCategory
     */
    protected function _getEncoder()
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
            $seoEncoder = $this->_getEncoder();

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
     * Returns id of object which must be saved
     *
     * @deprecated since v6.0.0 (2017-12-05); Use getEditObjectId() instead.
     *
     * @return string
     */
    protected function _getSaveObjectId()
    {
        return $this->getEditObjectId();
    }

    /**
     * Returns TRUE if current seo entry has fixed state
     *
     * @return bool
     */
    public function isEntryFixed()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sId = $this->_getSaveObjectId();
        $iLang = (int) $this->getEditLang();
        $iShopId = $this->getConfig()->getShopId();
        $sParam = $this->processParam($this->getActCatId());

        $sQ = "select oxfixed from oxseo where
                   oxseo.oxobjectid = :oxobjectid and
                   oxseo.oxshopid = :oxshopid and oxseo.oxlang = :oxlang and oxparams = :oxparams";

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        return (bool) \OxidEsales\Eshop\Core\DatabaseProvider::getMaster()->getOne($sQ, [
            ':oxobjectid' => $sId,
            ':oxshopid' => $iShopId,
            ':oxlang' => $iLang,
            ':oxparams' => $sParam
        ]);
    }
}
