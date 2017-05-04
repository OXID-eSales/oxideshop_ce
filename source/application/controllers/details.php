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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Article details information page.
 * Collects detailed article information, possible variants, such information
 * as crosselling, similarlist, picture gallery list, etc.
 * OXID eShop -> (Any chosen product).
 */
class Details extends oxUBase
{

    /**
     * Current class default template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/details/details.tpl';

    /**
     * Current product parent article object
     *
     * @var oxArticle
     */
    protected $_oParentProd = null;

    /**
     * If tags will be changed
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @var bool
     */
    protected $_blEditTags = null;

    /**
     * All tags
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @var array
     */
    protected $_aTags = null;

    /**
     * Class handling CAPTCHA image.
     *
     * @deprecated since 5.3.0 (2016.04.07); It will be moved to captcha_module module.
     *
     * @var object
     */
    protected $_oCaptcha = null;

    /**
     * Parent article name
     *
     * @var string
     */
    protected $_sParentName = null;

    /**
     * Parent article url
     *
     * @var string
     */
    protected $_sParentUrl = null;

    /**
     * Picture gallery
     *
     * @var array
     */
    protected $_aPicGallery = null;

    /**
     * Select lists
     *
     * @var array
     */
    protected $_aSelectLists = null;

    /**
     * Reviews of current article
     *
     * @var array
     */
    protected $_aReviews = null;

    /**
     * CrossSelling article list
     *
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products article list
     *
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Accessories of current article
     *
     * @var object
     */
    protected $_oAccessoires = null;

    /**
     * List of customer also bought these products
     *
     * @var object
     */
    protected $_aAlsoBoughtArts = null;

    /**
     * Search title
     *
     * @var string
     */
    protected $_sSearchTitle = null;

    /**
     * Marker if active product was fully initialized before returning it
     * (see details::getProduct())
     *
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
     * Bid price.
     *
     * @var string
     */
    protected $_sBidPrice = null;

    /**
     * Price alarm status.
     *
     * @var integer
     */
    protected $_iPriceAlarmStatus = null;

    /**
     * Search parameter for Html
     *
     * @var string
     */
    protected $_sSearchParamForHtml = null;

    /**
     * Array of id to form recommendation list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *             
     * @var array
     */
    protected $_aSimilarRecommListIds = null;


