<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Seo encoder category
 */
class SeoEncoderCategory extends \OxidEsales\Eshop\Core\SeoEncoder
{
    /** @var array _aCatCache cache for categories. */
    protected $_aCatCache = [];

    /**
     * Returns target "extension" (/)
     *
     * @return string
     */
    protected function _getUrlExtension()
    {
        return '/';
    }

    /**
     * _categoryUrlLoader loads category from db
     * returns false if cat needs to be encoded (load failed)
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCat  category object
     * @param int                                          $iLang active language id
     *
     * @access protected
     *
     * @return boolean
     */
    protected function _categoryUrlLoader($oCat, $iLang)
    {
        $sCacheId = $this->_getCategoryCacheId($oCat, $iLang);
        if (isset($this->_aCatCache[$sCacheId])) {
            $sSeoUrl = $this->_aCatCache[$sCacheId];
        } elseif (($sSeoUrl = $this->_loadFromDb('oxcategory', $oCat->getId(), $iLang))) {
            // caching
            $this->_aCatCache[$sCacheId] = $sSeoUrl;
        }

        return $sSeoUrl;
    }

    /**
     * _getCatecgoryCacheId return string for isntance cache id
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCat  category object
     * @param int                                          $iLang active language
     *
     * @access private
     *
     * @return string
     */
    private function _getCategoryCacheId($oCat, $iLang)
    {
        return $oCat->getId() . '_' . ((int) $iLang);
    }

    /**
     * Returns SEO uri for passed category
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCat         category object
     * @param int                                          $iLang        language
     * @param bool                                         $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getCategoryUri($oCat, $iLang = null, $blRegenerate = false)
    {
        startProfile(__FUNCTION__);
        $sCatId = $oCat->getId();

        // skipping external category URLs
        if ($oCat->oxcategories__oxextlink->value) {
            $sSeoUrl = null;
        } else {
            // not found in cache, process it from the top
            if (!isset($iLang)) {
                $iLang = $oCat->getLanguage();
            }

            $aCacheMap = [];
            $aStdLinks = [];

            while ($oCat && !($sSeoUrl = $this->_categoryUrlLoader($oCat, $iLang))) {
                if ($iLang != $oCat->getLanguage()) {
                    $sId = $oCat->getId();
                    $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                    $oCat->loadInLang($iLang, $sId);
                }

                // prepare oCat title part
                $sTitle = $this->_prepareTitle($oCat->oxcategories__oxtitle->value, false, $oCat->getLanguage());

                foreach (array_keys($aCacheMap) as $id) {
                    $aCacheMap[$id] = $sTitle . '/' . $aCacheMap[$id];
                }

                $aCacheMap[$oCat->getId()] = $sTitle;
                $aStdLinks[$oCat->getId()] = $oCat->getBaseStdLink($iLang);

                // load parent
                $oCat = $oCat->getParentCategory();
            }

            foreach ($aCacheMap as $sId => $sUri) {
                $this->_aCatCache[$sId . '_' . $iLang] = $this->_processSeoUrl($sSeoUrl . $sUri . '/', $sId, $iLang);
                $this->_saveToDb('oxcategory', $sId, $aStdLinks[$sId], $this->_aCatCache[$sId . '_' . $iLang], $iLang);
            }

            $sSeoUrl = $this->_aCatCache[$sCatId . '_' . $iLang];
        }

        stopProfile(__FUNCTION__);

        return $sSeoUrl;
    }

    /**
     * Returns category SEO url for specified page
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $category   Category object.
     * @param int                                          $pageNumber Number of the page which should be prepared.
     * @param int                                          $languageId Language id.
     * @param bool                                         $isFixed    Fixed url marker (default is null).
     *
     * @return string
     */
    public function getCategoryPageUrl($category, $pageNumber, $languageId = null, $isFixed = null)
    {
        if (!isset($languageId)) {
            $languageId = $category->getLanguage();
        }
        $stdUrl = $category->getBaseStdLink($languageId);
        $parameters = null;

        $stdUrl = $this->_trimUrl($stdUrl, $languageId);
        $seoUrl = $this->getCategoryUri($category, $languageId);

        if ($isFixed === null) {
            $isFixed = $this->_isFixed('oxcategory', $category->getId(), $languageId);
        }

        return $this->assembleFullPageUrl($category, 'oxcategory', $stdUrl, $seoUrl, $pageNumber, $parameters, $languageId, $isFixed);
    }

