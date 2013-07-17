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
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: oxwdetailspage.php 56456 13.7.12 11.20Z tadas.rimkus $
 */

class oxwDetailsPage extends oxWidget
{
    /**
     * List of article variants.
     *
     * @var array
     */
    protected $_aVariantList = null;
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * @var array
     */
    protected $_aComponentNames = array( 'oxcmp_cur' => 1, 'oxcmp_shop' => 1, 'oxcmp_basket' => 1, 'oxcmp_user' => 1 );

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'widget/details_page.tpl';

    /**
     * Current product parent article object
     *
     * @var oxArticle
     */
    protected $_oParentProd = null;

    /**
     * Marker if user can rate current product
     *
     * @var bool
     */
    protected $_blCanRate = null;

    /**
     * Marked which defines if current view is sortable or not
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * If tags will be changed
     * @var bool
     */
    protected $_blEditTags = null;

    /**
     * If tags can be changed
     * @var bool
     */
    protected $_blCanEditTags = null;

    /**
     * All tags
     * @var array
     */
    protected $_aTags = null;

    /**
     * Returns user recommendation list
     * @var array
     */
    protected $_aUserRecommList = null;

    /**
     * Class handling CAPTCHA image.
     * @var object
     */
    protected $_oCaptcha = null;

    /**
     * Media files
     * @var array
     */
    protected $_aMediaFiles = null;

    /**
     * History (last seen) products
     * @var array
     */
    protected $_aLastProducts = null;

    /**
     * Current product's vendor
     * @var oxVendor
     */
    protected $_oVendor = null;

    /**
     * Current product's manufacturer
     * @var oxManufacturer
     */
    protected $_oManufacturer = null;

    /**
     * Current product's category
     * @var object
     */
    protected $_oCategory = null;

    /**
     * Current product's attributes
     * @var object
     */
    protected $_aAttributes = null;

    /**
     * Parent article name
     * @var string
     */
    protected $_sParentName = null;

    /**
     * Parent article url
     * @var string
     */
    protected $_sParentUrl = null;

    /**
     * Picture gallery
     * @var array
     */
    protected $_aPicGallery = null;

    /**
     * Select lists
     * @var array
     */
    protected $_aSelectLists = null;

    /**
     * Reviews of current article
     * @var array
     */
    protected $_aReviews = null;

    /**
     * CrossSelling article list
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products article list
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Similar recommendation lists
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Accessories of current article
     * @var object
     */
    protected $_oAccessoires = null;

    /**
     * List of customer also bought thies products
     * @var object
     */
    protected $_aAlsoBoughtArts = null;

    /**
     * Search title
     * @var string
     */
    protected $_sSearchTitle = null;

    /**
     * Marker if active product was fully initialized before returning it
     * (see details::getProduct())
     * @var bool
     */
    protected $_blIsInitialized = false;

    /**
     * Current view link type
     *
     * @var int
     */
    protected $_iLinkType = null;

    /**
     * Is multi dimension variant view
     *
     * @var bool
     */
    protected $_blMdView = null;

    /**
     * Rating value
     * @var double
     */
    protected $_dRatingValue = null;

    /**
     * Ratng count
     * @var integer
     */
    protected $_iRatingCnt = null;

    /**
     * Bid price.
     * @var string
     */
    protected $_sBidPrice = null;

    /**
     * Price alarm status.
     * @var integer
     */
    protected $_iPriceAlarmStatus = null;

    /**
     * Search parameter for Html
     * @var string
     */
    protected $_sSearchParamForHtml = null;

    /**
     * Array of id to form recommendation list.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;


//
    /**
     * Returns current product parent article object if it is available
     *
     * @param string $sParentId parent product id
     *
     * @return oxArticle
     */
    protected function _getParentProduct( $sParentId )
    {
        if ( $sParentId && $this->_oParentProd === null ) {
            $this->_oParentProd = false;
            $oProduct = oxNew( 'oxArticle' );
            if ( ( $oProduct->load( $sParentId ) ) ) {
                $this->_processProduct( $oProduct );
                $this->_oParentProd = $oProduct;
            }
        }
        return $this->_oParentProd;
    }

    /**
     * In case list type is "search" returns search parameters which will be added to product details link
     *
     * @return string | null
     */
    protected function _getAddUrlParams()
    {
        if ( $this->getListType() == "search" ) {
            return $this->getDynUrlParams();
        }
    }
//
    /**
     * Processes product by setting link type and in case list type is search adds search parameters to details link
     *
     * @param object $oProduct product to process
     *
     * @return null
     */
    protected function _processProduct( $oProduct )
    {
        $oProduct->setLinkType( $this->getLinkType() );
        if ( $sAddParams = $this->_getAddUrlParams() ) {
            $oProduct->appendLink( $sAddParams );
        }
    }

