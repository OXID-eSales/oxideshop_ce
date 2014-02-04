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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Seo encoder base
 *
 * @package model
 */
class oxSeoEncoderVendor extends oxSeoEncoder
{
    /**
     * Singleton instance.
     *
     * @var oxvendor
     */
    protected static $_instance = null;

    /**
     * Root vendor uri cache
     *
     * @var string
     */
    protected $_aRootVendorUri = null;

    /**
     * Singleton method
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxSeoEncoderVendor") instead.
     *
     * @return oxSeoEncoderVendor
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxSeoEncoderVendor");
    }

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
     * @param oxVendor $oVendor      vendor object
     * @param int      $iLang        language
     * @param bool     $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getVendorUri( $oVendor, $iLang = null, $blRegenerate = false  )
    {
        if (!isset($iLang)) {
            $iLang = $oVendor->getLanguage();
        }
        // load from db
        if ( $blRegenerate || !( $sSeoUrl = $this->_loadFromDb( 'oxvendor', $oVendor->getId(), $iLang ) ) ) {

            if ($iLang != $oVendor->getLanguage()) {
                $sId = $oVendor->getId();
                $oVendor = oxNew('oxvendor');
                $oVendor->loadInLang( $iLang, $sId );
            }

            $sSeoUrl = '';
            if ( $oVendor->getId() != 'root' ) {
                if ( !isset( $this->_aRootVendorUri[$iLang] ) ) {
                    $oRootVendor = oxNew('oxvendor');
                    $oRootVendor->loadInLang( $iLang, 'root' );
                    $this->_aRootVendorUri[$iLang] = $this->getVendorUri( $oRootVendor, $iLang );
                }
                $sSeoUrl .= $this->_aRootVendorUri[$iLang];
            }

            $sSeoUrl .= $this->_prepareTitle( $oVendor->oxvendor__oxtitle->value, false, $oVendor->getLanguage() ) .'/';
            $sSeoUrl  = $this->_processSeoUrl( $sSeoUrl, $oVendor->getId(), $iLang );

            // save to db
            $this->_saveToDb( 'oxvendor', $oVendor->getId(), $oVendor->getBaseStdLink($iLang), $sSeoUrl, $iLang );
        }
        return $sSeoUrl;
    }

    /**
     * Returns vendor SEO url for specified page
     *
     * @param oxvendor $oVendor vendor object
     * @param int      $iPage   page tu prepare number
     * @param int      $iLang   language
     * @param bool     $blFixed fixed url marker (default is null)
     *
     * @return string
     */
    public function getVendorPageUrl( $oVendor, $iPage, $iLang = null, $blFixed = null )
    {
        if (!isset($iLang)) {
            $iLang = $oVendor->getLanguage();
        }
        $sStdUrl = $oVendor->getBaseStdLink($iLang) . '&amp;pgNr=' . $iPage;
        $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl( $sStdUrl, $iLang );
        $sSeoUrl = $this->getVendorUri( $oVendor, $iLang ) . $sParams . "/";

        if ( $blFixed === null ) {
            $blFixed = $this->_isFixed( 'oxvendor', $oVendor->getId(), $iLang );
        }
        return $this->_getFullUrl( $this->_getPageUri( $oVendor, 'oxvendor', $sStdUrl, $sSeoUrl, $sParams, $iLang, $blFixed ), $iLang );
    }

    /**
     * Encodes vendor categoru URLs into SEO format
     *
     * @param oxvendor $oVendor Vendor object
     * @param int      $iLang   language
     *
     * @return null
     */
    public function getVendorUrl( $oVendor, $iLang = null )
    {
        if (!isset($iLang)) {
            $iLang = $oVendor->getLanguage();
        }
        return $this->_getFullUrl( $this->getVendorUri( $oVendor, $iLang ), $iLang );
    }

    /**
     * Deletes Vendor seo entry
     *
     * @param oxvendor $oVendor Vendor object
     *
     * @return null
     */
    public function onDeleteVendor( $oVendor )
    {
        $oDb = oxDb::getDb();
        $sIdQuoted = $oDb->quote($oVendor->getId());
        $oDb->execute("delete from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxvendor'");
        $oDb->execute("delete from oxobject2seodata where oxobjectid = $sIdQuoted");
    }

    /**
     * Returns alternative uri used while updating seo
     *
     * @param string $sObjectId object id
     * @param int    $iLang     language id
     *
     * @return string
     */
    protected function _getAltUri( $sObjectId, $iLang )
    {
        $sSeoUrl = null;
        $oVendor = oxNew( "oxvendor" );
        if ( $oVendor->loadInLang( $iLang, $sObjectId ) ) {
            $sSeoUrl = $this->getVendorUri( $oVendor, $iLang, true );
        }
        return $sSeoUrl;
    }
}