    /**
     * Category URL encoder. If category has external URLs, skip encoding
     * for this category. If SEO id is not set, generates and saves SEO id
     * for category (\OxidEsales\Eshop\Core\SeoEncoder::_getSeoId()).
     * If category has subcategories, it iterates through them.
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory Category object
     * @param int                                          $iLang     Language
     *
     * @return string
     */
    public function getCategoryUrl($oCategory, $iLang = null)
    {
        $sUrl = '';
        if (!isset($iLang)) {
            $iLang = $oCategory->getLanguage();
        }
        // category may have specified url
        if (($sSeoUrl = $this->getCategoryUri($oCategory, $iLang))) {
            $sUrl = $this->_getFullUrl($sSeoUrl, $iLang);
        }

        return $sUrl;
    }

    /**
     * Marks related to category objects as expired
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory Category object
     */
    public function markRelatedAsExpired($oCategory)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // select it from table instead of using object carrying value
        // this is because this method is usually called inside update,
        // where object may already be carrying changed id
        $aCatInfo = $oDb->getAll("select oxrootid, oxleft, oxright from oxcategories where oxid = :oxid limit 1", [
            ':oxid' => $oCategory->getId()
        ]);

        // update sub cats
        $sQ = "update oxseo as seo1, (select oxid from oxcategories 
            where oxrootid = :oxrootid 
            and oxleft > :oxleft 
            and oxright < :oxright ) as seo2 
                set seo1.oxexpired = '1' where seo1.oxtype = 'oxcategory' and seo1.oxobjectid = seo2.oxid";
        $oDb->execute($sQ, [
            ':oxrootid' => $aCatInfo[0][0],
            ':oxleft' => (int) $aCatInfo[0][1],
            ':oxright' => (int) $aCatInfo[0][2]
        ]);

        // update subarticles
        $sQ = "update oxseo as seo1, (select o2c.oxobjectid as id from oxcategories as cat left join oxobject2category "
              ."as o2c on o2c.oxcatnid=cat.oxid where cat.oxrootid = :oxrootid and cat.oxleft >= :oxleft "
              ."and cat.oxright <= :oxright) as seo2 "
              ."set seo1.oxexpired = '1' where seo1.oxtype = 'oxarticle' and seo1.oxobjectid = seo2.id "
              ."and seo1.oxfixed = 0";
        $oDb->execute($sQ, [
            ':oxrootid' => $aCatInfo[0][0],
            ':oxleft' => (int) $aCatInfo[0][1],
            ':oxright' => (int) $aCatInfo[0][2]
        ]);
    }

    /**
     * deletes Category seo entries
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory Category object
     */
    public function onDeleteCategory($oCategory)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $params = [
            ':oxobjectid' => $oCategory->getId()
        ];

        $oDb->execute("update oxseo, (select oxseourl from oxseo where oxobjectid = :oxobjectid and oxtype = 'oxcategory') as test set oxseo.oxexpired=1 where oxseo.oxseourl like concat(test.oxseourl, '%') and (oxtype = 'oxcategory' or oxtype = 'oxarticle')", $params);
        $oDb->execute("delete from oxseo where oxseo.oxtype = 'oxarticle' and oxseo.oxparams = :oxparams", [
            ':oxparams' => $oCategory->getId()
        ]);
        $oDb->execute("delete from oxseo where oxobjectid = :oxobjectid and oxtype = 'oxcategory'", [
            ':oxobjectid' => $oCategory->getId()
        ]);
        $oDb->execute("delete from oxobject2seodata where oxobjectid = :oxobjectid", [
            ':oxobjectid' => $oCategory->getId()
        ]);
        $oDb->execute("delete from oxseohistory where oxobjectid = :oxobjectid", [
            ':oxobjectid' => $oCategory->getId()
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
    protected function _getAltUri($sObjectId, $iLang)
    {
        $sSeoUrl = null;
        $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCat->loadInLang($iLang, $sObjectId)) {
            $sSeoUrl = $this->getCategoryUri($oCat, $iLang);
        }

        return $sSeoUrl;
    }
}