    /** NEEDED?
     * Returns prefix ID used by template engine.
     *
     * @return  string  $this->_sViewID view id
     */
    public function getViewId()
    {
        if ( isset( $this->_sViewId )) {
            return $this->_sViewId;
        }

            $sViewId = parent::getViewId().'|'.oxConfig::getParameter( 'anid' ).'|';


        return $this->_sViewId = $sViewId;
    }

////
////    /**
////     * If possible loads additional article info (oxarticle::getCrossSelling(),
////     * oxarticle::getAccessoires(), oxarticle::getReviews(), oxarticle::GetSimilarProducts(),
////     * oxarticle::GetCustomerAlsoBoughtThisProducts()), forms variants details
////     * navigation URLs
////     * loads selectlists (oxarticle::GetSelectLists()), prerares HTML meta data
////     * (details::_convertForMetaTags()). Returns name of template file
////     * details::_sThisTemplate
////     *
////     * @return  string  $this->_sThisTemplate   current template file name
////     */
////    public function render()
////    {
////        $myConfig = $this->getConfig();
////
////        $oProduct = $this->getProduct();
////
////        // assign template name
////        if ( $oProduct->oxarticles__oxtemplate->value ) {
////            $this->_sThisTemplate = $oProduct->oxarticles__oxtemplate->value;
////        }
////
////        if ( ( $sTplName = oxConfig::getParameter( 'tpl' ) ) ) {
////            $this->_sThisTemplate = 'custom/'.basename ( $sTplName );
////        }
////
////        parent::render();
////
////        $sPartial = oxConfig::getParameter('renderPartial');
////        $this->addTplParam('renderPartial', $sPartial);
////
////        switch ($sPartial) {
////            case "productInfo":
////                return 'page/details/ajax/fullproductinfo.tpl';
////                break;
////            case "detailsMain":
////                return 'page/details/ajax/productmain.tpl';
////                break;
////            default:
////                // #785A loads and sets locator data
////                $oLocator = oxNew( 'oxlocator', $this->getListType() );
////                $oLocator->setLocatorData( $oProduct, $this );
////
////                if ($myConfig->getConfigParam( 'bl_rssRecommLists' ) && $this->getSimilarRecommListIds()) {
////                    $oRss = oxNew('oxrssfeed');
////                    $this->addRssFeed($oRss->getRecommListsTitle( $oProduct ), $oRss->getRecommListsUrl( $oProduct ), 'recommlists');
////                }
////
////                return $this->_sThisTemplate;
////        }
////    }

    /** NEEDED, extending oxubase
     * Returns current view meta data
     * If $sMeta parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sMeta     user defined description, description content or empty value
     * @param int    $iLength   max length of result, -1 for no truncation
     * @param bool   $blDescTag if true - performs additional dublicate cleaning
     *
     * @return string
     */
    protected function _prepareMetaDescription( $sMeta, $iLength = 200, $blDescTag = false )
    {
        if ( !$sMeta ) {
            $oProduct = $this->getProduct();

            if ( $this->getConfig()->getConfigParam( 'bl_perfParseLongDescinSmarty' ) ) {
                $sMeta = $oProduct->getLongDesc();
            } else {
                $sMeta = $oProduct->getLongDescription()->value;
            }
            if ( $sMeta == '' ) {
                $sMeta = $oProduct->oxarticles__oxshortdesc->value;
            }
            $sMeta = $oProduct->oxarticles__oxtitle->value.' - '.$sMeta;
        }
        return parent::_prepareMetaDescription( $sMeta, $iLength, $blDescTag );
    }

    /** NEEDED, extending oxubase
     * Returns current view keywords seperated by comma
     * If $sKeywords parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sKeywords               user defined keywords, keywords content or empty value
     * @param bool   $blRemoveDuplicatedWords remove dublicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords = true )
    {
        if ( !$sKeywords ) {
            $oProduct = $this->getProduct();
            $sKeywords = trim( $this->getTitle() );

            if ( $oCatTree = $this->getCategoryTree() ) {
                foreach ( $oCatTree->getPath() as $oCat ) {
                    $sKeywords .= ", " . trim( $oCat->oxcategories__oxtitle->value );
                }
            }

            //adding search keys info
            if ( $sSearchKeys = trim( $oProduct->oxarticles__oxsearchkeys->value ) ) {
                $sKeywords .= ", ". $sSearchKeys;
            }

            $sKeywords = parent::_prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords );
        }

        return $sKeywords;
    }

    /** NEEDED
     * Checks if rating functionality is active
     *
     * @return bool
     */
    public function ratingIsActive()
    {
        return $this->getConfig()->getConfigParam( 'bl_perfLoadReviews' );
    }

    /**
     * Checks if rating functionality is on and allowed to user
     *
     * @return bool
     */
    public function canRate()
    {
        if ( $this->_blCanRate === null ) {

            $this->_blCanRate = false;

            if ( $this->ratingIsActive() && $oUser = $this->getUser() ) {

                $oRating = oxNew( 'oxrating' );
                $this->_blCanRate = $oRating->allowRating( $oUser->getId(), 'oxarticle', $this->getProduct()->getId() );
            }
        }

        return $this->_blCanRate;
    }

