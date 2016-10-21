<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Seo encoder base
 *
 */
class SeoEncoderVendor extends \oxSeoEncoder
{

    /**
     * Root vendor uri cache
     *
     * @var string
     */
    protected $_aRootVendorUri = null;

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
     * Returns part of SEO url excluding path
     *
     * @param oxVendor $vendor           Vendor object
     * @param int      $languageId       Language id
     * @param bool     $shouldRegenerate If TRUE - forces seo url regeneration
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
            if ($languageId != $vendor->getLanguage()) {
                $vendorId = $vendor->getId();
                $vendor = oxNew('oxvendor');
                $vendor->loadInLang($languageId, $vendorId);
            }

            $seoUrl = '';
            if ($vendor->getId() != 'root') {
                if (!isset($this->_aRootVendorUri[$languageId])) {
                    $rootVendor = oxNew('oxVendor');
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
     * Returns vendor SEO url for specified page
     *
     * @param oxVendor $vendor     Vendor object.
     * @param int      $pageNumber Number of the page which should be prepared.
     * @param int      $languageId Language id.
     * @param bool     $isFixed    Fixed url marker (default is null).
     *
     * @return string
     */
    public function getVendorPageUrl($vendor, $pageNumber, $languageId = null, $isFixed = null)
    {
        if (!isset($languageId)) {
            $languageId = $vendor->getLanguage();
        }
        $stdUrl = $vendor->getBaseStdLink($languageId) . '&amp;pgNr=' . $pageNumber;
        $parameters = (int) ($pageNumber + 1);

        $stdUrl = $this->_trimUrl($stdUrl, $languageId);
        $seoUrl = $this->getVendorUri($vendor, $languageId) . $parameters . "/";

        if ($isFixed === null) {
            $isFixed = $this->_isFixed('oxvendor', $vendor->getId(), $languageId);
        }

        return $this->_getFullUrl($this->_getPageUri($vendor, 'oxvendor', $stdUrl, $seoUrl, $parameters, $languageId, $isFixed), $languageId);
    }

    /**
     * Encodes vendor category URLs into SEO format.
     *
     * @param oxVendor $vendor     Vendor object
     * @param int      $languageId Language id
     *
     * @return null
     */
    public function getVendorUrl($vendor, $languageId = null)
    {
        if (!isset($languageId)) {
            $languageId = $vendor->getLanguage();
        }

        return $this->_getFullUrl($this->getVendorUri($vendor, $languageId), $languageId);
    }

    /**
     * Deletes Vendor seo entry
     *
     * @param oxVendor $vendor Vendor object
     */
    public function onDeleteVendor($vendor)
    {
        $database = oxDb::getDb();
        $vendorId = $vendor->getId();
        $database->execute("delete from oxseo where oxobjectid = ? and oxtype = 'oxvendor'", array($vendorId));
        $database->execute("delete from oxobject2seodata where oxobjectid = ?", array($vendorId));
        $database->execute("delete from oxseohistory where oxobjectid = ?", array($vendorId));
    }

    /**
     * Returns alternative uri used while updating seo.
     *
     * @param string $vendorId   Vendor id
     * @param int    $languageId Language id
     *
     * @return string
     */
    protected function _getAltUri($vendorId, $languageId)
    {
        $seoUrl = null;
        $vendor = oxNew("oxvendor");
        if ($vendor->loadInLang($languageId, $vendorId)) {
            $seoUrl = $this->getVendorUri($vendor, $languageId, true);
        }

        return $seoUrl;
    }
}
