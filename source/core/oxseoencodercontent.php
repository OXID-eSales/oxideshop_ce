<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

/**
 * Seo encoder base
 *
 * @package core
 */
class oxSeoEncoderContent extends oxSeoEncoder
{
    /**
     * Singleton instance.
     */
    protected static $_instance = null;

    /**
     * Singleton method
     *
     * @return oxSeoEncoderContent
     */
    public static function getInstance()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            self::$_instance = modInstances::getMod( __CLASS__ );
        }

        if (!self::$_instance) {
            self::$_instance = oxNew("oxSeoEncoderContent");
            if ( defined( 'OXID_PHP_UNIT' ) ) {
                modInstances::addMod( __CLASS__, self::$_instance);
            }
        }

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            // resetting cache
            self::$_instance->_aSeoCache = array();
        }

        return self::$_instance;
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
     * Returns SEO uri for content object. Includes parent category path info if
     * content is assigned to it
     *
     * @param oxcontent $oCont        content category object
     * @param int       $iLang        language
     * @param bool      $blRegenerate if TRUE forces seo url regeneration
     *
     * @deprecated since 20110810 use oxSeoEncoderContent::getContentUri()
     * @todo after you remove this method also change all other calls with public method
     *
     * @return string
     */
    protected function _getContentUri( $oCont, $iLang = null, $blRegenerate = false )
    {
        return $this->getContentUri( $oCont, $iLang, $blRegenerate );
    }

    /**
     * Returns SEO uri for content object. Includes parent category path info if
     * content is assigned to it
     *
     * @param oxcontent $oCont        content category object
     * @param int       $iLang        language
     * @param bool      $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getContentUri( $oCont, $iLang = null, $blRegenerate = false )
    {
        if (!isset($iLang)) {
            $iLang = $oCont->getLanguage();
        }
        //load details link from DB
        if ( $blRegenerate || !( $sSeoUrl = $this->_loadFromDb( 'oxcontent', $oCont->getId(), $iLang ) ) ) {

            if ( $iLang != $oCont->getLanguage() ) {
                $sId = $oCont->getId();
                $oCont = oxNew('oxcontent');
                $oCont->loadInLang( $iLang, $sId );
            }

            $sSeoUrl = '';
            if ( $oCont->oxcontents__oxcatid->value ) {
                $oCat = oxNew( 'oxcategory' );
                if ( $oCat->loadInLang( $iLang, $oCont->oxcontents__oxcatid->value ) ) {
                    if ( $oCat->oxcategories__oxparentid->value && $oCat->oxcategories__oxparentid->value != 'oxrootid' ) {
                        $oParentCat = oxNew( 'oxcategory' );
                        if ( $oParentCat->loadInLang( $iLang, $oCat->oxcategories__oxparentid->value ) ) {
                            $sSeoUrl .= oxSeoEncoderCategory::getInstance()->getCategoryUri( $oParentCat );
                        }
                    }
                }
            }

            $sSeoUrl .= $this->_prepareTitle( $oCont->oxcontents__oxtitle->value, false, $oCont->getLanguage() ) . '/';
            $sSeoUrl  = $this->_processSeoUrl( $sSeoUrl, $oCont->getId(), $iLang );

            $this->_saveToDb( 'oxcontent', $oCont->getId(), $oCont->getBaseStdLink($iLang), $sSeoUrl, $iLang );
        }
        return $sSeoUrl;
    }

    /**
     * encodeContentUrl encodes content link
     *
     * @param oxContent $oCont category object
     * @param int       $iLang language
     *
     * @access public
     *
     * @return void
     */
    public function getContentUrl( $oCont, $iLang = null)
    {
        if (!isset($iLang)) {
            $iLang = $oCont->getLanguage();
        }
        return $this->_getFullUrl( $this->_getContentUri( $oCont, $iLang ), $iLang );
    }

    /**
     * deletes content seo entries
     *
     * @param string $sId content ids
     *
     * @return null
     */
    public function onDeleteContent( $sId )
    {
        $oDb = oxDb::getDb();
        $sIdQuoted = $oDb->quote($sId);
        $oDb->execute("delete from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxcontent'");
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
        $oCont = oxNew( "oxcontent" );
        if ( $oCont->loadInLang( $iLang, $sObjectId ) ) {
            $sSeoUrl = $this->_getContentUri( $oCont, $iLang, true );
        }
        return $sSeoUrl;
    }
}
