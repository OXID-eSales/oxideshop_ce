<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\SimpleVariantList;
use OxidEsales\Eshop\Application\Model\Vendor;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Core\SortingValidator;
use stdClass;

/**
 * Article detailed information widget.
 */
class ArticleDetails extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
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
     *
     * @var array
     */
    protected $_aComponentNames = ['oxcmp_cur' => 1, 'oxcmp_shop' => 1, 'oxcmp_basket' => 1, 'oxcmp_user' => 1];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/product/details.tpl';

    /**
     * Current product parent article object.
     *
     * @var Article
     */
    protected $_oParentProd = null;

    /**
     * Marker if user can rate current product.
     *
     * @var bool
     */
    protected $_blCanRate = null;

    /**
     * Media files.
     *
     * @var array
     */
    protected $_aMediaFiles = null;

    /**
     * History (last seen) products.
     *
     * @var array
     */
    protected $_aLastProducts = null;

    /**
     * Current product's vendor.
     *
     * @var Vendor
     */
    protected $_oVendor = null;

    /**
     * Current product's manufacturer.
     *
     * @var Manufacturer
     */
    protected $_oManufacturer = null;

    /**
     * Current product's category.
     *
     * @var object
     */
    protected $_oCategory = null;

    /**
     * Current product's attributes.
     *
     * @var array
     */
    protected $_aAttributes = null;

    /**
     * Picture gallery.
     *
     * @var array
     */
    protected $_aPicGallery = null;

    /**
     * Reviews of current article.
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
     * Similar products article list.
     *
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Accessories of current article.
     *
     * @var object
     */
    protected $_oAccessoires = null;

    /**
     * List of customer also bought these products.
     *
     * @var object
     */
    protected $_aAlsoBoughtArts = null;

    /**
     * Search title.
     *
     * @var string
     */
    protected $_sSearchTitle = null;

    /**
     * Marker if active product was fully initialized before returning it.
     * (see details::getProduct())
     *
     * @var bool
     */
    protected $_blIsInitialized = false;

    /**
     * Current view link type.
     *
     * @var int
     */
    protected $_iLinkType = null;

    /**
     * Is multi dimension variant view.
     *
     * @var bool
     */
    protected $_blMdView = null;

    /**
     * Rating value.
     *
     * @var double
     */
    protected $_dRatingValue = null;

    /**
     * Rating count.
     *
     * @var integer
     */
    protected $_iRatingCnt = null;

    /**
     * Bid price.
     *
     * @var string
     */
    protected $_sBidPrice = null;

    /**
     * Marked which defines if current view is sortable or not.
     *
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * Array of id to form recommendation list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;

    /**
     * Template variable getter. Returns active zoom picture id.
     *
     * @return int
     */
    public function getActZoomPic()
    {
        return 1;
    }

    /**
     * Returns current product parent article object if it is available.
     *
     * @param string $sParentId parent product id
     *
     * @return Article
     */
    protected function _getParentProduct($sParentId)
    {
        if ($sParentId && $this->_oParentProd === null) {
            $this->_oParentProd = false;
            $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            if (($oProduct->load($sParentId))) {
                $this->_processProduct($oProduct);
                $this->_oParentProd = $oProduct;
            }
        }

        return $this->_oParentProd;
    }

    /**
     * In case list type is "search" returns search parameters which will be added to product details link.
     *
     * @return string|null
     */
    protected function _getAddUrlParams()
    {
        if ($this->getListType() == "search") {
            return $this->getDynUrlParams();
        }
    }

    /**
     * Processes product by setting link type and in case list type is search adds search parameters to details link.
     *
     * @param object $oProduct Product to process.
     */
    protected function _processProduct($oProduct)
    {
        $oProduct->setLinkType($this->getLinkType());
        if ($sAddParams = $this->_getAddUrlParams()) {
            $oProduct->appendLink($sAddParams);
        }
    }

    /**
     * Checks if rating functionality is active.
     *
     * @return bool
     */
    public function ratingIsActive()
    {
        return $this->getConfig()->getConfigParam('bl_perfLoadReviews');
    }

    /**
     * Checks if rating functionality is on and allowed to user.
     *
     * @return bool
     */
    public function canRate()
    {
        if ($this->_blCanRate === null) {
            $this->_blCanRate = false;

            if ($this->ratingIsActive() && $oUser = $this->getUser()) {
                $oRating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);
                $this->_blCanRate = $oRating->allowRating($oUser->getId(), 'oxarticle', $this->getProduct()->getId());
            }
        }

        return $this->_blCanRate;
    }

    /**
     * Loading full list of attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_aAttributes === null) {
            // all attributes this article has
            $aArtAttributes = $this->getProduct()->getAttributes();

            //making a new array for backward compatibility
            $this->_aAttributes = false;

            if (count($aArtAttributes)) {
                foreach ($aArtAttributes as $sKey => $oAttribute) {
                    $this->_aAttributes[$sKey] = new stdClass();
                    $this->_aAttributes[$sKey]->title = $oAttribute->oxattribute__oxtitle->value;
                    $this->_aAttributes[$sKey]->value = $oAttribute->oxattribute__oxvalue->value;
                }
            }
        }

        return $this->_aAttributes;
    }

    /**
     * Returns current view link type.
     *
     * @return int
     */
    public function getLinkType()
    {
        if ($this->_iLinkType === null) {
            $sListType = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('listtype');
            if ('vendor' == $sListType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_VENDOR;
            } elseif ('manufacturer' == $sListType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_MANUFACTURER;
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
     * Returns variant lists of current product
     * excludes currently viewed product.
     *
     * @return array|SimpleVariantList|ArticleList
     */
    public function getVariantListExceptCurrent()
    {
        $oList = $this->getVariantList();
        if (is_object($oList)) {
            $oList = clone $oList;
        }

        $sOxId = $this->getProduct()->getId();
        if (isset($oList[$sOxId])) {
            unset($oList[$sOxId]);
        }

        return $oList;
    }

    /**
     * Loading full list of variants,
     * if we are child and do not have any variants then let's load all parent variants as ours.
     *
     * @return array|SimpleVariantList|ArticleList
     */
    public function loadVariantInformation()
    {
        if ($this->_aVariantList === null) {
            $oProduct = $this->getProduct();

            //if we are child and do not have any variants then let's load all parent variants as ours
            if ($oParent = $oProduct->getParentArticle()) {
                $myConfig = $this->getConfig();

                $oParent->setNoVariantLoading(false);
                $this->_aVariantList = $oParent->getFullVariants(false);

                //lets additionally add parent article if it is sellable
                if (count($this->_aVariantList) && $myConfig->getConfigParam('blVariantParentBuyable')) {
                    //#1104S if parent is buyable load select lists too
                    $oParent->enablePriceLoad();
                    $oParent->aSelectlist = $oParent->getSelectLists();
                    $this->_aVariantList = array_merge([$oParent], $this->_aVariantList->getArray());
                }
            } else {
                //loading full list of variants
                $this->_aVariantList = $oProduct->getFullVariants(false);
            }

            // setting link type for variants ..
            foreach ($this->_aVariantList as $oVariant) {
                $this->_processProduct($oVariant);
            }
        }

        return $this->_aVariantList;
    }

    /**
     * Returns variant lists of current product.
     *
     * @return array|SimpleVariantList|ArticleList
     */
    public function getVariantList()
    {
        return $this->loadVariantInformation();
    }

    /**
     * Template variable getter. Returns media files of current product.
     *
     * @return array
     */
    public function getMediaFiles()
    {
        if ($this->_aMediaFiles === null) {
            $aMediaFiles = $this->getProduct()->getMediaUrls();
            $this->_aMediaFiles = count($aMediaFiles) ? $aMediaFiles : false;
        }

        return $this->_aMediaFiles;
    }

    /**
     * Template variable getter. Returns last seen products.
     *
     * @param int $iCnt product count
     *
     * @return array
     */
    public function getLastProducts($iCnt = 4)
    {
        if ($this->_aLastProducts === null) {
            //last seen products for #768CA
            $oProduct = $this->getProduct();
            $sParentIdField = 'oxarticles__oxparentid';
            $sArtId = $oProduct->$sParentIdField->value ? $oProduct->$sParentIdField->value : $oProduct->getId();

            $oHistoryArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
            $oHistoryArtList->loadHistoryArticles($sArtId, $iCnt);
            $this->_aLastProducts = $oHistoryArtList;
        }

        return $this->_aLastProducts;
    }

    /**
     * Template variable getter. Returns product's vendor.
     *
     * @return object
     */
    public function getManufacturer()
    {
        if ($this->_oManufacturer === null) {
            $this->_oManufacturer = $this->getProduct()->getManufacturer(false);
        }

        return $this->_oManufacturer;
    }

    /**
     * Template variable getter. Returns product's vendor.
     *
     * @return object
     */
    public function getVendor()
    {
        if ($this->_oVendor === null) {
            $this->_oVendor = $this->getProduct()->getVendor(false);
        }

        return $this->_oVendor;
    }

    /**
     * Template variable getter. Returns product's root category.
     *
     * @return object
     */
    public function getCategory()
    {
        if ($this->_oCategory === null) {
            $this->_oCategory = $this->getProduct()->getCategory();
        }

        return $this->_oCategory;
    }

    /**
     * Template variable getter. Returns picture gallery of current article.
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
     * Template variable getter. Returns active picture.
     *
     * @return object
     */
    public function getActPicture()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['ActPic'];
    }

    /**
     * Template variable getter. Returns true if there more pictures.
     *
     * @return bool
     */
    public function morePics()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['MorePics'];
    }

    /**
     * Template variable getter. Returns icons of current article.
     *
     * @return array
     */
    public function getIcons()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['Icons'];
    }

    /**
     * Template variable getter. Returns if to show zoom pictures.
     *
     * @return bool
     */
    public function showZoomPics()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['ZoomPic'];
    }

    /**
     * Template variable getter. Returns zoom pictures.
     *
     * @return array
     */
    public function getZoomPics()
    {
        $aPicGallery = $this->getPictureGallery();

        return $aPicGallery['ZoomPics'];
    }

    /**
     * Template variable getter. Returns reviews of current article.
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
     * Template variable getter. Returns cross selling.
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
     * Template variable getter. Returns similar article list.
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
                $this->_aSimilarRecommListIds = [$oProduct->getId()];
            }
        }

        return $this->_aSimilarRecommListIds;
    }

    /**
     * Template variable getter. Returns accessories of article.
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
     * Template variable getter. Returns list of customer also bought these products.
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
     * Template variable getter. Returns if price alarm is enabled.
     *
     * @return bool
     */
    public function isPriceAlarm()
    {
        return $this->getProduct()->isPriceAlarm();
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
     * Returns search title. It will be set in Locator.
     *
     * @return string
     */
    public function getSearchTitle()
    {
        return $this->_sSearchTitle;
    }

    /**
     * Returns search title setter.
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
     * @param string $sActCatPath category tree path.
     */
    public function setCatTreePath($sActCatPath)
    {
        $this->_sCatTreePath = $sActCatPath;
    }

    /**
     * Checks should persistent parameter input field be displayed.
     *
     * @return bool
     */
    public function isPersParam()
    {
        $oProduct = $this->getProduct();

        return $oProduct->oxarticles__oxisconfigurable->value;
    }

    /**
     * Template variable getter. Returns rating value.
     *
     * @return double
     */
    public function getRatingValue()
    {
        if ($this->_dRatingValue === null) {
            $this->_dRatingValue = (double) 0;
            if ($this->isReviewActive() && ($oDetailsProduct = $this->getProduct())) {
                $blShowVariantsReviews = $this->getConfig()->getConfigParam('blShowVariantReviews');
                $this->_dRatingValue = round($oDetailsProduct->getArticleRatingAverage($blShowVariantsReviews), 1);
            }
        }

        return (double) $this->_dRatingValue;
    }

    /**
     * Template variable getter. Returns if review module is on.
     *
     * @return bool
     */
    public function isReviewActive()
    {
        return $this->getConfig()->getConfigParam('bl_perfLoadReviews');
    }

    /**
     * Template variable getter. Returns rating count.
     *
     * @return integer
     */
    public function getRatingCount()
    {
        if ($this->_iRatingCnt === null) {
            $this->_iRatingCnt = false;
            if ($this->isReviewActive() && ($oDetailsProduct = $this->getProduct())) {
                $blShowVariantsReviews = $this->getConfig()->getConfigParam('blShowVariantReviews');
                $this->_iRatingCnt = $oDetailsProduct->getArticleRatingCount($blShowVariantsReviews);
            }
        }

        return $this->_iRatingCnt;
    }

    /**
     * Return price alarm status (if it was send).
     *
     * @return integer
     */
    public function getPriceAlarmStatus()
    {
        return $this->getViewParameter('iPriceAlarmStatus');
    }

    /**
     * Template variable getter. Returns bid price.
     *
     * @return string
     */
    public function getBidPrice()
    {
        if ($this->_sBidPrice === null) {
            $this->_sBidPrice = false;

            $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pa');
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $iPrice = \OxidEsales\Eshop\Core\Registry::getUtils()->currency2Float($aParams['price']);
            $this->_sBidPrice = \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($iPrice, $oCur);
        }

        return $this->_sBidPrice;
    }

    /**
     * Returns variant selection.
     *
     * @return array
     */
    public function getVariantSelections()
    {
        // finding parent
        $oProduct = $this->getProduct();
        $sParentIdField = 'oxarticles__oxparentid';
        if (($oParent = $this->_getParentProduct($oProduct->$sParentIdField->value))) {
            $sVarSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("varselid");

            return $oParent->getVariantSelections($sVarSelId, $oProduct->getId());
        }

        return $oProduct->getVariantSelections(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("varselid"));
    }

    /**
     * Returns pictures product object.
     *
     * @return ArticleList
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
     * Get product.
     *
     * @return Article
     */
    public function getProduct()
    {
        $myConfig = $this->getConfig();
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();

        if ($this->_oProduct === null) {
            if ($this->getViewParameter('_object')) {
                $this->_oProduct = $this->getViewParameter('_object');
            } else {
                //this option is only for lists and we must reset value
                //as blLoadVariants = false affect "ab price" functionality
                $myConfig->setConfigParam('blLoadVariants', true);

                $sOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('anid');

                // object is not yet loaded
                $this->_oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

                if (!$this->_oProduct->load($sOxid)) {
                    $myUtils->redirect($myConfig->getShopHomeUrl());
                    $myUtils->showMessageAndExit('');
                }

                $sVarSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("varselid");
                $aVarSelections = $this->_oProduct->getVariantSelections($sVarSelId);
                if ($aVarSelections && $aVarSelections['oActiveVariant'] && $aVarSelections['blPerfectFit']) {
                    $this->_oProduct = $aVarSelections['oActiveVariant'];
                }
            }
        }
        if (!$this->_blIsInitialized) {
            $this->_additionalChecksForArticle($myUtils, $myConfig);
        }

        return $this->_oProduct;
    }

    /**
     * Set item sorting for widget based of retrieved parameters.
     */
    protected function _setSortingParameters()
    {
        $sSortingParameters = $this->getViewParameter('sorting');
        if ($sSortingParameters) {
            list($sortBy, $sortOrder) = explode('|', $sSortingParameters);
            if ((new SortingValidator())->isValid($sortBy, $sortOrder)) {
                $this->setItemSorting($this->getSortIdent(), $sortBy, $sortOrder);
            }
        }
    }

    /**
     * Executes parent::render().
     * Returns name of template file to render.
     *
     * @return string $this->_sThisTemplate current template file name
     */
    public function render()
    {
        $oProduct = $this->getProduct();

        parent::render();

        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        // if category parameter is not found, use category from product
        $sCatId = $this->getViewParameter("cnid");

        if (!$sCatId && $oProduct->getCategory()) {
            $oCategory = $oProduct->getCategory();
        } else {
            $oCategory->load($sCatId);
        }
        $this->_setSortingParameters();

        $this->setActiveCategory($oCategory);

        /**
         * @var $oLocator oxLocator
         */
        $oLocator = oxNew('oxLocator', $this->getListType());
        $oLocator->setLocatorData($oProduct, $this);

        return $this->_sThisTemplate;
    }

    /**
     * Should we show MD variant selection? - Not for 1 dimension variants.
     *
     * @return bool
     */
    public function isMdVariantView()
    {
        if ($this->_blMdView === null) {
            $this->_blMdView = false;
            if ($this->getConfig()->getConfigParam('blUseMultidimensionVariants')) {
                $iMaxMdDepth = $this->getProduct()->getMdVariants()->getMaxDepth();
                $this->_blMdView = ($iMaxMdDepth > 1);
            }
        }

        return $this->_blMdView;
    }

    /**
     * Runs additional checks for article.
     *
     * @param Utils  $myUtils  General utils.
     * @param Config $myConfig Main shop configuration.
     */
    protected function _additionalChecksForArticle($myUtils, $myConfig)
    {
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
            $myUtils->redirect($myConfig->getShopHomeUrl());
            $myUtils->showMessageAndExit('');
        }

        $this->_processProduct($this->_oProduct);
        $this->_blIsInitialized = true;
    }

    /**
     * Returns default category sorting for selected category.
     *
     * @return array
     */
    public function getDefaultSorting()
    {
        $aSorting = parent::getDefaultSorting();

        $oCategory = $this->getActiveCategory();

        if ($this->getListType() != 'search' && $oCategory && $oCategory instanceof \OxidEsales\Eshop\Application\Model\Category) {
            if ($sSortBy = $oCategory->getDefaultSorting()) {
                $sSortDir = ($oCategory->getDefaultSortingMode()) ? "desc" : "asc";
                $aSorting = ['sortby' => $sSortBy, 'sortdir' => $sSortDir];
            }
        }

        return $aSorting;
    }
}
