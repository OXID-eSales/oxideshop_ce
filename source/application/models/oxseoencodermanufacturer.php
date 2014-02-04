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
class oxSeoEncoderManufacturer extends oxSeoEncoder
{
    /**
     * Singleton instance.
     *
     * @var oxmanufacturer
     */
    protected static $_instance = null;

    /**
     * Root manufacturer uri cache
     *
     * @var array
     */
    protected $_aRootManufacturerUri = null;

    /**
     * Singleton method
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxSeoEncoderManufacturer") instead.
     *
     * @return oxSeoEncoderManufacturer
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxSeoEncoderManufacturer");
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
     * @param oxmanufacturer $oManufacturer manufacturer object
     * @param int            $iLang         language
     * @param bool           $blRegenerate  if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getManufacturerUri( $oManufacturer, $iLang = null, $blRegenerate = false )
    {
        if (!isset($iLang)) {
            $iLang = $oManufacturer->getLanguage();
        }
        // load from db
        if ( $blRegenerate || !( $sSeoUrl = $this->_loadFromDb( 'oxmanufacturer', $oManufacturer->getId(), $iLang ) ) ) {

            if ( $iLang != $oManufacturer->getLanguage() ) {
                $sId = $oManufacturer->getId();
                $oManufacturer = oxNew('oxmanufacturer');
                $oManufacturer->loadInLang( $iLang, $sId );
            }

            $sSeoUrl = '';
            if ( $oManufacturer->getId() != 'root' ) {
                if ( !isset( $this->_aRootManufacturerUri[$iLang] ) ) {
                    $oRootManufacturer = oxNew('oxmanufacturer');
                    $oRootManufacturer->loadInLang( $iLang, 'root' );
                    $this->_aRootManufacturerUri[$iLang] = $this->getManufacturerUri( $oRootManufacturer, $iLang );
                }
                $sSeoUrl .= $this->_aRootManufacturerUri[$iLang];
            }

            $sSeoUrl .= $this->_prepareTitle( $oManufacturer->oxmanufacturers__oxtitle->value, false, $oManufacturer->getLanguage() ) .'/';
            $sSeoUrl  = $this->_processSeoUrl( $sSeoUrl, $oManufacturer->getId(), $iLang );

            // save to db
            $this->_saveToDb( 'oxmanufacturer', $oManufacturer->getId(), $oManufacturer->getBaseStdLink($iLang), $sSeoUrl, $iLang );
        }
        return $sSeoUrl;
    }

    /**
     * Returns Manufacturer SEO url for specified page
     *
     * @param oxManufacturer $oManufacturer manufacturer object
     * @param int            $iPage         page tu prepare number
     * @param int            $iLang         language
     * @param bool           $blFixed       fixed url marker (default is null)
     *
     * @return string
     */
    public function getManufacturerPageUrl( $oManufacturer, $iPage, $iLang = null, $blFixed = null )
    {
        if (!isset($iLang)) {
            $iLang = $oManufacturer->getLanguage();
        }
        $sStdUrl = $oManufacturer->getBaseStdLink($iLang) . '&amp;pgNr=' . $iPage;
        $sParams = $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl( $sStdUrl, $iLang );
        $sSeoUrl = $this->getManufacturerUri( $oManufacturer, $iLang ) . $sParams . "/";

        if ( $blFixed === null ) {
            $blFixed = $this->_isFixed( 'oxmanufacturers', $oManufacturer->getId(), $iLang );
        }
        return $this->_getFullUrl( $this->_getPageUri( $oManufacturer, 'oxmanufacturers', $sStdUrl, $sSeoUrl, $sParams, $iLang, $blFixed ), $iLang );
    }

    /**
     * Encodes manufacturer category URLs into SEO format
     *
     * @param oxmanufacturer $oManufacturer Manufacturer object
     * @param int            $iLang         language
     *
     * @return null
     */
    public function getManufacturerUrl( $oManufacturer, $iLang = null )
    {
        if (!isset($iLang)) {
            $iLang = $oManufacturer->getLanguage();
        }
        return $this->_getFullUrl( $this->getManufacturerUri( $oManufacturer, $iLang ), $iLang );
    }

    /**
     * Deletes manufacturer seo entry
     *
     * @param oxmanufacturer $oManufacturer Manufacturer object
     *
     * @return null
     */
    public function onDeleteManufacturer( $oManufacturer )
    {
        $oDb = oxDb::getDb();
        $sIdQuoted = $oDb->quote( $oManufacturer->getId() );
        $oDb->execute("delete from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxmanufacturer'");
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
        $oManufacturer = oxNew( "oxmanufacturer" );
        if ( $oManufacturer->loadInLang( $iLang, $sObjectId ) ) {
            $sSeoUrl = $this->getManufacturerUri( $oManufacturer, $iLang, true );
        }
        return $sSeoUrl;
    }
}
