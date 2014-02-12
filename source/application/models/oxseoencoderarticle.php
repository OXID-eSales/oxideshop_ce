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
 * Seo encoder for articles
 *
 * @package model
 */
class oxSeoEncoderArticle extends oxSeoEncoder
{
    /**
     * Singleton instance.
     *
     * @var oxSeoEncoderArticle
     */
    protected static $_instance = null;

    /**
     * Product parent title cache
     *
     * @var array
     */
    protected static $_aTitleCache = array();

    /**
     * Singleton method
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxSeoEncoderArticle") instead.
     *
     * @return oxSeoEncoderArticle
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxSeoEncoderArticle");
    }

    /**
     * Returns target "extension" (.html)
     *
     * @return string
     */
    protected function _getUrlExtension()
    {
        return '.html';
    }

    /**
     * Checks if current article is in same language as preferred (language id passed by param).
     * In case languages are not the same - reloads article object in different language
     *
     * @param oxArticle $oArticle article to check language
     * @param int       $iLang    user defined language id
     *
     * @return oxArticle
     */
    protected function _getProductForLang( $oArticle, $iLang )
    {
        if ( isset( $iLang ) && $iLang != $oArticle->getLanguage() ) {
            $sId = $oArticle->getId();
            $oArticle = oxNew( 'oxArticle' );
            $oArticle->setSkipAssign( true );
            $oArticle->loadInLang( $iLang, $sId );
        }

        return $oArticle;
    }

    /**
     * Returns SEO uri for passed article and active tag
     *
     * @param oxArticle $oArticle article object
     * @param int       $iLang    language id
     *
     * @return string
     */
    public function getArticleRecommUri( $oArticle, $iLang )
    {
        $sSeoUri = null;
        if ( $oRecomm = $this->_getRecomm( $oArticle, $iLang ) ) {
            //load details link from DB
            if ( !( $sSeoUri = $this->_loadFromDb( 'oxarticle', $oArticle->getId(), $iLang, null, $oRecomm->getId(), true ) ) ) {

                $oArticle = $this->_getProductForLang( $oArticle, $iLang );

                // create title part for uri
                $sTitle = $this->_prepareArticleTitle( $oArticle );

                // create uri for all categories
                $sSeoUri = oxRegistry::get("oxSeoEncoderRecomm")->getRecommUri( $oRecomm, $iLang );
                $sSeoUri = $this->_processSeoUrl( $sSeoUri . $sTitle, $oArticle->getId(), $iLang );

                $aStdParams = array( 'recommid' => $oRecomm->getId(), 'listtype' => $this->_getListType() );
                $this->_saveToDb(
                            'oxarticle',
                            $oArticle->getId(),
                            oxRegistry::get("oxUtilsUrl")->appendUrl(
                                    $oArticle->getBaseStdLink( $iLang ),
                                    $aStdParams
                            ),
                            $sSeoUri,
                            $iLang,
                            null,
                            0,
                            $oRecomm->getId()
                        );
            }
        }
        return $sSeoUri;
    }

    /**
     * Returns active recommendation list object if available
     *
     * @param oxArticle $oArticle product
     * @param int       $iLang    language id
     *
     * @return oxRecommList | null
     */
    protected function _getRecomm( $oArticle, $iLang )
    {
        $oList = null;
        $oView = $this->getConfig()->getActiveView();
        if ( $oView instanceof oxView ) {
            $oList = $oView->getActiveRecommList();
        }
        return $oList;
    }

    /**
     * Returns active list type
     *
     * @return string
     */
    protected function _getListType()
    {
        return $this->getConfig()->getActiveView()->getListType();
    }

