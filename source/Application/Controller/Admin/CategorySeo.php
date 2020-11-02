<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Category seo config class.
 */
class CategorySeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Updating showsuffix field.
     */
    public function save()
    {
        $sOxid = $this->getEditObjectId();
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($sOxid)) {
            $blShowSuffixParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('blShowSuffix');
            $sShowSuffixField = 'oxcategories__oxshowsuffix';
            $oCategory->$sShowSuffixField = new \OxidEsales\Eshop\Core\Field((int)$blShowSuffixParameter);
            $oCategory->save();

            $this->_getEncoder()->markRelatedAsExpired($oCategory);
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object.
     *
     * @return \OxidEsales\Eshop\Application\Model\SeoEncoderCategory
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getEncoder" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class);
    }

    /**
     * This SEO object supports suffixes so return TRUE.
     *
     * @return bool
     */
    public function isSuffixSupported()
    {
        return true;
    }

    /**
     * Returns url type.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getType" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getType()
    {
        return 'oxcategory';
    }

    /**
     * Returns true if SEO object id has suffix enabled.
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($this->getEditObjectId())) {
            return (bool)$oCategory->oxcategories__oxshowsuffix->value;
        }
    }

    /**
     * Returns seo uri.
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($this->getEditObjectId())) {
            return $this->_getEncoder()->getCategoryUri($oCategory, $this->getEditLang());
        }
    }
}
