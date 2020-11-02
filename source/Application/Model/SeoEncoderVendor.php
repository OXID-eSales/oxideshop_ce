<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Seo encoder base.
 */
class SeoEncoderVendor extends \OxidEsales\Eshop\Core\SeoEncoder
{
    /**
     * Root vendor uri cache.
     *
     * @var string
     */
    protected $_aRootVendorUri = null;

    /**
     * Returns target "extension" (/).
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getUrlExtension" in next major
     */
    protected function _getUrlExtension() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return '/';
    }

    /**
     * Returns part of SEO url excluding path.
     *
     * @param \OxidEsales\Eshop\Application\Model\Vendor $vendor           Vendor object
     * @param int                                        $languageId       Language id
     * @param bool                                       $shouldRegenerate If TRUE - forces seo url regeneration
     *
     * @return string
     */
    public function getVendorUri($vendor, $languageId = null, $shouldRegenerate = false)
    {
        if (!isset($languageId)) {
            $languageId = $vendor->getLanguage();
        }
        // load from db
        if ($shouldRegenerate || !($seoUrl = $this->_loadFromDb('oxvendor', $vendor->getId(), $languageId))) {
            if ($languageId !== $vendor->getLanguage()) {
                $vendorId = $vendor->getId();
                $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
                $vendor->loadInLang($languageId, $vendorId);
            }

            $seoUrl = '';
            if ('root' !== $vendor->getId()) {
                if (!isset($this->_aRootVendorUri[$languageId])) {
                    $rootVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
                    $rootVendor->loadInLang($languageId, 'root');
                    $this->_aRootVendorUri[$languageId] = $this->getVendorUri($rootVendor, $languageId);
                }
                $seoUrl .= $this->_aRootVendorUri[$languageId];
            }

            $seoUrl .= $this->_prepareTitle($vendor->oxvendor__oxtitle->value, false, $vendor->getLanguage()) . '/';
            $seoUrl = $this->_processSeoUrl($seoUrl, $vendor->getId(), $languageId);

            // save to db
            $this->_saveToDb('oxvendor', $vendor->getId(), $vendor->getBaseStdLink($languageId), $seoUrl, $languageId);
        }

        return $seoUrl;
    }

    /**
     * Returns vendor SEO url for specified page.
     *
     * @param \OxidEsales\Eshop\Application\Model\Vendor $vendor     vendor object
     * @param int                                        $pageNumber number of the page which should be prepared
     * @param int                                        $languageId language id
     * @param bool                                       $isFixed    fixed url marker (default is null)
     *
     * @return string
     */
    public function getVendorPageUrl($vendor, $pageNumber, $languageId = null, $isFixed = null)
    {
        if (!isset($languageId)) {
            $languageId = $vendor->getLanguage();
        }
        $stdUrl = $vendor->getBaseStdLink($languageId);
        $parameters = null;

        $stdUrl = $this->_trimUrl($stdUrl, $languageId);
        $seoUrl = $this->getVendorUri($vendor, $languageId);

        if (null === $isFixed) {
            $isFixed = $this->_isFixed('oxvendor', $vendor->getId(), $languageId);
        }

        return $this->assembleFullPageUrl($vendor, 'oxvendor', $stdUrl, $seoUrl, $pageNumber, $parameters, $languageId, $isFixed);
    }

    /**
     * Encodes vendor category URLs into SEO format.
     *
     * @param \OxidEsales\Eshop\Application\Model\Vendor $vendor     Vendor object
     * @param int                                        $languageId Language id
     */
    public function getVendorUrl($vendor, $languageId = null)
    {
        if (!isset($languageId)) {
            $languageId = $vendor->getLanguage();
        }

        return $this->_getFullUrl($this->getVendorUri($vendor, $languageId), $languageId);
    }

    /**
     * Deletes Vendor seo entry.
     *
     * @param \OxidEsales\Eshop\Application\Model\Vendor $vendor Vendor object
     */
    public function onDeleteVendor($vendor): void
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $vendorId = $vendor->getId();
        $database->execute("delete from oxseo where oxobjectid = :oxobjectid and oxtype = 'oxvendor'", [
            ':oxobjectid' => $vendorId,
        ]);
        $database->execute('delete from oxobject2seodata where oxobjectid = :oxobjectid', [
            ':oxobjectid' => $vendorId,
        ]);
        $database->execute('delete from oxseohistory where oxobjectid = :oxobjectid', [
            ':oxobjectid' => $vendorId,
        ]);
    }

    /**
     * Returns alternative uri used while updating seo.
     *
     * @param string $vendorId   Vendor id
     * @param int    $languageId Language id
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getAltUri" in next major
     */
    protected function _getAltUri($vendorId, $languageId) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $seoUrl = null;
        $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        if ($vendor->loadInLang($languageId, $vendorId)) {
            $seoUrl = $this->getVendorUri($vendor, $languageId, true);
        }

        return $seoUrl;
    }
}