    /** NEEDED
     * Checks if rating functionality is on and allowed to user
     *
     * @return bool
     */
    public function canChangeTags()
    {
        if ( $oUser = $this->getUser() ) {

            return true;
        }
        return false;
    }

//    /** NEEDED in main controller, used for reviews widget
//     * Saves user ratings and review text (oxReview object)
//     *
//     * @return null
//     */
//    public function saveReview()
//    {
//        if ( $this->canAcceptFormData() &&
//            ( $oUser = $this->getUser() ) && ( $oProduct = $this->getProduct() ) ) {
//
//            $dRating = oxConfig::getParameter( 'artrating' );
//            if ( $dRating !== null ) {
//                $dRating = (int) $dRating;
//            }
//
//            //save rating
//            if ( $dRating !== null && $dRating >= 1 && $dRating <= 5 ) {
//                $oRating = oxNew( 'oxrating' );
//                if ( $oRating->allowRating( $oUser->getId(), 'oxarticle', $oProduct->getId() ) ) {
//                    $oRating->oxratings__oxuserid   = new oxField( $oUser->getId() );
//                    $oRating->oxratings__oxtype     = new oxField( 'oxarticle' );
//                    $oRating->oxratings__oxobjectid = new oxField( $oProduct->getId() );
//                    $oRating->oxratings__oxrating   = new oxField( $dRating );
//                    $oRating->save();
//                    $oProduct->addToRatingAverage( $dRating );
//                }
//            }
//
//            if ( ( $sReviewText = trim( ( string ) oxConfig::getParameter( 'rvw_txt', true ) ) ) ) {
//                $oReview = oxNew( 'oxreview' );
//                $oReview->oxreviews__oxobjectid = new oxField( $oProduct->getId() );
//                $oReview->oxreviews__oxtype     = new oxField( 'oxarticle' );
//                $oReview->oxreviews__oxtext     = new oxField( $sReviewText, oxField::T_RAW );
//                $oReview->oxreviews__oxlang     = new oxField( oxRegistry::getLang()->getBaseLanguage() );
//                $oReview->oxreviews__oxuserid   = new oxField( $oUser->getId() );
//                $oReview->oxreviews__oxrating   = new oxField( ( $dRating !== null ) ? $dRating : 0);
//                $oReview->save();
//            }
//        }
//    }
//
//    /** USED in recomadd.php extending details, not this.
//     * Adds article to selected recommendation list
//     *
//     * @return null
//     */
//    public function addToRecomm()
//    {
//        if (!$this->getViewConfig()->getShowListmania()) {
//            return;
//        }
//
//        $sRecommText = trim( ( string ) oxConfig::getParameter( 'recomm_txt' ) );
//        $sRecommList = oxConfig::getParameter( 'recomm' );
//        $sArtId      = $this->getProduct()->getId();
//
//        if ( $sArtId ) {
//            $oRecomm = oxNew( 'oxrecommlist' );
//            $oRecomm->load( $sRecommList);
//            $oRecomm->addArticle( $sArtId, $sRecommText );
//        }
//    }
//
    /**
     * Adds tags from parameter
     *
     * @return null;
     */
    public function addTags()
    {
        $sTags  = $this->getConfig()->getRequestParameter('newTags', true );
        $sHighTag  = $this->getConfig()->getRequestParameter( 'highTags', true );
        if ( !$sTags && !$sHighTag) {
            return;
        }
        if ( $sHighTag ) {
            $sTags = getStr()->html_entity_decode( $sHighTag );
        }
        $oProduct = $this->getProduct();

        // set current user added tags for this article for later checking
        $aTaggedProducts = oxRegistry::getSession()->getVariable("aTaggedProducts");
        $aAddedTags = $aTaggedProducts? $aTaggedProducts[$oProduct->getId()] : array();

        $oArticleTagList = oxNew( "oxarticletaglist" );
        $oArticleTagList->load( $oProduct->getId() );
        $sSeparator = $oArticleTagList->get()->getSeparator();
        $aTags = array_unique( explode( $sSeparator, $sTags ) );

        $aResult = $this->_addTagsToList( $oArticleTagList, $aTags, $aAddedTags);

        if ( !empty( $aResult['tags'] ) ) {
            $oArticleTagList->save();
            foreach ( $aResult['tags'] as $sTag) {
                $aAddedTags[ $sTag ] = 1;
            }
            $aTaggedProducts[$oProduct->getId()] = $aAddedTags;
            oxRegistry::getSession()->setVariable( 'aTaggedProducts', $aTaggedProducts);
        }
        // for ajax call
        if ( $this->getConfig()->getRequestParameter( 'blAjax', true ) ) {
            oxRegistry::getUtils()->showMessageAndExit( json_encode( $aResult ) );
        }
    }

    /** NEEDED
     * Adds tags to passed oxArticleTagList object
     *
     * @param oxArticleTagList $oArticleTagList article tags list object
     * @param array            $aTags           tags array to add to list
     * @param array            $aAddedTags      tags, which are already added to list
     *
     * @return array
     */
    protected function _addTagsToList( $oArticleTagList, $aTags, $aAddedTags)
    {
        $aResult = array( 'tags' => array(), 'invalid' => array(), 'inlist' => array() );

        foreach ( $aTags as $sTag ) {
            $oTag = oxNew( "oxtag", $sTag );
            if ( $aAddedTags[$oTag->get()] != 1 ) {
                if ( $oTag->isValid() ) {
                    $oArticleTagList->addTag( $oTag );
                    $aResult['tags'][] = $oTag->get();
                } else {
                    $aResult['invalid'][] = $oTag->get();
                }
            } else {
                $aResult['inlist'][] = $oTag->get();
            }
        }

        return $aResult;
    }

    /**
     * Sets tags editing mode
     *
     * @return null
     */
    public function editTags()
    {
        if ( !$this->getUser() ) {
            return;
        }
        $oArticleTagList = oxNew("oxArticleTagList");
        $oArticleTagList->load( $this->getProduct()->getId() );
        $oTagSet = $oArticleTagList->get();
        $this->_aTags = $oTagSet->get();
        $this->_blEditTags = true;

        // for ajax call
        if ( $this->getConfig()->getRequestParameter( 'blAjax', true ) ) {
            oxRegistry::getUtils()->setHeader( "Content-Type: text/html; charset=".oxRegistry::getLang()->translateString( 'charset' ) );
            $oActView = oxNew( 'oxubase' );
            $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $oSmarty->assign('oView', $this );
            $oSmarty->assign('oViewConf', $this->getViewConfig() );
            oxRegistry::getUtils()->showMessageAndExit( $oSmarty->fetch( 'page/details/inc/editTags.tpl', $this->getViewId() ) );
        }
    }

    /**
     * Cancels tags editing mode
     *
     * @return null
     */
    public function cancelTags()
    {
        $oArticleTagList = oxNew("oxArticleTagList");
        $oArticleTagList->load( $this->getProduct()->getId() );
        $oTagSet = $oArticleTagList->get();
        $this->_aTags = $oTagSet->get();
        $this->_blEditTags = false;

        // for ajax call
        if ( oxConfig::getParameter( 'blAjax', true ) ) {
            oxRegistry::getUtils()->setHeader( "Content-Type: text/html; charset=".oxRegistry::getLang()->translateString( 'charset' ) );
            $oActView = oxNew( 'oxubase' );
            $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $oSmarty->assign('oView', $this );
            $oSmarty->assign('oViewConf', $this->getViewConfig() );
            oxRegistry::getUtils()->showMessageAndExit( $oSmarty->fetch( 'page/details/inc/tags.tpl', $this->getViewId() ) );
        }
    }