    /**
     * Returns SEO uri for passed article and active tag
     *
     * @param oxArticle $oArticle     article object
     * @param int       $iLang        language id
     * @param bool      $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleTagUri( $oArticle, $iLang, $blRegenerate = false )
    {
        $sSeoUri = null;
        if ( $sTag = $this->_getTag( $oArticle, $iLang ) ) {
            $iShopId = $this->getConfig()->getShopId();
            $sStdUrl = $oArticle->getStdTagLink( $sTag );
            if ( $blRegenerate || !( $sSeoUri = $this->_loadFromDb( 'dynamic', $this->getDynamicObjectId( $iShopId, $sStdUrl ), $iLang ) ) ) {
                // generating new if not found
                if ( $sSeoUri = oxRegistry::get("oxSeoEncoderTag")->getTagUri( $sTag, $iLang, $oArticle->getId() ) ) {
                    $sSeoUri .= $this->_prepareArticleTitle( $oArticle );
                    $sSeoUri  = $this->_processSeoUrl( $sSeoUri, $this->_getStaticObjectId( $iShopId, $sStdUrl ), $iLang );
                    $sSeoUri  = $this->_getDynamicUri( $sStdUrl, $sSeoUri, $iLang );
                }
            }
        }
        return $sSeoUri;
    }

    /**
     * Returns active tag if available
     *
     * @param oxArticle $oArticle product
     * @param int       $iLang    language id
     *
     * @return string | null
     */
    protected function _getTag( $oArticle, $iLang )
    {
        $sTag = null;
        $oView = $this->getConfig()->getTopActiveView();
        if ( $oView instanceof oxView ) {
            $sTag = $oView->getTag();
        }
        return $sTag;
    }

    /**
     * create article uri for given category and save it
     *
     * @param oxArticle  $oArticle  article object
     * @param oxCategory $oCategory category object
     * @param int        $iLang     language to generate uri for
     *
     * @return string
     */
    protected function _createArticleCategoryUri( $oArticle, $oCategory, $iLang )
    {
        startProfile(__FUNCTION__);
        $oArticle = $this->_getProductForLang( $oArticle, $iLang );

        // create title part for uri
        $sTitle = $this->_prepareArticleTitle( $oArticle );

        // writing category path
        $sSeoUri = $this->_processSeoUrl(
                            oxRegistry::get("oxSeoEncoderCategory")->getCategoryUri( $oCategory, $iLang ).$sTitle,
                            $oArticle->getId(), $iLang
                        );
        $sCatId = $oCategory->getId();
        $this->_saveToDb(
                    'oxarticle',
                    $oArticle->getId(),
                    oxRegistry::get("oxUtilsUrl")->appendUrl(
                            $oArticle->getBaseStdLink( $iLang ),
                            array( 'cnid' => $sCatId )
                    ),
                    $sSeoUri,
                    $iLang,
                    null,
                    0,
                    $sCatId
                );

        stopProfile(__FUNCTION__);

        return $sSeoUri;
    }

    /**
     * Returns SEO uri for passed article
     *
     * @param oxArticle $oArticle     article object
     * @param int       $iLang        language id
     * @param bool      $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleUri( $oArticle, $iLang, $blRegenerate = false )
    {
        startProfile(__FUNCTION__);

        $sActCatId = '';

        $oActCat = $this->_getCategory( $oArticle, $iLang );

        if ( $oActCat instanceof oxCategory ) {
            $sActCatId = $oActCat->getId();
        } elseif ( $oActCat = $this->_getMainCategory( $oArticle ) ) {
            $sActCatId = $oActCat->getId();
        }

        //load details link from DB
        if ( $blRegenerate || !( $sSeoUri = $this->_loadFromDb( 'oxarticle', $oArticle->getId(), $iLang, null, $sActCatId, true ) ) ) {
            if ( $oActCat ) {
                $blInCat = false;
                if ( $oActCat->isPriceCategory() ) {
                    $blInCat = $oArticle->inPriceCategory( $sActCatId );
                } else {
                    $blInCat = $oArticle->inCategory( $sActCatId );
                }
                if ( $blInCat ) {
                    $sSeoUri = $this->_createArticleCategoryUri( $oArticle, $oActCat, $iLang );
                }
            }
        }

        stopProfile(__FUNCTION__);

        return $sSeoUri;
    }

    /**
     * Returns active category if available
     *
     * @param oxArticle $oArticle product
     * @param int       $iLang    language id
     *
     * @return oxCategory | null
     */
    protected function _getCategory( $oArticle, $iLang )
    {
        $oCat = null;
        $oView = $this->getConfig()->getActiveView();
        if ( $oView instanceof oxUBase ) {
            $oCat = $oView->getActiveCategory();
        } elseif ( $oView instanceof oxView ) {
            $oCat = $oView->getActCategory();
        }
        return $oCat;
    }

