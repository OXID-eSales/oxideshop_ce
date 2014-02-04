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
class oxSeoEncoderCategory extends oxSeoEncoder
{
    /**
     * Singleton instance.
     */
    protected static $_instance = null;

    /**
     * _aCatCache cache for categories
     *
     * @var array
     * @access protected
     */
    protected $_aCatCache = array();

    /**
     * Singleton method
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxSeoEncoderCategory") instead.
     *
     * @return oxseoencodercategory
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxSeoEncoderCategory");
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
     * _categoryUrlLoader loads category from db
     * returns false if cat needs to be encoded (load failed)
     *
     * @param oxCategory $oCat  category object
     * @param int        $iLang active language id
     *
     * @access protected
     *
     * @return boolean
     */
    protected function _categoryUrlLoader( $oCat, $iLang )
    {
        $sSeoUrl = false;

        $sCacheId = $this->_getCategoryCacheId( $oCat, $iLang );
        if ( isset( $this->_aCatCache[$sCacheId] ) ) {
            $sSeoUrl = $this->_aCatCache[ $sCacheId ];
        } elseif ( ( $sSeoUrl = $this->_loadFromDb( 'oxcategory', $oCat->getId(), $iLang ) ) ) {
            // caching
            $this->_aCatCache[ $sCacheId ] = $sSeoUrl;
        }

        return $sSeoUrl;
    }

    /**
     * _getCatecgoryCacheId return string for isntance cache id
     *
     * @param oxCategory $oCat  category object
     * @param int        $iLang active language
     *
     * @access private
     *
     * @return string
     */
    private function _getCategoryCacheId( $oCat, $iLang )
    {
        return $oCat->getId() . '_' . ( (int) $iLang );
    }

    /**
     * Returns SEO uri for passed category
     *
     * @param oxcategory $oCat         category object
     * @param int        $iLang        language
     * @param bool       $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getCategoryUri( $oCat, $iLang = null, $blRegenerate = false  )
    {
        startProfile(__FUNCTION__);
        $sCatId = $oCat->getId();

        // skipping external category URLs
        if ( $oCat->oxcategories__oxextlink->value ) {
            $sSeoUrl = null;
        } else {
            // not found in cache, process it from the top
            if (!isset($iLang)) {
                $iLang = $oCat->getLanguage();
            }

            $aCacheMap = array();
            $aStdLinks = array();

            while ( $oCat && !($sSeoUrl = $this->_categoryUrlLoader( $oCat, $iLang ) )) {

                if ($iLang != $oCat->getLanguage()) {
                    $sId = $oCat->getId();
                    $oCat = oxNew('oxcategory');
                    $oCat->loadInLang($iLang, $sId);
                }

                // prepare oCat title part
                $sTitle = $this->_prepareTitle( $oCat->oxcategories__oxtitle->value, false, $oCat->getLanguage() );

                foreach ( array_keys( $aCacheMap ) as $id ) {
                    $aCacheMap[$id] = $sTitle . '/' . $aCacheMap[$id];
                }

                $aCacheMap[$oCat->getId()] = $sTitle;
                $aStdLinks[$oCat->getId()] = $oCat->getBaseStdLink($iLang);

                // load parent
                $oCat = $oCat->getParentCategory();
            }

            foreach ( $aCacheMap as $sId => $sUri ) {
                $this->_aCatCache[$sId.'_'.$iLang] = $this->_processSeoUrl( $sSeoUrl.$sUri.'/', $sId, $iLang );
                $this->_saveToDb( 'oxcategory', $sId, $aStdLinks[$sId], $this->_aCatCache[$sId.'_'.$iLang], $iLang );
            }

            $sSeoUrl = $this->_aCatCache[$sCatId.'_'.$iLang];
        }

        stopProfile(__FUNCTION__);

        return $sSeoUrl;
    }


    /**
     * Returns category SEO url for specified page
     *
     * @param oxcategory $oCategory category object
     * @param int        $iPage     page tu prepare number
     * @param int        $iLang     language
     * @param bool       $blFixed   fixed url marker (default is null)
     *
     * @return string
     */
    public function getCategoryPageUrl( $oCategory, $iPage, $iLang = null, $blFixed = null )
    {
        if (!isset($iLang)) {
            $iLang = $oCategory->getLanguage();
        }
        $sStdUrl = $oCategory->getBaseStdLink($iLang) . '&amp;pgNr=' . $iPage;
        $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl( $sStdUrl, $iLang );
        $sSeoUrl = $this->getCategoryUri( $oCategory, $iLang ) . $sParams . "/";

        if ( $blFixed === null ) {
            $blFixed = $this->_isFixed( 'oxcategory', $oCategory->getId(), $iLang );
        }
        return $this->_getFullUrl( $this->_getPageUri( $oCategory, 'oxcategory', $sStdUrl, $sSeoUrl, $sParams, $iLang, $blFixed ), $iLang );
    }

