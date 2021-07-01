<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Category seo config class
 */
class CategorySeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Updating showsuffix field
     *
     * @return null
     */
    public function save()
    {
        $sOxid = $this->getEditObjectId();
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($sOxid)) {
            $blShowSuffixParameter = Registry::getRequest()->getRequestEscapedParameter('blShowSuffix');
            $sShowSuffixField = 'oxcategories__oxshowsuffix';
            $oCategory->$sShowSuffixField = new \OxidEsales\Eshop\Core\Field((int) $blShowSuffixParameter);
            $oCategory->save();

            $this->getEncoder()->markRelatedAsExpired($oCategory);
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return \OxidEsales\Eshop\Application\Model\SeoEncoderCategory
     */
    protected function getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class);
    }

    /**
     * This SEO object supports suffixes so return TRUE
     *
     * @return bool
     */
    public function isSuffixSupported()
    {
        return true;
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function getType()
    {
        return 'oxcategory';
    }

    /**
     * Returns true if SEO object id has suffix enabled
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($this->getEditObjectId())) {
            return (bool) $oCategory->oxcategories__oxshowsuffix->value;
        }
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($this->getEditObjectId())) {
            return $this->getEncoder()->getCategoryUri($oCategory, $this->getEditLang());
        }
    }
}