    /**
     * Returns products main category id
     *
     * @param oxArticle $oArticle product
     *
     * @return string
     */
    protected function _getMainCategory( $oArticle )
    {
        $oMainCat = null;

        // if variant parent id must be used
        $sArtId = $oArticle->getId();
        if ( isset( $oArticle->oxarticles__oxparentid->value ) && $oArticle->oxarticles__oxparentid->value ) {
            $sArtId = $oArticle->oxarticles__oxparentid->value;
        }

        $oDb = oxDb::getDb();
        // add main category caching;
        $sQ = "select oxcatnid from ".getViewName( "oxobject2category" )." where oxobjectid = ".$oDb->quote( $sArtId )." order by oxtime";
        $sIdent = md5( $sQ );

        if ( ( $sMainCatId = $this->_loadFromCache( $sIdent, "oxarticle" ) ) === false ) {
            $sMainCatId = $oDb->getOne( $sQ );
            // storing in cache
            $this->_saveInCache( $sIdent, $sMainCatId, "oxarticle" );
        }

        if ( $sMainCatId ) {
            $oMainCat = oxNew( "oxCategory" );
            if ( ! $oMainCat->load( $sMainCatId )) {
                $oMainCat = null;
            }
        }

        return $oMainCat;
    }

    /**
     * Returns SEO uri for passed article
     *
     * @param oxArticle $oArticle article object
     * @param int       $iLang    language id
     *
     * @return string
     */
    public function getArticleMainUri( $oArticle, $iLang )
    {
        startProfile(__FUNCTION__);

        $oMainCat   = $this->_getMainCategory( $oArticle );
        $sMainCatId = $oMainCat ? $oMainCat->getId() : '';

        //load default article url from DB
        if ( !( $sSeoUri = $this->_loadFromDb( 'oxarticle', $oArticle->getId(), $iLang, null, $sMainCatId, true ) ) ) {
            // save for main category
            if ( $oMainCat ) {
                $sSeoUri = $this->_createArticleCategoryUri( $oArticle, $oMainCat, $iLang );
            } else {
                // get default article url
                $oArticle = $this->_getProductForLang( $oArticle, $iLang );
                $sSeoUri = $this->_processSeoUrl( $this->_prepareArticleTitle( $oArticle ), $oArticle->getId(), $iLang );

                // save default article url
                $this->_saveToDb(
                        'oxarticle',
                        $oArticle->getId(),
                        $oArticle->getBaseStdLink( $iLang ),
                        $sSeoUri,
                        $iLang,
                        null,
                        0,
                        ''
                    );
            }
        }

        stopProfile(__FUNCTION__);
        return $sSeoUri;
    }

    /**
     * Returns seo title for current article (if oxTitle field is empty, oxArtnum is used).
     * Additionally - if oxVarSelect is set - title is appended with its value
     *
     * @param oxArticle $oArticle article object
     *
     * @return string
     */
    protected function _prepareArticleTitle( $oArticle )
    {
        $sTitle = '';

        // create title part for uri
        if ( !( $sTitle = $oArticle->oxarticles__oxtitle->value ) ) {
            // taking parent article title
            if ( ( $sParentId = $oArticle->oxarticles__oxparentid->value ) ) {

                // looking in cache ..
                if ( !isset( self::$_aTitleCache[$sParentId] ) ) {
                    $oDb = oxDb::getDb();
                    $sQ = "select oxtitle from ".$oArticle->getViewName()." where oxid = ".$oDb->quote( $sParentId );
                    self::$_aTitleCache[$sParentId] = $oDb->getOne( $sQ );
                }
                $sTitle = self::$_aTitleCache[$sParentId];
            }
        }

        // variant has varselect value
        if ( $oArticle->oxarticles__oxvarselect->value ) {
            $sTitle .= ( $sTitle ? ' ' : '' ) . $oArticle->oxarticles__oxvarselect->value . ' ';
        } elseif ( !$sTitle || ( $oArticle->oxarticles__oxparentid->value ) ) {
            // in case nothing was found - looking for number
            $sTitle .= ( $sTitle ? ' ' : '' ) . $oArticle->oxarticles__oxartnum->value;
        }

        return $this->_prepareTitle( $sTitle, false, $oArticle->getLanguage() ) . '.html';
    }