    /**
     * Marked which defines if current view is sortable or not
     *
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * Returns current product parent article object if it is available
     *
     * @param string $sParentId parent product id
     *
     * @return oxArticle
     */
    protected function _getParentProduct($sParentId)
    {
        if ($sParentId && $this->_oParentProd === null) {
            $this->_oParentProd = false;
            $oProduct = oxNew('oxArticle');
            if (($oProduct->load($sParentId))) {
                $this->_processProduct($oProduct);
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
        if ($this->getListType() == "search") {
            return $this->getDynUrlParams();
        }
    }

    /**
     * Returns array of params => values which are used in hidden forms and as additional url params.
     * NOTICE: this method SHOULD return raw (non encoded into entities) parameters, because values
     * are processed by htmlentities() to avoid security and broken templates problems
     * This exact fix is added for article details to parse variant selection properly for widgets.
     *
     * @return array
     */
    public function getNavigationParams()
    {
        $aParams = parent::getNavigationParams();

        $aVarSelParams = oxRegistry::getConfig()->getRequestParameter('varselid');
        $aSelectListParams = oxRegistry::getConfig()->getRequestParameter('sel');
        if (!$aVarSelParams && !$aSelectListParams) {
            return $aParams;
        }

        if ($aVarSelParams) {
            foreach ($aVarSelParams as $iKey => $sValue) {
                $aParams["varselid[$iKey]"] = $sValue;
            }
        }

        if ($aSelectListParams) {
            foreach ($aSelectListParams as $iKey => $sValue) {
                $aParams["sel[$iKey]"] = $sValue;
            }
        }

        return $aParams;
    }


    /**
     * Processes product by setting link type and in case list type is search adds search parameters to details link
     *
     * @param object $oProduct product to process
     */
    protected function _processProduct($oProduct)
    {
        $oProduct->setLinkType($this->getLinkType());
        if ($sAddParams = $this->_getAddUrlParams()) {
            $oProduct->appendLink($sAddParams);
        }
    }

    /**
     * Returns prefix ID used by template engine.
     *
     * @return  string  $this->_sViewID view id
     */
    public function getViewId()
    {
        if (isset($this->_sViewId)) {
            return $this->_sViewId;
        }

        $sViewId = parent::getViewId() . '|' . $this->getConfig()->getRequestParameter('anid') . '|';


        return $this->_sViewId = $sViewId;
    }


    /**
     * If possible loads additional article info (oxArticle::getCrossSelling(),
     * oxArticle::getAccessoires(), oxArticle::getReviews(), oxArticle::GetSimilarProducts(),
     * oxArticle::GetCustomerAlsoBoughtThisProducts()), forms variants details
     * navigation URLs
     * loads select lists (oxArticle::GetSelectLists()), prepares HTML meta data
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
        if ($oProduct->oxarticles__oxtemplate->value) {
            $this->_sThisTemplate = $oProduct->oxarticles__oxtemplate->value;
        }

        if (($sTplName = oxRegistry::getConfig()->getRequestParameter('tpl'))) {
            $this->_sThisTemplate = 'custom/' . basename($sTplName);
        }

        parent::render();

        $sPartial = $this->getConfig()->getRequestParameter('renderPartial');
        $this->addTplParam('renderPartial', $sPartial);

        switch ($sPartial) {
            case "productInfo":
                return 'page/details/ajax/fullproductinfo.tpl';
                break;
            case "detailsMain":
                return 'page/details/ajax/productmain.tpl';
                break;
            default:
                // can not be removed, as it is used for breadcrumb loading
                $oLocator = oxNew('oxLocator', $this->getListType());
                $oLocator->setLocatorData($oProduct, $this);

                // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
                if ($myConfig->getConfigParam('bl_rssRecommLists') && $this->getSimilarRecommListIds()) {
                    $oRss = oxNew('oxRssFeed');
                    $sTitle = $oRss->getRecommListsTitle($oProduct);
                    $sUrl = $oRss->getRecommListsUrl($oProduct);
                    $this->addRssFeed($sTitle, $sUrl, 'recommlists');
                }
                // END deprecated                

                return $this->_sThisTemplate;
        }
    }

    /**
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
    protected function _prepareMetaDescription($sMeta, $iLength = 200, $blDescTag = false)
    {
        if (!$sMeta) {
            $oProduct = $this->getProduct();

            if ($this->getConfig()->getConfigParam('bl_perfParseLongDescinSmarty')) {
                $sMeta = $oProduct->getLongDesc();
            } else {
                $sMeta = $oProduct->getLongDescription()->value;
            }
            if ($sMeta == '') {
                $sMeta = $oProduct->oxarticles__oxshortdesc->value;
            }
            $sMeta = $oProduct->oxarticles__oxtitle->value . ' - ' . $sMeta;
        }

        return parent::_prepareMetaDescription($sMeta, $iLength, $blDescTag);
    }

    /**
     * Returns current view keywords seperated by comma
     * If $sKeywords parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sKeywords               user defined keywords, keywords content or empty value
     * @param bool   $blRemoveDuplicatedWords remove dublicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword($sKeywords, $blRemoveDuplicatedWords = true)
    {
        if (!$sKeywords) {
            $oProduct = $this->getProduct();
            $sKeywords = trim($this->getTitle());

            if ($oCatTree = $this->getCategoryTree()) {
                foreach ($oCatTree->getPath() as $oCat) {
                    $sKeywords .= ", " . trim($oCat->oxcategories__oxtitle->value);
                }
            }

            //adding search keys info
            if ($sSearchKeys = trim($oProduct->oxarticles__oxsearchkeys->value)) {
                $sKeywords .= ", " . $sSearchKeys;
            }

            $sKeywords = parent::_prepareMetaKeyword($sKeywords, $blRemoveDuplicatedWords);
        }

        return $sKeywords;
    }

    /**
     * Saves user ratings and review text (oxReview object)
     *
     * @return null
     */
    public function saveReview()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        if ($this->canAcceptFormData() &&
            ($oUser = $this->getUser()) && ($oProduct = $this->getProduct())
        ) {

            $dRating = $this->getConfig()->getRequestParameter('artrating');
            if ($dRating !== null) {
                $dRating = (int) $dRating;
            }

            //save rating
            if ($dRating !== null && $dRating >= 1 && $dRating <= 5) {
                $oRating = oxNew('oxrating');
                if ($oRating->allowRating($oUser->getId(), 'oxarticle', $oProduct->getId())) {
                    $oRating->oxratings__oxuserid = new oxField($oUser->getId());
                    $oRating->oxratings__oxtype = new oxField('oxarticle');
                    $oRating->oxratings__oxobjectid = new oxField($oProduct->getId());
                    $oRating->oxratings__oxrating = new oxField($dRating);
                    $oRating->save();
                    $oProduct->addToRatingAverage($dRating);
                }
            }

            if (($sReviewText = trim(( string ) $this->getConfig()->getRequestParameter('rvw_txt', true)))) {
                $oReview = oxNew('oxReview');
                $oReview->oxreviews__oxobjectid = new oxField($oProduct->getId());
                $oReview->oxreviews__oxtype = new oxField('oxarticle');
                $oReview->oxreviews__oxtext = new oxField($sReviewText, oxField::T_RAW);
                $oReview->oxreviews__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());
                $oReview->oxreviews__oxuserid = new oxField($oUser->getId());
                $oReview->oxreviews__oxrating = new oxField(($dRating !== null) ? $dRating : 0);
                $oReview->save();
            }
        }
    }

    /**
     * Adds article to selected recommendation list
     * 
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return null
     */
    public function addToRecomm()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        if (!$this->getViewConfig()->getShowListmania()) {
            return;
        }

        $sRecommText = trim(( string ) $this->getConfig()->getRequestParameter('recomm_txt'));
        $sRecommList = $this->getConfig()->getRequestParameter('recomm');
        $sArtId = $this->getProduct()->getId();

