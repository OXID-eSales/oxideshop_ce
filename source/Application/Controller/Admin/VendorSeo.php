<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;

/**
 * Vendor seo config class
 */
class VendorSeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Updating showsuffix field
     *
     * @return null
     */
    public function save()
    {
        $oVendor = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $oVendor->init('oxvendor');
        if ($oVendor->load($this->getEditObjectId())) {
            $sShowSuffixField = 'oxvendor__oxshowsuffix';
            $blShowSuffixParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('blShowSuffix');
            $oVendor->$sShowSuffixField = new \OxidEsales\Eshop\Core\Field((int) $blShowSuffixParameter);
            $oVendor->save();
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderVendor
     */
    protected function _getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class);
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
     * Returns true if SEO object id has suffix enabled
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        if ($oVendor->load($this->getEditObjectId())) {
            return (bool) $oVendor->oxvendor__oxshowsuffix->value;
        }
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
        return 'oxvendor';
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        if ($oVendor->load($this->getEditObjectId())) {
            return $this->_getEncoder()->getVendorUri($oVendor, $this->getEditLang());
        }
    }
}