    /**
     * Returns vendor seo uri for current article
     *
     * @param oxArticle $oArticle     article object
     * @param int       $iLang        language id
     * @param bool      $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleVendorUri( $oArticle, $iLang, $blRegenerate = false )
    {
        startProfile(__FUNCTION__);

        $sSeoUri = null;
        if ( $oVendor = $this->_getVendor( $oArticle, $iLang ) ) {
            //load details link from DB
            if ( $blRegenerate || !( $sSeoUri = $this->_loadFromDb( 'oxarticle', $oArticle->getId(), $iLang, null, $oVendor->getId(), true ) ) ) {

                $oArticle = $this->_getProductForLang( $oArticle, $iLang );

                // create title part for uri
                $sTitle = $this->_prepareArticleTitle( $oArticle );

                // create uri for all categories
                $sSeoUri = oxRegistry::get("oxSeoEncoderVendor")->getVendorUri( $oVendor, $iLang );
                $sSeoUri = $this->_processSeoUrl( $sSeoUri . $sTitle, $oArticle->getId(), $iLang );

                $aStdParams = array( 'cnid' => "v_".$oVendor->getId(), 'listtype' => $this->_getListType() );
                $this->_saveToDb(
                        'oxarticle',
                        $oArticle->getId(),
                        oxRegistry::get("oxUtilsUrl")->appendUrl(
                                $oArticle->getBaseStdLink( $iLang ),
                                $aStdParams
                        ),
                        $sSeoUri,
                        $iLang,
                        null,
                        0,
                        $oVendor->getId()
                    );
            }

            stopProfile(__FUNCTION__);
        }
        return $sSeoUri;
    }

    /**
     * Returns active vendor if available
     *
     * @param oxArticle $oArticle product
     * @param int       $iLang    language id
     *
     * @return oxvendor | null
     */
    protected function _getVendor( $oArticle, $iLang )
    {
        $oView = $this->getConfig()->getActiveView();

        $oVendor = null;
        if ( $sActVendorId = $oArticle->oxarticles__oxvendorid->value ) {
            if ( $oView instanceof oxView && ( $oActVendor = $oView->getActVendor() ) ) {
                $oVendor = $oActVendor;
            } else {
                $oVendor = oxNew( "oxVendor" );
            }
            if ( $oVendor->getId() !== $sActVendorId ) {
                $oVendor = oxNew( "oxVendor" );
                if ( !$oVendor->loadInLang( $iLang, $sActVendorId ) ) {
                    $oVendor = null;
                }
            }
        }

        return $oVendor;
    }

    /**
     * Returns manufacturer seo uri for current article
     *
     * @param oxArticle $oArticle     article object
     * @param int       $iLang        language id
     * @param bool      $blRegenerate if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getArticleManufacturerUri( $oArticle, $iLang, $blRegenerate = false )
    {
        $sSeoUri = null;
        startProfile(__FUNCTION__);
        if ( $oManufacturer = $this->_getManufacturer( $oArticle, $iLang ) ) {
            //load details link from DB
            if ( $blRegenerate || !( $sSeoUri = $this->_loadFromDb( 'oxarticle', $oArticle->getId(), $iLang, null, $oManufacturer->getId(), true ) ) ) {

                $oArticle = $this->_getProductForLang( $oArticle, $iLang );

                // create title part for uri
                $sTitle = $this->_prepareArticleTitle( $oArticle );

                // create uri for all categories
                $sSeoUri = oxRegistry::get("oxSeoEncoderManufacturer")->getManufacturerUri( $oManufacturer, $iLang );
                $sSeoUri = $this->_processSeoUrl( $sSeoUri . $sTitle, $oArticle->getId(), $iLang );

                $aStdParams = array( 'mnid' => $oManufacturer->getId(), 'listtype' => $this->_getListType() );
                $this->_saveToDb(
                        'oxarticle',
                        $oArticle->getId(),
                        oxRegistry::get("oxUtilsUrl")->appendUrl(
                                $oArticle->getBaseStdLink( $iLang ),
                                $aStdParams
                        ),
                        $sSeoUri,
                        $iLang,
                        null,
                        0,
                        $oManufacturer->getId()
                    );
            }

            stopProfile(__FUNCTION__);
        }
        return $sSeoUri;
    }

    /**
     * Returns active manufacturer if available
     *
     * @param oxArticle $oArticle product
     * @param int       $iLang    language id
     *
     * @return oxManufacturer | null
     */
    protected function _getManufacturer( $oArticle, $iLang )
    {
        $oManufacturer = null;
        if ( $sActManufacturerId = $oArticle->oxarticles__oxmanufacturerid->value ) {
            $oView = $this->getConfig()->getActiveView();

            if ( $oView instanceof oxView && ( $oActManufacturer = $oView->getActManufacturer() ) ) {
                $oManufacturer = $oActManufacturer;
            } else {
                $oManufacturer = oxNew( "oxManufacturer" );
            }

            if ( $oManufacturer->getId() !== $sActManufacturerId || $oManufacturer->getLanguage() != $iLang ) {
                $oManufacturer = oxNew( "oxManufacturer" );
                if ( !$oManufacturer->loadInLang( $iLang, $sActManufacturerId ) ) {
                    $oManufacturer = null;
                }
            }
        }

        return $oManufacturer;
    }

