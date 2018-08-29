<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxArticleInputException;
use oxOutOfStockException;
use oxNoArticleException;
use stdClass;

/**
 * UserBasketItem class, responsible for storing most important fields
 *
 */
class BasketItem extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Product ID
     *
     * @var string
     */
    protected $_sProductId = null;

    /**
     * Basket product title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Variant var select
     *
     * @var string
     */
    protected $_sVarSelect = null;

    /**
     * Product icon name
     *
     * @var string
     */
    protected $_sIcon = null;

    /**
     * Product details link
     *
     * @var string
     */
    protected $_sLink = null;

    /**
     * Item price
     *
     * @var \OxidEsales\Eshop\Core\Price
     */
    protected $_oPrice = null;

    /**
     * Item unit price
     *
     * @var \OxidEsales\Eshop\Core\Price
     */
    protected $_oUnitPrice = null;

    /**
     * Basket item total amount
     *
     * @var double
     */
    protected $_dAmount = 0.0;

    /**
     * Total basket item weight
     *
     * @var double
     */
    protected $_dWeight = 0;

    /**
     * Basket item select lists
     *
     * @var array
     */
    protected $_aSelList = [];

    /**
     * Shop id where product was put into basket
     *
     * @var string
     */
    protected $_sShopId = null;

    /**
     * Native product shop Id
     *
     * @var string
     */
    protected $_sNativeShopId = null;

    /**
     * Skip discounts marker
     *
     * @var boolean
     */
    protected $_blSkipDiscounts = false;

    /**
     * Persistent basket item parameters
     *
     * @var array
     */
    protected $_aPersistentParameters = [];

    /**
     * Buundle marker - marks if item is bundle or not
     *
     * @var boolean
     */
    protected $_blBundle = false;

    /**
     * Discount bundle marker - marks if item is discount bundle or not
     *
     * @var boolean
     */
    protected $_blIsDiscountArticle = false;

    /**
     * This item article
     *
     * @var \OxidEsales\Eshop\Application\Model\Article
     */
    protected $_oArticle = null;

    /**
     * Image NON SSL url
     *
     * @var string
     */
    protected $_sDimageDirNoSsl = null;

    /**
     * Image SSL url
     *
     * @var string
     */
    protected $_sDimageDirSsl = null;

    /**
     * User chosen selectlists
     *
     * @var array
     */
    protected $_aChosenSelectlist = [];

    /**
     * Used wrapping paper Id
     *
     * @var string
     */
    protected $_sWrappingId = null;

    /**
     * Wishlist user Id
     *
     * @var string
     */
    protected $_sWishId = null;

    /**
     * Wish article Id
     *
     * @var string
     */
    protected $_sWishArticleId = null;

    /**
     * Article stock check (live db check) status
     *
     * @var bool
     */
    protected $_blCheckArticleStock = true;


    /**
     * Basket Item language Id
     *
     * @var bool
     */
    protected $_iLanguageId = null;

    /**
     * Ssl mode
     *
     * @var bool
     */
    protected $_blSsl = null;

    /**
     * Icon url
     *
     * @var string
     */
    protected $_sIconUrl = null;


    /**
     * Regular Item unit price - price without basket item discounts
     *
     * @var \OxidEsales\Eshop\Core\Price
     */
    protected $_oRegularUnitPrice = null;

    /**
     * Basket item's individual key.
     *
     * @var string
     */
    protected $basketItemKey = null;

    /**
     * Getter for basketItemkey.
     *
     * @return string | null
     */
    public function getBasketItemKey()
    {
        return $this->basketItemKey;
    }

    /**
     * Setter for basketItemkey.
     *
     * @param string $itemKey
     */
    public function setBasketItemKey($itemKey)
    {
        $this->basketItemKey = $itemKey;
    }

    /**
     * Return regular unit price
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getRegularUnitPrice()
    {
        return $this->_oRegularUnitPrice;
    }

    /**
     * Set regular unit price
     *
     * @param \OxidEsales\Eshop\Core\Price $oRegularUnitPrice regular price
     */
    public function setRegularUnitPrice($oRegularUnitPrice)
    {
        $this->_oRegularUnitPrice = $oRegularUnitPrice;
    }


    /**
     * Assigns basic params to basket item
     *  - oxbasketitem::_setArticle();
     *  - oxbasketitem::setAmount();
     *  - oxbasketitem::_setSelectList();
     *  - oxbasketitem::setPersParams();
     *  - oxbasketitem::setBundle().
     *
     * @param string $sProductID product id
     * @param double $dAmount    amount
     * @param array  $aSel       selection
     * @param array  $aPersParam persistent params
     * @param bool   $blBundle   bundle
     *
     * @throws oxNoArticleException, oxOutOfStockException, oxArticleInputException
     */
    public function init($sProductID, $dAmount, $aSel = null, $aPersParam = null, $blBundle = null)
    {
        $this->_setArticle($sProductID);
        $this->setAmount($dAmount);
        $this->_setSelectList($aSel);
        $this->setPersParams($aPersParam);
        $this->setBundle($blBundle);
        $this->setLanguageId(\OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage());
    }

    /**
     * Initializes basket item from oxorderarticle object
     *  - oxbasketitem::_setFromOrderArticle() - assigns $oOrderArticle parameter
     *  to oxBasketItem::_oArticle. Thus oxOrderArticle is used as oxArticle (calls
     *  standard methods implemented by oxIArticle interface);
     *  - oxbasketitem::setAmount();
     *  - oxbasketitem::_setSelectList();
     *  - oxbasketitem::setPersParams().
     *
     * @param \OxidEsales\Eshop\Application\Model\OrderArticle $oOrderArticle order article to load info from
     */
    public function initFromOrderArticle($oOrderArticle)
    {
        $this->_setFromOrderArticle($oOrderArticle);
        $this->setAmount($oOrderArticle->oxorderarticles__oxamount->value);
        $this->_setSelectList($oOrderArticle->getOrderArticleSelectList());
        $this->setPersParams($oOrderArticle->getPersParams());
        $this->setBundle($oOrderArticle->isBundle());
    }

    /**
     * Marks if item is discount bundle ( oxbasketitem::_blIsDiscountArticle )
     *
     * @param bool $blIsDiscountArticle if item is discount bundle
     */
    public function setAsDiscountArticle($blIsDiscountArticle)
    {
        $this->_blIsDiscountArticle = $blIsDiscountArticle;
    }

    /**
     * Sets stock control mode
     *
     * @param bool $blStatus stock control mode
     */
    public function setStockCheckStatus($blStatus)
    {
        $this->_blCheckArticleStock = $blStatus;
    }

    /**
     * Returns stock control mode
     *
     * @return bool
     */
    public function getStockCheckStatus()
    {
        return $this->_blCheckArticleStock;
    }

    /**
     * Sets item amount and weight which depends on amount
     * ( oxbasketitem::dAmount, oxbasketitem::dWeight )
     *
     * @param double $dAmount    amount
     * @param bool   $blOverride Whether to override current amount.
     * @param string $sItemKey   item key
     *
     * @throws oxArticleInputException
     * @throws oxOutOfStockException
     */
    public function setAmount($dAmount, $blOverride = true, $sItemKey = null)
    {
        try {
            //validating amount
            $dAmount = \OxidEsales\Eshop\Core\Registry::getInputValidator()->validateBasketAmount($dAmount);
        } catch (\OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx) {
            $oEx->setArticleNr($this->getProductId());
            $oEx->setProductId($this->getProductId());
            // setting additional information for exception and then rethrowing
            throw $oEx;
        }

        $oArticle = $this->getArticle(true);
        $dAmount = $this->applyPackageOnAmount($oArticle, $dAmount);

        // setting default
        $iOnStock = true;

        if ($blOverride) {
            $this->_dAmount = $dAmount;
        } else {
            $this->_dAmount += $dAmount;
        }

        // checking for stock
        if ($this->getStockCheckStatus() == true) {
            $dArtStockAmount = $this->getSession()->getBasket()->getArtStockInBasket($oArticle->getId(), $sItemKey);
            $selectForUpdate = false;
            if ($this->getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
                $selectForUpdate = true;
            }
            $iOnStock = $oArticle->checkForStock($this->_dAmount, $dArtStockAmount, $selectForUpdate);
            if ($iOnStock !== true) {
                if ($iOnStock === false) {
                    // no stock !
                    $this->_dAmount = 0;
                } else {
                    // limited stock
                    $this->_dAmount = $iOnStock;
                }
            }
        }

        // calculating general weight
        $this->_dWeight = $oArticle->oxarticles__oxweight->value * $this->_dAmount;

        if ($iOnStock !== true) {
            /** @var \OxidEsales\Eshop\Core\Exception\OutOfStockException $oEx */
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\OutOfStockException::class);
            $oEx->setMessage('ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK');
            $oEx->setArticleNr($oArticle->oxarticles__oxartnum->value);
            $oEx->setProductId($oArticle->getProductId());
            $oEx->setRemainingAmount($this->_dAmount);
            $oEx->setBasketIndex($sItemKey);
            throw $oEx;
        }
    }

    /**
     * Apply checks for package on amount
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     * @param double                                      $amount
     *
     * @return double
     */
    protected function applyPackageOnAmount($article, $amount)
    {
        return $amount;
    }

    /**
     * Sets $this->_oPrice
     *
     * @param object $oPrice price
     */
    public function setPrice($oPrice)
    {
        $this->_oUnitPrice = clone $oPrice;

        $this->_oPrice = clone $oPrice;
        $this->_oPrice->multiply($this->getAmount());
    }

    /**
     * Returns article icon picture url
     *
     * @return string
     */
    public function getIconUrl()
    {
        // icon url must be (re)loaded in case icon is not set or shop was switched between ssl/nonssl
        if ($this->_sIconUrl === null || $this->_blSsl != $this->getConfig()->isSsl()) {
            $this->_sIconUrl = $this->getArticle()->getIconUrl();
        }

        return $this->_sIconUrl;
    }

    /**
     * Retrieves the article .Throws an exception if article does not exist,
     * is not buyable or visible.
     *
     * @param bool   $blCheckProduct       checks if product is buyable and visible
     * @param string $sProductId           product id
     * @param bool   $blDisableLazyLoading disable lazy loading
     *
     * @throws oxArticleException exception in case of no current object product id is set
     * @throws oxNoArticleException exception in case if product not exitst or not visible
     * @throws oxArticleInputException exception if product is not buyable (stock and so on)
     *
     * @return \OxidEsales\Eshop\Application\Model\Article|oxOrderArticle
     */
    public function getArticle($blCheckProduct = false, $sProductId = null, $blDisableLazyLoading = false)
    {
        if ($this->_oArticle === null || (!$this->_oArticle->isOrderArticle() && $blDisableLazyLoading)) {
            $sProductId = $sProductId ? $sProductId : $this->_sProductId;
            if (!$sProductId) {
                //this exception may not be caught, anyhow this is a critical exception
                /** @var \OxidEsales\Eshop\Core\Exception\ArticleException $oEx */
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleException::class);
                $oEx->setMessage('EXCEPTION_ARTICLE_NOPRODUCTID');
                throw $oEx;
            }

            $this->_oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            // #M773 Do not use article lazy loading on order save
            if ($blDisableLazyLoading) {
                $this->_oArticle->modifyCacheKey('_allviews');
                $this->_oArticle->disableLazyLoading();
            }

            // performance:
            // - skipping variants loading
            // - skipping 'ab' price info
            // - load parent field
            $this->_oArticle->setNoVariantLoading(true);
            $this->_oArticle->setLoadParentData(true);
            if (!$this->_oArticle->load($sProductId)) {
                /** @var \OxidEsales\Eshop\Core\Exception\NoArticleException $oEx */
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\NoArticleException::class);
                $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
                $oEx->setMessage(sprintf($oLang->translateString('ERROR_MESSAGE_ARTICLE_ARTICLE_DOES_NOT_EXIST', $oLang->getBaseLanguage()), $sProductId));
                $oEx->setArticleNr($sProductId);
                $oEx->setProductId($sProductId);
                throw $oEx;
            }

            // cant put not visible product to basket (M:1286)
            if ($blCheckProduct && !$this->_oArticle->isVisible()) {
                /** @var \OxidEsales\Eshop\Core\Exception\NoArticleException $oEx */
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\NoArticleException::class);
                $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
                $oEx->setMessage(sprintf($oLang->translateString('ERROR_MESSAGE_ARTICLE_ARTICLE_DOES_NOT_EXIST', $oLang->getBaseLanguage()), $this->_oArticle->oxarticles__oxartnum->value));
                $oEx->setArticleNr($sProductId);
                $oEx->setProductId($sProductId);
                throw $oEx;
            }

            // cant put not buyable product to basket
            if ($blCheckProduct && !$this->_oArticle->isBuyable()) {
                /** @var \OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx */
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class);
                $oEx->setMessage('ERROR_MESSAGE_ARTICLE_ARTICLE_NOT_BUYABLE');
                $oEx->setArticleNr($sProductId);
                $oEx->setProductId($sProductId);
                throw $oEx;
            }
        }

        return $this->_oArticle;
    }

    /**
     * Returns bundle amount
     *
     * @return double
     */
    public function getdBundledAmount()
    {
        return $this->isBundle() ? $this->_dAmount : 0;
    }

    /**
     * Returns the price.
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getPrice()
    {
        return $this->_oPrice;
    }

    /**
     * Returns the price.
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getUnitPrice()
    {
        return $this->_oUnitPrice;
    }

    /**
     * Returns the amount of item.
     *
     * @return double
     */
    public function getAmount()
    {
        return $this->_dAmount;
    }

    /**
     * returns the total weight.
     *
     * @return double
     */
    public function getWeight()
    {
        return $this->_dWeight;
    }

    /**
     * Returns product title
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->_sTitle === null || $this->getLanguageId() != \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage()) {
            $oArticle = $this->getArticle();
            $this->_sTitle = $oArticle->oxarticles__oxtitle->value;

            if ($oArticle->oxarticles__oxvarselect->value) {
                $this->_sTitle = $this->_sTitle . ', ' . $this->getVarSelect();
            }
        }

        return $this->_sTitle;
    }

    /**
     * Returns product details URL
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->_sLink === null || $this->getLanguageId() != \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage()) {
            $this->_sLink = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->cleanUrl($this->getArticle()->getLink(), ['force_sid']);
        }

        return $this->getSession()->processUrl($this->_sLink);
    }

    /**
     * Returns ID of shop from which this product was added into basket
     *
     * @return string
     */
    public function getShopId()
    {
        return $this->_sShopId;
    }

    /**
     * Returns user passed select list information
     *
     * @return array
     */
    public function getSelList()
    {
        return $this->_aSelList;
    }

    /**
     * Returns user chosen select list information
     *
     * @return array
     */
    public function getChosenSelList()
    {
        return $this->_aChosenSelectlist;
    }

    /**
     * Returns true if product is bundle
     *
     * @return bool
     */
    public function isBundle()
    {
        return $this->_blBundle;
    }

    /**
     * Returns true if product is given as discount
     *
     * @return bool
     */
    public function isDiscountArticle()
    {
        return $this->_blIsDiscountArticle;
    }

    /**
     * Returns true if discount must be skipped for current product
     *
     * @return bool
     */
    public function isSkipDiscount()
    {
        return $this->_blSkipDiscounts;
    }

    /**
     * Special getter function for backwards compatibility.
     * Executes methods by rule "get".$sVariableName and returns
     * result processed by executed function.
     *
     * @param string $sName parameter name
     *
     * @return mixed
     */
    public function __get($sName)
    {
        if ($sName == 'oProduct') {
            return $this->getArticle();
        }
    }

    /**
     * Does not return _oArticle var on serialisation
     *
     * @return array
     */
    public function __sleep()
    {
        $aRet = [];
        foreach (get_object_vars($this) as $sKey => $sVar) {
            if ($sKey != '_oArticle') {
                $aRet[] = $sKey;
            }
        }

        return $aRet;
    }

    /**
     * Assigns general product parameters to oxbasketitem object :
     *  - sProduct    - oxarticle object ID;
     *  - title       - products title;
     *  - icon        - icon name;
     *  - link        - details URL's;
     *  - sShopId     - current shop ID;
     *  - sNativeShopId  - article shop ID;
     *  - _sDimageDirNoSsl - NON SSL mode image path;
     *  - _sDimageDirSsl   - SSL mode image path;
     *
     * @param string $sProductId product id
     *
     * @throws oxNoArticleException exception
     */
    protected function _setArticle($sProductId)
    {
        $oConfig = $this->getConfig();
        $oArticle = $this->getArticle(true, $sProductId);

        // product ID
        $this->_sProductId = $sProductId;

        $this->_sTitle = null;
        $this->_sVarSelect = null;
        $this->getTitle();

        // icon and details URL's
        $this->_sIcon = $oArticle->oxarticles__oxicon->value;
        $this->_sIconUrl = $oArticle->getIconUrl();
        $this->_blSsl = $oConfig->isSsl();

        // removing force_sid from the link (in case it'll change)
        $this->_sLink = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->cleanUrl($oArticle->getLink(), ['force_sid']);

        // shop Ids
        $this->_sShopId = $oConfig->getShopId();
        $this->_sNativeShopId = $oArticle->oxarticles__oxshopid->value;

        // SSL/NON SSL image paths
        $this->_sDimageDirNoSsl = $oArticle->nossl_dimagedir;
        $this->_sDimageDirSsl = $oArticle->ssl_dimagedir;
    }

    /**
     * Assigns general product parameters to oxbasketitem object:
     *  - sProduct    - oxarticle object ID;
     *  - title       - products title;
     *  - sShopId     - current shop ID;
     *  - sNativeShopId  - article shop ID;
     *
     * @param \OxidEsales\Eshop\Application\Model\OrderArticle $oOrderArticle order article
     */
    protected function _setFromOrderArticle($oOrderArticle)
    {
        // overriding whole article
        $this->_oArticle = $oOrderArticle;

        // product ID
        $this->_sProductId = $oOrderArticle->getProductId();

        // products title
        $this->_sTitle = $oOrderArticle->oxarticles__oxtitle->value;

        // shop Ids
        $this->_sShopId = $this->getConfig()->getShopId();
        $this->_sNativeShopId = $oOrderArticle->oxarticles__oxshopid->value;
    }

    /**
     * Stores item select lists ( oxbasketitem::aSelList )
     *
     * @param array $aSelList item select lists
     */
    protected function _setSelectList($aSelList)
    {
        // checking for default select list
        $aSelectLists = $this->getArticle()->getSelectLists();
        if (!$aSelList || is_array($aSelList) && count($aSelList) == 0) {
            if ($iSelCnt = count($aSelectLists)) {
                $aSelList = array_fill(0, $iSelCnt, '0');
            }
        }

        $this->_aSelList = $aSelList;

        //
        if (is_array($this->_aSelList) && count($this->_aSelList)) {
            foreach ($this->_aSelList as $conkey => $iSel) {
                $this->_aChosenSelectlist[$conkey] = new stdClass();
                $this->_aChosenSelectlist[$conkey]->name = $aSelectLists[$conkey]['name'];
                $this->_aChosenSelectlist[$conkey]->value = $aSelectLists[$conkey][$iSel]->name;
            }
        }
    }

    /**
     * Get persistent parameters ( oxbasketitem::_aPersistentParameters )
     *
     * @return array
     */
    public function getPersParams()
    {
        return $this->_aPersistentParameters;
    }

    /**
     * Stores items persistent parameters ( oxbasketitem::_aPersistentParameters )
     *
     * @param array $aPersParam items persistent parameters
     */
    public function setPersParams($aPersParam)
    {
        $this->_aPersistentParameters = $aPersParam;
    }

    /**
     * Marks if item is bundle ( oxbasketitem::blBundle )
     *
     * @param bool $blBundle if item is bundle
     */
    public function setBundle($blBundle)
    {
        $this->_blBundle = $blBundle;
    }

    /**
     * Used to set "skip discounts" status for basket item
     *
     * @param bool $blSkip set true to skip discounts
     */
    public function setSkipDiscounts($blSkip)
    {
        $this->_blSkipDiscounts = $blSkip;
    }

    /**
     * Returns product Id
     *
     * @return string product id
     */
    public function getProductId()
    {
        return $this->_sProductId;
    }

    /**
     * Product wrapping paper id setter
     *
     * @param string $sWrapId wrapping paper id
     */
    public function setWrapping($sWrapId)
    {
        $this->_sWrappingId = $sWrapId;
    }

    /**
     * Returns wrapping paper ID (if such was applied)
     *
     * @return string
     */
    public function getWrappingId()
    {
        return $this->_sWrappingId;
    }

    /**
     * Returns basket item wrapping object
     *
     * @return oxwrapping
     */
    public function getWrapping()
    {
        $oWrap = null;
        if ($sWrapId = $this->getWrappingId()) {
            $oWrap = oxNew(\OxidEsales\Eshop\Application\Model\Wrapping::class);
            $oWrap->load($sWrapId);
        }

        return $oWrap;
    }

    /**
     * Returns wishlist user Id
     *
     * @return string
     */
    public function getWishId()
    {
        return $this->_sWishId;
    }

    /**
     * Wish user id setter
     *
     * @param string $sWishId user id
     */
    public function setWishId($sWishId)
    {
        $this->_sWishId = $sWishId;
    }

    /**
     * Wish article Id setter
     *
     * @param string $sArticleId wish article id
     */
    public function setWishArticleId($sArticleId)
    {
        $this->_sWishArticleId = $sArticleId;
    }

    /**
     * Returns wish article Id
     *
     * @return string
     */
    public function getWishArticleId()
    {
        return $this->_sWishArticleId;
    }

    /**
     * Returns formatted regular unit price
     *
     * @deprecated in v4.8/5.1 on 2013-10-08; use oxPrice smarty formatter
     *
     * @return string
     */
    public function getFRegularUnitPrice()
    {
        return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($this->getRegularUnitPrice()->getPrice());
    }

    /**
     * Returns formatted unit price
     *
     * @deprecated in v4.8/5.1 on 2013-10-08; use oxPrice smarty formatter
     *
     * @return string
     */
    public function getFUnitPrice()
    {
        return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($this->getUnitPrice()->getPrice());
    }

    /**
     * Returns formatted total price
     *
     * @deprecated in v4.8/5.1 on 2013-10-08; use oxPrice smarty formatter
     *
     * @return string
     */
    public function getFTotalPrice()
    {
        return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($this->getPrice()->getPrice());
    }

    /**
     * Returns formatted total price
     *
     * @return string
     */
    public function getVatPercent()
    {
        return \OxidEsales\Eshop\Core\Registry::getLang()->formatVat($this->getPrice()->getVat());
    }

    /**
     * Returns varselect value
     *
     * @return string
     */
    public function getVarSelect()
    {
        if ($this->_sVarSelect === null || $this->getLanguageId() != \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage()) {
            $oArticle = $this->getArticle();
            $sVarSelectValue = $oArticle->oxarticles__oxvarselect->value;
            $this->_sVarSelect = (!empty($sVarSelectValue) || $sVarSelectValue === '0') ? $sVarSelectValue : '';
        }

        return $this->_sVarSelect;
    }

    /**
     * Get language id
     *
     * @return integer
     */
    public function getLanguageId()
    {
        return $this->_iLanguageId;
    }

    /**
     * Set language Id, reload basket content on language change.
     *
     * @param integer $iLanguageId language id
     */
    public function setLanguageId($iLanguageId)
    {
        $iOldLang = $this->_iLanguageId;
        $this->_iLanguageId = $iLanguageId;

        // #0003777: reload content on language change
        if ($iOldLang !== null && $iOldLang != $iLanguageId) {
            try {
                $this->_setArticle($this->getProductId());
            } catch (\OxidEsales\Eshop\Core\Exception\NoArticleException $oEx) {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx);
            } catch (\OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx) {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx);
            }
        }
    }
}
