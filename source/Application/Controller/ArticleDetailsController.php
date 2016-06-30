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

namespace OxidEsales\Eshop\Application\Controller;

use oxArticle;
use oxArticleList;
use oxArticleTagList;
use oxCategory;
use oxDeliveryList;
use oxDeliverySetList;
use oxPaymentList;
use oxRegistry;
use oxField;
use oxTagCloud;
use oxVariantSelectList;

/**
 * Article details information page.
 * Collects detailed article information, possible variants, such information
 * as crosselling, similarlist, picture gallery list, etc.
 * OXID eShop -> (Any chosen product).
 */
class ArticleDetailsController extends \oxUBase
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
     * @param string $parentId parent product id
     *
     * @return oxArticle
     */
    protected function _getParentProduct($parentId)
    {
        if ($parentId && $this->_oParentProd === null) {
            $this->_oParentProd = false;
            $article = oxNew('oxArticle');
            if (($article->load($parentId))) {
                $this->_processProduct($article);
                $this->_oParentProd = $article;
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
        $parameters = parent::getNavigationParams();

        $variantSelectionListId = oxRegistry::getConfig()->getRequestParameter('varselid');
        $selectListParameters = oxRegistry::getConfig()->getRequestParameter('sel');
        if (!$variantSelectionListId && !$selectListParameters) {
            return $parameters;
        }

        if ($variantSelectionListId) {
            foreach ($variantSelectionListId as $key => $value) {
                $parameters["varselid[$key]"] = $value;
            }
        }

        if ($selectListParameters) {
            foreach ($selectListParameters as $key => $value) {
                $parameters["sel[$key]"] = $value;
            }
        }

        return $parameters;
    }


    /**
     * Processes product by setting link type and in case list type is search adds search parameters to details link
     *
     * @param oxArticle $article Product to process
     */
    protected function _processProduct($article)
    {
        $article->setLinkType($this->getLinkType());
        if ($dynamicParameters = $this->_getAddUrlParams()) {
            $article->appendLink($dynamicParameters);
        }
    }

    /**
     * Generates current view id.
     *
     * @return string
     */
    protected function generateViewId()
    {
        return parent::generateViewId() . '|' . $this->getConfig()->getRequestParameter('anid') . '|';
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
        $config = $this->getConfig();

        $article = $this->getProduct();

        // assign template name
        if ($article->oxarticles__oxtemplate->value) {
            $this->_sThisTemplate = $article->oxarticles__oxtemplate->value;
        }

        if (($templateName = oxRegistry::getConfig()->getRequestParameter('tpl'))) {
            $this->_sThisTemplate = 'custom/' . basename($templateName);
        }

        parent::render();

        $renderPartial = $this->getConfig()->getRequestParameter('renderPartial');
        $this->addTplParam('renderPartial', $renderPartial);

        switch ($renderPartial) {
            case "productInfo":
                return 'page/details/ajax/fullproductinfo.tpl';
                break;
            case "detailsMain":
                return 'page/details/ajax/productmain.tpl';
                break;
            default:
                // can not be removed, as it is used for breadcrumb loading
                $locator = oxNew('oxLocator', $this->getListType());
                $locator->setLocatorData($article, $this);

                // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
                if ($config->getConfigParam('bl_rssRecommLists') && $this->getSimilarRecommListIds()) {
                    $rssFeeds = oxNew('oxRssFeed');
                    $title = $rssFeeds->getRecommListsTitle($article);
                    $url = $rssFeeds->getRecommListsUrl($article);
                    $this->addRssFeed($title, $url, 'recommlists');
                }
                // END deprecated

                return $this->_sThisTemplate;
        }
    }

    /**
     * Returns current view meta data
     * If $meta parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $meta           User defined description, description content or empty value
     * @param int    $length         Max length of result, -1 for no truncation
     * @param bool   $descriptionTag If true - performs additional duplicate cleaning
     *
     * @return string
     */
    protected function _prepareMetaDescription($meta, $length = 200, $descriptionTag = false)
    {
        if (!$meta) {
            $article = $this->getProduct();

            if ($this->getConfig()->getConfigParam('bl_perfParseLongDescinSmarty')) {
                $meta = $article->getLongDesc();
            } else {
                $meta = $article->getLongDescription()->value;
            }
            if ($meta == '') {
                $meta = $article->oxarticles__oxshortdesc->value;
            }
            $meta = $article->oxarticles__oxtitle->value . ' - ' . $meta;
        }

        return parent::_prepareMetaDescription($meta, $length, $descriptionTag);
    }

    /**
     * Returns current view keywords seperated by comma
     * If $keywords parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $keywords              User defined keywords, keywords content or empty value
     * @param bool   $removeDuplicatedWords Remove duplicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword($keywords, $removeDuplicatedWords = true)
    {
        if (!$keywords) {
            $article = $this->getProduct();
            $keywords = trim($this->getTitle());

            if ($categoryTree = $this->getCategoryTree()) {
                foreach ($categoryTree->getPath() as $category) {
                    $keywords .= ", " . trim($category->oxcategories__oxtitle->value);
                }
            }

            // Adding search keys info
            if ($searchKeys = trim($article->oxarticles__oxsearchkeys->value)) {
                $keywords .= ", " . $searchKeys;
            }

            $keywords = parent::_prepareMetaKeyword($keywords, $removeDuplicatedWords);
        }

        return $keywords;
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
            ($user = $this->getUser()) && ($article = $this->getProduct())
        ) {
            $articleRating = $this->getConfig()->getRequestParameter('artrating');
            if ($articleRating !== null) {
                $articleRating = (int) $articleRating;
            }

            //save rating
            if ($articleRating !== null && $articleRating >= 1 && $articleRating <= 5) {
                $rating = oxNew('oxRating');
                if ($rating->allowRating($user->getId(), 'oxarticle', $article->getId())) {
                    $rating->oxratings__oxuserid = new oxField($user->getId());
                    $rating->oxratings__oxtype = new oxField('oxarticle');
                    $rating->oxratings__oxobjectid = new oxField($article->getId());
                    $rating->oxratings__oxrating = new oxField($articleRating);
                    $rating->save();
                    $article->addToRatingAverage($articleRating);
                }
            }

            if (($reviewText = trim(( string ) $this->getConfig()->getRequestParameter('rvw_txt', true)))) {
                $review = oxNew('oxReview');
                $review->oxreviews__oxobjectid = new oxField($article->getId());
                $review->oxreviews__oxtype = new oxField('oxarticle');
                $review->oxreviews__oxtext = new oxField($reviewText, oxField::T_RAW);
                $review->oxreviews__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());
                $review->oxreviews__oxuserid = new oxField($user->getId());
                $review->oxreviews__oxrating = new oxField(($articleRating !== null) ? $articleRating : 0);
                $review->save();
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

        $recommendationText = trim(( string ) $this->getConfig()->getRequestParameter('recomm_txt'));
        $recommendationListId = $this->getConfig()->getRequestParameter('recomm');
        $articleId = $this->getProduct()->getId();

        if ($articleId) {
            $recommendationList = oxNew('oxRecommList');
            $recommendationList->load($recommendationListId);
            $recommendationList->addArticle($articleId, $recommendationText);
        }
    }

    /**
     * Adds tags from parameter
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @return null
     */
    public function addTags()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        $tags = $this->getConfig()->getRequestParameter('newTags', true);
        $highTag = $this->getConfig()->getRequestParameter('highTags', true);
        if (!$tags && !$highTag) {
            return;
        }
        if ($highTag) {
            $tags = getStr()->html_entity_decode($highTag);
        }
        $article = $this->getProduct();

        // set current user added tags for this article for later checking
        $taggedArticles = oxRegistry::getSession()->getVariable("aTaggedProducts");
        $addedTags = $taggedArticles ? $taggedArticles[$article->getId()] : array();

        $articleTagList = oxNew("oxArticleTagList");
        $articleTagList->load($article->getId());
        $separator = $articleTagList->get()->getSeparator();
        $uniqueTags = array_unique(explode($separator, $tags));

        $result = $this->_addTagsToList($articleTagList, $uniqueTags, $addedTags);

        if (!empty($result['tags'])) {
            $articleTagList->save();
            foreach ($result['tags'] as $tag) {
                $addedTags[$tag] = 1;
            }
            $taggedArticles[$article->getId()] = $addedTags;
            oxRegistry::getSession()->setVariable('aTaggedProducts', $taggedArticles);
        }
        // for ajax call
        if ($this->getConfig()->getRequestParameter('blAjax', true)) {
            oxRegistry::getUtils()->showMessageAndExit(json_encode($result));
        }
    }

    /**
     * Adds tags to passed oxArticleTagList object
     *
     * @param oxArticleTagList $articleTagList Article tags list object
     * @param array            $tags           Tags array to add to list
     * @param array            $addedTags      Tags, which are already added to list
     *
     * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
     *             
     * @return array
     */
    protected function _addTagsToList($articleTagList, $tags, $addedTags)
    {
        $result = array('tags' => array(), 'invalid' => array(), 'inlist' => array());

        foreach ($tags as $tagName) {
            $tag = oxNew("oxTag", $tagName);
            if ($addedTags[$tag->get()] != 1) {
                if ($tag->isValid()) {
                    $articleTagList->addTag($tag);
                    $result['tags'][] = $tag->get();
                } else {
                    $result['invalid'][] = $tag->get();
                }
            } else {
                $result['inlist'][] = $tag->get();
            }
        }

        return $result;
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
        $articleTagList = oxNew("oxArticleTagList");
        $articleTagList->load($this->getProduct()->getId());
        $tagSet = $articleTagList->get();
        $this->_aTags = $tagSet->get();
        $this->_blEditTags = true;

        // for ajax call
        if ($this->getConfig()->getRequestParameter('blAjax', true)) {
            $charset = oxRegistry::getLang()->translateString('charset');
            oxRegistry::getUtils()->setHeader("Content-Type: text/html; charset=" . $charset);
            $smarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $smarty->assign('oView', $this);
            $smarty->assign('oViewConf', $this->getViewConfig());
            oxRegistry::getUtils()->showMessageAndExit(
                $smarty->fetch('page/details/inc/editTags.tpl', $this->getViewId())
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
        $articleTagList = oxNew("oxArticleTagList");
        $articleTagList->load($this->getProduct()->getId());
        $tagSet = $articleTagList->get();
        $this->_aTags = $tagSet->get();
        $this->_blEditTags = false;

        // for ajax call
        if (oxRegistry::getConfig()->getRequestParameter('blAjax', true)) {
            $charset = oxRegistry::getLang()->translateString('charset');
            oxRegistry::getUtils()->setHeader("Content-Type: text/html; charset=" . $charset);
            $smarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $smarty->assign('oView', $this);
            $smarty->assign('oViewConf', $this->getViewConfig());
            oxRegistry::getUtils()->showMessageAndExit(
                $smarty->fetch('page/details/inc/tags.tpl', $this->getViewId())
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
        if ($article = $this->getProduct()) {
            return $article->getId();
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
        $config = $this->getConfig();
        $utils = oxRegistry::getUtils();

        if ($this->_oProduct === null) {
            //this option is only for lists and we must reset value
            //as blLoadVariants = false affect "ab price" functionality
            $config->setConfigParam('blLoadVariants', true);

            $articleId = $this->getConfig()->getRequestParameter('anid');

            // object is not yet loaded
            $this->_oProduct = oxNew('oxArticle');

            if (!$this->_oProduct->load($articleId)) {
                $utils->redirect($config->getShopHomeURL());
                $utils->showMessageAndExit('');
            }

            $variantSelectionId = $this->getConfig()->getRequestParameter("varselid");
            $variantSelections = $this->_oProduct->getVariantSelections($variantSelectionId);
            if ($variantSelections && $variantSelections['oActiveVariant'] && $variantSelections['blPerfectFit']) {
                $this->_oProduct = $variantSelections['oActiveVariant'];
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
        $config = $this->getConfig();
        $utils = oxRegistry::getUtils();

        $shouldContinue = true;
        if (!$this->_oProduct->isVisible()) {
            $shouldContinue = false;
        } elseif ($this->_oProduct->oxarticles__oxparentid->value) {
            $parentArticle = $this->_getParentProduct($this->_oProduct->oxarticles__oxparentid->value);
            if (!$parentArticle || !$parentArticle->isVisible()) {
                $shouldContinue = false;
            }
        }

        if (!$shouldContinue) {
            $utils->redirect($config->getShopHomeURL());
            $utils->showMessageAndExit('');
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
            $listType = $this->getConfig()->getRequestParameter('listtype');
            if ('vendor' == $listType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_VENDOR;
            } elseif ('manufacturer' == $listType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_MANUFACTURER;
                // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
            } elseif ('tag' == $listType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_TAG;
                // END deprecated
                // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            } elseif ('recommlist' == $listType) {
                $this->_iLinkType = OXARTICLE_LINKTYPE_RECOMM;
                // END deprecated
            } else {
                $this->_iLinkType = OXARTICLE_LINKTYPE_CATEGORY;

                // price category has own type..
                $activeCategory = $this->getActiveCategory();
                if ($activeCategory && $activeCategory->isPriceCategory()) {
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
            if (($parentArticle = $this->_getParentProduct($this->getProduct()->oxarticles__oxparentid->value))) {
                $this->_sParentName = $parentArticle->oxarticles__oxtitle->value;
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
            if (($parentArticle = $this->_getParentProduct($this->getProduct()->oxarticles__oxparentid->value))) {
                $this->_sParentUrl = $parentArticle->getLink();
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
        $picturesGallery = $this->getPictureGallery();

        return $picturesGallery['ActPicID'];
    }

    /**
     * Template variable getter. Returns active picture
     *
     * @return object
     */
    public function getActPicture()
    {
        $picturesGallery = $this->getPictureGallery();

        return $picturesGallery['ActPic'];
    }

    /**
     * Template variable getter. Returns pictures of current article
     *
     * @return array
     */
    public function getPictures()
    {
        $picturesGallery = $this->getPictureGallery();

        return $picturesGallery['Pics'];
    }

    /**
     * Template variable getter. Returns selected picture
     *
     * @param string $pictureNumber
     *
     * @return string
     */
    public function getArtPic($pictureNumber)
    {
        $picturesGallery = $this->getPictureGallery();

        return $picturesGallery['Pics'][$pictureNumber];
    }

    /**
     * Template variable getter. Returns selectLists of current article
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
            if ($article = $this->getProduct()) {
                $this->_oCrossSelling = $article->getCrossSelling();
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
            if ($article = $this->getProduct()) {
                $this->_oSimilarProducts = $article->getSimilarProducts();
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

            if ($article = $this->getProduct()) {
                $this->_aSimilarRecommListIds = array($article->getId());
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
            if ($article = $this->getProduct()) {
                $this->_oAccessoires = $article->getAccessoires();
            }
        }

        return $this->_oAccessoires;
    }

    /**
     * Template variable getter. Returns list of customer also bought these products
     *
     * @return oxArticleList|null
     */
    public function getAlsoBoughtTheseProducts()
    {
        if ($this->_aAlsoBoughtArts === null) {
            $this->_aAlsoBoughtArts = false;
            if ($article = $this->getProduct()) {
                $this->_aAlsoBoughtArts = $article->getCustomerAlsoBoughtThisProducts();
            }
        }

        return $this->_aAlsoBoughtArts;
    }

    /**
     * Template variable getter. Returns if price alarm is enabled
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
     * @param int $languageId language id
     *
     * @return oxArticle
     */
    protected function _getSubject($languageId)
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
     * @param string $title search title
     */
    public function setSearchTitle($title)
    {
        $this->_sSearchTitle = $title;
    }

    /**
     * Active category path setter.
     *
     * @param string $activeCategoryPath Category tree path
     */
    public function setCatTreePath($activeCategoryPath)
    {
        $this->_sCatTreePath = $activeCategoryPath;
    }

    /**
     * If product details are accessed by vendor url
     * view must not be indexable
     *
     * @return int
     */
    public function noIndex()
    {
        $listType = $this->getConfig()->getRequestParameter('listtype');
        if ($listType && ('vendor' == $listType || 'manufacturer' == $listType)) {
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
        if ($article = $this->getProduct()) {
            // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
            $tag = $this->getTag();
            $articleTitle = $article->oxarticles__oxtitle->value;
            $variantSelectionId = $article->oxarticles__oxvarselect->value;

            $variantSelectionValue = $variantSelectionId ? ' ' . $variantSelectionId : '';
            $tagValue = !empty($tag) ? ' - ' . $tag : '';

            return $articleTitle . $variantSelectionValue . $tagValue;
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
        $meta = parent::getMetaDescription();

        // @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
        if ($tag = $this->getTag()) {
            $meta = $tag . ' - ' . $meta;
        }
        // END deprecated

        return $meta;
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
        if (($article = $this->getProduct())) {
            if ($article->oxarticles__oxparentid->value) {
                $article = $this->_getParentProduct($article->oxarticles__oxparentid->value);
            }

            $utilsUrl = oxRegistry::get("oxUtilsUrl");
            if (oxRegistry::getUtils()->seoIsActive()) {
                $url = $utilsUrl->prepareCanonicalUrl($article->getBaseSeoLink($article->getLanguage(), true));
            } else {
                $url = $utilsUrl->prepareCanonicalUrl($article->getBaseStdLink($article->getLanguage()));
            }

            return $url;
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
            $paths = $this->_getSearchBreadCrumb();
            // @deprecated v5.3 (2016-05-04); Will be moved to own module.
        } elseif ('tag' == $this->getListType()) {
            $paths = $this->_getTagBreadCrumb();
            // END deprecated
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        } elseif ('recommlist' == $this->getListType()) {
            $paths = $this->_getRecommendationListBredCrumb();
            // END deprecated
        } elseif ('vendor' == $this->getListType()) {
            $paths = $this->_getVendorBreadCrumb();
        } else {
            $paths = $this->_getCategoryBreadCrumb();
        }

        return $paths;
    }

    /**
     * Validates email address.
     * If email address is OK - creates price alarm object and saves it (oxPriceAlarm::save()).
     * If email is wrong - returns false.
     * Sends price alarm notification mail to shop owner.
     *
     * @return null
     */
    public function addMe()
    {
        $config = $this->getConfig();
        $utils = oxRegistry::getUtils();

        $parameters = $this->getConfig()->getRequestParameter('pa');
        if (!isset($parameters['email']) || !$utils->isValidEmail($parameters['email'])) {
            $this->_iPriceAlarmStatus = 0;
            return;
        }

        $parameters['aid'] = $this->getProduct()->getId();
        $activeCurrency = $config->getActShopCurrencyObject();
        // convert currency to default
        $price = $utils->currency2Float($parameters['price']);

        $priceAlarm = oxNew("oxPriceAlarm");
        $priceAlarm->oxpricealarm__oxuserid = new oxField(oxRegistry::getSession()->getVariable('usr'));
        $priceAlarm->oxpricealarm__oxemail = new oxField($parameters['email']);
        $priceAlarm->oxpricealarm__oxartid = new oxField($parameters['aid']);
        $priceAlarm->oxpricealarm__oxprice = new oxField($utils->fRound($price, $activeCurrency));
        $priceAlarm->oxpricealarm__oxshopid = new oxField($config->getShopId());
        $priceAlarm->oxpricealarm__oxcurrency = new oxField($activeCurrency->name);

        $priceAlarm->oxpricealarm__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());

        $priceAlarm->save();

        // Send Email
        $email = oxNew('oxEmail');
        $this->_iPriceAlarmStatus = (int) $email->sendPricealarmNotification($parameters, $priceAlarm);
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

            $parameters = $this->getConfig()->getRequestParameter('pa');
            $activeCurrency = $this->getConfig()->getActShopCurrencyObject();
            $price = oxRegistry::getUtils()->currency2Float($parameters['price']);
            $this->_sBidPrice = oxRegistry::getLang()->formatCurrency($price, $activeCurrency);
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
        $article = $this->getProduct();
        $variantSelectionListId = $this->getConfig()->getRequestParameter("varselid");
        if (($articleParent = $this->_getParentProduct($article->oxarticles__oxparentid->value))) {
            return $articleParent->getVariantSelections($variantSelectionListId, $article->getId());
        }

        return $article->getVariantSelections($variantSelectionListId);
    }

    /**
     * Returns pictures product object
     *
     * @return oxArticle
     */
    public function getPicturesProduct()
    {
        $variantSelections = $this->getVariantSelections();
        if ($variantSelections && $variantSelections['oActiveVariant'] && !$variantSelections['blPerfectFit']) {
            return $variantSelections['oActiveVariant'];
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
        $config = $this->getConfig();
        $minRating = $config->getConfigParam("iRDFaMinRating");
        $maxRating = $config->getConfigParam("iRDFaMaxRating");

        $article = $this->getProduct();
        $count = $article->oxarticles__oxratingcnt->value;
        if (isset($minRating) && isset($maxRating) && $maxRating != '' && $minRating != '' && $count > 0) {
            $normalizedRating = array();
            $value = ((4 * ($article->oxarticles__oxrating->value - $minRating) / ($maxRating - $minRating))) + 1;
            $normalizedRating["count"] = $count;
            $normalizedRating["value"] = round($value, 2);

            return $normalizedRating;
        }

        return false;
    }

    /**
     * Sets and returns validity period of given object
     *
     * @param string $configVariableName object name
     *
     * @return array
     */
    public function getRDFaValidityPeriod($configVariableName)
    {
        if ($configVariableName) {
            $validity = array();
            $days = $this->getConfig()->getConfigParam($configVariableName);
            $from = oxRegistry::get("oxUtilsDate")->getTime();

            $through = $from + ($days * 24 * 60 * 60);
            $validity["from"] = date('Y-m-d\TH:i:s', $from) . "Z";
            $validity["through"] = date('Y-m-d\TH:i:s', $through) . "Z";

            return $validity;
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
     * @return oxArticle|false
     */
    public function getBundleArticle()
    {
        $article = $this->getProduct();
        if ($article && $article->oxarticles__oxbundleid->value) {
            $bundle = oxNew("oxArticle");
            $bundle->load($article->oxarticles__oxbundleid->value);

            return $bundle;
        }

        return false;
    }

    /**
     * Gets accepted payment methods
     *
     * @return oxPaymentList
     */
    public function getRDFaPaymentMethods()
    {
        $price = $this->getProduct()->getPrice()->getBruttoPrice();
        $paymentList = oxNew("oxPaymentList");
        $paymentList->loadRDFaPaymentList($price);

        return $paymentList;
    }

    /**
     * Returns delivery methods with assigned delivery sets.
     *
     * @return oxDeliverySetList
     */
    public function getRDFaDeliverySetMethods()
    {
        $deliverySetList = oxNew("oxDeliverySetList");
        $deliverySetList->loadRDFaDeliverySetList();

        return $deliverySetList;
    }

    /**
     * Template variable getter. Returns delivery list for current product
     *
     * @return oxDeliveryList
     */
    public function getProductsDeliveryList()
    {
        $article = $this->getProduct();
        $deliveryList = oxNew("oxDeliveryList");
        $deliveryList->loadDeliveryListForProduct($article);

        return $deliveryList;
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
        if ($this->getUser()) {
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
        $tagList = oxNew("oxArticleTagList");
        $tagList->setArticleId($this->getProduct()->getId());
        $tagCloud = oxNew("oxTagCloud");
        $tagCloud->setTagList($tagList);
        $tagCloud->setExtendedMode(true);

        return $tagCloud;
    }

    /**
     * Template variable getter. Returns if to show zoom pictures
     *
     * @return bool
     */
    public function showZoomPics()
    {
        $pictureGallery = $this->getPictureGallery();

        return $pictureGallery['ZoomPic'];
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
        $sorting = parent::getDefaultSorting();
        $activeCategory = $this->getActiveCategory();

        if ($this->getListType() != 'search' && $activeCategory && $activeCategory instanceof oxCategory) {
            if ($categorySorting = $activeCategory->getDefaultSorting()) {
                $sortingDirection = ($activeCategory->getDefaultSortingMode()) ? "desc" : "asc";
                $sorting = array('sortby' => $categorySorting, 'sortdir' => $sortingDirection);
            }
        }

        return $sorting;
    }

    /**
     * Returns sorting parameters separated by "|"
     *
     * @return string
     */
    public function getSortingParameters()
    {
        $sorting = $this->getSorting($this->getSortIdent());
        if (!is_array($sorting)) {
            return null;
        }

        return implode('|', $sorting);
    }

    /**
     * Vendor bread crumb
     *
     * @return array
     */
    protected function _getVendorBreadCrumb()
    {
        $paths = array();
        $vendorPath = array();

        $vendor = oxNew('oxVendor');
        $vendor->load('root');

        $vendorPath['link'] = $vendor->getLink();
        $vendorPath['title'] = $vendor->oxvendor__oxtitle->value;
        $paths[] = $vendorPath;

        $vendor = $this->getActVendor();
        if (is_a($vendor, 'oxVendor')) {
            $vendorPath['link'] = $vendor->getLink();
            $vendorPath['title'] = $vendor->oxvendor__oxtitle->value;
            $paths[] = $vendorPath;
        }

        return $paths;
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
        $paths = array();
        $recommListPath = array();
        $baseLanguageId = oxRegistry::getLang()->getBaseLanguage();
        $recommListPath['title'] = oxRegistry::getLang()->translateString('LISTMANIA', $baseLanguageId, false);
        $paths[] = $recommListPath;

        return $paths;
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
        $paths = array();

        $tagPath = array();

        $baseLanguageId = oxRegistry::getLang()->getBaseLanguage();
        $selfLink = $this->getViewConfig()->getSelfLink();

        $tagPath['title'] = oxRegistry::getLang()->translateString('TAGS', $baseLanguageId, false);
        $tagPath['link'] = oxRegistry::get("oxSeoEncoder")->getStaticUrl($selfLink . 'cl=tags');
        $paths[] = $tagPath;

        $searchTagParameter = oxRegistry::getConfig()->getRequestParameter('searchtag');
        $stringModifier = getStr();
        $tagPath['title'] = $stringModifier->ucfirst($searchTagParameter);
        $tagPath['link'] = oxRegistry::get("oxSeoEncoderTag")->getTagUrl($searchTagParameter);
        $paths[] = $tagPath;

        return $paths;
    }

    /**
     * Search bread crumb
     *
     * @return array
     */
    protected function _getSearchBreadCrumb()
    {
        $paths = array();
        $searchPath = array();

        $baseLanguageId = oxRegistry::getLang()->getBaseLanguage();
        $translatedString = oxRegistry::getLang()->translateString('SEARCH_RESULT', $baseLanguageId, false);
        $selfLink = $this->getViewConfig()->getSelfLink();
        $sessionToken = oxRegistry::getSession()->getVariable('sess_stoken');

        $searchPath['title'] = sprintf($translatedString, $this->getSearchParamForHtml());
        $searchPath['link'] = $selfLink . 'stoken=' . $sessionToken . "&amp;cl=search&amp;".
                            "searchparam=" . $this->getSearchParamForHtml();

        $paths[] = $searchPath;

        return $paths;
    }

    /**
     * Category bread crumb
     *
     * @return array
     */
    protected function _getCategoryBreadCrumb()
    {
        $paths = array();

        $categoryTree = $this->getCatTreePath();

        if ($categoryTree) {
            foreach ($categoryTree as $category) {
                /** @var oxCategory $category */
                $categoryPath = array();

                $categoryPath['link'] = $category->getLink();
                $categoryPath['title'] = $category->oxcategories__oxtitle->value;

                $paths[] = $categoryPath;
            }
        }

        return $paths;
    }
}