    /**
     * return article main url, with path of its default category
     *
     * @param oxArticle $oArticle product
     * @param int       $iLang    language id
     *
     * @return string
     */
    public function getArticleMainUrl( $oArticle, $iLang = null )
    {
        if ( !isset( $iLang ) ) {
            $iLang = $oArticle->getLanguage();
        }

        return $this->_getFullUrl( $this->getArticleMainUri( $oArticle, $iLang ), $iLang );
    }

    /**
     * Encodes article URLs into SEO format
     *
     * @param oxArticle $oArticle Article object
     * @param int       $iLang    language
     * @param int       $iType    type
     *
     * @return string
     */
    public function getArticleUrl( $oArticle, $iLang = null, $iType = 0 )
    {
        if ( !isset( $iLang ) ) {
            $iLang = $oArticle->getLanguage();
        }

        $sUri = null;
        switch ( $iType ) {
            case OXARTICLE_LINKTYPE_VENDOR :
                $sUri = $this->getArticleVendorUri( $oArticle, $iLang );
                break;
            case OXARTICLE_LINKTYPE_MANUFACTURER :
                $sUri = $this->getArticleManufacturerUri( $oArticle, $iLang );
                break;
            case OXARTICLE_LINKTYPE_TAG :
                $sUri = $this->getArticleTagUri( $oArticle, $iLang );
                break;
            case OXARTICLE_LINKTYPE_RECOMM :
                $sUri = $this->getArticleRecommUri( $oArticle, $iLang );
                break;
            case OXARTICLE_LINKTYPE_PRICECATEGORY : // goes price category urls to default (category urls)
            default:
                $sUri = $this->getArticleUri( $oArticle, $iLang );
                break;
        }

        // if was unable to fetch type uri - returning main
        if ( !$sUri ) {
            $sUri = $this->getArticleMainUri( $oArticle, $iLang );
        }

        return $this->_getFullUrl( $sUri, $iLang );
    }

    /**
     * deletes article seo entries
     *
     * @param oxArticle $oArticle article to remove
     *
     * @return null
     */
    public function onDeleteArticle( $oArticle )
    {
        $oDb = oxDb::getDb();
        $sIdQuoted = $oDb->quote( $oArticle->getId() );
        $oDb->execute("delete from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxarticle'");
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
        $oArticle = oxNew( "oxArticle" );
        $oArticle->setSkipAssign( true );
        if ( $oArticle->loadInLang( $iLang, $sObjectId ) ) {
            // choosing URI type to generate
            switch( $this->_getListType() ) {
                case 'vendor':
                    $sSeoUrl = $this->getArticleVendorUri( $oArticle, $iLang, true );
                    break;
                case 'manufacturer':
                    $sSeoUrl = $this->getArticleManufacturerUri( $oArticle, $iLang, true );
                    break;
                case 'tag':
                    $sSeoUrl = $this->getArticleTagUri( $oArticle, $iLang, true );
                    break;
                default:
                    $sSeoUrl = $this->getArticleUri( $oArticle, $iLang, true );
                    break;
            }
        }
        return $sSeoUrl;
    }
}
