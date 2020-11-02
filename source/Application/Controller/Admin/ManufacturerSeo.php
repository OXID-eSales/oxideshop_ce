<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Manufacturer seo config class.
 */
class ManufacturerSeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Updating showsuffix field.
     */
    public function save()
    {
        $oManufacturer = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $oManufacturer->init('oxmanufacturers');
        if ($oManufacturer->load($this->getEditObjectId())) {
            $sShowSuffixField = 'oxmanufacturers__oxshowsuffix';
            $blShowSuffixParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('blShowSuffix');
            $oManufacturer->$sShowSuffixField = new \OxidEsales\Eshop\Core\Field((int)$blShowSuffixParameter);
            $oManufacturer->save();
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object.
     *
     * @return \OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getEncoder" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class);
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
        return 'oxmanufacturer';
    }

    /**
     * Returns true if SEO object id has suffix enabled.
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        if ($oManufacturer->load($this->getEditObjectId())) {
            return (bool)$oManufacturer->oxmanufacturers__oxshowsuffix->value;
        }
    }

    /**
     * Returns seo uri.
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        if ($oManufacturer->load($this->getEditObjectId())) {
            return $this->_getEncoder()->getManufacturerUri($oManufacturer, $this->getEditLang());
        }
    }
}