        if ($sArtId) {
            $oRecomm = oxNew('oxrecommlist');
            $oRecomm->load($sRecommList);
            $oRecomm->addArticle($sArtId, $sRecommText);
        }
    }

    /**
     * Adds tags from parameter
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *
     * @return null;
     */
    public function addTags()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        $sTags = $this->getConfig()->getRequestParameter('newTags', true);
        $sHighTag = $this->getConfig()->getRequestParameter('highTags', true);
        if (!$sTags && !$sHighTag) {
            return;
        }
        if ($sHighTag) {
            $sTags = getStr()->html_entity_decode($sHighTag);
        }
        $oProduct = $this->getProduct();

        // set current user added tags for this article for later checking
        $aTaggedProducts = oxRegistry::getSession()->getVariable("aTaggedProducts");
        $aAddedTags = $aTaggedProducts ? $aTaggedProducts[$oProduct->getId()] : array();

        $oArticleTagList = oxNew("oxArticleTagList");
        $oArticleTagList->load($oProduct->getId());
        $sSeparator = $oArticleTagList->get()->getSeparator();
        $aTags = array_unique(explode($sSeparator, $sTags));

        $aResult = $this->_addTagsToList($oArticleTagList, $aTags, $aAddedTags);

        if (!empty($aResult['tags'])) {
            $oArticleTagList->save();
            foreach ($aResult['tags'] as $sTag) {
                $aAddedTags[$sTag] = 1;
            }
            $aTaggedProducts[$oProduct->getId()] = $aAddedTags;
            oxRegistry::getSession()->setVariable('aTaggedProducts', $aTaggedProducts);
        }
        // for ajax call
        if ($this->getConfig()->getRequestParameter('blAjax', true)) {
            oxRegistry::getUtils()->showMessageAndExit(json_encode($aResult));
        }
    }

    /**
     * Adds tags to passed oxArticleTagList object
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *
     * @param oxArticleTagList $oArticleTagList article tags list object
     * @param array            $aTags           tags array to add to list
     * @param array            $aAddedTags      tags, which are already added to list
     *
     * @return array
     */
    protected function _addTagsToList($oArticleTagList, $aTags, $aAddedTags)
    {
        $aResult = array('tags' => array(), 'invalid' => array(), 'inlist' => array());

        foreach ($aTags as $sTag) {
            $oTag = oxNew("oxtag", $sTag);
            if ($aAddedTags[$oTag->get()] != 1) {
                if ($oTag->isValid()) {
                    $oArticleTagList->addTag($oTag);
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
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *
     * @return null
     */
    public function editTags()
    {
        if (!$this->getUser()) {
            return;
        }
        $oArticleTagList = oxNew("oxArticleTagList");
        $oArticleTagList->load($this->getProduct()->getId());
        $oTagSet = $oArticleTagList->get();
        $this->_aTags = $oTagSet->get();
        $this->_blEditTags = true;

        // for ajax call
        if ($this->getConfig()->getRequestParameter('blAjax', true)) {
            $sCharset = oxRegistry::getLang()->translateString('charset');
            oxRegistry::getUtils()->setHeader("Content-Type: text/html; charset=" . $sCharset);
            $oActView = oxNew('oxubase');
            $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $oSmarty->assign('oView', $this);
            $oSmarty->assign('oViewConf', $this->getViewConfig());
            oxRegistry::getUtils()->showMessageAndExit(
                $oSmarty->fetch('page/details/inc/editTags.tpl', $this->getViewId())
            );
        }
    }

    /**
     * Cancels tags editing mode
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     */
    public function cancelTags()
    {
        $oArticleTagList = oxNew("oxArticleTagList");
        $oArticleTagList->load($this->getProduct()->getId());
        $oTagSet = $oArticleTagList->get();
        $this->_aTags = $oTagSet->get();
        $this->_blEditTags = false;

        // for ajax call
        if (oxRegistry::getConfig()->getRequestParameter('blAjax', true)) {
            $sCharset = oxRegistry::getLang()->translateString('charset');
            oxRegistry::getUtils()->setHeader("Content-Type: text/html; charset=" . $sCharset);
            $oActView = oxNew('oxubase');
            $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $oSmarty->assign('oView', $this);
            $oSmarty->assign('oViewConf', $this->getViewConfig());
            oxRegistry::getUtils()->showMessageAndExit(
                $oSmarty->fetch('page/details/inc/tags.tpl', $this->getViewId())
            );
        }
    }

    /**
     * Returns active product id to load its seo meta info
     *
     * @return string
     */
    protected function _getSeoObjectId()
    {
        if ($oProduct = $this->getProduct()) {
            return $oProduct->getId();
        }
    }

    /**
     * Returns if tags will be edit
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *
     * @return bool
     */
    public function getEditTags()
    {
        return $this->_blEditTags;
    }

    /**
     * Returns all tags
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->_aTags;
    }

    /**
     * Returns current product
     *
     * @return oxArticle
     */
    public function getProduct()
    {
        $oConfig = $this->getConfig();
        $oUtils = oxRegistry::getUtils();

        if ($this->_oProduct === null) {

            //this option is only for lists and we must reset value
            //as blLoadVariants = false affect "ab price" functionality
            $oConfig->setConfigParam('blLoadVariants', true);

            $sOxid = $this->getConfig()->getRequestParameter('anid');

            // object is not yet loaded
            $this->_oProduct = oxNew('oxarticle');

            if (!$this->_oProduct->load($sOxid)) {
                $oUtils->redirect($oConfig->getShopHomeURL());
                $oUtils->showMessageAndExit('');
            }

            $sVarSelIdParameter = $this->getConfig()->getRequestParameter("varselid");
            $aVariantSelections = $this->_oProduct->getVariantSelections($sVarSelIdParameter);
            if ($aVariantSelections && $aVariantSelections['oActiveVariant'] && $aVariantSelections['blPerfectFit']) {
                $this->_oProduct = $aVariantSelections['oActiveVariant'];
            }
        }

        // additional checks
        if (!$this->_blIsInitialized) {
            $this->_additionalChecksForArticle();
        }

        return $this->_oProduct;
    }

    /**
     * Runs additional checks for article.
     */
    protected function _additionalChecksForArticle()
    {
        $oConfig = $this->getConfig();
        $oUtils = oxRegistry::getUtils();

        $blContinue = true;
        if (!$this->_oProduct->isVisible()) {
            $blContinue = false;
        } elseif ($this->_oProduct->oxarticles__oxparentid->value) {
            $oParent = $this->_getParentProduct($this->_oProduct->oxarticles__oxparentid->value);
            if (!$oParent || !$oParent->isVisible()) {
                $blContinue = false;
            }
        }

        if (!$blContinue) {
            $oUtils->redirect($oConfig->getShopHomeURL());
            $oUtils->showMessageAndExit('');
        }

        $this->_processProduct($this->_oProduct);
        $this->_blIsInitialized = true;
    }

    /**
     * Returns current view link type
     *
     * @return int
     */
    public function getLinkType()
    {
        if ($this->_iLinkType === null) {
            $sListType = $this->getConfig()->getRequestParameter('listtype');
            if ('vendor' == $sListType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_VENDOR;
            } elseif ('manufacturer' == $sListType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_MANUFACTURER;
                // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
            } elseif ('tag' == $sListType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_TAG;
                // END deprecated
                // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            } elseif ('recommlist' == $sListType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_RECOMM;
                // END deprecated
            } else {
                $this->_iLinkType = OXARTICLE_LINKTYPE_CATEGORY;

                // price category has own type..
                if (($oCat = $this->getActiveCategory()) && $oCat->isPriceCategory()) {
                    $this->_iLinkType = OXARTICLE_LINKTYPE_PRICECATEGORY;
                }
            }
        }

        return $this->_iLinkType;
    }

    /**
     * Template variable getter. Returns if draw parent url
     *
     * @return bool
     */
    public function drawParentUrl()
    {
        return $this->getProduct()->isVariant();
    }

    /**
     * Template variable getter. Returns parent article name
     *
     * @deprecated since v5.1.0 (2013-08-06); not used code anymore
     *
     * @return string
     */
    public function getParentName()
    {
        if ($this->_sParentName === null) {
            $this->_sParentName = false;
            if (($oParent = $this->_getParentProduct($this->getProduct()->oxarticles__oxparentid->value))) {
                $this->_sParentName = $oParent->oxarticles__oxtitle->value;
            }
        }

        return $this->_sParentName;
    }

    /**
     * Template variable getter. Returns parent article name
     *
     * @deprecated since v5.1.0 (2013-08-06); not used code anymore
     *
     * @return string
     */
    public function getParentUrl()
    {
        if ($this->_sParentUrl === null) {
            $this->_sParentUrl = false;
            if (($oParent = $this->_getParentProduct($this->getProduct()->oxarticles__oxparentid->value))) {
                $this->_sParentUrl = $oParent->getLink();
            }
        }

        return $this->_sParentUrl;
    }

    /**
     * Template variable getter. Returns picture gallery of current article
     *
     * @return array
     */
    public function getPictureGallery()
    {
        if ($this->_aPicGallery === null) {
            //get picture gallery
            $this->_aPicGallery = $this->getPicturesProduct()->getPictureGallery();
        }

        return $this->_aPicGallery;
    }

    /**
     * Template variable getter. Returns id of active picture
     *
     * @return string
     */
    public function getActPictureId()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['ActPicID'];
    }

    /**
     * Template variable getter. Returns active picture
     *
     * @return object
     */
    public function getActPicture()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['ActPic'];
    }

    /**
     * Template variable getter. Returns pictures of current article
     *
     * @return array
     */
    public function getPictures()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['Pics'];
    }

    /**
     * Template variable getter. Returns selected picture
     *
     * @param string $sPicNr picture number
     *
     * @return string
     */
    public function getArtPic($sPicNr)
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['Pics'][$sPicNr];
    }

    /**
     * Template variable getter. Returns selectlists of current article
     *
     * @return array
     */
    public function getSelectLists()
    {
        if ($this->_aSelectLists === null) {
            $this->_aSelectLists = false;
            if ($this->getConfig()->getConfigParam('bl_perfLoadSelectLists')) {
                $this->_aSelectLists = $this->getProduct()->getSelectLists();
            }
        }

        return $this->_aSelectLists;
    }

    /**
     * Template variable getter. Returns reviews of current article
     *
     * @return array
     */
    public function getReviews()
    {
        if ($this->_aReviews === null) {
            $this->_aReviews = false;
            if ($this->getConfig()->getConfigParam('bl_perfLoadReviews')) {
                $this->_aReviews = $this->getProduct()->getReviews();
            }
        }

        return $this->_aReviews;
    }

    /**
     * Template variable getter. Returns cross selling
     *
     * @return object
     */
    public function getCrossSelling()
    {
        if ($this->_oCrossSelling === null) {
            $this->_oCrossSelling = false;
            if ($oProduct = $this->getProduct()) {
                $this->_oCrossSelling = $oProduct->getCrossSelling();
            }
        }

        return $this->_oCrossSelling;
    }

    /**
     * Template variable getter. Returns similar article list
     *
     * @return object
     */
    public function getSimilarProducts()
    {
        if ($this->_oSimilarProducts === null) {
            $this->_oSimilarProducts = false;
            if ($oProduct = $this->getProduct()) {
                $this->_oSimilarProducts = $oProduct->getSimilarProducts();
            }
        }

        return $this->_oSimilarProducts;
    }

    /**
     * Return array of id to form recommend list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *             
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ($this->_aSimilarRecommListIds === null) {
            $this->_aSimilarRecommListIds = false;

            if ($oProduct = $this->getProduct()) {
                $this->_aSimilarRecommListIds = array($oProduct->getId());
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
        if ($this->_oAccessoires === null) {
            $this->_oAccessoires = false;
            if ($oProduct = $this->getProduct()) {
                $this->_oAccessoires = $oProduct->getAccessoires();
            }
        }

        return $this->_oAccessoires;
    }

    /**
     * Template variable getter. Returns list of customer also bought these products
     *
     * @return object
     */
    public function getAlsoBoughtTheseProducts()
    {
        if ($this->_aAlsoBoughtArts === null) {
            $this->_aAlsoBoughtArts = false;
            if ($oProduct = $this->getProduct()) {
                $this->_aAlsoBoughtArts = $oProduct->getCustomerAlsoBoughtThisProducts();
            }
        }

        return $this->_aAlsoBoughtArts;
    }

    /**
     * Template variable getter. Returns if price alarm is disabled
     *
     * @return object
     */
    public function isPriceAlarm()
    {
        // #419 disabling price alarm if article has fixed price
        $oProduct = $this->getProduct();
        if (isset($oProduct->oxarticles__oxblfixedprice->value) && $oProduct->oxarticles__oxblfixedprice->value) {
            return 0;
        }

        return 1;
    }

    /**
     * returns object, associated with current view.
     * (the object that is shown in frontend)
     *
     * @param int $iLang language id
     *
     * @return object
     */
    protected function _getSubject($iLang)
    {
        return $this->getProduct();
    }

    /**
     * Returns search title. It will be set in oxLocator
     *
     * @return string
     */
    public function getSearchTitle()
    {
        return $this->_sSearchTitle;
    }

    /**
     * Returns search title setter
     *
     * @param string $sTitle search title
     */
    public function setSearchTitle($sTitle)
    {
        $this->_sSearchTitle = $sTitle;
    }

    /**
     * active category path setter
     *
     * @param string $sActCatPath category tree path
     */
    public function setCatTreePath($sActCatPath)
    {
        $this->_sCatTreePath = $sActCatPath;
    }

    /**
     * If product details are accessed by vendor url
     * view must not be indexable
     *
     * @return int
     */
    public function noIndex()
    {
        $sListType = $this->getConfig()->getRequestParameter('listtype');
        if ($sListType && ('vendor' == $sListType || 'manufacturer' == $sListType)) {
            return $this->_iViewIndexState = VIEW_INDEXSTATE_NOINDEXFOLLOW;
        }

        return parent::noIndex();
    }

    /**
     * Returns current view title. Default is null
     *
     * @return null
     */
    public function getTitle()
    {
        if ($oProduct = $this->getProduct()) {
            // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
            $sTag = $this->getTag();
            $sTitleField = 'oxarticles__oxtitle';
            $sVarSelField = 'oxarticles__oxvarselect';

            $sVarSelValue = $oProduct->$sVarSelField->value ? ' ' . $oProduct->$sVarSelField->value : '';
            $sTagValue = !empty($sTag) ? ' - ' . $sTag : '';

            return $oProduct->$sTitleField->value . $sVarSelValue . $sTagValue;
            // END deprecated
        }
    }

    /**
     * Template variable getter. Returns meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $sMeta = parent::getMetaDescription();

        // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
        if ($sTag = $this->getTag()) {
            $sMeta = $sTag . ' - ' . $sMeta;
        }
        // END deprecated

        return $sMeta;
    }

    /**
     * Template variable getter. Returns current tag
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @return string
     */
    public function getTag()
    {
        return oxRegistry::getConfig()->getRequestParameter("searchtag");
    }

    /**
     * Returns view canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        if (($oProduct = $this->getProduct())) {
            if ($oProduct->oxarticles__oxparentid->value) {
                $oProduct = $this->_getParentProduct($oProduct->oxarticles__oxparentid->value);
            }

            $oUtils = oxRegistry::get("oxUtilsUrl");
            if (oxRegistry::getUtils()->seoIsActive()) {
                $sUrl = $oUtils->prepareCanonicalUrl($oProduct->getBaseSeoLink($oProduct->getLanguage(), true));
            } else {
                $sUrl = $oUtils->prepareCanonicalUrl($oProduct->getBaseStdLink($oProduct->getLanguage()));
            }

            return $sUrl;
        }
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        if ('search' == $this->getListType()) {
            $aPaths = $this->_getSearchBreadCrumb();
            // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
        } elseif ('tag' == $this->getListType()) {
            $aPaths = $this->_getTagBreadCrumb();
            // END deprecated
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        } elseif ('recommlist' == $this->getListType()) {
            $aPaths = $this->_getRecommendationListBredCrumb();
            // END deprecated
        } elseif ('vendor' == $this->getListType()) {
            $aPaths = $this->_getVendorBreadCrumb();
        } else {
            $aPaths = $this->_getCategoryBreadCrumb();
        }

        return $aPaths;
    }

    /**
     * Template variable getter. Returns object of handling CAPTCHA image
     *
     * @deprecated since 5.3.0 (2016.04.07); It will be moved to captcha_module module.
     *
     * @return object
     */
    public function getCaptcha()
    {
        if ($this->_oCaptcha === null) {
            $this->_oCaptcha = oxNew('oxCaptcha');
        }

        return $this->_oCaptcha;
    }

    /**
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
        $myUtils = oxRegistry::getUtils();

        //control captcha
        $sMac = $this->getConfig()->getRequestParameter('c_mac');
        $sMacHash = $this->getConfig()->getRequestParameter('c_mach');
        $oCaptcha = $this->getCaptcha();
        if (!$oCaptcha->pass($sMac, $sMacHash)) {
            $this->_iPriceAlarmStatus = 2;

            return;
        }

        $aParams = $this->getConfig()->getRequestParameter('pa');
        if (!isset($aParams['email']) || !$myUtils->isValidEmail($aParams['email'])) {
            $this->_iPriceAlarmStatus = 0;

            return;
        }
        $aParams['aid'] = $this->getProduct()->getId();
        $oCur = $myConfig->getActShopCurrencyObject();
        // convert currency to default
        $dPrice = $myUtils->currency2Float($aParams['price']);

        $oAlarm = oxNew("oxPriceAlarm");
        $oAlarm->oxpricealarm__oxuserid = new oxField(oxRegistry::getSession()->getVariable('usr'));
        $oAlarm->oxpricealarm__oxemail = new oxField($aParams['email']);
        $oAlarm->oxpricealarm__oxartid = new oxField($aParams['aid']);
        $oAlarm->oxpricealarm__oxprice = new oxField($myUtils->fRound($dPrice, $oCur));
        $oAlarm->oxpricealarm__oxshopid = new oxField($myConfig->getShopId());
        $oAlarm->oxpricealarm__oxcurrency = new oxField($oCur->name);

        $oAlarm->oxpricealarm__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());

        $oAlarm->save();

        // Send Email
        $oEmail = oxNew('oxEmail');
        $this->_iPriceAlarmStatus = (int) $oEmail->sendPricealarmNotification($aParams, $oAlarm);
    }

    /**
     * Return price alarm status (if it was send)
     *
     * @return integer
     */
    public function getPriceAlarmStatus()
    {
        return $this->_iPriceAlarmStatus;
    }

    /**
     * Template variable getter. Returns bid price
     *
     * @return string
     */
    public function getBidPrice()
    {
        if ($this->_sBidPrice === null) {
            $this->_sBidPrice = false;

            $aParams = $this->getConfig()->getRequestParameter('pa');
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $iPrice = oxRegistry::getUtils()->currency2Float($aParams['price']);
            $this->_sBidPrice = oxRegistry::getLang()->formatCurrency($iPrice, $oCur);
        }

        return $this->_sBidPrice;
    }

    /**
     * Returns variant selection
     *
     * @return oxVariantSelectList
     */
    public function getVariantSelections()
    {
        // finding parent
        $oProduct = $this->getProduct();
        $sVarSelParameter = $this->getConfig()->getRequestParameter("varselid");
        $sParentIdField = 'oxarticles__oxparentid';
        if (($oParent = $this->_getParentProduct($oProduct->$sParentIdField->value))) {
            return $oParent->getVariantSelections($sVarSelParameter, $oProduct->getId());
        }

        return $oProduct->getVariantSelections($sVarSelParameter);
    }

    /**
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

    /**
     * Template variable getter. Returns search parameter for Html
     *
     * @return string
     */
    public function getSearchParamForHtml()
    {
        if ($this->_sSearchParamForHtml === null) {
            $this->_sSearchParamForHtml = $this->getConfig()->getRequestParameter('searchparam');
        }

        return $this->_sSearchParamForHtml;
    }

    /**
     * Returns if page has rdfa
     *
     * @return bool
     */
    public function showRdfa()
    {
        return $this->getConfig()->getConfigParam('blRDFaEmbedding');
    }

    /**
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
        if (isset($iMin) && isset($iMax) && $iMax != '' && $iMin != '' && $iCount > 0) {
            $aNormalizedRating = array();
            $iValue = ((4 * ($oProduct->oxarticles__oxrating->value - $iMin) / ($iMax - $iMin))) + 1;
            $aNormalizedRating["count"] = $iCount;
            $aNormalizedRating["value"] = round($iValue, 2);

            return $aNormalizedRating;
        }

        return false;
    }

    /**
     * Sets and returns validity period of given object
     *
     * @param string $sShopConfVar object name
     *
     * @return array
     */
    public function getRDFaValidityPeriod($sShopConfVar)
    {
        if ($sShopConfVar) {
            $aValidity = array();
            $iDays = $this->getConfig()->getConfigParam($sShopConfVar);
            $iFrom = oxRegistry::get("oxUtilsDate")->getTime();

            $iThrough = $iFrom + ($iDays * 24 * 60 * 60);
            $aValidity["from"] = date('Y-m-d\TH:i:s', $iFrom) . "Z";
            $aValidity["through"] = date('Y-m-d\TH:i:s', $iThrough) . "Z";

            return $aValidity;
        }

        return false;
    }

    /**
     * Gets business function of the gr:Offering
     *
     * @return string
     */
    public function getRDFaBusinessFnc()
    {
        return $this->getConfig()->getConfigParam("sRDFaBusinessFnc");
    }

    /**
     * Gets the types of customers for which the given gr:Offering is valid
     *
     * @return array
     */
    public function getRDFaCustomers()
    {
        return $this->getConfig()->getConfigParam("aRDFaCustomers");
    }

    /**
     * Gets information whether prices include vat
     *
     * @return int
     */
    public function getRDFaVAT()
    {
        return $this->getConfig()->getConfigParam("iRDFaVAT");
    }

    /**
     * Gets a generic description of product condition
     *
     * @return string
     */
    public function getRDFaGenericCondition()
    {
        return $this->getConfig()->getConfigParam("iRDFaCondition");
    }

    /**
     * Returns bundle product
     *
     * @return object
     */
    public function getBundleArticle()
    {
        $oProduct = $this->getProduct();
        if ($oProduct && $oProduct->oxarticles__oxbundleid->value) {
            $oArticle = oxNew("oxArticle");
            $oArticle->load($oProduct->oxarticles__oxbundleid->value);

            return $oArticle;
        }

        return false;
    }


    /**
     * Gets accepted payment methods
     *
     * @return array
     */
    public function getRDFaPaymentMethods()
    {
        $iPrice = $this->getProduct()->getPrice()->getBruttoPrice();
        $oPayments = oxNew("oxPaymentList");
        $oPayments->loadRDFaPaymentList($iPrice);

        return $oPayments;
    }

    /**
     * Returns delivery methods with assigned delivery sets.
     *
     * @return object
     */
    public function getRDFaDeliverySetMethods()
    {
        $oDelSets = oxNew("oxDeliverySetList");
        $oDelSets->loadRDFaDeliverySetList();

        return $oDelSets;
    }

    /**
     * Template variable getter. Returns delivery list for current product
     *
     * @return object
     */
    public function getProductsDeliveryList()
    {
        $oProduct = $this->getProduct();
        $oDelList = oxNew("oxDeliveryList");
        $oDelList->loadDeliveryListForProduct($oProduct);

        return $oDelList;
    }

    /**
     * Gets content id of delivery information page
     *
     * @return string
     */
    public function getRDFaDeliveryChargeSpecLoc()
    {
        return $this->getConfig()->getConfigParam("sRDFaDeliveryChargeSpecLoc");
    }

    /**
     * Gets content id of payments
     *
     * @return string
     */
    public function getRDFaPaymentChargeSpecLoc()
    {
        return $this->getConfig()->getConfigParam("sRDFaPaymentChargeSpecLoc");
    }

    /**
     * Gets content id of company info page (About Us)
     *
     * @return string
     */
    public function getRDFaBusinessEntityLoc()
    {
        return $this->getConfig()->getConfigParam("sRDFaBusinessEntityLoc");
    }

    /**
     * Returns if to show products left stock
     *
     * @return string
     */
    public function showRDFaProductStock()
    {
        return $this->getConfig()->getConfigParam("blShowRDFaProductStock");
    }

    /**
     * Checks if rating functionality is on and allowed to user
     * 
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @return bool
     */
    public function canChangeTags()
    {
        if ($oUser = $this->getUser()) {

            return true;
        }

        return false;
    }

    /**
     * Returns tag cloud manager class
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @return oxTagCloud
     */
    public function getTagCloudManager()
    {
        /** @var oxArticleTagList $oTagList */
        $oTagList = oxNew("oxArticleTagList");
        //$oTagList->load($this->getProduct()->getId());
        $oTagList->setArticleId($this->getProduct()->getId());
        $oTagCloud = oxNew("oxTagCloud");
        $oTagCloud->setTagList($oTagList);
        $oTagCloud->setExtendedMode(true);

        return $oTagCloud;
    }

    /**
     * Template variable getter. Returns if to show zoom pictures
     *
     * @return bool
     */
    public function showZoomPics()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['ZoomPic'];
    }

    /**
     * Template variable getter. Returns if review module is on
     *
     * @return bool
     */
    public function isReviewActive()
    {
        return $this->getConfig()->getConfigParam('bl_perfLoadReviews');
    }

    /**
     * Returns default category sorting for selected category
     *
     * @return array
     */
    public function getDefaultSorting()
    {
        $aSorting = parent::getDefaultSorting();
        $oCategory = $this->getActiveCategory();

        if ($this->getListType() != 'search' && $oCategory && $oCategory instanceof oxCategory) {
            if ($sSortBy = $oCategory->getDefaultSorting()) {
                $sSortDir = ($oCategory->getDefaultSortingMode()) ? "desc" : "asc";
                $aSorting = array('sortby' => $sSortBy, 'sortdir' => $sSortDir);
            }
        }

        return $aSorting;
    }

    /**
     * Returns sorting parameters separated by "|"
     *
     * @return string
     */
    public function getSortingParameters()
    {
        $aSorting = $this->getSorting($this->getSortIdent());
        if (!is_array($aSorting)) {
            return null;
        }

        return implode('|', $aSorting);
    }

    /**
     * Vendor bread crumb
     *
     * @return array
     */
    protected function _getVendorBreadCrumb()
    {
        $aPaths = array();
        $aCatPath = array();

        $oCat = oxNew('oxVendor');
        $oCat->load('root');

        $aCatPath['link'] = $oCat->getLink();
        $aCatPath['title'] = $oCat->oxvendor__oxtitle->value;
        $aPaths[] = $aCatPath;

        $oCat = $this->getActVendor();
        if (is_a($oCat, 'oxVendor')) {
            $aCatPath['link'] = $oCat->getLink();
            $aCatPath['title'] = $oCat->oxvendor__oxtitle->value;
            $aPaths[] = $aCatPath;
        }

        return $aPaths;
    }

    /**
     * Recommendation list bread crumb
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *             
     * @return array
     */
    protected function _getRecommendationListBredCrumb()
    {
        $aPaths = array();
        $aCatPath = array();
        $iBaseLanguage = oxRegistry::getLang()->getBaseLanguage();
        $aCatPath['title'] = oxRegistry::getLang()->translateString('LISTMANIA', $iBaseLanguage, false);
        $aPaths[] = $aCatPath;

        return $aPaths;
    }

    /**
     * Tag bread crumb
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @return array
     */
    protected function _getTagBreadCrumb()
    {
        $aPaths = array();

        $aCatPath = array();

        $iBaseLanguage = oxRegistry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();

        $aCatPath['title'] = oxRegistry::getLang()->translateString('TAGS', $iBaseLanguage, false);
        $aCatPath['link'] = oxRegistry::get("oxSeoEncoder")->getStaticUrl($sSelfLink . 'cl=tags');
        $aPaths[] = $aCatPath;

        $sSearchTagParameter = oxRegistry::getConfig()->getRequestParameter('searchtag');
        $oStr = getStr();
        $aCatPath['title'] = $oStr->ucfirst($sSearchTagParameter);
        $aCatPath['link'] = oxRegistry::get("oxSeoEncoderTag")->getTagUrl($sSearchTagParameter);
        $aPaths[] = $aCatPath;

        return $aPaths;
    }

    /**
     * Search bread crumb
     *
     * @return array
     */
    protected function _getSearchBreadCrumb()
    {
        $aPaths = array();
        $aCatPath = array();

        $iBaseLanguage = oxRegistry::getLang()->getBaseLanguage();
        $sTranslatedString = oxRegistry::getLang()->translateString('SEARCH_RESULT', $iBaseLanguage, false);
        $sSelfLink = $this->getViewConfig()->getSelfLink();
        $sSessionToken = oxRegistry::getSession()->getVariable('sess_stoken');

        $aCatPath['title'] = sprintf($sTranslatedString, $this->getSearchParamForHtml());
        $aCatPath['link'] = $sSelfLink . 'stoken=' . $sSessionToken . "&amp;cl=search&amp;".
                            "searchparam=" . $this->getSearchParamForHtml();

        $aPaths[] = $aCatPath;

        return $aPaths;
    }

    /**
     * Category bread crumb
     *
     * @return array
     */
    protected function _getCategoryBreadCrumb()
    {
        $aPaths = array();

        $oCatTree = $this->getCatTreePath();

        if ($oCatTree) {

            foreach ($oCatTree as $oCat) {
                $aCatPath = array();

                $aCatPath['link'] = $oCat->getLink();
                $aCatPath['title'] = $oCat->oxcategories__oxtitle->value;

                $aPaths[] = $aCatPath;
            }

            return $aPaths;
        }

        return $aPaths;
    }
}