    /** NEEDED?
     * Returns active product id to load its seo meta info
     *
     * @return string
     */
    protected function _getSeoObjectId()
    {
        if ( $oProduct = $this->getProduct() ) {
            return $oProduct->getId();
        }
    }

    /** NEEDED
     * loading full list of attributes
     *
     * @return array $_aAttributes
     */
    public function getAttributes()
    {
        if ( $this->_aAttributes === null ) {
            // all attributes this article has
            $aArtAttributes = $this->getProduct()->getAttributes();

            //making a new array for backward compatibility
            $this->_aAttributes = false;

            if ( count( $aArtAttributes ) ) {
                foreach ( $aArtAttributes as $sKey => $oAttribute ) {
                    $this->_aAttributes[$sKey] = new stdClass();
                    $this->_aAttributes[$sKey]->title = $oAttribute->oxattribute__oxtitle->value;
                    $this->_aAttributes[$sKey]->value = $oAttribute->oxattribute__oxvalue->value;
                }
            }
        }
        return $this->_aAttributes;
    }

    /** NEEDED?
     * Returns if tags will be edit
     *
     * @return bool
     */
    public function getEditTags()
    {
        return $this->_blEditTags;
    }

    /** NEEDED
     * Returns all tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->_aTags;
    }

    /** NEEDED
     * Returns tag cloud manager class
     *
     * @return oxTagCloud
     */
    public function getTagCloudManager()
    {
        $oManager = oxNew( "oxTagCloud" );
        $oManager->setExtendedMode( true );
        $oManager->setProductId( $this->getProduct()->getId() );
        return $oManager;
    }
//
////    /**
////     * Returns current product
////     *
////     * @return oxarticle
////     */
////    public function getProduct()
////    {
////        $myConfig = $this->getConfig();
////        $myUtils = oxRegistry::getUtils();
////
////        if ( $this->_oProduct === null ) {
////
////            //this option is only for lists and we must reset value
////            //as blLoadVariants = false affect "ab price" functionality
////            $myConfig->setConfigParam( 'blLoadVariants', true );
////
////            $sOxid = oxConfig::getParameter( 'anid' );
////
////            // object is not yet loaded
////            $this->_oProduct = oxNew( 'oxarticle' );
////
////            if ( !$this->_oProduct->load( $sOxid ) ) {
////                $myUtils->redirect( $myConfig->getShopHomeURL() );
////                $myUtils->showMessageAndExit( '' );
////            }
////
////            $aVariantSelections = $this->_oProduct->getVariantSelections( oxConfig::getParameter( "varselid" ) );
////            if ($aVariantSelections && $aVariantSelections['oActiveVariant'] && $aVariantSelections['blPerfectFit']) {
////                $this->_oProduct = $aVariantSelections['oActiveVariant'];
////            }
////        }
////
////        // additional checks
////        if ( !$this->_blIsInitialized ) {
////
////            $blContinue = true;
////            if ( !$this->_oProduct->isVisible() ) {
////                $blContinue = false;
////            } elseif ( $this->_oProduct->oxarticles__oxparentid->value ) {
////                $oParent = $this->_getParentProduct( $this->_oProduct->oxarticles__oxparentid->value );
////                if ( !$oParent || !$oParent->isVisible() ) {
////                    $blContinue = false;
////                }
////            }
////
////            if ( !$blContinue ) {
////                $myUtils->redirect( $myConfig->getShopHomeURL() );
////                $myUtils->showMessageAndExit( '' );
////            }
////
////            $this->_processProduct( $this->_oProduct );
////            $this->_blIsInitialized = true;
////        }
////
////        return $this->_oProduct;
////    }
//
    /**
     * Returns current view link type
     *
     * @return int
     */
    public function getLinkType()
    {
        if ( $this->_iLinkType === null ) {
            $sListType = oxConfig::getParameter( 'listtype' );
            if ( 'vendor' == $sListType ) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_VENDOR;
            } elseif ( 'manufacturer' == $sListType ) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_MANUFACTURER;
            } elseif ( 'tag' == $sListType ) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_TAG;
            } elseif ( 'recommlist' == $sListType ) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_RECOMM;
            } else {
                $this->_iLinkType = OXARTICLE_LINKTYPE_CATEGORY;

                // price category has own type..
                if ( ( $oCat = $this->getActiveCategory() ) && $oCat->isPriceCategory() ) {
                    $this->_iLinkType = OXARTICLE_LINKTYPE_PRICECATEGORY;
                }
            }
        }

        return $this->_iLinkType;
    }

    /** NEEDED
     * Template variable getter. Returns object of handling CAPTCHA image
     *
     * @return object
     */
    public function getCaptcha()
    {
        if ( $this->_oCaptcha === null ) {
            $this->_oCaptcha = oxNew('oxCaptcha');
        }
        return $this->_oCaptcha;
    }

    /** NEEDED
     * Template variable getter. Returns media files of current product
     *
     * @return array
     */
    public function getMediaFiles()
    {
        if ( $this->_aMediaFiles === null ) {
            $aMediaFiles = $this->getProduct()->getMediaUrls();
            $this->_aMediaFiles = count($aMediaFiles) ? $aMediaFiles : false;
        }
        return $this->_aMediaFiles;
    }

    /** NEEDED?
     * Template variable getter. Returns last seen products
     *
     * @param int $iCnt product count
     *
     * @return array
     */
    public function getLastProducts( $iCnt = 4 )
    {
        if ( $this->_aLastProducts === null ) {
            //last seen products for #768CA
            $oProduct = $this->getProduct();
            $sArtId = $oProduct->oxarticles__oxparentid->value?$oProduct->oxarticles__oxparentid->value:$oProduct->getId();

            $oHistoryArtList = oxNew( 'oxarticlelist' );
            $oHistoryArtList->loadHistoryArticles( $sArtId, $iCnt );
            $this->_aLastProducts = $oHistoryArtList;
        }
        return $this->_aLastProducts;
    }

    /** NEEDED
     * Template variable getter. Returns product's vendor
     *
     * @return object
     */
    public function getManufacturer()
    {
        if ( $this->_oManufacturer === null ) {
            $this->_oManufacturer = $this->getProduct()->getManufacturer( false );
        }
        return $this->_oManufacturer;
    }

    /** NEEDED
     * Template variable getter. Returns picture gallery of current article
     *
     * @return array
     */
    public function getPictureGallery()
    {
        if ( $this->_aPicGallery === null ) {
            //get picture gallery
            $this->_aPicGallery = $this->getPicturesProduct()->getPictureGallery();
        }
        return $this->_aPicGallery;
    }

    /** NEEDED
     * Template variable getter. Returns active picture
     *
     * @return object
     */
    public function getActPicture()
    {
        $aPicGallery = $this->getPictureGallery();
        return $aPicGallery['ActPic'];
    }

    /** NEEDED
     * Template variable getter. Returns true if there more pictures
     *
     * @return bool
     */
    public function morePics()
    {
        $aPicGallery = $this->getPictureGallery();
        return $aPicGallery['MorePics'];
    }

    /** NEEDED
     * Template variable getter. Returns icons of current article
     *
     * @return array
     */
    public function getIcons()
    {
        $aPicGallery = $this->getPictureGallery();
        return $aPicGallery['Icons'];
    }

    /** NEEDED
     * Template variable getter. Returns if to show zoom pictures
     *
     * @return bool
     */
    public function showZoomPics()
    {
        $aPicGallery = $this->getPictureGallery();
        return $aPicGallery['ZoomPic'];
    }

    /** NEEDED
     * Template variable getter. Returns zoom pictures
     *
     * @return array
     */
    public function getZoomPics()
    {
        $aPicGallery = $this->getPictureGallery();
        return $aPicGallery['ZoomPics'];
    }

    /** ???? USED, but really, 1 ?
     * Template variable getter. Returns active zoom picture id
     *
     * @return array
     */
    public function getActZoomPic()
    {
        return 1;
    }

    /**
     * Template variable getter. Returns reviews of current article
     *
     * @return array
     */
    public function getReviews()
    {
        if ( $this->_aReviews === null ) {
            $this->_aReviews = false;
            if ( $this->getConfig()->getConfigParam( 'bl_perfLoadReviews' ) ) {
                $this->_aReviews = $this->getProduct()->getReviews();
            }
        }
        return $this->_aReviews;
    }

    /** NEEDED? extending oxubase
     * Template variable getter. Returns crosssellings
     *
     * @return object
     */
    public function getCrossSelling()
    {
        if ( $this->_oCrossSelling === null ) {
            $this->_oCrossSelling = false;
            if ( $oProduct = $this->getProduct() ) {
                $this->_oCrossSelling = $oProduct->getCrossSelling();
            }
        }
        return $this->_oCrossSelling;
    }

    /** NEEDED
     * Template variable getter. Returns similar article list
     *
     * @return object
     */
    public function getSimilarProducts()
    {
        if ( $this->_oSimilarProducts === null ) {
            $this->_oSimilarProducts = false;
            if ( $oProduct = $this->getProduct() ) {
                $this->_oSimilarProducts = $oProduct->getSimilarProducts();
            }
        }
        return $this->_oSimilarProducts;
    }

    /** NEEDED
     * Return array of id to form recommend list.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ( $this->_aSimilarRecommListIds === null ) {
            $this->_aSimilarRecommListIds = false;

            if ( $oProduct = $this->getProduct() ) {
                $this->_aSimilarRecommListIds = array( $oProduct->getId() );
            }
        }
        return $this->_aSimilarRecommListIds;
    }

    /**
     * Template variable getter. Returns accessories of article
     *
     * @return object
     */
    public function getAccessoires()
    {
        if ( $this->_oAccessoires === null ) {
            $this->_oAccessoires = false;
            if ( $oProduct = $this->getProduct() ) {
                $this->_oAccessoires = $oProduct->getAccessoires();
            }
        }
        return $this->_oAccessoires;
    }

    /** NEEDED, unless used in another widget ?
     * Template variable getter. Returns list of customer also bought these products
     *
     * @return object
     */
    public function getAlsoBoughtTheseProducts()
    {
        if ( $this->_aAlsoBoughtArts === null ) {
            $this->_aAlsoBoughtArts = false;
            if ( $oProduct = $this->getProduct() ) {
                $this->_aAlsoBoughtArts = $oProduct->getCustomerAlsoBoughtThisProducts();
            }
        }
        return $this->_aAlsoBoughtArts;
    }

    /** NEEDED
     * Template variable getter. Returns if price alarm is disabled
     *
     * @return object
     */
    public function isPriceAlarm()
    {
        // #419 disabling price alarm if article has fixed price
        $oProduct = $this->getProduct();
        if ( isset( $oProduct->oxarticles__oxblfixedprice->value ) && $oProduct->oxarticles__oxblfixedprice->value ) {
            return 0;
        }
        return 1;
    }

    /** NEEDED? overriding oxubase
     * returns object, associated with current view.
     * (the object that is shown in frontend)
     *
     * @param int $iLang language id
     *
     * @return object
     */
    protected function _getSubject( $iLang )
    {
        return $this->getProduct();
    }

    /** NEEDED
     * Returns search title. It will be setted in oxLocator
     *
     * @return string
     */
    public function getSearchTitle()
    {
        return $this->_sSearchTitle;
    }

    /** NEEDED
     * Returns search title setter
     *
     * @param string $sTitle search title
     *
     * @return null
     */
    public function setSearchTitle( $sTitle )
    {
        $this->_sSearchTitle = $sTitle;
    }

    /** NEEDED, oxlocator
     * active category path setter
     *
     * @param string $sActCatPath category tree path
     *
     * @return string
     */
    public function setCatTreePath( $sActCatPath )
    {
        $this->_sCatTreePath = $sActCatPath;
    }

    /** NEEDED? overrides oxubase
     * If product details are accessed by vendor url
     * view must not be indexable
     *
     * @return int
     */
    public function noIndex()
    {
        $sListType = oxConfig::getParameter( 'listtype' );
        if ( $sListType && ( 'vendor' == $sListType || 'manufacturer' == $sListType ) ) {
            return $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXFOLLOW;
        }
        return parent::noIndex();
    }

    /** NEEDED?
     * Returns current view title. Default is null
     *
     * @return null
     */
    public function getTitle()
    {
        if ( $oProduct = $this->getProduct() ) {
            $sTag = $this->getTag();
            return $oProduct->oxarticles__oxtitle->value . ( $oProduct->oxarticles__oxvarselect->value ? ' ' . $oProduct->oxarticles__oxvarselect->value : '' ) . (!empty($sTag) ? ' - '.$sTag : '');
        }
    }

    /** NEEDED, overrides oxubase
     * Template variable getter. Returns meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $sMeta = parent::getMetaDescription();

        if($sTag = $this->getTag()) {
            $sMeta = $sTag.' - ' . $sMeta;
        }

        return $sMeta;
    }

    /** NEEDED, might be used from tag.php
     * Template variable getter. Returns current tag
     *
     * @return string
     */
    public function getTag()
    {
        return oxConfig::getParameter("searchtag", 1);
    }

    /** NEEDED? base.tpl, overrides oxubase
     * Returns view canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        if ( ( $oProduct = $this->getProduct() ) ) {
            if ( $oProduct->oxarticles__oxparentid->value ) {
                $oProduct = $this->_getParentProduct( $oProduct->oxarticles__oxparentid->value );
            }

            $oUtils = oxRegistry::get("oxUtilsUrl");
            if ( oxRegistry::getUtils()->seoIsActive() ) {
                $sUrl = $oUtils->prepareCanonicalUrl( $oProduct->getBaseSeoLink( $oProduct->getLanguage(), true ) );
            } else {
                $sUrl = $oUtils->prepareCanonicalUrl( $oProduct->getBaseStdLink( $oProduct->getLanguage() ) );
            }
            return $sUrl;
        }
    }

    /** NEEDED
     * Checks should persistent parameter input field be displayed
     *
     * @return bool
     */
    public function isPersParam()
    {
        $oProduct = $this->getProduct();
        return $oProduct->oxarticles__oxisconfigurable->value;
    }

    /**
     * Template variable getter. Returns rating value
     *
     * @return double
     */
    public function getRatingValue()
    {

        if ( $this->_dRatingValue === null ) {
            $this->_dRatingValue = (double) 0;
            if ( $this->isReviewActive() && ( $oDetailsProduct = $this->getProduct() ) ) {
                $this->_dRatingValue = round( $oDetailsProduct->getArticleRatingAverage( $this->getConfig()->getConfigParam( 'blShowVariantReviews' ) ), 1);
            }
        }

        return (double) $this->_dRatingValue;
    }

    /** NEEDED, same method in recommlist.php
     * Template variable getter. Returns if review module is on
     *
     * @return bool
     */
    public function isReviewActive()
    {
        return $this->getConfig()->getConfigParam( 'bl_perfLoadReviews' );
    }

    /**
     * Template variable getter. Returns rating count
     *
     * @return integer
     */
    public function getRatingCount()
    {
        if ( $this->_iRatingCnt === null ) {
            $this->_iRatingCnt = false;
            if ( $this->isReviewActive() && ( $oDetailsProduct = $this->getProduct() ) ) {
                $this->_iRatingCnt = $oDetailsProduct->getArticleRatingCount( $this->getConfig()->getConfigParam( 'blShowVariantReviews' ) );
            }
        }
        return $this->_iRatingCnt;
    }

    /** NEEDED, fnc for form
     * Validates email
     * address. If email is wrong - returns false and exits. If email
     * address is OK - creates price alarm object and saves it
     * (oxpricealarm::save()). Sends price alarm notification mail
     * to shop owner.
     *
     * @return  bool    false on error
     */
    public function addme()
    {
        $myConfig = $this->getConfig();
        $myUtils  = oxRegistry::getUtils();

        //control captcha
        $sMac     = oxConfig::getParameter( 'c_mac' );
        $sMacHash = oxConfig::getParameter( 'c_mach' );
        $oCaptcha = $this->getCaptcha();
        if ( !$oCaptcha->pass( $sMac, $sMacHash ) ) {
            $this->_iPriceAlarmStatus = 2;
            return;
        }

        $aParams = oxConfig::getParameter( 'pa' );
        if ( !isset( $aParams['email'] ) || !$myUtils->isValidEmail( $aParams['email'] ) ) {
            $this->_iPriceAlarmStatus = 0;
            return;
        }
        $aParams['aid'] = $this->getProduct()->getId();
        $oCur = $myConfig->getActShopCurrencyObject();
        // convert currency to default
        $dPrice = $myUtils->currency2Float( $aParams['price'] );

        $oAlarm = oxNew( "oxpricealarm" );
        $oAlarm->oxpricealarm__oxuserid = new oxField( oxSession::getVar( 'usr' ));
        $oAlarm->oxpricealarm__oxemail  = new oxField( $aParams['email']);
        $oAlarm->oxpricealarm__oxartid  = new oxField( $aParams['aid']);
        $oAlarm->oxpricealarm__oxprice  = new oxField( $myUtils->fRound( $dPrice, $oCur ));
        $oAlarm->oxpricealarm__oxshopid = new oxField( $myConfig->getShopId());
        $oAlarm->oxpricealarm__oxcurrency = new oxField( $oCur->name);

        $oAlarm->oxpricealarm__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());

        $oAlarm->save();

        // Send Email
        $oEmail = oxNew( 'oxemail' );
        $this->_iPriceAlarmStatus = (int) $oEmail->sendPricealarmNotification( $aParams, $oAlarm );
    }

    /** NEEDED, unless pricealarm is integrated
     * Return price alarm status (if it was send)
     *
     * @return integer
     */
    public function getPriceAlarmStatus()
    {
        return $this->_iPriceAlarmStatus;
    }

    /** NEEDED, unless pricealarm is integrated
     * Template variable getter. Returns bid price
     *
     * @return string
     */
    public function getBidPrice()
    {
        if ( $this->_sBidPrice === null ) {
            $this->_sBidPrice = false;

            $aParams = oxConfig::getParameter( 'pa' );
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $iPrice = oxRegistry::getUtils()->currency2Float( $aParams['price'] );
            $this->_sBidPrice = oxRegistry::getLang()->formatCurrency( $iPrice, $oCur );
        }
        return $this->_sBidPrice;
    }

    /** NEEDED
     * Returns variant selection
     *
     * @return oxVariantSelectList
     */
    public function getVariantSelections()
    {
        // finding parent
        $oProduct = $this->getProduct();
        if ( ( $oParent = $this->_getParentProduct( $oProduct->oxarticles__oxparentid->value ) ) ) {
            return $oParent->getVariantSelections( oxConfig::getParameter( "varselid" ), $oProduct->getId() );
        }

        return $oProduct->getVariantSelections( oxConfig::getParameter( "varselid" ) );
    }
