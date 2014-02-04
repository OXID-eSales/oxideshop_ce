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
 * Article seo config class
 */
class Article_Seo extends Object_Seo
{
    /**
     * Chosen category id
     *
     * @var string
     */
    protected $_sActCatId = null;

    /**
     * Product selections (categories, vendors etc assigned)
     *
     * @var array
     */
    protected $_aSelectionList = null;

    /**
     * Returns active selection type - oxcategory, oxmanufacturer, oxvendor or oxtag
     *
     * @return string
     */
    public function getActCatType()
    {
        $sType = false;
        $aData = oxConfig::getParameter( "aSeoData" );
        if ( $aData && isset( $aData["oxparams"] ) ) {
            $oStr = getStr();
            $iEndPos = $oStr->strpos( $aData["oxparams"], "#" );
            $sType = $oStr->substr( $aData["oxparams"], 0, $iEndPos );
        } elseif ( $aList = $this->getSelectionList() ) {
            reset( $aList );
            $sType = key( $aList );
        }

        return $sType;
    }

    /**
     * Returns active category (manufacturer/vendor/tag) language id
     *
     * @return int
     */
    public function getActCatLang()
    {
        if ( oxConfig::getParameter( "editlanguage" ) !== null ) {
            return $this->_iEditLang;
        }

        $iLang = false;
        $aData = oxConfig::getParameter( "aSeoData" );
        if ( $aData && isset( $aData["oxparams"] ) ) {
            $oStr = getStr();
            $iStartPos = $oStr->strpos( $aData["oxparams"], "#" );
            $iEndPos = $oStr->strpos( $aData["oxparams"], "#", $iStartPos + 1 );
            $iLang = $oStr->substr( $aData["oxparams"], $iEndPos + 1 );
        } elseif ( $aList = $this->getSelectionList() ) {
            $aList = reset( $aList );
            $iLang = key( $aList );
        }

        return (int) $iLang;
    }

    /**
     * Returns active category (manufacturer/vendor/tag) id
     *
     * @return string
     */
    public function getActCatId()
    {
        $sId = false;
        $aData = oxConfig::getParameter( "aSeoData" );
        if ( $aData && isset( $aData["oxparams"] ) ) {
            $oStr = getStr();
            $iStartPos = $oStr->strpos( $aData["oxparams"], "#" );
            $iEndPos = $oStr->strpos( $aData["oxparams"], "#", $iStartPos + 1 );
            $iLen = $oStr->strlen( $aData["oxparams"] );
            $sId = $oStr->substr( $aData["oxparams"], $iStartPos + 1, $iEndPos - $iLen );
        } elseif ( $aList = $this->getSelectionList() ) {
            $oItem = reset( $aList[$this->getActCatType()][$this->getActCatLang()] );
            $sId = $oItem->getId();
        }

        return $sId;
    }

    /**
     * Returns product selections array [type][language] (categories, vendors etc assigned)
     *
     * @return array
     */
    public function getSelectionList()
    {
        if ( $this->_aSelectionList === null ) {
            $this->_aSelectionList = array();

            $oProduct = oxNew( 'oxarticle' );
            $oProduct->load( $this->getEditObjectId() );

            if ( $oCatList = $this->_getCategoryList( $oProduct ) ) {
                $this->_aSelectionList["oxcategory"][$this->_iEditLang] = $oCatList;
            }

            if ( $oVndList = $this->_getVendorList( $oProduct ) ) {
                $this->_aSelectionList["oxvendor"][$this->_iEditLang] = $oVndList;
            }

            if ( $oManList = $this->_getManufacturerList( $oProduct ) ) {
                $this->_aSelectionList["oxmanufacturer"][$this->_iEditLang] = $oManList;
            }

            $aLangs = $oProduct->getAvailableInLangs();
            foreach ( $aLangs as $iLang => $sLangTitle ) {
                if ( $oTagList = $this->_getTagList( $oProduct, $iLang ) ) {
                    $this->_aSelectionList["oxtag"][$iLang] = $oTagList;
                }
            }
        }

        return $this->_aSelectionList;
    }