    /**
     * Category URL encoder. If category has external URLs, skip encoding
     * for this category. If SEO id is not set, generates and saves SEO id
     * for category (oxSeoEncoder::_getSeoId()).
     * If category has subcategories, it iterates through them.
     *
     * @param oxCategory $oCategory Category object
     * @param int        $iLang     Language
     *
     * @return string
     */
    public function getCategoryUrl( $oCategory, $iLang = null )
    {
        $sUrl = '';
        if (!isset($iLang)) {
            $iLang = $oCategory->getLanguage();
        }
        // category may have specified url
        if ( ( $sSeoUrl = $this->getCategoryUri( $oCategory, $iLang ) ) ) {
            $sUrl = $this->_getFullUrl( $sSeoUrl, $iLang );
        }
        return $sUrl;
    }

    /**
     * Marks related to category objects as expired
     *
     * @param oxCategory $oCategory Category object
     *
     * @return null
     */
    public function markRelatedAsExpired( $oCategory )
    {
        $oDb = oxDb::getDb();
        $sIdQuoted = $oDb->quote($oCategory->getId());

        // select it from table instead of using object carrying value
        // this is because this method is usually called inside update,
        // where object may already be carrying changed id
        $aCatInfo = $oDb->getAll("select oxrootid, oxleft, oxright from oxcategories where oxid = $sIdQuoted limit 1");
        $sCatRootIdQuoted = $oDb->quote( $aCatInfo[0][0] );

        // update sub cats
        $sQ = "update oxseo as seo1, (select oxid from oxcategories where oxrootid={$sCatRootIdQuoted} and oxleft > ".((int) $aCatInfo[0][1] )." and oxright < ".((int) $aCatInfo[0][2] ).") as seo2 set seo1.oxexpired = '1' where seo1.oxtype = 'oxcategory' and seo1.oxobjectid = seo2.oxid";
        $oDb->execute( $sQ );

        // update subarticles
        $sQ = "update oxseo as seo1, (select o2c.oxobjectid as id from oxcategories as cat left join oxobject2category as o2c on o2c.oxcatnid=cat.oxid where cat.oxrootid={$sCatRootIdQuoted} and cat.oxleft >= ".((int) $aCatInfo[0][1] )." and cat.oxright <= ".((int) $aCatInfo[0][2] ).") as seo2 set seo1.oxexpired = '1' where seo1.oxtype = 'oxarticle' and seo1.oxobjectid = seo2.id";
        $oDb->execute( $sQ );
    }


    /**
     * deletes Category seo entries
     *
     * @param oxCategory $oCategory Category object
     *
     * @return null
     */
    public function onDeleteCategory( $oCategory )
    {
        $oDb = oxDb::getDb();
        $sIdQuoted = $oDb->quote($oCategory->getId());
        $oDb->execute("update oxseo, (select oxseourl from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxcategory') as test set oxseo.oxexpired=1 where oxseo.oxseourl like concat(test.oxseourl, '%') and (oxtype = 'oxcategory' or oxtype = 'oxarticle')");
        $oDb->execute("delete from oxseo where oxseo.oxtype = 'oxarticle' and oxseo.oxparams = $sIdQuoted" );
        $oDb->execute("delete from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxcategory'");
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
        $oCat = oxNew( "oxcategory" );
        if ( $oCat->loadInLang( $iLang, $sObjectId ) ) {
            $sSeoUrl = $this->getCategoryUri( $oCat, $iLang );
        }
        return $sSeoUrl;
    }
}