//
    /** NEEDED
     * Returns pictures product object
     *
     * @return oxArticle
     */
    public function getPicturesProduct()
    {
        $aVariantSelections = $this->getVariantSelections();
        if ($aVariantSelections && $aVariantSelections['oActiveVariant'] && !$aVariantSelections['blPerfectFit']) {
            return $aVariantSelections['oActiveVariant'];
        }
        return $this->getProduct();
    }

    /** NEEDED? overriding oxubase
     * Template variable getter. Returns search parameter for Html
     *
     * @return string
     */
    public function getSearchParamForHtml()
    {
        if ( $this->_sSearchParamForHtml === null ) {
            $this->_sSearchParamForHtml = oxConfig::getParameter( 'searchparam' );
        }
        return $this->_sSearchParamForHtml;
    }

    /** USED in details, RDFA
     * Returns if page has rdfa
     *
     * @return bool
     */
    public function showRdfa()
    {
        return $this->getConfig()->getConfigParam( 'blRDFaEmbedding' );
    }

    /** USED in details, RDFA
     * Sets normalized rating
     *
     * @return array
     */
    public function getRDFaNormalizedRating()
    {
        $myConfig = $this->getConfig();
        $iMin = $myConfig->getConfigParam("iRDFaMinRating");
        $iMax = $myConfig->getConfigParam("iRDFaMaxRating");

        $oProduct = $this->getProduct();
        $iCount = $oProduct->oxarticles__oxratingcnt->value;
        if ( isset($iMin) && isset($iMax) && $iMax != '' && $iMin != '' && $iCount > 0 ) {
            $aNomalizedRating = array();
            $iValue = ((4*($oProduct->oxarticles__oxrating->value - $iMin)/($iMax - $iMin)))+1;
            $aNomalizedRating["count"] = $iCount;
            $aNomalizedRating["value"] = round($iValue, 2);
            return $aNomalizedRating;
        }
        return false;
    }

    /** USED in details, RDFA
     * Sets and returns validity period of given object
     *
     * @param string $sShopConfVar object name
     *
     * @return array
     */
    public function getRDFaValidityPeriod($sShopConfVar)
    {
        if ( $sShopConfVar ) {
            $aValidity = array();
            $iDays = $this->getConfig()->getConfigParam($sShopConfVar);
            $iFrom = oxRegistry::get("oxUtilsDate")->getTime();

            $iThrough = $iFrom + ($iDays * 24 * 60 * 60);
            $aValidity["from"] = date('Y-m-d\TH:i:s', $iFrom)."Z";
            $aValidity["through"] = date('Y-m-d\TH:i:s', $iThrough)."Z";

            return $aValidity;
        }
        return false;
    }

    /** USED in details, RDFA
     * Gets business function of the gr:Offering
     *
     * @return string
     */
    public function getRDFaBusinessFnc()
    {
        return $this->getConfig()->getConfigParam("sRDFaBusinessFnc");
    }

    /** USED in details, RDFA
     * Gets the types of customers for which the given gr:Offering is valid
     *
     * @return array
     */
    public function getRDFaCustomers()
    {
        return $this->getConfig()->getConfigParam("aRDFaCustomers");
    }

    /** USED in details, RDFA
     * Gets information whether prices include vat
     *
     * @return int
     */
    public function getRDFaVAT()
    {
        return $this->getConfig()->getConfigParam("iRDFaVAT");
    }

    /** USED in details, RDFA
     * Gets a generic description of product condition
     *
     * @return string
     */
    public function getRDFaGenericCondition()
    {
        return $this->getConfig()->getConfigParam("iRDFaCondition");
    }

    /** USED in details, RDFA
     * Returns bundle product
     *
     * @return object
     */
    public function getBundleArticle()
    {
        $oProduct = $this->getProduct();
        if ( $oProduct && $oProduct->oxarticles__oxbundleid->value ) {
            $oArticle = oxNew("oxarticle");
            $oArticle->load($oProduct->oxarticles__oxbundleid->value);
            return $oArticle;
        }
        return false;
    }

    /** USED in details, RDFA
     * Gets accepted payment methods
     *
     * @return array
     */
    public function getRDFaPaymentMethods()
    {
        $iPrice = $this->getProduct()->getPrice()->getBruttoPrice();
        $oPayments = oxNew("oxpaymentlist");
        $oPayments->loadRDFaPaymentList($iPrice);
        return $oPayments;
    }

    /** USED in details, RDFA
     * Returns delivery methods with assigned deliverysets.
     *
     * @return object
     */
    public function getRDFaDeliverySetMethods()
    {
        $oDelSets = oxNew("oxdeliverysetlist");
        $oDelSets->loadRDFaDeliverySetList();
        return $oDelSets;
    }

    /** USED in details, RDFA
     * Template variable getter. Returns delivery list for current product
     *
     * @return object
     */
    public function getProductsDeliveryList()
    {
        $oProduct = $this->getProduct();
        $oDelList = oxNew( "oxDeliveryList" );
        $oDelList->loadDeliveryListForProduct( $oProduct );
        return $oDelList;
    }

    /** USED in details, RDFA
     * Gets content id of delivery information page
     *
     * @return string
     */
    public function getRDFaDeliveryChargeSpecLoc()
    {
        return $this->getConfig()->getConfigParam("sRDFaDeliveryChargeSpecLoc");
    }

    /** USED in details, RDFA
     * Gets content id of payments
     *
     * @return string
     */
    public function getRDFaPaymentChargeSpecLoc()
    {
        return $this->getConfig()->getConfigParam("sRDFaPaymentChargeSpecLoc");
    }

    /** USED in details, RDFA
     * Gets content id of company info page (About Us)
     *
     * @return string
     */
    public function getRDFaBusinessEntityLoc()
    {
        return $this->getConfig()->getConfigParam("sRDFaBusinessEntityLoc");
    }

    /** USED in details, RDFA
     * Returns if to show products left stock
     *
     * @return string
     */
    public function showRDFaProductStock()
    {
        return $this->getConfig()->getConfigParam("blShowRDFaProductStock");
    }

    /** NEEDED
     * Get product article
     *
     * @return oxArticle
     */
    public function getProduct()
    {
//        $sOxid = oxRegistry::getConfig()->getRequestParameter( 'anid' );
//        /**
//         * @var $oArticle oxArticle
//         */
//        $oArticle = oxNew( 'oxArticle' );
//        $oArticle->load( $sOxid );
//
//
//        return $oArticle;

        $myConfig = $this->getConfig();
        $myUtils = oxRegistry::getUtils();

        if ( $this->_oProduct === null ) {

            //this option is only for lists and we must reset value
            //as blLoadVariants = false affect "ab price" functionality
            $myConfig->setConfigParam( 'blLoadVariants', true );

            $sOxid = oxConfig::getParameter( 'anid' );

            // object is not yet loaded
            $this->_oProduct = oxNew( 'oxarticle' );

            if ( !$this->_oProduct->load( $sOxid ) ) {
                $myUtils->redirect( $myConfig->getShopHomeURL() );
                $myUtils->showMessageAndExit( '' );
    }

            $aVariantSelections = $this->_oProduct->getVariantSelections( oxConfig::getParameter( "varselid" ) );
            if ($aVariantSelections && $aVariantSelections['oActiveVariant'] && $aVariantSelections['blPerfectFit']) {
                $this->_oProduct = $aVariantSelections['oActiveVariant'];
    }
    }

        // additional checks
        if ( !$this->_blIsInitialized ) {

            $blContinue = true;
            if ( !$this->_oProduct->isVisible() ) {
                $blContinue = false;
            } elseif ( $this->_oProduct->oxarticles__oxparentid->value ) {
                $oParent = $this->_getParentProduct( $this->_oProduct->oxarticles__oxparentid->value );
                if ( !$oParent || !$oParent->isVisible() ) {
                    $blContinue = false;
    }
    }

            if ( !$blContinue ) {
                $myUtils->redirect( $myConfig->getShopHomeURL() );
                $myUtils->showMessageAndExit( '' );
    }

            $this->_processProduct( $this->_oProduct );
            $this->_blIsInitialized = true;
    }

        return $this->_oProduct;
    }

    /**
     * If possible loads additional article info (oxarticle::getCrossSelling(),
     * oxarticle::getAccessoires(), oxarticle::getReviews(), oxarticle::GetSimilarProducts(),
     * oxarticle::GetCustomerAlsoBoughtThisProducts()), forms variants details
     * navigation URLs
     * loads selectlists (oxarticle::GetSelectLists()), prerares HTML meta data
     * (details::_convertForMetaTags()). Returns name of template file
     * details::_sThisTemplate
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {

        $myConfig = $this->getConfig();

        $oProduct = $this->getProduct();

        // assign template name
        if ( $oProduct->oxarticles__oxtemplate->value ) {
            $this->_sThisTemplate = $oProduct->oxarticles__oxtemplate->value;
        }

        if ( ( $sTplName = oxConfig::getParameter( 'tpl' ) ) ) {
            $this->_sThisTemplate = 'custom/'.basename ( $sTplName );
        }

        parent::render();

        $sPartial = oxConfig::getParameter('renderPartial');
        $this->addTplParam('renderPartial', $sPartial);

        $oCategory = new oxCategory();
        $oCategory->setId( oxRegistry::getConfig()->getRequestParameter( "cnid" ) );
        $this->setActiveCategory( $oCategory );

        switch ($sPartial) {
            case "productInfo":
                return 'page/details/ajax/fullproductinfo.tpl';
                break;
            case "detailsMain":
                return 'page/details/ajax/productmain.tpl';
                break;
            default:
                // #785A loads and sets locator data

                /**
                 * @var $oLocator oxLocator
                 */
                $oLocator = oxNew( 'oxlocator', $this->getListType() );
                $oLocator->setLocatorData( $oProduct, $this );
//                var_dump( $oLocator, $this->getListType() );
                if ($myConfig->getConfigParam( 'bl_rssRecommLists' ) && $this->getSimilarRecommListIds()) {
                    $oRss = oxNew('oxrssfeed');
                    $this->addRssFeed($oRss->getRecommListsTitle( $oProduct ), $oRss->getRecommListsUrl( $oProduct ), 'recommlists');
                }
                return $this->_sThisTemplate;
        }
    }
}