    /**
     * Returns array of product categories
     *
     * @param oxarticle $oArticle Article object
     *
     * @return array
     */
    protected function _getCategoryList( $oArticle )
    {
        $sMainCatId = false;
        if ( $oMainCat = $oArticle->getCategory() ) {
            $sMainCatId = $oMainCat->getId();
        }

        $aCatList = array();
        $iLang = $this->getEditLang();

        // adding categories
        $sO2CView = getViewName( 'oxobject2category');
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        $sQ = "select oxobject2category.oxcatnid as oxid from $sO2CView as oxobject2category where oxobject2category.oxobjectid="
              . $oDb->quote( $oArticle->getId() ) . " union ".$oArticle->getSqlForPriceCategories('oxid');

        $oRs = $oDb->execute( $sQ );
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $oCat = oxNew('oxcategory');
                if ( $oCat->loadInLang( $iLang, current( $oRs->fields ) ) ) {
                    if ( $sMainCatId == $oCat->getId() ) {
                        $sSuffix = oxRegistry::getLang()->translateString( '(main category)', $this->getEditLang() );
                        $oCat->oxcategories__oxtitle = new oxField( $oCat->oxcategories__oxtitle->getRawValue() . " " . $sSuffix, oxField::T_RAW );
                    }
                    $aCatList[] = $oCat;
                }
                $oRs->moveNext();
            }
        }

        return $aCatList;
    }

    /**
     * Returns array containing product vendor object
     *
     * @param oxArticle $oArticle Article object
     *
     * @return array
     */
    protected function _getVendorList( $oArticle )
    {
        if ( $oArticle->oxarticles__oxvendorid->value ) {
            $oVendor = oxNew( 'oxvendor' );
            if ( $oVendor->loadInLang( $this->getEditLang(), $oArticle->oxarticles__oxvendorid->value ) ) {
                return array( $oVendor );
            }
        }
    }

    /**
     * Returns array containing product manufacturer object
     *
     * @param oxarticle $oArticle Article object
     *
     * @return array
     */
    protected function _getManufacturerList( $oArticle )
    {
        if ( $oArticle->oxarticles__oxmanufacturerid->value ) {
            $oManufacturer = oxNew( 'oxmanufacturer' );
            if ( $oManufacturer->loadInLang( $this->getEditLang(), $oArticle->oxarticles__oxmanufacturerid->value ) ) {
                    return array( $oManufacturer );
            }
        }
    }

    /**
     * Returns product tags array for given language
     *
     * @param oxArticle $oArticle Article object
     * @param int       $iLang    language id
     *
     * @return array
     */
    protected function _getTagList( $oArticle, $iLang )
    {
        $oArticleTagList = oxNew("oxarticletaglist");
        $oArticleTagList->setLanguage( $iLang );
        $oArticleTagList->load( $oArticle->getId() );
        $aTagsList = array();
        if ( count( $aTags = $oArticleTagList->getArray() ) ) {
            $sShopId = $this->getConfig()->getShopId();
            $iProdId = $oArticle->getId();
            foreach ( $aTags as $sTitle => $oTagObject ) {
                // A. we do not have oxTag object yet, so reusing manufacturers for general interface
                $oTag = oxNew( "oxManufacturer" );
                $oTag->setLanguage( $iLang );
                $oTag->setId( md5( strtolower ( $sShopId . $this->_getStdUrl( $iProdId, "oxtag", "tag", $iLang, $sTitle ) ) ) );
                $oTag->oxmanufacturers__oxtitle = new oxField( $sTitle );
                $aTagsList[] = $oTag;
            }
        }

        return $aTagsList;
    }

    /**
     * Returns active category object, used for seo url getter
     *
     * @return oxcategory | null
     */
    public function getActCategory()
    {
        $oCat = oxNew( 'oxcategory' );
        return ( $oCat->load( $this->getActCatId() ) ) ? $oCat : null;
    }

    /**
     * Returns active tag, used for seo url getter
     *
     * @return string | null
     */
    public function getTag()
    {
        if ( $this->getActCatType() == 'oxtag' ) {

            $iLang  = $this->getActCatLang();
            $sTagId = $this->getActCatId();

            $oProduct = oxNew( 'oxarticle' );
            $oProduct->loadInLang( $iLang, $this->getEditObjectId() );

            $aList = $this->_getTagList( $oProduct, $iLang );
            foreach ( $aList as $oTag ) {
                if ( $oTag->getId() == $sTagId ) {
                    return $oTag->getTitle();
                }
            }
        }
    }

    /**
     * Returns active vendor object if available
     *
     * @return oxvendor | null
     */
    public function getActVendor()
    {
        $oVendor = oxNew( 'oxvendor' );
        return ( $this->getActCatType() == 'oxvendor' && $oVendor->load( $this->getActCatId() ) ) ? $oVendor : null;
    }

    /**
     * Returns active manufacturer object if available
     *
     * @return oxmanufacturer | null
     */
    public function getActManufacturer()
    {
        $oManufacturer = oxNew( 'oxmanufacturer' );
        return ( $this->getActCatType() == 'oxmanufacturer' && $oManufacturer->load( $this->getActCatId() ) ) ? $oManufacturer : null;
    }

    /**
     * Returns list type for current seo url
     *
     * @return string
     */
    public function getListType()
    {
        switch ( $this->getActCatType() ) {
            case 'oxvendor':
                return 'vendor';
            case 'oxmanufacturer':
                return 'manufacturer';
            case 'oxtag':
                return 'tag';
        }
    }

    /**
     * Returns editable object language id
     *
     * @return int
     */
    public function getEditLang()
    {
        return $this->getActCatLang();
    }

    /**
     * Returns alternative seo entry id
     *
     * @return null
     */
    protected function _getAltSeoEntryId()
    {
        return $this->getEditObjectId();
    }

    /**
     * Returns seo entry type
     *
     * @return string
     */
    protected function _getSeoEntryType()
    {
        if ( $this->getTag() ) {
            return 'dynamic';
        } else {
            return $this->_getType();
        }
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
        return 'oxarticle';
    }

    /**
     * Processes parameter before writing to db
     *
     * @param string $sParam parameter to process
     *
     * @return string
     */
    public function processParam( $sParam )
    {
        if ( $this->getTag() ) {
            return '';
        } else {
            return $this->getActCatId();
        }
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderCategory
     */
    protected function _getEncoder()
    {
        return oxRegistry::get("oxSeoEncoderArticle");
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oProduct = oxNew( 'oxarticle' );
        if ( $oProduct->load( $this->getEditObjectId() ) ) {
            $oEncoder = $this->_getEncoder();
            switch ( $this->getActCatType() ) {
                case 'oxvendor':
                    return $oEncoder->getArticleVendorUri( $oProduct, $this->getEditLang() );
                case 'oxmanufacturer':
                    return $oEncoder->getArticleManufacturerUri( $oProduct, $this->getEditLang() );
                case 'oxtag':
                    return $oEncoder->getArticleTagUri( $oProduct, $this->getActCatLang() );
                default:
                    if ( $this->getActCatId() ) {
                        return $oEncoder->getArticleUri( $oProduct, $this->getEditLang() );
                    } else {
                        return $oEncoder->getArticleMainUri( $oProduct, $this->getEditLang() );
                    }
            }
        }
    }

    /**
     * Returns objects standard url
     *
     * @param string $sOxid     object id
     * @param string $sCatType  preferred type - oxvendor/oxmanufacturer/oxtag.. [default is NULL]
     * @param string $sListType preferred list type tag/vendor/manufacturer.. [default is NULL]
     * @param string $iLang     preferred language id [default is NULL]
     * @param string $sTag      preferred tag [default is NULL]
     *
     * @return string
     */
    protected function _getStdUrl( $sOxid, $sCatType = null, $sListType = null, $iLang = null, $sTag = null )
    {
        $iLang = $iLang !== null ? $iLang : $this->getEditLang();
        $sCatType  = $sCatType !== null ? $sCatType : $this->getActCatType();
        $sListType = $sListType !== null ? $sListType : $this->getListType();

        $aParams = array();
        if ( $sListType ) {
            $aParams["listtype"] = $sListType;
        }

        $oProduct = oxNew( 'oxarticle' );
        $oProduct->loadInLang( $iLang, $sOxid );

        // adding vendor or manufacturer id
        switch ( $sCatType ) {
            case 'oxvendor':
                $aParams["cnid"] = "v_" . $this->getActCatId();
                break;
            case 'oxmanufacturer':
                $aParams["mnid"] = $this->getActCatId();
                break;
            case 'oxtag':
                $aParams["searchtag"] = $sTag !== null ? $sTag : $this->getTag();
                break;
            default:
                $aParams["cnid"] = $this->getActCatId();
                break;
        }

        return trim( oxRegistry::get("oxUtilsUrl")->appendUrl( $oProduct->getBaseStdLink( $iLang, true, false ), $aParams ), '&amp;' );
    }

    /**
     * Returns TRUE, as this view support category selector
     *
     * @return bool
     */
    public function showCatSelect()
    {
        return true;
    }

    /**
     * Returns id of object which must be saved
     *
     * @return string
     */
    protected function _getSaveObjectId()
    {
        $sId = $this->getEditObjectId();
        if ( $this->getActCatType() == 'oxtag' ) {
            $sId = $this->_getEncoder()->getDynamicObjectId( $this->getConfig()->getShopId(), $this->_getStdUrl( $sId ) );
        }
        return $sId;
    }

    /**
     * Returns TRUE if current seo entry has fixed state
     *
     * @return bool
     */
    public function isEntryFixed()
    {
        $oDb = oxDb::getDb();

        $sId   = $this->_getSaveObjectId();
        $iLang = (int) $this->getEditLang();
        $iShopId = $this->getConfig()->getShopId();
        $sParam  = $this->processParam( $this->getActCatId() );

        $sQ = "select oxfixed from oxseo where
                   oxseo.oxobjectid = " . $oDb->quote( $sId ) . " and
                   oxseo.oxshopid = '{$iShopId}' and oxseo.oxlang = {$iLang} and oxparams = ".$oDb->quote( $sParam );

        return (bool) oxDb::getDb()->getOne( $sQ, false, false );
    }
}