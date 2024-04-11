<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use Exception;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use oxList;

// defining supported link types
define('OXARTICLE_LINKTYPE_CATEGORY', 0);
define('OXARTICLE_LINKTYPE_VENDOR', 1);
define('OXARTICLE_LINKTYPE_MANUFACTURER', 2);
define('OXARTICLE_LINKTYPE_PRICECATEGORY', 3);
// @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
define('OXARTICLE_LINKTYPE_RECOMM', 5);
// END deprecated

/**
 * Article manager.
 * Creates fully detailed article object, with such information as VAT,
 * discounts, etc.
 */
class Article extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel implements \OxidEsales\Eshop\Application\Model\Contract\ArticleInterface, \OxidEsales\Eshop\Core\Contract\IUrl
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxarticle';

    /**
     * Set $_blUseLazyLoading to true if you want to load only actually used fields not full object, depending on views.
     *
     * @var bool
     */
    protected $_blUseLazyLoading = true;

    /**
     * item key the usage with oxuserbasketitem
     *
     * @var string (md5 hash)
     */
    protected $_sItemKey;

    /**
     * Variable controls price calculation type (set true, to calculate price
     * with taxes and etc, or false to return base article price).
     *
     * @var bool
     */
    protected $_blCalcPrice = true;

    /**
     * Article oxPrice object.
     *
     * @var \OxidEsales\Eshop\Core\Price
     */
    protected $_oPrice = null;


    /**
     * cached article variant min price
     *
     * @var double|null
     */
    protected $_dVarMinPrice = null;

    /**
     * cached article variant max price
     *
     * @var double|null
     */
    protected $_dVarMaxPrice = null;

    /**
     * caches article vat
     *
     * @var double|null
     */
    protected $_dArticleVat = null;

    /**
     * Status of article - buyable/not buyable.
     *
     * @var bool
     */
    protected $_blNotBuyable = false;

    /**
     * Indicates if we should load variants for current article. When $_blLoadVariants is set to false then
     * neither simple nor full variants for this article are loaded.
     *
     * @var bool
     */
    protected $_blLoadVariants = true;

    /**
     * Article variants without empty stock, not orderable flagged variants
     *
     * @var array
     */
    protected $_aVariants = null;

    /**
     * Article variants with empty stock, not orderable flagged variants
     *
     * @var array
     */
    protected $_aVariantsWithNotOrderables = null;

    /**
     * $_blNotBuyableParent is set to true, when article has variants and is not buyable due to:
     *      a) config option
     *      b) it is not active
     *      c) all variants are not active
     *
     * @var bool
     */
    protected $_blNotBuyableParent = false;


    /**
     * $_blHasVariants is set to true if article has any variants.
     */
    protected $_blHasVariants = false;

    /**
     * $_blHasVariants is set to true if article has multidimensional variants.
     */
    protected $_blHasMdVariants = false;

    /**
     * If set true, then this object is on comparison list
     *
     * @var bool
     */
    protected $_blIsOnComparisonList = false;

    /**
     * user object
     *
     * @var \OxidEsales\Eshop\Application\Model\User
     */
    protected $_oUser = null;

    /**
     * Performance issue. Sometimes you want to load articles without calculating
     * correct discounts and prices etc.
     *
     * @var bool
     */
    protected $_blLoadPrice = true;

    /**
     * $_fPricePerUnit holds price per unit value in active shop currency.
     * $_fPricePerUnit is calculated from
     * \OxidEsales\Eshop\Application\Model\Article::oxarticles__oxunitquantity->value
     * and from \OxidEsales\Eshop\Application\Model\Article::oxarticles__oxuniname->value. If either one of these
     * values is empty then $_fPricePerUnit is not calculated. Example: In case when product price is 10 EUR and
     * product quantity is 0.5 (liters) then $_fPricePerUnit would be 20,00
     */
    protected $_fPricePerUnit = null;

    /**
     * Variable used to force load parent data in export
     */
    protected $_blLoadParentData = false;

    /**
     * Variable used to determine if setting parentId to empty value is allowed
     */
    protected $_blAllowEmptyParentId = false;

    /**
     * Variable used to force load parent data in export
     */
    protected $_blSkipAssign = false;

    /**
     * Set $_blSkipDiscounts to true if you want to skip the discount.
     *
     * @var bool
     */
    protected $_blSkipDiscounts = null;

    /**
     * Object holding the list of attributes and attribute values associated with this article
     * @var \OxidEsales\Eshop\Application\Model\AttributeList
     */
    protected $_oAttributeList = null;

    /**
     * Object holding the list of attributes and attribute values associated with this article and displayable in basket
     * @var \OxidEsales\Eshop\Application\Model\AttributeList
     */
    protected $basketAttributeList = null;

    /**
     * Indicates whether the price is "From" price
     *
     * @var bool
     */
    protected $_blIsRangePrice = null;

    /**
     * The list of article media URLs
     *
     * @var string
     */
    protected $_aMediaUrls = null;

    /**
     * Array containing references to already loaded parent articles, in order for variant to skip parent data loading
     *
     * @var array
     */
    protected static $_aLoadedParents;

    /**
     * Cached select lists array
     *
     * @var array
     */
    protected static $_aSelList;

    /**
     * Select lists for tpl
     *
     * @var array
     */
    protected $_aDispSelList;

    /**
     * Marks that current object is managed by SEO
     *
     * @var bool
     */
    protected $_blIsSeoObject = true;

    /**
     * loaded amount prices
     *
     * @var \OxidEsales\Eshop\Application\Model\AmountPriceList
     */
    protected $_oAmountPriceList = null;

    /**
     * Article details link type (default is 0):
     *     0 - category link
     *     1 - vendor link
     *     2 - manufacturer link
     *
     * @var int
     */
    protected $_iLinkType = 0;

    /**
     * Standard/dynamic article urls for languages
     *
     * @var array
     */
    protected $_aStdUrls = [];

    /**
     * Seo article urls for languages
     *
     * @var array
     */
    protected $_aSeoUrls = [];

    /**
     * Additional parameters to seo urls
     *
     * @var array
     */
    protected $_aSeoAddParams = [];

    /**
     * Additional parameters to std urls
     *
     * @var array
     */
    protected $_aStdAddParams = [];

    /**
     * Image url
     *
     * @var string
     */
    protected $_sDynImageDir = null;

    /**
     * More details link
     *
     * @var string
     */
    protected $_sMoreDetailLink = null;

    /**
     * To basket link
     *
     * @var string
     */
    protected $_sToBasketLink = null;

    /**
     * Article stock status when article is initially loaded.
     *
     * @var int
     */
    protected $_iStockStatusOnLoad = null;

    /**
     * Article original parameters when loaded.
     *
     * @var array
     */
    protected $_aSortingFieldsOnLoad = [];

    /**
     * Stock status
     *
     * @var integer
     */
    protected $_iStockStatus = null;

    /**
     * T price
     *
     * @var object
     */
    protected $_oTPrice = null;

    /**
     * Amount price list info
     *
     * @var object
     */
    protected $_oAmountPriceInfo = null;

    /**
     * Amount price
     *
     * @var double
     */
    protected $_dAmountPrice = null;

    /**
     * Articles manufacturer ids cache
     *
     * @var array
     */
    protected static $_aArticleManufacturers = [];

    /**
     * Articles vendor ids cache
     *
     * @var array
     */
    protected static $_aArticleVendors = [];

    /**
     * Articles category ids cache
     *
     * @var array
     */
    protected static $_aArticleCats = [];

    /**
     * Do not copy certain parent fields to variant
     *
     * @var array
     */
    protected $_aNonCopyParentFields = [
        'oxarticles__oxinsert',
        'oxarticles__oxtimestamp',
        'oxarticles__oxnid',
        'oxarticles__oxid',
        'oxarticles__oxparentid'
    ];

    /**
     * Override certain parent fields to variant
     *
     * @var array
     */
    protected $_aCopyParentField = [
        'oxarticles__oxnonmaterial',
        'oxarticles__oxfreeshipping',
        'oxarticles__oxisdownloadable',
        'oxarticles__oxshowcustomagreement'
    ];

    /**
     * Multidimensional variant tree structure
     *
     * @var \OxidEsales\Eshop\Application\Model\MdVariant
     */
    protected $_oMdVariants = null;

    /**
     * Product long description field
     *
     * @var \OxidEsales\Eshop\Core\Field
     */
    protected $_oLongDesc = null;

    /**
     * Variant selections array
     *
     * @see getVariantSelections()
     *
     * @var array
     */
    protected $_aVariantSelections = [];

    /**
     * Array of product selections
     *
     * @var array
     */
    protected static $_aSelections = [];

    /**
     * Category instance cache
     *
     * @var array
     */
    protected static $_aCategoryCache = [];

    /**
     * stores if are stored any amount price
     *
     * @var bool
     */
    protected static $_blHasAmountPrice = null;

    /**
     * stores downloadable file list
     *
     * @var array|\OxidEsales\Eshop\Core\Model\ListModel
     */
    protected $_aArticleFiles = null;

    /**
     * If admin can edit any field.
     *
     * @var bool
     */
    protected $_blCanUpdateAnyField = null;

    /**
     * Triggered action type
     *
     * @var integer
     */
    protected $actionType = ACTION_NA;

    /**
     * Constructor, sets shop ID for article (\OxidEsales\Eshop\Core\Config::getShopId()),
     * initiates parent constructor (parent::oxI18n()).
     *
     * @param array $aParams The array of names and values of oxArticle instance properties to be set on object
     *                       instantiation
     */
    public function __construct($aParams = null)
    {
        if ($aParams && is_array($aParams)) {
            foreach ($aParams as $sParam => $mValue) {
                $this->$sParam = $mValue;
            }
        }
        parent::__construct();
        $this->init('oxarticles');
    }

    /**
     * Magic getter, deals with values which are loaded on demand.
     * Additionally it sets default value for unknown picture fields
     *
     * @param string $sName Variable name
     *
     * @return mixed
     */
    public function __get($sName)
    {
        $this->$sName = parent::__get($sName);
        if ($this->$sName) {
            // since the field could have been loaded via lazy loading
            $this->assignParentFieldValue($sName);
        }

        return $this->$sName;
    }

    /**
     * @param \OxidEsales\Eshop\Application\Model\AmountPriceList $amountPriceList
     */
    public function setAmountPriceList($amountPriceList)
    {
        $this->_oAmountPriceList = $amountPriceList;
    }

    /**
     * @return \OxidEsales\Eshop\Application\Model\AmountPriceList
     */
    protected function getAmountPriceList()
    {
        return $this->_oAmountPriceList;
    }

    /**
     * Checks whether object is in list or not
     * It's needed for oxArticle so that it can pass this to widgets
     *
     * @return bool
     */
    public function isInList()
    {
        return parent::isInList();
    }

    /**
     * Sets object ID, additionally sets $this->oxarticles__oxnid field value
     *
     * @param string $sId New ID
     *
     * @return string|null
     */
    public function setId($sId = null)
    {
        $sId = parent::setId($sId);

        // TODO: in \OxidEsales\Eshop\Core\Model\BaseModel::setId make it to check if exists and update, not recreate, then delete this overload
        $this->oxarticles__oxnid = $this->oxarticles__oxid;

        return $sId;
    }

    /**
     * Returns part of sql query used in active snippet. Query checks
     * if product "oxactive = 1". If config option "blUseTimeCheck" is TRUE
     * additionally checks if "oxactivefrom < current data < oxactiveto"
     *
     * @param bool $blForceCoreTable force core table usage
     *
     * @return string
     */
    public function getActiveCheckQuery($blForceCoreTable = null)
    {
        $viewName = $this->getViewName($blForceCoreTable);

        $query = " $viewName.oxactive = 1 ";

        $query .= " and $viewName.oxhidden = 0 ";

        if (Registry::getConfig()->getConfigParam('blUseTimeCheck')) {
            $query = $this->addSqlActiveRangeSnippet($query, $viewName);
        }

        return $query;
    }

    /**
     * Returns part of sql query used in active snippet. If config
     * option "blUseStock" is TRUE checks if "oxstockflag != 2 or
     * ( oxstock + oxvarstock ) > 0". If config option "blVariantParentBuyable"
     * is TRUE checks if product has variants, and if has - checks is
     * there at least one variant which is buyable. If config option
     * option "blUseTimeCheck" is TRUE additionally checks if variants
     * "oxactivefrom < current data < oxactiveto"
     *
     * @param bool $blForceCoreTable force core table usage
     *
     * @return string
     */
    public function getStockCheckQuery($blForceCoreTable = null)
    {
        $myConfig = Registry::getConfig();
        $sTable = $this->getViewName($blForceCoreTable);

        $sQ = "";

        //do not check for variants
        if ($myConfig->getConfigParam('blUseStock')) {
            $sQ = " and ( $sTable.oxstockflag != 2 or ( $sTable.oxstock + $sTable.oxvarstock ) > 0  ) ";
            //V #M513: When Parent article is not purchasable, it's visibility should be displayed in shop only if any of Variants is available.
            if (!$myConfig->getConfigParam('blVariantParentBuyable')) {
                $activeCheck = 'art.oxactive = 1';
                if ($myConfig->getConfigParam('blUseTimeCheck')) {
                    $activeCheck = $this->addSqlActiveRangeSnippet($activeCheck, 'art');
                }
                $sQ = " $sQ and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and $activeCheck and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";
            }
        }

        return $sQ;
    }

    /**
     * Returns part of query which checks if product is variant of current
     * object. Additionally if config option "blUseStock" is TRUE checks
     * stock state "( oxstock > 0 or ( oxstock <= 0 and ( oxstockflag = 1
     * or oxstockflag = 4 ) )"
     *
     * @param bool $blRemoveNotOrderables remove or leave non orderable products
     * @param bool $blForceCoreTable      force core table usage
     *
     * @return string
     */
    public function getVariantsQuery($blRemoveNotOrderables, $blForceCoreTable = null)
    {
        $sTable = $this->getViewName($blForceCoreTable);
        $sQ = " and $sTable.oxparentid = '" . $this->getId() . "' ";

        //checking if variant is active and stock status
        if (Registry::getConfig()->getConfigParam('blUseStock')) {
            $sQ .= " and ( $sTable.oxstock > 0 or ( $sTable.oxstock <= 0 and $sTable.oxstockflag != 2 ";
            if ($blRemoveNotOrderables) {
                $sQ .= " and $sTable.oxstockflag != 3 ";
            }
            $sQ .= " ) ) ";
        }

        return $sQ;
    }

    /**
     * Return unit quantity
     *
     * @return string
     */
    public function getUnitQuantity()
    {
        return $this->oxarticles__oxunitquantity->value;
    }

    /**
     * Return Size of product: length*width*height
     *
     * @return double
     */
    public function getSize()
    {
        return $this->oxarticles__oxlength->value *
               $this->oxarticles__oxwidth->value *
               $this->oxarticles__oxheight->value;
    }

    /**
     * Return product weight
     *
     * @return double
     */
    public function getWeight()
    {
        return $this->oxarticles__oxweight->value;
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $blForceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    public function getSqlActiveSnippet($blForceCoreTable = null)
    {
        return "( {$this->createSqlActiveSnippet($blForceCoreTable)} ) ";
    }

    /**
     *
     * Getter for action type.
     *
     * @return int
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $forceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    protected function createSqlActiveSnippet($forceCoreTable)
    {
        // check if article is still active
        $sQ = $this->getActiveCheckQuery($forceCoreTable);

        // stock and variants check
        $sQ .= $this->getStockCheckQuery($forceCoreTable);

        return $sQ;
    }

    /**
     * Assign condition setter. In case article assignment is skipped ($_blSkipAssign = true), it does not perform
     * additional
     *
     * @param bool $blSkipAssign Whether to skip assign process for the article
     */
    public function setSkipAssign($blSkipAssign)
    {
        $this->_blSkipAssign = $blSkipAssign;
    }

    /**
     * Disables article price loading. Should be called before assign(), or load()
     */
    public function disablePriceLoad()
    {
        $this->_blLoadPrice = false;
    }

    /**
     * Enable article price loading, if disabled.
     */
    public function enablePriceLoad()
    {
        $this->_blLoadPrice = true;
    }

    /**
     * Returns item key used with oxuserbasket
     *
     * @return string
     */
    public function getItemKey()
    {
        return $this->_sItemKey;
    }

    /**
     * Sets item key used with oxuserbasket
     *
     * @param string $sItemKey Item key
     */
    public function setItemKey($sItemKey)
    {
        $this->_sItemKey = $sItemKey;
    }

    /**
     * Disables/enables variant loading
     *
     * @param bool $blLoadVariants skip variant loading or not
     */
    public function setNoVariantLoading($blLoadVariants)
    {
        $this->_blLoadVariants = !$blLoadVariants;
    }

    /**
     * Checks if article is buyable.
     *
     * @return bool
     */
    public function isBuyable()
    {
        return !($this->_blNotBuyableParent || $this->_blNotBuyable);
    }

    /**
     * Checks if price alarm is enabled.
     *
     * @return bool
     */
    public function isPriceAlarm()
    {
        // #419 disabling price alarm if article has fixed price
        return !(($this->__isset('oxarticles__oxblfixedprice') || $this->__get('oxarticles__oxblfixedprice')) && $this->__get('oxarticles__oxblfixedprice')->value);
    }

    /**
     * Checks whether article is inluded in comparison list
     *
     * @return bool
     */
    public function isOnComparisonList()
    {
        return $this->_blIsOnComparisonList;
    }

    /**
     * Set if article is inluded in comparison list
     *
     * @param bool $blOnList Whether is article on the list
     */
    public function setOnComparisonList($blOnList)
    {
        $this->_blIsOnComparisonList = $blOnList;
    }

    /**
     * A setter for $_blLoadParentData (whether article parent info should be laoded fully) class variable
     *
     * @param bool $blLoadParentData Whether to load parent data
     */
    public function setLoadParentData($blLoadParentData)
    {
        $this->_blLoadParentData = $blLoadParentData;
    }

    /**
     * Getter for do we load parent data
     *
     * @return bool
     */
    public function getLoadParentData()
    {
        return $this->_blLoadParentData;
    }

    /**
     * Returns true if the field is multilanguage
     *
     * @param string $sFieldName Field name
     *
     * @return bool
     */
    public function isMultilingualField($sFieldName)
    {
        if ('oxlongdesc' == $sFieldName) {
            return true;
        }

        return parent::isMultilingualField($sFieldName);
    }

    /**
     * Returns formatted price per unit
     *
     * @deprecated since v5.1 (2013-09-25); use oxPrice template engine plugin for formatting in templates
     * @return string
     */
    public function getFUnitPrice()
    {
        if ($this->_fPricePerUnit == null) {
            if ($oPrice = $this->getUnitPrice()) {
                if ($dPrice = $this->getPriceForView($oPrice)) {
                    $this->_fPricePerUnit = Registry::getLang()->formatCurrency($dPrice);
                }
            }
        }

        return $this->_fPricePerUnit;
    }

    /**
     * Returns price per unit
     *
     * @return \OxidEsales\Eshop\Core\Price|null
     */
    public function getUnitPrice()
    {
        // Performance
        if (!Registry::getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return null;
        }

        $oPrice = null;
        if ((double) $this->getUnitQuantity() && $this->oxarticles__oxunitname->value) {
            $oPrice = clone $this->getPrice();
            $oPrice->divide((double) $this->getUnitQuantity());
        }

        return $oPrice;
    }

    /**
     * Returns formatted article min price
     *
     * @deprecated since v5.1 (2013-10-04); use oxPrice template engine plugin for formatting in templates
     *
     * @return string
     */
    public function getFMinPrice()
    {
        $sPrice = '';
        if ($oPrice = $this->getMinPrice()) {
            $dPrice = $this->getPriceForView($oPrice);
            $sPrice = Registry::getLang()->formatCurrency($dPrice);
        }

        return $sPrice;
    }

    /**
     * Returns formatted min article variant price
     *
     * @deprecated since v5.1 (2013-10-04); use oxPrice template engine plugin for formatting in templates
     *
     * @return string
     */
    public function getFVarMinPrice()
    {
        $sPrice = '';
        if ($oPrice = $this->getVarMinPrice()) {
            $dPrice = $this->getPriceForView($oPrice);
            $sPrice = Registry::getLang()->formatCurrency($dPrice);
        }

        return $sPrice;
    }

    /**
     * Returns article min price of variants
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getVarMinPrice()
    {
        if (!Registry::getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return null;
        }

        $oPrice = null;
        $dPrice = $this->calculateVarMinPrice();

        $oPrice = $this->getPriceObject();
        $oPrice->setPrice($dPrice);

        $this->calculatePrice($oPrice);

        return $oPrice;
    }

    /**
     * Calculates lowest price of available article variants.
     *
     * @return double
     */
    protected function calculateVarMinPrice()
    {
        $dPrice = $this->getVarMinRawPrice();

        return $this->preparePrice($dPrice, $this->getArticleVat());
    }

    /**
     * Returns article min price in calculation included variants
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getMinPrice()
    {
        if (!Registry::getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        $oPrice = null;
        $dPrice = $this->getRawPrice();
        if ($this->getVarMinRawPrice() !== null && $dPrice > $this->getVarMinRawPrice()) {
            $dPrice = $this->getVarMinRawPrice();
        }

        $dPrice = $this->prepareModifiedPrice($dPrice);

        $oPrice = $this->getPriceObject();
        $oPrice->setPrice($dPrice);
        $this->calculatePrice($oPrice);

        return $oPrice;
    }

    /**
     * @param double $dPrice
     *
     * @return double
     */
    protected function prepareModifiedPrice($dPrice)
    {
        $dPrice = $this->preparePrice($dPrice, $this->getArticleVat());

        return $dPrice;
    }

    /**
     * Returns true if article has variant with different price
     *
     * @return bool
     */
    public function isRangePrice()
    {
        if ($this->_blIsRangePrice === null) {
            $this->setRangePrice(false);

            if ($this->hasAnyVariant()) {
                $dPrice = $this->getRawPrice();
                $dMinPrice = $this->getVarMinRawPrice();
                $dMaxPrice = $this->getVarMaxPrice();

                if ($dMinPrice != $dMaxPrice) {
                    $this->setRangePrice();
                } elseif (!$this->isParentNotBuyable() && $dPrice != $dMinPrice) {
                    $this->setRangePrice();
                }
            }
        }

        return $this->_blIsRangePrice;
    }


    /**
     * Setter to set if article has range price
     *
     * @param bool $blIsRangePrice - true if range, else false
     *
     * @return null
     */
    public function setRangePrice($blIsRangePrice = true)
    {
        return $this->_blIsRangePrice = $blIsRangePrice;
    }

    public function hasActiveTimeRange(): bool
    {
        $activeFrom = $this->oxarticles__oxactivefrom->value;
        $activeTo = $this->oxarticles__oxactiveto->value;
        $now = Registry::getUtilsDate()->getTime();

        if (!$this->hasProductValidTimeRange()) {
            return false;
        }

        return (Registry::getUtilsDate()->isEmptyDate($activeTo) || strtotime($activeTo) >= $now)
            && (Registry::getUtilsDate()->isEmptyDate($activeFrom) || strtotime($activeFrom) <= $now);
    }


    /**
     * Checks if article has visible status. Returns TRUE if its visible
     *
     * @return bool
     */
    public function isVisible()
    {
        // admin preview mode
        if (($blCanPreview = Registry::getUtils()->canPreview()) !== null) {

            return $blCanPreview;
        }

        $blUseTimeCheck = Registry::getConfig()->getConfigParam('blUseTimeCheck');
        if (
            !$this->oxarticles__oxactive->value
            && (($blUseTimeCheck && !$this->hasActiveTimeRange()) || !$blUseTimeCheck)
        ) {

            return false;
        }

        // stock flags
        if (Registry::getConfig()->getConfigParam('blUseStock') && $this->oxarticles__oxstockflag->value == 2) {
            $iOnStock = $this->oxarticles__oxstock->value + $this->oxarticles__oxvarstock->value;
            if (Registry::getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
                $session = Registry::getSession();
                $iOnStock += $session->getBasketReservations()->getReservedAmount($this->getId());
            }
            if ($iOnStock <= 0) {

                return false;
            }
        }

        return true;
    }

    /**
     * Assigns to oxarticle object some base parameters/values (such as
     * detaillink, moredetaillink, etc).
     *
     * @param array $aRecord Array representing current field values
     *
     * @return null
     */
    public function assign($aRecord)
    {
        startProfile('articleAssign');

        // load object from database
        parent::assign($aRecord);

        //clear seo urls
        $this->_aSeoUrls = [];

        $this->oxarticles__oxnid = $this->oxarticles__oxid;

        // check for simple article.
        if ($this->_blSkipAssign) {
            return;
        }

        $this->assignParentFieldValues();
        $this->assignNotBuyableParent();

        // assign only for a first load time
        if (!$this->isLoaded()) {
            $this->setShopValues($this);
        }

        $this->assignStock();
        $this->assignDynImageDir();
        $this->assignComparisonListFlag();

        stopProfile('articleAssign');
    }

    /**
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function setShopValues($article)
    {
    }

    /**
     * Loads object data from DB (object data ID must be passed to method).
     * Converts dates (\OxidEsales\Eshop\Application\Model\Article::oxarticles__oxinsert)
     * to international format (oxUtils.php \OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate(...)).
     * Returns true if article was loaded successfully.
     *
     * @param string $sOXID Article object ID
     *
     * @return bool
     */
    public function load($sOXID)
    {
        // A. #1325 resetting to avoid problems when reloading (details etc)
        $this->_blNotBuyableParent = false;

        $aData = $this->loadData($sOXID);

        if ($aData) {
            $this->assign($aData);

            $this->saveSortingFieldValuesOnLoad();

            $this->_iStockStatusOnLoad = $this->_iStockStatus;

            $this->_isLoaded = true;

            return true;
        }

        return false;
    }

    /**
     * Loads data from database and returns it.
     *
     * @param string $articleId
     *
     * @return array
     */
    protected function loadData($articleId)
    {
        return $this->loadFromDb($articleId);
    }

    /**
     * Checks whether sorting fields changed from last article loading.
     *
     * @return bool
     */
    public function hasSortingFieldsChanged()
    {
        $aSortingFields = Registry::getConfig()->getConfigParam('aSortCols');
        $aSortingFields = !empty($aSortingFields) ? (array) $aSortingFields : [];
        $blChanged = false;
        foreach ($aSortingFields as $sField) {
            $sParameterName = 'oxarticles__' . $sField;
            $currentValueOfField = $this->$sParameterName instanceof Field ? $this->$sParameterName->value : '';
            $valueOfFieldOnLoad = $this->_aSortingFieldsOnLoad[$sParameterName] ?? null;
            if ($valueOfFieldOnLoad !== $currentValueOfField) {
                $blChanged = true;
                break;
            }
        }

        return $blChanged;
    }

    /**
     * Calculates and saves product rating average
     *
     * @param integer $rating new rating value
     */
    public function addToRatingAverage($rating)
    {
        $dOldRating = $this->oxarticles__oxrating->value;
        $dOldCnt = $this->oxarticles__oxratingcnt->value;
        $this->oxarticles__oxrating->setValue(($dOldRating * $dOldCnt + $rating) / ($dOldCnt + 1));
        $this->oxarticles__oxratingcnt->setValue($dOldCnt + 1);
        $dRating = ($dOldRating * $dOldCnt + $rating) / ($dOldCnt + 1);
        $dRatingCnt = (int) ($dOldCnt + 1);
        // oxarticles.oxtimestamp = oxarticles.oxtimestamp to keep old timestamp value
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = "update oxarticles
                  set oxarticles.oxrating = :oxrating,
                      oxarticles.oxratingcnt = :oxratingcnt,
                      oxarticles.oxtimestamp = oxarticles.oxtimestamp
                  where oxarticles.oxid = :oxid";
        $oDb->execute($query, [
            ':oxrating' => $dRating,
            ':oxratingcnt' => $dRatingCnt,
            ':oxid' => $this->getId()
        ]);
    }

    /**
     * Set product rating average
     *
     * @param integer $iRating new rating value
     */
    public function setRatingAverage($iRating)
    {
        $this->oxarticles__oxrating = new \OxidEsales\Eshop\Core\Field($iRating);
    }

    /**
     * Set product rating count
     *
     * @param integer $iRatingCnt new rating count
     */
    public function setRatingCount($iRatingCnt)
    {
        $this->oxarticles__oxratingcnt = new \OxidEsales\Eshop\Core\Field($iRatingCnt);
    }

    /**
     * Returns product rating average
     *
     * @param bool $blIncludeVariants - include variant ratings
     *
     * @return double
     */
    public function getArticleRatingAverage($blIncludeVariants = false)
    {
        if (!$blIncludeVariants) {
            return round($this->oxarticles__oxrating->value, 1);
        } else {
            $oRating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);

            return $oRating->getRatingAverage($this->getId(), 'oxarticle', $this->getVariantIds());
        }
    }

    /**
     * Returns product rating count
     *
     * @param bool $blIncludeVariants - include variant ratings
     *
     * @return int
     */
    public function getArticleRatingCount($blIncludeVariants = false)
    {
        if (!$blIncludeVariants) {
            return $this->oxarticles__oxratingcnt->value;
        } else {
            $oRating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);

            return $oRating->getRatingCount($this->getId(), 'oxarticle', $this->getVariantIds());
        }
    }


    /**
     * Collects user written reviews about an article.
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel
     */
    public function getReviews()
    {
        $aIds = [$this->getId()];

        if ($this->oxarticles__oxparentid->value) {
            $aIds[] = $this->oxarticles__oxparentid->value;
        }

        // showing variant reviews ..
        if (Registry::getConfig()->getConfigParam('blShowVariantReviews')) {
            $aAdd = $this->getVariantIds();
            if (is_array($aAdd)) {
                $aIds = array_merge($aIds, $aAdd);
            }
        }

        $oReview = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $oRevs = $oReview->loadList('oxarticle', $aIds);

        //if no review found, return null
        if ($oRevs->count() < 1) {
            return null;
        }

        return $oRevs;
    }

    /**
     * Loads and returns array with cross selling information.
     *
     * @return array
     */
    public function getCrossSelling()
    {
        $oCrosslist = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oCrosslist->loadArticleCrossSell($this->oxarticles__oxid->value);
        if ($oCrosslist->count()) {
            return $oCrosslist;
        }
    }

    /**
     * Loads and returns array with accessories information.
     *
     * @return array
     */
    public function getAccessoires()
    {
        $myConfig = Registry::getConfig();

        // Performance
        if (!$myConfig->getConfigParam('bl_perfLoadAccessoires')) {
            return;
        }

        $oAcclist = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oAcclist->setSqlLimit(0, $myConfig->getConfigParam('iNrofCrossellArticles'));
        $oAcclist->loadArticleAccessoires($this->oxarticles__oxid->value);

        if ($oAcclist->count()) {
            return $oAcclist;
        }
    }

    /**
     * Returns a list of similar products.
     *
     * @return array
     */
    public function getSimilarProducts()
    {
        // Performance
        $myConfig = Registry::getConfig();
        if (!$myConfig->getConfigParam('bl_perfLoadSimilar')) {
            return;
        }

        // Check configured number of similar products (bug #6062)
        if ($myConfig->getConfigParam('iNrofSimilarArticles') < 1) {
            return;
        }

        $sArticleTable = $this->getViewName();

        $sAttribs = '';
        $iCnt = 0;
        $this->getAttribsString($sAttribs, $iCnt);

        if (!$sAttribs) {
            return null;
        }

        $aList = $this->getSimList($sAttribs, $iCnt);

        if (count($aList)) {
            uasort($aList, function ($a, $b) { return $a <=> $b; });

            $sSearch = $this->generateSimListSearchStr($sArticleTable, $aList);

            $oSimilarlist = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
            $oSimilarlist->setSqlLimit(0, $myConfig->getConfigParam('iNrofSimilarArticles'));
            $oSimilarlist->selectString($sSearch);

            return $oSimilarlist;
        }
    }

    /**
     * Loads and returns articles list, bought by same customer.
     *
     * @return \OxidEsales\Eshop\Application\Model\ArticleList|null
     */
    public function getCustomerAlsoBoughtThisProducts()
    {
        // Performance
        $myConfig = Registry::getConfig();
        if (!$myConfig->getConfigParam('bl_perfLoadCustomerWhoBoughtThis')) {
            return;
        }

        // selecting products that fits
        $sQ = $this->generateSearchStrForCustomerBought();

        $oArticles = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArticles->setSqlLimit(0, $myConfig->getConfigParam('iNrofCustomerWhoArticles'));
        $oArticles->selectString($sQ);
        if ($oArticles->count()) {
            return $oArticles;
        }
    }

    /**
     * Returns list object with info about article price that depends on amount in basket.
     * Takes data from oxprice2article table. Returns false if such info is not set.
     *
     * @return mixed
     */
    public function loadAmountPriceInfo()
    {
        $myConfig = Registry::getConfig();
        if (!$myConfig->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice || !$this->_blCalcPrice || !$this->hasAmountPrice()) {
            return [];
        }

        if ($this->_oAmountPriceInfo === null) {
            $this->_oAmountPriceInfo = [];
            if (count(($aAmPriceList = $this->buildAmountPriceList()->getArray()))) {
                $this->_oAmountPriceInfo = $this->fillAmountPriceList($aAmPriceList);
            }
        }

        return $this->_oAmountPriceInfo;
    }

    /**
     * Returns all selectlists this article has (used in oxbasket)
     *
     * @param string $sKeyPrefix Optional key prefix
     *
     * @return array
     */
    public function getSelectLists($sKeyPrefix = null)
    {
        //#1468C - more then one article in basket with different selectlist...
        //optionall function parameter $sKeyPrefix added, used only in basket.php
        $sKey = $this->getId();
        if (isset($sKeyPrefix)) {
            $sKey = $sKeyPrefix . '__' . $sKey;
        }

        if (!isset(self::$_aSelList[$sKey])) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sSLViewName = $tableViewNameGenerator->getViewName('oxselectlist');

            $sQ = "select {$sSLViewName}.* from oxobject2selectlist join {$sSLViewName} on $sSLViewName.oxid=oxobject2selectlist.oxselnid
                   where oxobject2selectlist.oxobjectid = :oxobjectid order by oxobject2selectlist.oxsort";

            // all selectlists this article has
            $oLists = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oLists->init('oxselectlist');
            $oLists->selectString($sQ, [':oxobjectid' => $this->getId()]);

            //#1104S if this is variant ant it has no selectlists, trying with parent
            if ($oLists->count() == 0 && $this->getFieldData('oxparentid')) {
                $oLists->selectString($sQ, [':oxobjectid' => $this->oxarticles__oxparentid->value]);
            }

            // We do not need to calculate price here as there are method to get current article vat
            /*if ( $this->getPrice() != null ) {
                $dVat = $this->getPrice()->getVat();
            }*/
            $dVat = $this->getArticleVat();

            $iCnt = 0;
            self::$_aSelList[$sKey] = [];
            foreach ($oLists as $oSelectlist) {
                self::$_aSelList[$sKey][$iCnt] = $oSelectlist->getFieldList($dVat);
                self::$_aSelList[$sKey][$iCnt]['name'] = $oSelectlist->oxselectlist__oxtitle->value;
                $iCnt++;
            }
        }

        return self::$_aSelList[$sKey];
    }

    /**
     * Returns amount of variants article has
     *
     * @return mixed
     */
    public function getVariantsCount()
    {
        return $this->oxarticles__oxvarcount->value;
    }

    /**
     * Checks if article has multidimensional variants
     *
     * @return bool
     */
    public function hasMdVariants()
    {
        return $this->_blHasMdVariants;
    }

    /**
     * Returns if article has intangible agreement with which customer will have to agree.
     *
     * @return bool
     */
    public function hasIntangibleAgreement()
    {
        return $this->oxarticles__oxshowcustomagreement->value && $this->oxarticles__oxnonmaterial->value && !$this->hasDownloadableAgreement();
    }

    /**
     * Returns if article has downloadable agreement with which customer will have to agree.
     *
     * @return bool
     */
    public function hasDownloadableAgreement()
    {
        return $this->oxarticles__oxshowcustomagreement->value && $this->oxarticles__oxisdownloadable->value;
    }

    /**
     * Returns variants selections lists array
     *
     * @param array  $aFilterIds    ids of active selections [optional]
     * @param string $sActVariantId active variant id [optional]
     * @param int    $iLimit        limit variant lists count (if non zero, return limited number of multidimensional
     *                              variant selections)
     *
     * @return array
     */
    public function getVariantSelections($aFilterIds = null, $sActVariantId = null, $iLimit = 0)
    {
        $iLimit = (int) $iLimit;
        if (!isset($this->_aVariantSelections[$iLimit])) {
            $aVariantSelections = false;
            if ($this->oxarticles__oxvarcount->value) {
                $oVariants = $this->getVariants(false);
                $aVariantSelections = oxNew(\OxidEsales\Eshop\Application\Model\VariantHandler::class)->buildVariantSelections(
                    $this->oxarticles__oxvarname->getRawValue(),
                    $oVariants,
                    $aFilterIds,
                    $sActVariantId,
                    $iLimit
                );

                if (!empty($oVariants) && empty($aVariantSelections['rawselections'])) {
                    $aVariantSelections = false;
                }
            }
            $this->_aVariantSelections[$iLimit] = $aVariantSelections;
        }

        return $this->_aVariantSelections[$iLimit];
    }

    /**
     * Returns product selections lists array (used in azure theme)
     *
     * @param int   $iLimit  if given - will load limited count of selections [optional]
     * @param array $aFilter selection filter [optional]
     *
     * @return array
     */
    public function getSelections($iLimit = null, $aFilter = null)
    {
        $sId = $this->getId() . ((int) $iLimit);
        if (!array_key_exists($sId, self::$_aSelections)) {
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sSLViewName = $tableViewNameGenerator->getViewName('oxselectlist');

            $sQ = "select {$sSLViewName}.* from oxobject2selectlist join {$sSLViewName} on $sSLViewName.oxid=oxobject2selectlist.oxselnid
                   where oxobject2selectlist.oxobjectid = :oxobjectid order by oxobject2selectlist.oxsort";

            if (($iLimit = (int) $iLimit)) {
                $sQ .= " limit $iLimit ";
            }

            // vat value for price
            $dVat = 0;
            if (($oPrice = $this->getPrice()) != null) {
                $dVat = $oPrice->getVat();
            }

            // all selectlists this article has
            $oList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oList->init('oxselectlist');
            $oList->getBaseObject()->setVat($dVat);
            $oList->selectString($sQ, [':oxobjectid' => $this->getId()]);

            //#1104S if this is variant and it has no selectlists, trying with parent
            if ($oList->count() == 0 && $this->oxarticles__oxparentid->value) {
                $oList->selectString($sQ, [':oxobjectid' => $this->oxarticles__oxparentid->value]);
            }

            self::$_aSelections[$sId] = $oList->count() ? $oList : false;
        }

        if (self::$_aSelections[$sId]) {
            // marking active from filter
            $aFilter = ($aFilter === null) ? Registry::getRequest()->getRequestEscapedParameter("sel") : $aFilter;
            if ($aFilter) {
                $iSelIdx = 0;
                foreach (self::$_aSelections[$sId] as $oSelection) {
                    if (isset($aFilter[$iSelIdx])) {
                        $oSelection->setActiveSelectionByIndex($aFilter[$iSelIdx]);
                    }
                    $iSelIdx++;
                }
            }
        }

        return self::$_aSelections[$sId];
    }

    /**
     * Returns variant list (list contains oxArticle objects)
     *
     * @param bool $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of stock
     *                                    [optional]
     * @param bool $blForceCoreTable      if true forces core table use, default is false [optional]
     *
     * @return \OxidEsales\Eshop\Application\Model\ArticleList
     */
    public function getFullVariants($blRemoveNotOrderables = true, $blForceCoreTable = null)
    {
        return $this->loadVariantList(false, $blRemoveNotOrderables, $blForceCoreTable);
    }

    /**
     * Collects and returns article variants.
     * Note: Only active variants are returned by this method. If you need full variant list use
     * \OxidEsales\Eshop\Application\Model\Article::getAdminVariants()
     *
     * @param bool $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of stock
     * @param bool $blForceCoreTable      if true forces core table use, default is false [optional]
     *
     * @return array
     */
    public function getVariants($blRemoveNotOrderables = true, $blForceCoreTable = null)
    {
        return $this->loadVariantList($this->isInList(), $blRemoveNotOrderables, $blForceCoreTable);
    }

    /**
     * Simple way to get variants without querying oxArticle table first. This is basically used for lists.
     *
     * @return null
     */
    public function getSimpleVariants()
    {
        if ($this->oxarticles__oxvarcount->value) {
            return $this->getVariants();
        }
    }

    /**
     * Loads article variants and returns variants list object. Article language may
     * be set by passing with parameter, or GET/POST/Session variable.
     *
     * @param string $sLanguage shop language.
     *
     * @return object
     */
    public function getAdminVariants($sLanguage = null)
    {
        $oVariants = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        if (($sId = $this->getId())) {
            $oBaseObj = $oVariants->getBaseObject();

            if (is_null($sLanguage)) {
                $oBaseObj->setLanguage(Registry::getLang()->getBaseLanguage());
            } else {
                $oBaseObj->setLanguage($sLanguage);
            }

            $sSql = "select * from " . $oBaseObj->getViewName() . "
                where oxparentid = :oxparentid
                order by oxsort ";
            $oVariants->selectString($sSql, [':oxparentid' => $sId]);

            //if we have variants then depending on config option the parent may be non buyable
            if (!Registry::getConfig()->getConfigParam('blVariantParentBuyable') && ($oVariants->count() > 0)) {
                //$this->blNotBuyable = true;
                $this->_blNotBuyableParent = true;
            }
        }

        return $oVariants;
    }

    /**
     * Loads and returns article category object. First tries to load
     * assigned category and is such category does not exist, tries to
     * load category by price
     *
     * @return \OxidEsales\Eshop\Application\Model\Category|null
     */
    public function getCategory()
    {
        $shopId = Registry::getConfig()->getShopId();
        $id = $this->getParentId();
        if (!$id) {
            $id = $this->getId();
        }

        $this->initializeShopArticleCategoryCache($shopId);
        if (\array_key_exists($id, self::$_aCategoryCache[$shopId])) {
            return self::$_aCategoryCache[$shopId][$id];
        }

        startProfile('getCategory');

        $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $category->setLanguage($this->getLanguage());

        $str = Str::getStr();
        $where = $category->getSqlActiveSnippet();
        $select = $this->generateSearchStr($id);
        $select .= (
            $str->strstr(
                $select,
                'where'
            ) ? ' and ' : ' where '
        ) . $where . " order by oxobject2category.oxtime limit 1";


        // category not found ?
        $record = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select($select);
        if ($record && $record->count() > 0) {
            $category->assign($record->fields);
        } else {
            $select = $this->generateSearchStr($id, true);
            $select .= ($str->strstr($select, 'where') ? ' and ' : ' where ') . $where . " limit 1";

            // looking for price category
            $record = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select($select);
            if ($record && $record->count() > 0) {
                $category->assign($record->fields);
            } else {
                $category = null;
            }
        }

        // add the category instance to cache
        self::$_aCategoryCache[$shopId][$id] = $category;
        stopProfile('getCategory');

        return $category;
    }

    private function initializeShopArticleCategoryCache($shopId): void
    {
        if (!\array_key_exists($shopId, self::$_aCategoryCache)) {
            self::$_aCategoryCache[$shopId] = [];
        }
    }

    /**
     * Returns ID's of categories where this article is assigned
     *
     * @param bool $blActCats select categories if all parents are active
     * @param bool $blSkipCache Whether to skip cache
     *
     * @return array
     */
    public function getCategoryIds($blActCats = false, $blSkipCache = false)
    {
        $sArticleId = $this->getId();

        if (!isset(self::$_aArticleCats[$sArticleId]) || $blSkipCache) {
            $sSql = $this->getCategoryIdsSelect($blActCats);
            $aCategoryIds = $this->selectCategoryIds($sSql, 'oxcatnid');

            $sSql = $this->getSqlForPriceCategories();
            $aPriceCategoryIds = $this->selectCategoryIds($sSql, 'oxid');

            self::$_aArticleCats[$sArticleId] = array_unique(array_merge($aCategoryIds, $aPriceCategoryIds));
        }

        return self::$_aArticleCats[$sArticleId];
    }

    /**
     * Returns current article vendor object. If $blShopCheck = false, then
     * vendor loading will fallback to oxI18n object and blReadOnly parameter
     * will be set to true if vendor is not assigned to current shop
     *
     * @param bool $blShopCheck Set false if shop check is not required (default is true)
     *
     * @return object
     */
    public function getVendor($blShopCheck = true)
    {
        $sVendorId = $this->getVendorId();
        if ($sVendorId) {
            $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        } elseif (!$blShopCheck && $this->oxarticles__oxvendorid->value) {
            $oVendor = $this->createMultilanguageVendorObject();
            $sVendorId = $this->oxarticles__oxvendorid->value;
        }
        if ($sVendorId && $oVendor && $oVendor->load($sVendorId) && $oVendor->oxvendor__oxactive->value) {
            return $oVendor;
        }

        return null;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Model\MultiLanguageModel
     */
    protected function createMultilanguageVendorObject()
    {
        $oVendor = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
        $oVendor->init('oxvendor');
        $oVendor->setReadOnly(true);

        return $oVendor;
    }

    /**
     * Returns article object vendor ID. Result is cached into self::$_aArticleVendors
     *
     * @return string
     */
    public function getVendorId()
    {
        $sVendorId = false;
        if ($this->oxarticles__oxvendorid->value) {
            $sVendorId = $this->oxarticles__oxvendorid->value;
        }

        return $sVendorId;
    }

    /**
     * Returns article object Manufacturer ID. Result is cached into self::$_aArticleManufacturers
     *
     * @return string
     */
    public function getManufacturerId()
    {
        return $this->oxarticles__oxmanufacturerid->value ?: false;
    }

    /**
     * Returns current article Manufacturer object. If $blShopCheck = false, then
     * Manufacturer blReadOnly parameter will be set to true. If Manufacturer is
     * not assigned to current shop
     *
     * @param bool $blShopCheck Set false if shop check is not required (default is true)
     *
     * @return \OxidEsales\Eshop\Application\Model\Manufacturer|null
     */
    public function getManufacturer($blShopCheck = true)
    {
        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        if (
            !($sManufacturerId = $this->getManufacturerId()) &&
            !$blShopCheck && $this->oxarticles__oxmanufacturerid->value
        ) {
            $this->updateManufacturerBeforeLoading($oManufacturer);
            $sManufacturerId = $this->oxarticles__oxmanufacturerid->value;
        }

        if ($sManufacturerId && $oManufacturer->load($sManufacturerId)) {
            if (!Registry::getConfig()->getConfigParam('bl_perfLoadManufacturerTree')) {
                $oManufacturer->setReadOnly(true);
            }
            $oManufacturer = $oManufacturer->oxmanufacturers__oxactive->value ? $oManufacturer : null;
        } else {
            $oManufacturer = null;
        }

        return $oManufacturer;
    }

    /**
     * Checks if article is assigned to category $sCatNID.
     *
     * @param string $sCatNid category ID
     *
     * @return bool
     */
    public function inCategory($sCatNid)
    {
        return in_array($sCatNid, $this->getCategoryIds());
    }

    /**
     * Checks if article is assigned to passed category (even checks
     * if this category is "price category"). Returns true on success.
     *
     * @param string $sCatId category ID
     *
     * @return bool
     */
    public function isAssignedToCategory($sCatId)
    {
        // variant handling
        $sOXID = $this->getId();
        if (isset($this->oxarticles__oxparentid->value) && $this->oxarticles__oxparentid->value) {
            $sOXID = $this->oxarticles__oxparentid->value;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sSelect = $this->generateSelectCatStr($sOXID, $sCatId);
        $sOXID = $oDb->getOne($sSelect);
        // article is assigned to passed category!
        if (isset($sOXID) && $sOXID) {
            return true;
        }

        // maybe this category is price category ?
        if (Registry::getConfig()->getConfigParam('bl_perfLoadPrice') && $this->_blLoadPrice) {
            $dPriceFromTo = $this->getPrice()->getBruttoPrice();
            if ($dPriceFromTo > 0) {
                $sSelect = $this->generateSelectCatStr($sOXID, $sCatId, $dPriceFromTo);
                $sOXID = $oDb->getOne($sSelect);
                // article is assigned to passed category!
                if (isset($sOXID) && $sOXID) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns T price
     *
     * @return \OxidEsales\Eshop\Core\Price|null
     */
    public function getTPrice()
    {
        if (!Registry::getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        // return cached result, since oPrice is created ONLY in this function [or function of EQUAL level]
        if ($this->_oTPrice !== null) {
            return $this->_oTPrice;
        }

        $oPrice = $this->getPriceObject();

        $dBasePrice = $this->oxarticles__oxtprice->value;
        $dBasePrice = $this->preparePrice($dBasePrice, $this->getArticleVat());

        $oPrice->setPrice($dBasePrice);

        $this->applyVAT($oPrice, $this->getArticleVat());
        $this->applyCurrency($oPrice);

        if ($this->isParentNotBuyable()) {
            // if parent article is not buyable then compare agains min article variant price
            $oPrice2 = $this->getVarMinPrice();
        } else {
            // else compare against article price
            $oPrice2 = $this->getPrice();
        }

        if ($oPrice->getPrice() <= $oPrice2->getPrice()) {
            // if RRP price is less or equal to comparable price then return
            return;
        }

        $this->_oTPrice = $oPrice;

        return $this->_oTPrice;
    }

    /**
     * Checks if discount should be skipped for this article in basket. Returns true if yes.
     *
     * @return bool
     */
    public function skipDiscounts()
    {
        // already loaded skip discounts config
        if ($this->_blSkipDiscounts !== null) {
            return $this->_blSkipDiscounts;
        }

        if ($this->oxarticles__oxskipdiscounts->value) {
            return true;
        }


        $this->_blSkipDiscounts = false;
        if (Registry::get(\OxidEsales\Eshop\Application\Model\DiscountList::class)->hasSkipDiscountCategories()) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category', $this->getLanguage());
            $sViewName = $tableViewNameGenerator->getViewName('oxcategories', $this->getLanguage());
            $sSelect = "select 1 from $sO2CView as $sO2CView
                left join {$sViewName} on {$sViewName}.oxid = $sO2CView.oxcatnid
                where $sO2CView.oxobjectid = :oxobjectid
                    and {$sViewName}.oxactive = :oxactive
                    and {$sViewName}.oxskipdiscounts = :oxskipdiscounts ";
            $params = [
                ':oxobjectid' => $this->getId(),
                ':oxactive' => 1,
                ':oxskipdiscounts' => 1
            ];
            $this->_blSkipDiscounts = ($oDb->getOne($sSelect, $params) == 1);
        }

        return $this->_blSkipDiscounts;
    }

    /**
     * Sets the current oxPrice object
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice the new price object
     */
    public function setPrice(\OxidEsales\Eshop\Core\Price $oPrice)
    {
        $this->_oPrice = $oPrice;
    }

    /**
     * Returns base article price from database. Price may differ according to users group
     * Override this function if you want e.g. different prices for diff. usergroups.
     *
     * @param double $dAmount article amount. Default is 1
     *
     * @return double
     */
    public function getBasePrice($dAmount = 1)
    {
        // override this function if you want e.g. different prices
        // for diff. user groups.

        // Performance
        $myConfig = Registry::getConfig();
        if (!$myConfig->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        // GroupPrice or DB price ajusted by AmountPrice
        $dPrice = $this->getModifiedAmountPrice($dAmount);

        return $dPrice;
    }

    /**
     * Modifies given amount price.
     *
     * @param int $amount
     *
     * @return double
     */
    protected function getModifiedAmountPrice($amount)
    {
        return $this->getAmountPrice($amount);
    }

    /**
     * Calculates and returns price of article (adds taxes and discounts).
     *
     * @param float|int $dAmount article amount.
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getPrice($dAmount = 1)
    {
        $myConfig = Registry::getConfig();
        // Performance
        if (!$myConfig->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        // return cached result, since oPrice is created ONLY in this function [or function of EQUAL level]
        if ($dAmount != 1 || $this->_oPrice === null) {
            // module
            $dBasePrice = $this->getBasePrice($dAmount);
            $dBasePrice = $this->preparePrice($dBasePrice, $this->getArticleVat());

            $oPrice = $this->getPriceObject();

            $oPrice->setPrice($dBasePrice);

            // price handling
            if (!$this->_blCalcPrice && $dAmount == 1) {
                return $this->_oPrice = $oPrice;
            }

            $this->calculatePrice($oPrice);
            if ($dAmount != 1) {
                return $oPrice;
            }

            $this->_oPrice = $oPrice;
        }

        return $this->_oPrice;
    }

    /**
     * sets article user
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser user to set
     */
    public function setArticleUser($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * @return \OxidEsales\Eshop\Application\Model\User article user.
     */
    public function getArticleUser()
    {
        if ($this->_oUser) {
            return $this->_oUser;
        }

        return $this->getUser();
    }

    /**
     * Creates, calculates and returns oxPrice object for basket product.
     *
     * @param float  $dAmount  Amount
     * @param array  $aSelList Selection list
     * @param object $oBasket  User shopping basket object
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getBasketPrice($dAmount, $aSelList, $oBasket)
    {
        $oUser = $oBasket->getBasketUser();
        $this->setArticleUser($oUser);

        $oBasketPrice = $this->getPriceObject($oBasket->isCalculationModeNetto());

        // get base price
        $dBasePrice = $this->getBasePrice($dAmount);

        $dBasePrice = $this->modifySelectListPrice($dBasePrice, $aSelList);
        $dBasePrice = $this->preparePrice($dBasePrice, $this->getArticleVat(), $oBasket->isCalculationModeNetto());

        // applying select list price

        // setting price
        $oBasketPrice->setPrice($dBasePrice);

        $dVat = Registry::get(\OxidEsales\Eshop\Application\Model\VatSelector::class)->getBasketItemVat($this, $oBasket);
        $this->calculatePrice($oBasketPrice, $dVat);

        // returning final price object
        return $oBasketPrice;
    }

    /**
     * Deletes record and other information related to this article such as images from DB,
     * also removes variants. Returns true if entry was deleted.
     *
     * @param string $sOXID Article id
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }
        if (!$sOXID) {
            return false;
        }

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->startTransaction();
        try {
            // #2339 delete first variants before deleting parent product
            $this->deleteVariantRecords($sOXID);
            $this->load($sOXID);
            $this->deletePics();
            $this->onChangeResetCounts($sOXID, $this->oxarticles__oxvendorid->value, $this->oxarticles__oxmanufacturerid->value);

            // delete self
            $deleted = parent::delete($sOXID);

            $this->deleteRecords($sOXID);

            Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderArticle::class)->onDeleteArticle($this);

            $this->onChange(ACTION_DELETE, $sOXID, $this->oxarticles__oxparentid->value);

            $database->commitTransaction();
        } catch (Exception $exception) {
            $database->rollbackTransaction();

            throw $exception;
        }

        return $deleted;
    }

    /**
     * Reduce article stock. return the affected amount
     *
     * @param float $dAmount              amount to reduce
     * @param bool  $blAllowNegativeStock are negative stocks allowed?
     *
     * @return float
     */
    public function reduceStock($dAmount, $blAllowNegativeStock = false)
    {
        $this->actionType = ACTION_UPDATE_STOCK;
        $this->beforeUpdate();

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = 'select oxstock
            from oxarticles
            where oxid = :oxid FOR UPDATE ';
        $actualStock = $database->getOne($query, [
            ':oxid' => $this->getId()
        ]);

        $iStockCount = $actualStock - $dAmount;
        if (!$blAllowNegativeStock && ($iStockCount < 0)) {
            $dAmount += $iStockCount;
            $iStockCount = 0;
        }
        $this->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field($iStockCount);

        $query = 'update oxarticles set oxarticles.oxstock = :oxstock where oxarticles.oxid = :oxid';
        $database->execute($query, [
            ':oxstock' => $iStockCount,
            ':oxid' => $this->getId()
        ]);
        $this->onChange(ACTION_UPDATE_STOCK);

        return $dAmount;
    }

    /**
     * Recursive function. Updates quantity of sold articles.
     * Return true if amount was changed in database.
     *
     * @param float $dAmount Number of articles sold
     *
     * @return mixed
     */
    public function updateSoldAmount($dAmount = 0)
    {
        if (!$dAmount) {
            return;
        }
        $rs = false;
        // article is not variant - should be updated current amount
        if (!$this->oxarticles__oxparentid->value) {
            //updating by SQL query, due to wrong behaviour if saving article using not admin mode
            $dAmount = (double) $dAmount;
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $query = "update oxarticles
                      set oxarticles.oxsoldamount = (oxarticles.oxsoldamount + :amount)
                      where oxarticles.oxid = :oxid";
            $rs = $oDb->execute($query, [
                ':oxid' => $this->oxarticles__oxid->value,
                ':amount' => $dAmount
            ]);

            return (bool) $rs;
        } elseif ($this->oxarticles__oxparentid->value) {
            // article is variant - should be updated this article parent amount
            $oUpdateArticle = $this->getParentArticle();
            if ($oUpdateArticle) {
                $oUpdateArticle->updateSoldAmount($dAmount);
            }
        }

        return $rs;
    }

    /**
     * Disables reminder functionality for article
     *
     * @return bool
     */
    public function disableReminder()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = "update oxarticles set oxarticles.oxremindactive = 2 where oxarticles.oxid = :oxid";

        return (bool) $oDb->execute($query, [':oxid' => $this->oxarticles__oxid->value]);
    }

    /**
     * (\OxidEsales\Eshop\Application\Model\Article::_saveArtLongDesc()) save the object using parent::save() method.
     *
     * @return bool
     */
    public function save()
    {
        $this->assignParentDependFields();
        $blRet = parent::save();
        // saving long description
        $this->saveArtLongDesc();

        return $blRet;
    }

    /**
     * Changes article variant to parent article
     */
    public function resetParent()
    {
        $sParentId = $this->oxarticles__oxparentid->value;
        $this->oxarticles__oxparentid = new \OxidEsales\Eshop\Core\Field('', \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_blAllowEmptyParentId = true;
        $this->save();
        $this->_blAllowEmptyParentId = false;

        if ($sParentId !== '') {
            $this->onChange(ACTION_UPDATE, null, $sParentId);
        }
    }

    /**
     * collect article pics, icons, zoompic and puts it all in an array
     * structure of array (ActPicID, ActPic, MorePics, Pics, Icons, ZoomPic)
     *
     * @return array
     */
    public function getPictureGallery()
    {
        $myConfig = Registry::getConfig();

        //initialize
        $blMorePic = false;
        $aArtPics = [];
        $aArtIcons = [];
        $iActPicId = 1;
        $sActPic = $this->getPictureUrl($iActPicId);

        if (Registry::getRequest()->getRequestEscapedParameter('actpicid')) {
            $iActPicId = Registry::getRequest()->getRequestEscapedParameter('actpicid');
        }

        $oStr = Str::getStr();
        $iCntr = 0;
        $iPicCount = $myConfig->getConfigParam('iPicCount');
        $blCheckActivePicId = true;

        for ($i = 1; $i <= $iPicCount; $i++) {
            $sPicVal = $this->getPictureUrl($i);
            $sIcoVal = $this->getIconUrl($i);
            if (
                !$oStr->strstr($sIcoVal, 'nopic_ico.jpg') && !$oStr->strstr($sIcoVal, 'nopic.jpg') &&
                !$oStr->strstr($sPicVal, 'nopic_ico.jpg') && !$oStr->strstr($sPicVal, 'nopic.jpg') &&
                !$oStr->strstr($sIcoVal, 'nopic.webp') && !$oStr->strstr($sPicVal, 'nopic.webp') &&
                $sPicVal !== null
            ) {
                if ($iCntr) {
                    $blMorePic = true;
                }
                $aArtIcons[$i] = $sIcoVal;
                $aArtPics[$i] = $sPicVal;
                $iCntr++;

                if ($iActPicId == $i) {
                    $sActPic = $sPicVal;
                    $blCheckActivePicId = false;
                }
            } elseif ($blCheckActivePicId && $iActPicId <= $i) {
                // if picture is empty, setting active pic id to next
                // picture
                $iActPicId++;
            }
        }

        $blZoomPic = false;
        $aZoomPics = [];
        $iZoomPicCount = $myConfig->getConfigParam('iPicCount');

        for ($j = 1, $c = 1; $j <= $iZoomPicCount; $j++) {
            $sVal = $this->getZoomPictureUrl($j);

            if ($sVal && !$oStr->strstr($sVal, 'nopic.jpg')) {
                $blZoomPic = true;
                $aZoomPics[$c]['id'] = $c;
                $aZoomPics[$c]['file'] = $sVal;
                //anything is better than empty name, because <img src=""> calls shop once more = x2 SLOW.
                if (!$sVal) {
                    $aZoomPics[$c]['file'] = "nopic.jpg";
                }
                $c++;
            }
        }

        $aPicGallery = [
            'ActPicID' => $iActPicId,
            'ActPic'   => $sActPic,
            'MorePics' => $blMorePic,
            'Pics'     => $aArtPics,
            'Icons'    => $aArtIcons,
            'ZoomPic'  => $blZoomPic,
            'ZoomPics' => $aZoomPics
        ];

        return $aPicGallery;
    }

    /**
     * This function is triggered whenever article is saved or deleted or after the stock is changed.
     * Originally we need to update the oxstock for possible article parent in case parent is not buyable
     * Plus you may want to extend this function to update some extended information.
     * Call \OxidEsales\Eshop\Application\Model\Article::onChange($sAction, $sOXID) with ID parameter when changes are
     * executed over SQL.
     * (or use module class instead of oxArticle if such exists)
     *
     * @param string $action          Action constant
     * @param string $articleId       Article ID
     * @param string $parentArticleId Parent ID
     *
     * @return null
     */
    public function onChange($action = null, $articleId = null, $parentArticleId = null)
    {
        $this->actionType = !is_null($action) ? $action : $this->actionType;
        $myConfig = Registry::getConfig();

        if (!isset($articleId)) {
            if ($this->getId()) {
                $articleId = $this->getId();
            }
            if (!isset($articleId)) {
                $articleId = $this->oxarticles__oxid->value;
            }
            if ($this->oxarticles__oxparentid && $this->oxarticles__oxparentid->value) {
                $parentArticleId = $this->oxarticles__oxparentid->value;
            }
        }
        if (!isset($articleId)) {
            return;
        }

        //if (isset($sOXID) && !$myConfig->blVariantParentBuyable && $myConfig->blUseStock)
        if ($myConfig->getConfigParam('blUseStock')) {
            //if article has variants then updating oxvarstock field
            //getting parent id
            if (!isset($parentArticleId)) {
                $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
                $sQ = 'select oxparentid from oxarticles where oxid = :oxid';
                $parentArticleId = $oDb->getOne($sQ, [
                    ':oxid' => $articleId
                ]);
            }
            //if we have parent id then update stock
            if ($parentArticleId) {
                $this->onChangeUpdateStock($parentArticleId);
            }
        }
        //if we have parent id then update count
        //update count even if blUseStock is not active
        if ($parentArticleId) {
            $this->onChangeUpdateVarCount($parentArticleId);
        }

        $sId = ($parentArticleId) ? $parentArticleId : $articleId;
        $this->setVarMinMaxPrice($sId);

        $this->updateParentDependFields();

        // resetting articles count cache if stock has changed and some
        // articles goes offline (M:1448)
        if ($action === ACTION_UPDATE_STOCK) {
            $this->assignStock();
            $this->onChangeStockResetCount($articleId);
        }

        ContainerFacade::dispatch(new AfterModelUpdateEvent($this));
    }

    /**
     * Returns custom article VAT value if possible
     * By default value is taken from oxarticle__oxvat field
     *
     * @return double
     */
    public function getCustomVAT()
    {
        if ($this->__isset('oxarticles__oxvat') || $this->__get('oxarticles__oxvat')) {
            return $this->oxarticles__oxvat->value;
        }
    }

    /**
     * Checks if stock configuration allows to buy user chosen amount $dAmount
     *
     * @param double     $dAmount         buyable amount
     * @param double|int $dArtStockAmount stock amount
     * @param bool       $selectForUpdate Set true to select for update
     *
     * @return mixed
     */
    public function checkForStock($dAmount, $dArtStockAmount = 0, $selectForUpdate = false)
    {
        $myConfig = Registry::getConfig();
        if (!$myConfig->getConfigParam('blUseStock')) {
            return true;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        // fetching DB info as its up-to-date
        $sQ = 'select oxstock, oxstockflag from oxarticles
            where oxid = :oxid';
        $sQ .= $selectForUpdate ? ' FOR UPDATE ' : '';
        $rs = $oDb->select($sQ, [
            ':oxid' => $this->getId()
        ]);

        $iOnStock = 0;
        if ($rs !== false && $rs->count() > 0) {
            $iOnStock = $rs->fields['oxstock'] - $dArtStockAmount;
            $iStockFlag = $rs->fields['oxstockflag'];

            //When using stockflag 1 and 4 with basket reservations enabled but disallowing
            //negative stock values we would allow to reserve more items than are initially available
            //by keeping the stock level not lower than zero. When discarding reservations
            //stock level might differ from original value.
            if (
                !$myConfig->getConfigParam('blPsBasketReservationEnabled')
                || ($myConfig->getConfigParam('blPsBasketReservationEnabled')
                    && $myConfig->getConfigParam('blAllowNegativeStock'))
            ) {
                // foreign stock is also always considered as on stock
                if ($iStockFlag == 1 || $iStockFlag == 4) {
                    return true;
                }
            }
            if (!$myConfig->getConfigParam('blAllowUnevenAmounts')) {
                $iOnStock = floor($iOnStock);
            }
        }
        if (Registry::getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
            $session = Registry::getSession();
            $iOnStock += $session->getBasketReservations()->getReservedAmount($this->getId());
        }
        if ($iOnStock >= $dAmount) {
            return true;
        } else {
            if ($iOnStock > 0) {
                return $iOnStock;
            } else {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class);
                $oEx->setMessage('ERROR_MESSAGE_ARTICLE_ARTICLE_NOT_BUYABLE');
                Registry::getUtilsView()->addErrorToDisplay($oEx);

                return false;
            }
        }
    }

    /**
     * Get article long description
     *
     * @return object $oField field object
     */
    public function getLongDescription()
    {
        if ($this->_oLongDesc === null) {
            // initializing
            $this->_oLongDesc = new \OxidEsales\Eshop\Core\Field();

            // choosing which to get..
            $sOxid = $this->getId();
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sViewName = $tableViewNameGenerator->getViewName('oxartextends', $this->getLanguage());

            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sDbValue = $oDb->getOne("select oxlongdesc from {$sViewName} where oxid = :oxid", [
                ':oxid' => $sOxid
            ]);

            if ($sDbValue != false) {
                $this->_oLongDesc->setValue($sDbValue, \OxidEsales\Eshop\Core\Field::T_RAW);
            } elseif ($this->oxarticles__oxparentid && $this->oxarticles__oxparentid->value) {
                if (!$this->isAdmin() || $this->_blLoadParentData) {
                    $oParent = $this->getParentArticle();
                    if ($oParent) {
                        $this->_oLongDesc->setValue($oParent->getLongDescription()->getRawValue(), \OxidEsales\Eshop\Core\Field::T_RAW);
                    }
                }
            }
        }

        return $this->_oLongDesc;
    }

    /**
     * Save article long description to oxartext table
     *
     * @param string $longDescription description to set
     */
    public function setArticleLongDesc($longDescription)
    {
        // setting current value
        $this->_oLongDesc = new \OxidEsales\Eshop\Core\Field($longDescription, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->oxarticles__oxlongdesc = new \OxidEsales\Eshop\Core\Field($longDescription, \OxidEsales\Eshop\Core\Field::T_RAW);
    }

    /**
     * the uninitilized list of attributes
     * use getAttributes
     * @return \OxidEsales\Eshop\Application\Model\AttributeList
     */
    protected function newAttributeList()
    {
        return oxNew(\OxidEsales\Eshop\Application\Model\AttributeList::class);
    }

    /**
     * Loads and returns attribute list associated with this article
     *
     * @return \OxidEsales\Eshop\Application\Model\AttributeList
     */
    public function getAttributes()
    {
        if ($this->_oAttributeList === null) {
            $this->_oAttributeList = $this->newAttributelist();
            $this->_oAttributeList->loadAttributes($this->getId(), $this->getParentId());
        }

        return $this->_oAttributeList;
    }

    /**
     * Loads and returns attribute list for display in basket
     *
     * @return \OxidEsales\Eshop\Application\Model\AttributeList
     */
    public function getAttributesDisplayableInBasket()
    {
        if ($this->basketAttributeList === null) {
            $this->basketAttributeList = $this->newAttributelist();
            $this->basketAttributeList->loadAttributesDisplayableInBasket($this->getId(), $this->getParentId());
        }

        return $this->basketAttributeList;
    }


    /**
     * Appends article seo url with additional request parameters
     *
     * @param string $sAddParams additional parameters which needs to be added to product url
     * @param int    $iLang      language id
     */
    public function appendLink($sAddParams, $iLang = null)
    {
        if ($sAddParams) {
            if ($iLang === null) {
                $iLang = $this->getLanguage();
            }

            $this->_aSeoAddParams[$iLang] = isset($this->_aSeoAddParams[$iLang]) ? $this->_aSeoAddParams[$iLang] . "&amp;" : "";
            $this->_aSeoAddParams[$iLang] .= $sAddParams;
        }
    }

    /**
     * Returns raw article seo url
     *
     * @param int  $iLang  language id
     * @param bool $blMain force to return main url [optional]
     *
     * @return string
     */
    public function getBaseSeoLink($iLang, $blMain = false)
    {
        /** @var \OxidEsales\Eshop\Application\Model\SeoEncoderArticle $oEncoder */
        $oEncoder = Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderArticle::class);
        if (!$blMain) {
            return $oEncoder->getArticleUrl($this, $iLang, $this->getLinkType());
        }

        return $oEncoder->getArticleMainUrl($this, $iLang);
    }

    /**
     * Gets article link
     *
     * @param int  $iLang  language id [optional]
     * @param bool $blMain force to return main url [optional]
     *
     * @return string
     */
    public function getLink($iLang = null, $blMain = false)
    {
        if (!Registry::getUtils()->seoIsActive()) {
            return $this->getStdLink($iLang);
        }

        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        $iLinkType = $this->getLinkType();
        if (!isset($this->_aSeoUrls[$iLang][$iLinkType])) {
            $this->_aSeoUrls[$iLang][$iLinkType] = $this->getBaseSeoLink($iLang, $blMain);
        }

        $sUrl = $this->_aSeoUrls[$iLang][$iLinkType];
        if (isset($this->_aSeoAddParams[$iLang])) {
            $sUrl .= ((strpos($sUrl . $this->_aSeoAddParams[$iLang], '?') === false) ? '?' : '&amp;') . $this->_aSeoAddParams[$iLang];
        }

        return $sUrl;
    }

    /**
     * Returns main object URL. If SEO is ON returned link will be in SEO form,
     * else URL will have dynamic form
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getMainLink($iLang = null)
    {
        return $this->getLink($iLang, true);
    }

    /**
     * Resets details link
     *
     * @param int $iType type of link to load
     */
    public function setLinkType($iType)
    {
        // resetting details link, to force new
        $this->_sDetailLink = null;

        // setting link type
        $this->_iLinkType = (int) $iType;
    }

    /**
     * Get link type
     *
     * @return int
     */
    public function getLinkType()
    {
        return $this->_iLinkType;
    }

    /**
     * Appends article dynamic url with additional request parameters
     *
     * @param string $sAddParams additional parameters which needs to be added to product url
     * @param int    $iLang      language id
     */
    public function appendStdLink($sAddParams, $iLang = null)
    {
        if ($sAddParams) {
            if ($iLang === null) {
                $iLang = $this->getLanguage();
            }

            $this->_aStdAddParams[$iLang] = isset($this->_aStdAddParams[$iLang]) ? $this->_aStdAddParams[$iLang] . "&amp;" : "";
            $this->_aStdAddParams[$iLang] .= $sAddParams;
        }
    }

    /**
     * Returns base dynamic url: shopurl/index.php?cl=details
     *
     * @param int  $iLang   language id
     * @param bool $blAddId add current object id to url or not [optional]
     * @param bool $blFull  return full including domain name [optional]
     *
     * @return string
     */
    public function getBaseStdLink($iLang, $blAddId = true, $blFull = true)
    {
        $sUrl = '';
        if ($blFull) {
            //always returns shop url, not admin
            $sUrl = Registry::getConfig()->getShopUrl($iLang, false);
        }

        $sUrl .= "index.php?cl=details" . ($blAddId ? "&amp;anid=" . $this->getId() : "");

        return $sUrl . (isset($this->_aStdAddParams[$iLang]) ? "&amp;" . $this->_aStdAddParams[$iLang] : "");
    }

    /**
     * Returns standard URL to product
     *
     * @param int   $iLang   required language. optional
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink($iLang = null, $aParams = [])
    {
        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        if (!isset($this->_aStdUrls[$iLang])) {
            $this->_aStdUrls[$iLang] = $this->getBaseStdLink($iLang);
        }

        return Registry::getUtilsUrl()->processUrl($this->_aStdUrls[$iLang], true, $aParams, $iLang);
    }

    /**
     * Return article media URL
     *
     * @return array
     */
    public function getMediaUrls()
    {
        if ($this->_aMediaUrls === null) {
            $this->_aMediaUrls = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $this->_aMediaUrls->init("oxmediaurl");
            $this->_aMediaUrls->getBaseObject()->setLanguage($this->getLanguage());

            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sViewName = $tableViewNameGenerator->getViewName("oxmediaurls", $this->getLanguage());
            $sQ = "select * from {$sViewName} where oxobjectid = :oxobjectid";
            $this->_aMediaUrls->selectString($sQ, [
                ':oxobjectid' => $this->getId()
            ]);
        }

        return $this->_aMediaUrls;
    }

    /**
     * Get image url
     *
     * @return array
     */
    public function getDynImageDir()
    {
        return $this->_sDynImageDir;
    }

    /**
     * Returns select lists to display
     *
     * @return array
     */
    public function getDispSelList()
    {
        if ($this->_aDispSelList === null) {
            if (Registry::getConfig()->getConfigParam('bl_perfLoadSelectLists') && Registry::getConfig()->getConfigParam('bl_perfLoadSelectListsInAList')) {
                $this->_aDispSelList = $this->getSelectLists();
            }
        }

        return $this->_aDispSelList;
    }

    /**
     * Get more details link
     *
     * @return string
     */
    public function getMoreDetailLink()
    {
        if ($this->_sMoreDetailLink == null) {
            // and assign special article values
            $this->_sMoreDetailLink = Registry::getConfig()->getShopHomeUrl() . 'cl=moredetails';

            // not always it is okey, as not all the time active category is the same as primary article cat.
            if ($sActCat = Registry::getRequest()->getRequestEscapedParameter('cnid')) {
                $this->_sMoreDetailLink .= '&amp;cnid=' . $sActCat;
            }
            $this->_sMoreDetailLink .= '&amp;anid=' . $this->getId();
        }

        return $this->_sMoreDetailLink;
    }

    /**
     * Get to basket link
     *
     * @return string
     */
    public function getToBasketLink()
    {
        if ($this->_sToBasketLink == null) {
            $myConfig = Registry::getConfig();

            if (Registry::getUtils()->isSearchEngine()) {
                $this->_sToBasketLink = $this->getLink();
            } else {
                // and assign special article values
                $this->_sToBasketLink = $myConfig->getShopHomeUrl();

                // override some classes as these should never showup
                $actControllerId = Registry::getConfig()->getRequestControllerId();
                if ($actControllerId == 'thankyou') {
                    $actControllerId = 'basket';
                }
                $this->_sToBasketLink .= 'cl=' . $actControllerId;

                // this is not very correct
                if ($sActCat = Registry::getRequest()->getRequestEscapedParameter('cnid')) {
                    $this->_sToBasketLink .= '&amp;cnid=' . $sActCat;
                }

                $this->_sToBasketLink .= '&amp;fnc=tobasket&amp;aid=' . $this->getId() . '&amp;anid=' . $this->getId();

                if ($sTpl = basename(Registry::getRequest()->getRequestEscapedParameter('tpl'))) {
                    $this->_sToBasketLink .= '&amp;tpl=' . $sTpl;
                }
            }
        }

        return $this->_sToBasketLink;
    }

    /**
     * Get stock status
     *
     * @return integer
     */
    public function getStockStatus()
    {
        return $this->_iStockStatus;
    }

    /**
     * Get stock status as it was on loading this object.
     *
     * @return integer
     */
    public function getStockStatusOnLoad()
    {
        return $this->_iStockStatusOnLoad;
    }

    /**
     * Get stock
     *
     * @return float
     */
    public function getStock()
    {
        return $this->oxarticles__oxstock->value;
    }

    /**
     * Returns formatted delivery date. If the date is past or not set ('0000-00-00') returns false.
     *
     * @deprecated since v6.2 (2020-02-26); use getRestockDate();
     * @return string|bool
     */
    public function getDeliveryDate()
    {
        return $this->getRestockDate();
    }

    /**
     * Returns formatted delivery date. If the date is past or not set ('0000-00-00') returns false.
     *
     * @return string|bool
     */
    public function getRestockDate()
    {
        $restockDate = $this->getFieldData('oxdelivery');
        if ($restockDate >= date('Y-m-d')) {
            return Registry::getUtilsDate()->formatDBDate($restockDate);
        }

        return false;
    }

    /**
     * Returns rounded T price.
     *
     * @deprecated since v5.1 (2013-10-03); use getTPrice() and oxPrice modifier;
     *
     * @return double|bool
     */
    public function getFTPrice()
    {
        // module
        if ($oPrice = $this->getTPrice()) {
            if ($dPrice = $this->getPriceForView($oPrice)) {
                return Registry::getLang()->formatCurrency($dPrice);
            }
        }
    }

    /**
     * Returns formatted product's price.
     *
     * @deprecated since v5.1 (2013-10-04); use oxPrice template engine plugin for formatting in templates
     *
     * @return double
     */
    public function getFPrice()
    {
        if ($oPrice = $this->getPrice()) {
            $dPrice = $this->getPriceForView($oPrice);

            return Registry::getLang()->formatCurrency($dPrice);
        }
    }

    /**
     * Resets oxremindactive status.
     * If remindActive status is 2, reminder is already sent.
     */
    public function resetRemindStatus()
    {
        if (
            $this->oxarticles__oxremindactive->value == 2 &&
            $this->oxarticles__oxremindamount->value <= $this->oxarticles__oxstock->value
        ) {
            $this->oxarticles__oxremindactive->value = 1;
        }
    }

    /**
     * Returns formatted product's NETTO price.
     *
     * @deprecated since v5.1 (2013-10-03); use getPrice() and oxPrice modifier;
     *
     * @return double
     */
    public function getFNetPrice()
    {
        if ($oPrice = $this->getPrice()) {
            return Registry::getLang()->formatCurrency($oPrice->getNettoPrice());
        }
    }

    /**
     * Returns true if parent is not buyable
     *
     * @return bool
     */
    public function isParentNotBuyable()
    {
        return $this->_blNotBuyableParent;
    }

    /**
     * Returns true if article is not buyable
     *
     * @return bool
     */
    public function isNotBuyable()
    {
        return $this->_blNotBuyable;
    }

    /**
     * Sets product state - buyable or not
     *
     * @param bool $blBuyable state - buyable or not (default false)
     */
    public function setBuyableState($blBuyable = false)
    {
        $this->_blNotBuyable = !$blBuyable;
    }

    /**
     * Sets selectlists of current product
     *
     * @param array $aSelList selectlist
     */
    public function setSelectlist($aSelList)
    {
        $this->_aDispSelList = $aSelList;
    }

    /**
     * Returns article picture
     *
     * @param int $iIndex picture index
     *
     * @return string
     */
    public function getPictureUrl($iIndex = 1)
    {
        if ($iIndex) {
            $sImgName = false;
            if (!$this->isFieldEmpty("oxarticles__oxpic" . $iIndex)) {
                $sImgName = basename($this->{"oxarticles__oxpic$iIndex"}->value);
            }

            $sSize = Registry::getConfig()->getConfigParam('aDetailImageSizes');

            return Registry::getPictureHandler()
                ->getProductPicUrl("product/{$iIndex}/", $sImgName, $sSize, 'oxpic' . $iIndex);
        }
    }

    /**
     * Returns article icon picture url. If no index specified, will
     * return main icon url.
     *
     * @param int $iIndex picture index
     *
     * @return string
     */
    public function getIconUrl($iIndex = 0)
    {
        $sImgName = false;
        $sDirname = "product/1/";
        if ($iIndex && !$this->isFieldEmpty("oxarticles__oxpic{$iIndex}")) {
            $sImgName = basename($this->{"oxarticles__oxpic$iIndex"}->value);
            $sDirname = "product/{$iIndex}/";
        } elseif (!$this->isFieldEmpty("oxarticles__oxicon")) {
            $sImgName = basename($this->oxarticles__oxicon->value);
            $sDirname = "product/icon/";
        } elseif (!$this->isFieldEmpty("oxarticles__oxpic1")) {
            $sImgName = basename($this->oxarticles__oxpic1->value);
        }

        $sSize = Registry::getConfig()->getConfigParam('sIconsize');

        $sIconUrl = Registry::getPictureHandler()->getProductPicUrl($sDirname, $sImgName, $sSize, $iIndex);

        return $sIconUrl;
    }

    /**
     * Returns article thumbnail picture url
     *
     * @param bool $bSsl to force SSL
     *
     * @return string
     */
    public function getThumbnailUrl($bSsl = null)
    {
        $sImgName = false;
        $sDirname = "product/1/";
        if (!$this->isFieldEmpty("oxarticles__oxthumb")) {
            $sImgName = basename($this->oxarticles__oxthumb->value);
            $sDirname = "product/thumb/";
        } elseif (!$this->isFieldEmpty("oxarticles__oxpic1")) {
            $sImgName = basename($this->oxarticles__oxpic1->value);
        }

        $sSize = Registry::getConfig()->getConfigParam('sThumbnailsize');

        return Registry::getPictureHandler()->getProductPicUrl($sDirname, $sImgName, $sSize, 0, $bSsl);
    }

    /**
     * Returns article zoom picture url
     *
     * @param int $iIndex picture index
     *
     * @return string
     */
    public function getZoomPictureUrl($iIndex = 0)
    {
        if ($iIndex > 0 && !$this->isFieldEmpty("oxarticles__oxpic" . $iIndex)) {
            $sImgName = basename($this->{"oxarticles__oxpic" . $iIndex}->value);
            $sSize = Registry::getConfig()->getConfigParam("sZoomImageSize");

            return Registry::getPictureHandler()->getProductPicUrl(
                "product/{$iIndex}/",
                $sImgName,
                $sSize,
                'oxpic' . $iIndex
            );
        }
    }

    /**
     * apply article and article use
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice target price
     */
    public function applyVats(\OxidEsales\Eshop\Core\Price $oPrice)
    {
        $this->applyVAT($oPrice, $this->getArticleVat());
    }

    /**
     * Applies discounts which should be applied in general case (for 0 amount)
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice Price object
     */
    public function applyDiscountsForVariant($oPrice)
    {
        // apply discounts
        if (!$this->skipDiscounts()) {
            $oDiscountList = Registry::get(\OxidEsales\Eshop\Application\Model\DiscountList::class);
            $aDiscounts = $oDiscountList->getArticleDiscounts($this, $this->getArticleUser());

            reset($aDiscounts);
            foreach ($aDiscounts as $oDiscount) {
                $oPrice->setDiscount($oDiscount->getAddSum(), $oDiscount->getAddSumType());
            }
            $oPrice->calculateDiscount();
        }
    }

    /**
     * Get parent article
     *
     * @return Article
     */
    public function getParentArticle()
    {
        if ($this->oxarticles__oxparentid && ($sParentId = $this->oxarticles__oxparentid->value)) {
            $sIndex = $sParentId . "_" . $this->getLanguage();
            if (!isset(self::$_aLoadedParents[$sIndex])) {
                self::$_aLoadedParents[$sIndex] = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                self::$_aLoadedParents[$sIndex]->_blLoadPrice = false;
                self::$_aLoadedParents[$sIndex]->_blLoadVariants = false;

                if (!self::$_aLoadedParents[$sIndex]->loadInLang($this->getLanguage(), $sParentId)) {
                    //return false in case parent product failed to load
                    self::$_aLoadedParents[$sIndex] = false;
                }
            }

            return self::$_aLoadedParents[$sIndex];
        }
    }

    /**
     * Updates article variants oxremindactive field, as variants inherit this setting from parent
     */
    public function updateVariantsRemind()
    {
        // check if it is parent article
        if (!$this->isVariant() && $this->hasAnyVariant()) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sUpdate = "update oxarticles
                        set oxremindactive = :oxremindactive
                        where oxparentid = :oxparentid and
                              oxshopid = :oxshopid";
            $oDb->execute($sUpdate, [
                ':oxremindactive' => $this->oxarticles__oxremindactive->value,
                ':oxparentid' => $this->getId(),
                ':oxshopid' => $this->getShopId()
            ]);
        }
    }

    /**
     * Returns product id (oxid)
     * (required for interface oxIArticle)
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->getId();
    }

    /**
     * Returns product parent id (oxparentid)
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->oxarticles__oxparentid instanceof Field ? $this->oxarticles__oxparentid->value : '';
    }

    /**
     * Returns false if object is not derived from oxorderarticle class
     *
     * @return bool
     */
    public function isOrderArticle()
    {
        return false;
    }

    /**
     * Returns TRUE if product is variant, and false if not
     *
     * @return bool
     */
    public function isVariant(): bool
    {
        $isVariant = false;
        if (isset($this->oxarticles__oxparentid) && false !== $this->oxarticles__oxparentid) {
            $isVariant = (bool) $this->oxarticles__oxparentid->value;
        }

        return $isVariant;
    }

    /**
     * Returns TRUE if product is multidimensional variant, and false if not
     *
     * @return bool
     */
    public function isMdVariant()
    {
        $oMdVariant = oxNew(\OxidEsales\Eshop\Application\Model\VariantHandler::class);

        return $oMdVariant->isMdVariant($this);
    }

    /**
     * get Sql for loading price categories which include this article
     *
     * @param string $sFields fields to load from oxCategories
     *
     * @return string
     */
    public function getSqlForPriceCategories($sFields = '')
    {
        if (!$sFields) {
            $sFields = 'oxid';
        }
        $sSelectWhere = "select $sFields from " . $this->getObjectViewName('oxcategories') . " where";
        $sQuotedPrice = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($this->oxarticles__oxprice->value);

        return "$sSelectWhere oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= $sQuotedPrice and oxpriceto >= $sQuotedPrice"
               . " union $sSelectWhere oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= $sQuotedPrice"
               . " union $sSelectWhere oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= $sQuotedPrice";
    }

    /**
     * Checks if article is assigned to price category $sCatNID.
     *
     * @param string $categoryPriceId Price category ID
     *
     * @return bool
     */
    public function inPriceCategory($categoryPriceId)
    {
        return (bool) $this->fetchFirstInPriceCategory($categoryPriceId);
    }

    /**
     * Fetch the article corresponding to this object in the price category with the given id.
     *
     * @param string $categoryPriceId The id of the category we want to check, if this article is in.
     *
     * @return string One, if the given article is in the given price category, else empty string.
     */
    protected function fetchFirstInPriceCategory($categoryPriceId)
    {
        $database = $this->getDatabase();

        $query = $this->createFetchFirstInPriceCategorySql($categoryPriceId);

        $result = $database->getOne($query);

        return $result;
    }

    /**
     * Create the sql for the fetchFirstInPriceCategory method.
     *
     * @param string $categoryPriceId The price category id.
     *
     * @return string The wished sql.
     */
    protected function createFetchFirstInPriceCategorySql($categoryPriceId)
    {
        $database = $this->getDatabase();

        $quotedPrice = $database->quote($this->oxarticles__oxprice->value);
        $quotedCategoryId = $database->quote($categoryPriceId);

        $query = "select 1 from " . $this->getObjectViewName('oxcategories') . " where oxid=$quotedCategoryId and"
                 . "(   (oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= $quotedPrice and oxpriceto >= $quotedPrice)"
                 . " or (oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= $quotedPrice)"
                 . " or (oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= $quotedPrice)"
                 . ")";

        return $query;
    }

    /**
     * Get the database object.
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    protected function getDatabase()
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
    }

    /**
     * Returns multidimensional variant structure
     *
     * @return \OxidEsales\Eshop\Application\Model\MdVariant
     */
    public function getMdVariants()
    {
        if ($this->_oMdVariants) {
            return $this->_oMdVariants;
        }

        $oParentArticle = $this->getParentArticle();
        if ($oParentArticle) {
            $oVariants = $oParentArticle->getVariants();
        } else {
            $oVariants = $this->getVariants();
        }

        /** @var \OxidEsales\Eshop\Application\Model\VariantHandler $oVariantHandler */
        $oVariantHandler = oxNew(\OxidEsales\Eshop\Application\Model\VariantHandler::class);
        $this->_oMdVariants = $oVariantHandler->buildMdVariants($oVariants, $this->getId());

        return $this->_oMdVariants;
    }

    /**
     * Returns first level variants from multidimensional variants list
     *
     * @return \OxidEsales\Eshop\Application\Model\MdVariant
     */
    public function getMdSubvariants()
    {
        return $this->getMdVariants()->getMdSubvariants();
    }

    /**
     * Return article picture file name
     *
     * @param string $sFieldName article picture field name
     * @param int    $iIndex     article picture index
     *
     * @return string
     */
    public function getPictureFieldValue($sFieldName, $iIndex = null)
    {
        if ($sFieldName) {
            $sFieldName = "oxarticles__" . $sFieldName . $iIndex;

            if ($this->$sFieldName && $this->$sFieldName->value)
            {
                return $this->$sFieldName->value;
            }
        }

        return '';
    }

    /**
     * Get master zoom picture url
     *
     * @param int $iIndex picture index
     *
     * @return string
     */
    public function getMasterZoomPictureUrl($iIndex)
    {
        $sPicUrl = false;
        $sPicName = basename($this->{"oxarticles__oxpic" . $iIndex}->value);

        if ($sPicName && $sPicName != "nopic.jpg") {
            $sPicUrl = Registry::getConfig()->getPictureUrl("master/product/" . $iIndex . "/" . $sPicName);
            if (!$sPicUrl || basename($sPicUrl) == "nopic.jpg") {
                $sPicUrl = false;
            }
        }

        return $sPicUrl;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getMasterPicturePath(string $file): string
    {
        return Registry::getConfig()->getMasterPicturePath($file);
    }

    /**
     * Returns oxarticles__oxunitname value processed by \OxidEsales\Eshop\Core\Language::translateString()
     *
     * @return string
     */
    public function getUnitName()
    {
        if ($this->oxarticles__oxunitname->value) {
            return Registry::getLang()->translateString($this->oxarticles__oxunitname->value);
        }
    }

    /**
     * Return article downloadable file list (oxlist of oxfile)
     *
     * @param bool $blAddFromParent - return with parent files if not buyable
     *
     * @return null|oxList of oxFile
     */
    public function getArticleFiles($blAddFromParent = false)
    {
        if ($this->_aArticleFiles === null) {
            $this->_aArticleFiles = false;

            $sQ = "SELECT * FROM `oxfiles` WHERE `oxartid` = :oxartid";

            if (!Registry::getConfig()->getConfigParam('blVariantParentBuyable') && $blAddFromParent) {
                $sQ .= " OR `oxartId` = :oxparentid";
            }

            $oArticleFiles = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oArticleFiles->init("oxfile");
            $oArticleFiles->selectString($sQ, [
                ':oxartid' => $this->getId(),
                ':oxparentid' => $this->oxarticles__oxparentid->value
            ]);
            $this->_aArticleFiles = $oArticleFiles;
        }

        return $this->_aArticleFiles;
    }

    /**
     * Returns oxarticles__oxisdownloadable value
     *
     * @return bool
     */
    public function isDownloadable()
    {
        return $this->oxarticles__oxisdownloadable->value;
    }

    /**
     * Checks if articles has amount price
     *
     * @return bool
     */
    public function hasAmountPrice()
    {
        if (self::$_blHasAmountPrice === null) {
            self::$_blHasAmountPrice = false;

            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "SELECT 1 FROM `oxprice2article` LIMIT 1";

            if ($oDb->getOne($sQ)) {
                self::$_blHasAmountPrice = true;
            }
        }

        return self::$_blHasAmountPrice;
    }

    /**
     * Loads and returns variants list.
     *
     * @param bool      $loadSimpleVariants    if parameter $blSimple - list will be filled with oxSimpleVariant
     *                                         objects, else - oxArticle
     * @param bool      $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of
     *                                         stock [optional]
     * @param bool|null $forceCoreTableUsage   if true forces core table use, default is false [optional]
     *
     * @return array|\OxidEsales\Eshop\Application\Model\SimpleVariantList|\OxidEsales\Eshop\Application\Model\ArticleList
     */
    protected function loadVariantList($loadSimpleVariants, $blRemoveNotOrderables = true, $forceCoreTableUsage = null)
    {
        $variants = [];
        if (($articleId = $this->getId())) {
            //do not load me as a parent later
            self::$_aLoadedParents[$articleId . "_" . $this->getLanguage()] = $this;

            $config = Registry::getConfig();

            if (
                !$this->_blLoadVariants ||
                (!$this->isAdmin() && !$config->getConfigParam('blLoadVariants')) ||
                (!$this->isAdmin() && !$this->oxarticles__oxvarcount->value)
            ) {
                return $variants;
            }

            // cache
            $cacheKey = $loadSimpleVariants ? "simple" : "full";
            if ($blRemoveNotOrderables) {
                if (isset($this->_aVariants[$cacheKey])) {
                    return $this->_aVariants[$cacheKey];
                }
                $this->_aVariants[$cacheKey] = &$variants;
            } elseif (!$blRemoveNotOrderables) {
                if (isset($this->_aVariantsWithNotOrderables[$cacheKey])) {
                    return $this->_aVariantsWithNotOrderables[$cacheKey];
                }
                $this->_aVariantsWithNotOrderables[$cacheKey] = &$variants;
            }

            if (($this->_blHasVariants = $this->hasAnyVariant($forceCoreTableUsage))) {
                //load simple variants for lists
                if ($loadSimpleVariants) {
                    $variants = oxNew(\OxidEsales\Eshop\Application\Model\SimpleVariantList::class);
                    $variants->setParent($this);
                } else {
                    //loading variants
                    $variants = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                    $variants->getBaseObject()->modifyCacheKey('_variants');
                }

                startProfile("selectVariants");
                $forceCoreTableUsage = (bool) $forceCoreTableUsage;

                $baseObject = $variants->getBaseObject();
                $this->updateVariantsBaseObject($baseObject, $forceCoreTableUsage);

                $sArticleTable = $this->getViewName($forceCoreTableUsage);

                $query = $this->getLoadVariantsQuery($blRemoveNotOrderables, $forceCoreTableUsage, $baseObject, $sArticleTable);
                $variants->selectString($query);

                //if this is multidimensional variants, make additional processing
                if ($config->getConfigParam('blUseMultidimensionVariants')) {
                    $oMdVariants = oxNew(\OxidEsales\Eshop\Application\Model\VariantHandler::class);
                    $this->_blHasMdVariants = $oMdVariants->isMdVariant($variants->current());
                }
                stopProfile("selectVariants");
            }

            //if we have variants then depending on config option the parent may be non buyable
            if (!$config->getConfigParam('blVariantParentBuyable') && $this->_blHasVariants) {
                $this->_blNotBuyableParent = true;
            }

            //if we have variants, but all variants are incative means article may be non buyable (depends on config option)
            if (!$config->getConfigParam('blVariantParentBuyable') && count($variants) == 0 && $this->_blHasVariants) {
                $this->_blNotBuyable = true;
            }
        }

        return $variants;
    }

    /**
     * Selects category IDs from given SQL statement and ID field name
     *
     * @param string $query sql statement
     * @param string $field category ID field name
     *
     * @return array
     */
    protected function selectCategoryIds($query, $field)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $aResult = $oDb->getAll($query);
        $aReturn = [];

        foreach ($aResult as $aValue) {
            $aValue = array_change_key_case($aValue, CASE_LOWER);

            $aReturn[] = $aValue[$field];
        }

        return $aReturn;
    }

    /**
     * Returns query for article categories select
     *
     * @param bool $blActCats select categories if all parents are active
     *
     * @return string
     */
    protected function getCategoryIdsSelect($blActCats = false)
    {
        $sO2CView = $this->getObjectViewName('oxobject2category');
        $sCatView = $this->getObjectViewName('oxcategories');

        $sArticleIdSql = 'oxobject2category.oxobjectid=' . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($this->getId());
        if ($this->getParentId()) {
            $sArticleIdSql = '(' . $sArticleIdSql . ' or oxobject2category.oxobjectid=' . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($this->getParentId()) . ')';
        }
        $sActiveCategorySql = $blActCats ? $this->getActiveCategorySelectSnippet() : '';

        $sSelect = "select
                        oxobject2category.oxcatnid as oxcatnid
                     from $sO2CView as oxobject2category
                        left join $sCatView as oxcategories on oxcategories.oxid = oxobject2category.oxcatnid
                    where $sArticleIdSql and oxcategories.oxid is not null and oxcategories.oxactive = 1 $sActiveCategorySql
                    order by oxobject2category.oxtime";

        return $sSelect;
    }

    /**
     * Returns active category select snippet
     *
     * @return string
     */
    protected function getActiveCategorySelectSnippet()
    {
        $sCatView = $this->getObjectViewName('oxcategories');

        return "and oxcategories.oxhidden = 0 and (select count(cats.oxid) from $sCatView as cats where cats.oxrootid = oxcategories.oxrootid and cats.oxleft < oxcategories.oxleft and cats.oxright > oxcategories.oxright and ( cats.oxhidden = 1 or cats.oxactive = 0 ) ) = 0 ";
    }

    /**
     * Calculates price of article (adds taxes, currency and discounts).
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice price object
     * @param double                       $dVat   vat value, optional, if passed, bypasses
     *                                             "bl_perfCalcVatOnlyForBasketOrder" config value
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    protected function calculatePrice($oPrice, $dVat = null)
    {
        // apply VAT only if configuration requires it
        if (isset($dVat) || !Registry::getConfig()->getConfigParam('bl_perfCalcVatOnlyForBasketOrder')) {
            $this->applyVAT($oPrice, isset($dVat) ? $dVat : $this->getArticleVat());
        }

        // apply currency
        $this->applyCurrency($oPrice);
        // apply discounts
        if (!$this->skipDiscounts()) {
            $oDiscountList = Registry::get(\OxidEsales\Eshop\Application\Model\DiscountList::class);
            $aDiscounts = $oDiscountList->getArticleDiscounts($this, $this->getArticleUser());

            reset($aDiscounts);
            foreach ($aDiscounts as $oDiscount) {
                $oPrice->setDiscount($oDiscount->getAddSum(), $oDiscount->getAddSumType());
            }
            $oPrice->calculateDiscount();
        }

        return $oPrice;
    }

    /**
     * Checks if parent has ANY variant assigned
     *
     * @param bool $blForceCoreTable force core table usage
     *
     * @return bool
     */
    protected function hasAnyVariant($blForceCoreTable = null)
    {
        if (($sId = $this->getId())) {
            if ($this->oxarticles__oxshopid->value == Registry::getConfig()->getShopId()) {
                return (bool) $this->oxarticles__oxvarcount->value;
            }
            $sArticleTable = $this->getViewName($blForceCoreTable);

            $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            return (bool)$db->getOne("select 1 from $sArticleTable where oxparentid = :oxparentid", [
                ':oxparentid' => $sId
            ]);
        }

        return false;
    }

    /**
     * Check if stock status has changed since loading the article
     *
     * @return bool
     */
    protected function isStockStatusChanged()
    {
        return $this->_iStockStatus != $this->_iStockStatusOnLoad;
    }

    /**
     * Check if visibility has changed since loading the article
     *
     * @return bool
     */
    protected function isVisibilityChanged()
    {
        return $this->isStockStatusChanged() && ($this->_iStockStatus == -1 || $this->_iStockStatusOnLoad == -1);
    }

    /**
     * inserts article long description to artextends table
     *
     * @return null
     */
    protected function saveArtLongDesc()
    {
        if (in_array("oxlongdesc", $this->_aSkipSaveFields)) {
            return;
        }

        if ($this->_blEmployMultilanguage) {
            $sValue = $this->getLongDescription()->getRawValue();
            if ($sValue !== null) {
                $oArtExt = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
                $oArtExt->init('oxartextends');
                $oArtExt->setLanguage((int) $this->getLanguage());
                if (!$oArtExt->load($this->getId())) {
                    $oArtExt->setId($this->getId());
                }
                $oArtExt->oxartextends__oxlongdesc = new \OxidEsales\Eshop\Core\Field($sValue, \OxidEsales\Eshop\Core\Field::T_RAW);
                $oArtExt->save();
            }
        } else {
            $oArtExt = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
            $oArtExt->setEnableMultilang(false);
            $oArtExt->init('oxartextends');
            $aObjFields = $oArtExt->getAllFields(true);
            if (!$oArtExt->load($this->getId())) {
                $oArtExt->setId($this->getId());
            }

            foreach ($aObjFields as $sKey => $sValue) {
                if (preg_match('/^oxlongdesc(_(\d{1,2}))?$/', $sKey)) {
                    $sField = $this->getFieldLongName($sKey);

                    if (isset($this->$sField)) {
                        $sLongDesc = null;
                        if ($this->$sField instanceof \OxidEsales\Eshop\Core\Field) {
                            $sLongDesc = $this->$sField->getRawValue();
                        } elseif (is_object($this->$sField)) {
                            $sLongDesc = $this->$sField->value;
                        }
                        if (isset($sLongDesc)) {
                            $sAEField = $oArtExt->getFieldLongName($sKey);
                            $oArtExt->$sAEField = new \OxidEsales\Eshop\Core\Field($sLongDesc, \OxidEsales\Eshop\Core\Field::T_RAW);
                        }
                    }
                }
            }
            $oArtExt->save();
        }
    }

    /**
     * Removes object data fields (oxarticles__oxtimestamp, oxarticles__oxparentid, oxarticles__oxinsert).
     */
    protected function skipSaveFields()
    {
        $this->_aSkipSaveFields = [];

        $this->_aSkipSaveFields[] = 'oxtimestamp';
        // $this->_aSkipSaveFields[] = 'oxlongdesc';
        $this->_aSkipSaveFields[] = 'oxinsert';
        $this->addSkippedSaveFieldsForMapping();

        if (!$this->_blAllowEmptyParentId && (!isset($this->oxarticles__oxparentid->value) || $this->oxarticles__oxparentid->value == '')) {
            $this->_aSkipSaveFields[] = 'oxparentid';
        }
    }

    /**
     * Merges two discount arrays. If there are two the same
     * discounts, discount values will be added.
     *
     * @param array $aDiscounts     Discount array
     * @param array $aItemDiscounts Discount array
     *
     * @return array $aDiscounts
     */
    protected function mergeDiscounts($aDiscounts, $aItemDiscounts)
    {
        foreach ($aItemDiscounts as $sKey => $oDiscount) {
            // add prices of the same discounts
            if (array_key_exists($sKey, $aDiscounts)) {
                $aDiscounts[$sKey]->dDiscount += $oDiscount->dDiscount;
            } else {
                $aDiscounts[$sKey] = $oDiscount;
            }
        }

        return $aDiscounts;
    }

    /**
     * get user Group A, B or C price, returns db price if user is not in groups
     *
     * @return double
     */
    protected function getGroupPrice()
    {
        $sPriceSufix = $this->getUserPriceSufix();
        $sVarName = "oxarticles__oxprice{$sPriceSufix}";
        $dPrice = $this->$sVarName->value;

        // #1437/1436C - added config option, and check for zero A,B,C price values
        if (Registry::getConfig()->getConfigParam('blOverrideZeroABCPrices') && (double) $dPrice == 0) {
            $dPrice = $this->oxarticles__oxprice->value;
        }

        return $dPrice;
    }

    /**
     * Modifies article price depending on given amount.
     * Takes data from oxprice2article table.
     *
     * @param int $amount Basket amount
     *
     * @return double
     */
    protected function getAmountPrice($amount = 1)
    {
        startProfile("_getAmountPrice");

        $dPrice = $this->getGroupPrice();
        $oAmtPrices = $this->buildAmountPriceList();
        foreach ($oAmtPrices as $oAmPrice) {
            if (
                $oAmPrice->oxprice2article__oxamount->value <= $amount
                && $amount <= $oAmPrice->oxprice2article__oxamountto->value
                && $dPrice > $oAmPrice->oxprice2article__oxaddabs->value
            ) {
                $dPrice = $oAmPrice->oxprice2article__oxaddabs->value;
            }
        }

        stopProfile("_getAmountPrice");

        return $dPrice;
    }

    /**
     * Modifies article price according to selected select list value
     *
     * @param double $dPrice      Modifiable price
     * @param array  $aChosenList Selection list array
     *
     * @return double
     */
    protected function modifySelectListPrice($dPrice, $aChosenList = null)
    {
        $myConfig = Registry::getConfig();
        // #690
        if ($myConfig->getConfigParam('bl_perfLoadSelectLists') && $myConfig->getConfigParam('bl_perfUseSelectlistPrice')) {
            $aSelLists = $this->getSelectLists();

            foreach ($aSelLists as $key => $aSel) {
                if (isset($aChosenList[$key]) && isset($aSel[$aChosenList[$key]])) {
                    $oSel = $aSel[$aChosenList[$key]];
                    if ($oSel->priceUnit == 'abs') {
                        $dPrice += $oSel->price;
                    } elseif ($oSel->priceUnit == '%') {
                        $dPrice += \OxidEsales\Eshop\Core\Price::percent($dPrice, $oSel->price);
                    }
                }
            }
        }

        return $dPrice;
    }

    /**
     * Fills amount price list object and sets amount price for article object
     *
     * @param array $aAmPriceList Amount price list
     *
     * @return array
     */
    protected function fillAmountPriceList($aAmPriceList)
    {
        $oLang = Registry::getLang();

        // trying to find lowest price value
        foreach ($aAmPriceList as $sId => $oItem) {
            /** @var \OxidEsales\Eshop\Core\Price $oItemPrice */
            $oItemPrice = $this->getPriceObject();
            if ($oItem->oxprice2article__oxaddabs->value) {
                $dBasePrice = $oItem->oxprice2article__oxaddabs->value;
                $dBasePrice = $this->prepareModifiedPrice($dBasePrice);

                $oItemPrice->setPrice($dBasePrice);
                $this->calculatePrice($oItemPrice);
            } else {
                $dBasePrice = $this->getGroupPrice();

                $dBasePrice = $this->prepareModifiedPrice($dBasePrice);

                $oItemPrice->setPrice($dBasePrice);
                $oItemPrice->subtractPercent($oItem->oxprice2article__oxaddperc->value);
            }

            $aAmPriceList[$sId]->fbrutprice = $oLang->formatCurrency($oItemPrice->getBruttoPrice());
            $aAmPriceList[$sId]->fnetprice = $oLang->formatCurrency($oItemPrice->getNettoPrice());

            if ($quantity = $this->getUnitQuantity()) {
                $aAmPriceList[$sId]->fbrutamountprice = $oLang->formatCurrency($oItemPrice->getBruttoPrice() / $quantity);
                $aAmPriceList[$sId]->fnetamountprice = $oLang->formatCurrency($oItemPrice->getNettoPrice() / $quantity);
            }
        }

        return $aAmPriceList;
    }

    /**
     * Collects and returns active/all variant ids of article.
     *
     * @param bool $blActiveVariants Parameter to load only active variants.
     *
     * @return array
     */
    public function getVariantIds($blActiveVariants = true)
    {
        $aSelect = [];
        $sId = $this->getId();
        if ($sId) {
            $sActiveSqlSnippet = "";
            if ($blActiveVariants) {
                $sActiveSqlSnippet = " and " . $this->getSqlActiveSnippet(true);
            }
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
            $sQ = "select oxid from " . $this->getViewName(true) . "
                where oxparentid = :oxparentid" . $sActiveSqlSnippet . " order by oxsort";
            $oRs = $oDb->select($sQ, [
                ':oxparentid' => $sId
            ]);
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    $aSelect[] = reset($oRs->fields);
                    $oRs->fetchRow();
                }
            }
        }

        return $aSelect;
    }

    /**
     * retrieve article VAT (cached)
     *
     * @return double
     */
    public function getArticleVat()
    {
        if (!isset($this->_dArticleVat)) {
            $this->_dArticleVat = Registry::get(\OxidEsales\Eshop\Application\Model\VatSelector::class)->getArticleVat($this);
        }

        return $this->_dArticleVat;
    }

    public function hasProductValidTimeRange(): bool
    {
        return !Registry::getUtilsDate()->isEmptyDate($this->oxarticles__oxactivefrom->value)
            || !Registry::getUtilsDate()->isEmptyDate($this->oxarticles__oxactiveto->value);
    }

    public function isProductAlwaysActive(): bool
    {
        return !empty($this->oxarticles__oxactive->value);
    }

    /**
     * Applies VAT to article
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice Price object
     * @param double                       $dVat   VAT percent
     */
    protected function applyVAT(\OxidEsales\Eshop\Core\Price $oPrice, $dVat)
    {
        startProfile(__FUNCTION__);
        $oPrice->setVAT($dVat);
        /** @var \OxidEsales\Eshop\Application\Model\VatSelector $oVatSelector */
        $oVatSelector = Registry::get(\OxidEsales\Eshop\Application\Model\VatSelector::class);
        if (($dVat = $oVatSelector->getArticleUserVat($this)) !== false) {
            $oPrice->setUserVat($dVat);
        }
        stopProfile(__FUNCTION__);
    }

    /**
     * Applies currency factor
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice Price object
     * @param object                       $oCur   Currency object
     */
    protected function applyCurrency(\OxidEsales\Eshop\Core\Price $oPrice, $oCur = null)
    {
        if (!$oCur) {
            $oCur = Registry::getConfig()->getActShopCurrencyObject();
        }

        $oPrice->multiply($oCur->rate);
    }

    /**
     * gets attribs string
     *
     * @param string $sAttributeSql Attribute selection snippet
     * @param int    $iCnt          The number of selected attributes
     */
    protected function getAttribsString(&$sAttributeSql, &$iCnt)
    {
        // we do not use lists here as we don't need this overhead right now
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sSelect = 'select oxattrid from oxobject2attribute
            where oxobject2attribute.oxobjectid = :oxobjectid';
        if ($this->getParentId()) {
            $sSelect .= ' OR oxobject2attribute.oxobjectid = :oxparentid';
        }
        $sAttributeSql = '';
        $aAttributeIds = $oDb->getCol($sSelect, [
            ':oxobjectid' => $this->getId(),
            ':oxparentid' => $this->getParentId()
        ]);
        if (is_array($aAttributeIds) && count($aAttributeIds)) {
            $aAttributeIds = array_unique($aAttributeIds);
            $iCnt = count($aAttributeIds);
            $sAttributeSql .= 't1.oxattrid IN ( ' . implode(',', $oDb->quoteArray($aAttributeIds)) . ') ';
        }
    }

    /**
     * Gets similar list.
     *
     * @param string $sAttributeSql Attribute selection snippet
     * @param int    $iCnt          Similar list article count
     *
     * @return array
     */
    protected function getSimList($sAttributeSql, $iCnt)
    {
        // #523A
        $iAttrPercent = Registry::getConfig()->getConfigParam('iAttributesPercent') / 100;
        // 70% same attributes
        if (!$iAttrPercent || $iAttrPercent < 0 || $iAttrPercent > 1) {
            $iAttrPercent = 0.70;
        }
        // #1137V iAttributesPercent = 100 doesn't work
        $iHitMin = ceil($iCnt * $iAttrPercent);

        $aExcludeIds = [];
        $aExcludeIds[] = $this->getId();
        if ($this->getParentId()) {
            $aExcludeIds[] = $this->getParentId();
        }

        // we do not use lists here as we don't need this overhead right now
        $sSelect = "select oxobjectid from oxobject2attribute as t1 where
                    ( $sAttributeSql )
                    and t1.oxobjectid NOT IN (" . implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aExcludeIds)) . ")
                    group by t1.oxobjectid having count(*) >= :minhit LIMIT 0, 20";

        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getCol($sSelect, [
            ':minhit' => $iHitMin
        ]);
    }

    /**
     * Generates search string for similar list.
     *
     * @param string $sArticleTable Article table name
     * @param array  $aList         A list of original articles
     *
     * @return string
     */
    protected function generateSimListSearchStr($sArticleTable, $aList)
    {
        $sFieldList = $this->getSelectFields();
        $aList = array_slice($aList, 0, Registry::getConfig()->getConfigParam('iNrofSimilarArticles'));

        $sSearch = "select $sFieldList from $sArticleTable where " . $this->getSqlActiveSnippet() . "  and $sArticleTable.oxissearch = 1 and $sArticleTable.oxid in ( ";

        $sSearch .= implode(',', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aList)) . ')';

        // #524A -- randomizing articles in attribute list
        $sSearch .= ' order by rand() ';

        return $sSearch;
    }

    /**
     * Generates SearchString for getCategory()
     *
     * @param string $sOXID            Article ID
     * @param bool   $blSearchPriceCat Whether to perform the search within price categories
     *
     * @return string
     */
    protected function generateSearchStr($sOXID, $blSearchPriceCat = false)
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCatView = $tableViewNameGenerator->getViewName('oxcategories', $this->getLanguage());
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');

        // we do not use lists here as we don't need this overhead right now
        if (!$blSearchPriceCat) {
            return "select {$sCatView}.* from {$sO2CView} as oxobject2category left join {$sCatView} on
                         {$sCatView}.oxid = oxobject2category.oxcatnid
                         where oxobject2category.oxobjectid=" . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sOXID) . " and {$sCatView}.oxid is not null ";
        }
        return "select {$sCatView}.* from {$sCatView} where
                      '{$this->oxarticles__oxprice->value}' >= {$sCatView}.oxpricefrom and
                      '{$this->oxarticles__oxprice->value}' <= {$sCatView}.oxpriceto ";
    }

    /**
     * Generates SQL select string for getCustomerAlsoBoughtThisProduct
     *
     * @return string
     */
    protected function generateSearchStrForCustomerBought()
    {
        $sArtTable = $this->getViewName();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sOrderArtTable = $tableViewNameGenerator->getViewName('oxorderarticles');

        // fetching filter params
        $sIn = " '{$this->oxarticles__oxid->value}' ";
        if ($this->oxarticles__oxparentid->value) {
            // adding article parent
            $sIn .= ", '{$this->oxarticles__oxparentid->value}' ";
            $sParentIdForVariants = $this->oxarticles__oxparentid->value;
        } else {
            $sParentIdForVariants = $this->getId();
        }

        // adding variants
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

        $params = [
            ':oxparentid' => $sParentIdForVariants,
            ':oxid' => $this->oxarticles__oxid->value
        ];
        $oRs = $oDb->select("select oxid from {$sArtTable}
            where oxparentid = :oxparentid
            and oxid != :oxid ", $params);
        if ($oRs != false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $sIn .= ", " . $oDb->quote(current($oRs->fields)) . " ";
                $oRs->fetchRow();
            }
        }

        $iLimit = (int) Registry::getConfig()->getConfigParam('iNrofCustomerWhoArticles');
        $iLimit = $iLimit ? ($iLimit * 10) : 50;

        // building sql (optimized)
        return "select distinct {$sArtTable}.* from (
                   select d.oxorderid as suborderid from {$sOrderArtTable} as d use index ( oxartid ) where d.oxartid in ( {$sIn} ) limit {$iLimit}
               ) as suborder
               left join {$sOrderArtTable} force index ( oxorderid ) on suborder.suborderid = {$sOrderArtTable}.oxorderid
               left join {$sArtTable} on {$sArtTable}.oxid = {$sOrderArtTable}.oxartid
               where {$sArtTable}.oxid not in ( {$sIn} )
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' ) and " . $this->getSqlActiveSnippet();

        /* non optimized, but could be used if index forcing is not supported
        // building sql
        $sQ = "select distinct {$sArtTable}.* from {$sOrderArtTable}, {$sArtTable} where {$sOrderArtTable}.oxorderid in (
                   select {$sOrderArtTable}.oxorderid from {$sOrderArtTable} where {$sOrderArtTable}.oxartid in ( {$sIn} )
               ) and {$sArtTable}.oxid = {$sOrderArtTable}.oxartid and {$sArtTable}.oxid not in ( {$sIn} )
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' )
               and ".$this->getSqlActiveSnippet();
        */
    }

    /**
     * Generates select string for isAssignedToCategory()
     *
     * @param string $sOXID        Article ID
     * @param string $sCatId       Category ID
     * @param bool   $dPriceFromTo Article price for price categories
     *
     * @return string
     */
    protected function generateSelectCatStr($sOXID, $sCatId, $dPriceFromTo = false)
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCategoryView = $tableViewNameGenerator->getViewName('oxcategories');
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sOXID = $oDb->quote($sOXID);
        $sCatId = $oDb->quote($sCatId);

        if (!$dPriceFromTo) {
            $sSelect = "select oxobject2category.oxcatnid from $sO2CView as oxobject2category ";
            $sSelect .= "left join $sCategoryView as oxcategories on oxcategories.oxid = oxobject2category.oxcatnid ";
            $sSelect .= "where oxobject2category.oxcatnid=$sCatId and oxobject2category.oxobjectid=$sOXID ";
            $sSelect .= "and oxcategories.oxactive = 1 order by oxobject2category.oxtime ";
        } else {
            $dPriceFromTo = $oDb->quote($dPriceFromTo);
            $sSelect = "select oxcategories.oxid from $sCategoryView as oxcategories where ";
            $sSelect .= "oxcategories.oxid=$sCatId and $dPriceFromTo >= oxcategories.oxpricefrom and ";
            $sSelect .= "$dPriceFromTo <= oxcategories.oxpriceto ";
        }

        return $sSelect;
    }

    /**
     * Collecting assigned to article amount-price list.
     *
     * @return \OxidEsales\Eshop\Application\Model\AmountPriceList
     */
    protected function buildAmountPriceList()
    {
        if ($this->getAmountPriceList() === null) {
            /** @var \OxidEsales\Eshop\Application\Model\AmountPriceList $oAmPriceList */
            $oAmPriceList = oxNew(\OxidEsales\Eshop\Application\Model\AmountPriceList::class);
            $this->setAmountPriceList($oAmPriceList);

            if (!$this->skipDiscounts()) {
                //collecting assigned to article amount-price list
                $oAmPriceList->load($this);

                // prepare abs prices if currently having percentages
                $oBasePrice = $this->getGroupPrice();
                foreach ($oAmPriceList as $oAmPrice) {
                    if ($oAmPrice->oxprice2article__oxaddperc->value) {
                        $oAmPrice->oxprice2article__oxaddabs = new \OxidEsales\Eshop\Core\Field(
                            \OxidEsales\Eshop\Core\Price::percent($oBasePrice, 100 - $oAmPrice->oxprice2article__oxaddperc->value),
                            \OxidEsales\Eshop\Core\Field::T_RAW
                        );
                    }
                }
            }

            $this->setAmountPriceList($oAmPriceList);
        }

        return $this->_oAmountPriceList;
    }

    /**
     * Detects if field is empty.
     *
     * @param string $sFieldName Field name
     *
     * @return bool
     */
    protected function isFieldEmpty($sFieldName)
    {
        $mValue = $this->$sFieldName->value;

        if (is_null($mValue)) {
            return true;
        }

        if ($mValue === '') {
            return true;
        }

        // certain fields with zero value treat as empty
        $aZeroValueFields = ['oxarticles__oxprice', 'oxarticles__oxvat', 'oxarticles__oxunitquantity'];

        if (!$mValue && in_array($sFieldName, $aZeroValueFields)) {
            return true;
        }


        if (!strcmp($mValue, '0000-00-00 00:00:00') || !strcmp($mValue, '0000-00-00')) {
            return true;
        }

        $sFieldName = strtolower($sFieldName);

        if (
            $sFieldName == 'oxarticles__oxicon' && (strpos($mValue, "nopic_ico.jpg") !== false || strpos(
                $mValue,
                "nopic.jpg"
            ) !== false)
        ) {
            return true;
        }

        if (
            strpos($mValue, "nopic.jpg") !== false && ($sFieldName == 'oxarticles__oxthumb' || substr(
                $sFieldName,
                0,
                17
            ) == 'oxarticles__oxpic' || substr($sFieldName, 0, 18) == 'oxarticles__oxzoom')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Assigns parent field values to article
     *
     * @param string $sFieldName field name
     *
     * @return null
     */
    protected function assignParentFieldValue($sFieldName)
    {
        if (!($oParentArticle = $this->getParentArticle())) {
            return;
        }

        $sCopyFieldName = $this->getFieldLongName($sFieldName);

        // assigning only these which parent article has
        if ($oParentArticle->$sCopyFieldName != null) {
            // only overwrite database values
            if (substr($sCopyFieldName, 0, 12) != 'oxarticles__') {
                return;
            }

            //do not copy certain fields
            if (in_array($sCopyFieldName, $this->_aNonCopyParentFields)) {
                return;
            }

            //skip picture parent value assignment in case master image is set for variant
            if ($this->isFieldEmpty($sCopyFieldName) && $this->isImageField($sCopyFieldName) && $this->hasMasterImage(1)) {
                return;
            }

            //COPY THE VALUE
            if ($this->isFieldEmpty($sCopyFieldName)) {
                $this->$sCopyFieldName = clone $oParentArticle->$sCopyFieldName;
            }
        }
    }

    /**
     * Detects if field is an image field by field name
     *
     * @param string $sFieldName Field name
     *
     * @return bool
     */
    protected function isImageField($sFieldName)
    {
        return (stristr($sFieldName, '_oxthumb') || stristr($sFieldName, '_oxicon') || stristr(
            $sFieldName,
            '_oxzoom'
        ) || stristr($sFieldName, '_oxpic'));
    }

    /**
     * Assigns parent field values to article
     */
    protected function assignParentFieldValues()
    {
        startProfile('articleAssignParentInternal');
        if ($this->oxarticles__oxparentid->value) {
            // yes, we are in fact a variant
            if (!$this->isAdmin() || ($this->_blLoadParentData && $this->isAdmin())) {
                foreach ($this->_aFieldNames as $sFieldName => $sVal) {
                    $this->assignParentFieldValue($sFieldName);
                }
            }
        }
        stopProfile('articleAssignParentInternal');
    }

    /**
     * if we have variants then depending on config option the parent may be non buyable
     */
    protected function assignNotBuyableParent()
    {
        if (
            !Registry::getConfig()->getConfigParam('blVariantParentBuyable') &&
            ($this->_blHasVariants || $this->oxarticles__oxvarstock->value || $this->oxarticles__oxvarcount->value)
        ) {
            $this->_blNotBuyableParent = true;
        }
    }

    /**
     * Assigns stock status to article
     */
    protected function assignStock()
    {
        $myConfig = Registry::getConfig();
        // -----------------------------------
        // stock
        // -----------------------------------

        // #1125 A. must round (using floor()) value taken from database and cast to int
        if (!$myConfig->getConfigParam('blAllowUnevenAmounts') && !$this->isAdmin()) {
            $this->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field((int) floor($this->oxarticles__oxstock->value));
        }
        //GREEN light
        $this->_iStockStatus = 0;

        // if we have flag /*1 or*/ 4 - we show always green light
        if (
            $myConfig->getConfigParam('blUseStock') && /*$this->oxarticles__oxstockflag->value != 1 && */
            $this->oxarticles__oxstockflag->value != 4
        ) {
            //ORANGE light
            $stock = $this->getAvailableStock();

            if ($stock > 0 && $this->isLowStock()) {
                $this->_iStockStatus = 1;
            }

            //RED light
            if ($stock <= 0) {
                $this->_iStockStatus = -1;
            }
        }


        // stock
        if ($myConfig->getConfigParam('blUseStock') && ($this->oxarticles__oxstockflag->value == 3 || $this->oxarticles__oxstockflag->value == 2)) {
            $iOnStock = $this->oxarticles__oxstock->value;
            if (Registry::getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
                $session = Registry::getSession();
                if ($reservations = $session->getBasketReservations()) {
                    $iOnStock += $reservations->getReservedAmount($this->getId());
                }
            }
            if ($iOnStock <= 0) {
                $this->setBuyableState(false);
            }
        }

        //exceptional handling for variant parent stock:
        if ($this->_blNotBuyable && $this->oxarticles__oxvarstock->value) {
            $this->setBuyableState(true);
            //but then at least setting notBuaybleParent to true
            $this->_blNotBuyableParent = true;
        }

        //special treatment for lists when blVariantParentBuyable config option is set to false
        //then we just hide "to basket" button.
        //if variants are not loaded in the list and this article has variants and parent is not buyable then this article is not buyable
        if (!$myConfig->getConfigParam('blVariantParentBuyable') && !$myConfig->getConfigParam('blLoadVariants') && $this->oxarticles__oxvarstock->value) {
            $this->setBuyableState(false);
        }

        //setting to non buyable when variant list is empty (for example not loaded or inactive) and $this is non buyable parent
        if (!$this->_blNotBuyable && $this->_blNotBuyableParent && $this->oxarticles__oxvarcount->value == 0) {
            $this->setBuyableState(false);
        }
    }

    /**
     * assigns dynimagedir to article
     */
    protected function assignDynImageDir()
    {
        $myConfig = Registry::getConfig();

        $sThisShop = $this->oxarticles__oxshopid->value;

        $this->_sDynImageDir = $myConfig->getPictureUrl(null, false);
        $this->dabsimagedir = $myConfig->getPictureDir(false); //$sThisShop
        $this->nossl_dimagedir = $myConfig->getPictureUrl(null, false, false, null, $sThisShop); //$sThisShop
        $this->ssl_dimagedir = $myConfig->getPictureUrl(null, false, true, null, $sThisShop); //$sThisShop
    }

    /**
     * Adds a flag if article is on comparisonlist.
     */
    protected function assignComparisonListFlag()
    {
        // #657 add a flag if article is on comparisonlist

        $aItems = Registry::getSession()->getVariable('aFiltcompproducts');
        if (isset($aItems[$this->getId()])) {
            $this->_blIsOnComparisonList = true;
        }
    }

    /**
     * Sets article creation date
     * (\OxidEsales\Eshop\Application\Model\Article::oxarticles__oxinsert). Then executes parent method
     * parent::_insert() and returns insertion status.
     *
     * @return bool
     */
    protected function insert()
    {
        // set oxinsert
        $sNow = date('Y-m-d H:i:s', Registry::getUtilsDate()->getTime());
        $this->oxarticles__oxinsert = new \OxidEsales\Eshop\Core\Field($sNow);
        if (!is_object($this->oxarticles__oxsubclass) || $this->oxarticles__oxsubclass->value == '') {
            $this->oxarticles__oxsubclass = new \OxidEsales\Eshop\Core\Field('oxarticle');
        }

        return parent::insert();
    }

    /**
     * Executes \OxidEsales\Eshop\Application\Model\Article::_skipSaveFields() and updates article information
     *
     * @return bool
     */
    protected function update()
    {
        $this->setUpdateSeo(true);
        $this->setUpdateSeoOnFieldChange('oxtitle');

        $this->skipSaveFields();

        return parent::update();
    }

    /**
     * Deletes records in database
     *
     * @param string $articleId Article ID
     *
     * @return int
     */
    protected function deleteRecords($articleId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        //remove other records
        $sDelete = 'delete from oxobject2article where oxarticlenid = :articleId or oxobjectid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxobject2attribute where oxobjectid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxobject2category where oxobjectid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxobject2selectlist where oxobjectid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxprice2article where oxartid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxreviews where oxtype="oxarticle" and oxobjectid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxratings where oxobjectid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxaccessoire2article where oxobjectid = :articleId or oxarticlenid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        //#1508C - deleting oxobject2delivery entries added
        $sDelete = 'delete from oxobject2delivery where oxobjectid = :articleId and oxtype=\'oxarticles\' ';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxartextends where oxid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        //delete the record
        foreach ($this->getLanguageSetTables("oxartextends") as $sSetTbl) {
            $oDb->execute("delete from $sSetTbl where oxid = :articleId", [
                ':articleId' => $articleId
            ]);
        }

        $sDelete = 'delete from oxactions2article where oxartid = :articleId';
        $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);

        $sDelete = 'delete from oxobject2list where oxobjectid = :articleId';

        return $oDb->execute($sDelete, [
            ':articleId' => $articleId
        ]);
    }

    /**
     * Deletes variant records
     *
     * @param string $sOXID Article ID
     */
    protected function deleteVariantRecords($sOXID)
    {
        if ($sOXID) {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            //collect variants to remove recursively
            $query = 'select oxid from ' . $this->getViewName() . ' where oxparentid = :oxparentid';
            $rs = $database->select($query, [
                ':oxparentid' => $sOXID
            ]);
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF) {
                    $oArticle->setId($rs->fields[0]);
                    $oArticle->delete();
                    $rs->fetchRow();
                }
            }
        }
    }

    /**
     * Delete pics
     */
    protected function deletePics()
    {
        $myConfig = Registry::getConfig();
        $oPictureHandler = Registry::getPictureHandler();

        //deleting custom main icon
        $oPictureHandler->deleteMainIcon($this);

        //deleting custom thumbnail
        $oPictureHandler->deleteThumbnail($this);

        // deleting master image and all generated images
        $iPicCount = $myConfig->getConfigParam('iPicCount');
        for ($i = 1; $i <= $iPicCount; $i++) {
            $oPictureHandler->deleteArticleMasterPicture($this, $i);
        }
    }

    /**
     * Resets category and vendor counts. This method is supposed to be called on article change trigger.
     *
     * @param string $sOxid           object to reset id ID
     * @param string $sVendorId       Vendor ID
     * @param string $sManufacturerId Manufacturer ID
     */
    protected function onChangeResetCounts($sOxid, $sVendorId = null, $sManufacturerId = null)
    {
        $myUtilsCount = Registry::getUtilsCount();

        if ($sVendorId) {
            $myUtilsCount->resetVendorArticleCount($sVendorId);
        }

        if ($sManufacturerId) {
            $myUtilsCount->resetManufacturerArticleCount($sManufacturerId);
        }

        $aCategoryIds = $this->getCategoryIds();
        //also reseting category counts
        foreach ($aCategoryIds as $sCatId) {
            $myUtilsCount->resetCatArticleCount($sCatId);
        }
    }

    /**
     * Updates article stock. This method is supposed to be called on article change trigger.
     *
     * @param string $parentId product parent id
     */
    protected function onChangeUpdateStock($parentId)
    {
        if ($parentId) {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $query = 'SELECT oxstock, oxvendorid, oxmanufacturerid FROM oxarticles WHERE oxid = :oxid';
            $rs = $database->select($query, [
                ':oxid' => $parentId
            ]);
            $oldStock = $rs->fields[0];
            $vendorId = $rs->fields[1];
            $manufacturerId = $rs->fields[2];

            $query = 'SELECT SUM(oxstock) FROM ' . $this->getViewName(true) . '
                WHERE oxparentid = :oxparentid
                AND ' . $this->getSqlActiveSnippet(true) . '
                AND oxstock > 0 ';
            $stock = (float) $database->getOne($query, [
                ':oxparentid' => $parentId
            ]);

            $query = 'UPDATE oxarticles SET oxvarstock = :oxvarstock WHERE oxid = :oxid';
            $database->execute($query, [
                ':oxvarstock' => $stock,
                ':oxid' => $parentId
            ]);

            //now lets update category counts
            //first detect stock status change for this article (to or from 0)
            if ($stock < 0) {
                $stock = 0;
            }
            if ($oldStock < 0) {
                $oldStock = 0;
            }
            if ($this->oxarticles__oxstockflag->value == 2 && $oldStock xor $stock) {
                //means the stock status could be changed (oxstock turns from 0 to 1 or from 1 to 0)
                // so far we leave it like this but later we could move all count resets to one or two functions
                $this->onChangeResetCounts($parentId, $vendorId, $manufacturerId);
            }
        }
    }

    /**
     * Resets article count cache when stock value is zero and article goes offline.
     *
     * @param string $sOxid product id
     */
    protected function onChangeStockResetCount($sOxid)
    {
        $myConfig = Registry::getConfig();

        if (
            $myConfig->getConfigParam('blUseStock') && $this->oxarticles__oxstockflag->value == 2 &&
            ($this->oxarticles__oxstock->value + $this->oxarticles__oxvarstock->value) <= 0
        ) {
            $this->onChangeResetCounts(
                $sOxid,
                $this->oxarticles__oxvendorid->value,
                $this->oxarticles__oxmanufacturerid->value
            );
        }
    }

    /**
     * Updates variant count. This method is supposed to be called on article change trigger.
     *
     * @param string $parentId Parent ID
     */
    protected function onChangeUpdateVarCount($parentId)
    {
        if ($parentId) {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            $query = "SELECT COUNT(*) AS varcount FROM oxarticles WHERE oxparentid = :oxparentid";
            $varCount = (int) $database->getOne($query, [
                ':oxparentid' => $parentId
            ]);

            $query = "UPDATE oxarticles SET oxvarcount = :oxvarcount WHERE oxid = :oxid";
            $database->execute($query, [
                ':oxvarcount' => $varCount,
                ':oxid' => $parentId
            ]);
        }
    }

    /**
     * Updates variant min price. This method is supposed to be called on article change trigger.
     *
     * @param string $sParentId Parent ID
     */
    protected function setVarMinMaxPrice($sParentId)
    {
        if ($sParentId) {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
            $sQ = '
                SELECT
                    MIN( IF( `oxarticles`.`oxprice` > 0, `oxarticles`.`oxprice`, `p`.`oxprice` ) ) AS `varminprice`,
                    MAX( IF( `oxarticles`.`oxprice` > 0, `oxarticles`.`oxprice`, `p`.`oxprice` ) ) AS `varmaxprice`
                FROM ' . $this->getViewName(true) . ' AS `oxarticles`
                    LEFT JOIN ' . $this->getViewName(true) . ' AS `p` ON ( `p`.`oxid` = `oxarticles`.`oxparentid` AND `p`.`oxprice` > 0 )
                WHERE ' . $this->getSqlActiveSnippet(true) . '
                    AND ( `oxarticles`.`oxparentid` = :oxparentid )';
            $aPrices = $database->getRow($sQ, [
                ':oxparentid' => $sParentId
            ]);
            if (isset($aPrices['varminprice'], $aPrices['varmaxprice'])) {
                $sQ = '
                    UPDATE `oxarticles`
                    SET
                        `oxvarminprice` = :oxvarminprice,
                        `oxvarmaxprice` = :oxvarmaxprice
                    WHERE
                        `oxid` = :oxid';
                $params = [
                    ':oxvarminprice' => $aPrices['varminprice'],
                    ':oxvarmaxprice' => $aPrices['varmaxprice'],
                    ':oxid' => $sParentId
                ];
            } else {
                $sQ = '
                    UPDATE `oxarticles`
                    SET
                        `oxvarminprice` = `oxprice`,
                        `oxvarmaxprice` = `oxprice`
                    WHERE
                        `oxid` = :oxid';
                $params = [':oxid' => $sParentId];
            }
            $database->execute($sQ, $params);
        }
    }

    /**
     * Checks if article has uploaded master image for selected picture
     *
     * @param int $iIndex master picture index
     *
     * @return bool
     */
    protected function hasMasterImage($iIndex)
    {
        $sPicName = basename($this->{"oxarticles__oxpic" . $iIndex}->value);

        if ($sPicName == "nopic.jpg" || $sPicName == "") {
            return false;
        }
        if (
            $this->isVariant() &&
            $this->getParentArticle() &&
            $this->getParentArticle()->{"oxarticles__oxpic" . $iIndex}->value == $this->{"oxarticles__oxpic" . $iIndex}->value
        ) {
            return false;
        }

        $sMasterPic = 'product/' . $iIndex . "/" . $sPicName;

        if (Registry::getConfig()->getMasterPicturePath($sMasterPic)) {
            return true;
        }

        return false;
    }


    /**
     * Checks and return true if price view mode is netto
     *
     * @return bool
     */
    protected function isPriceViewModeNetto()
    {
        $blResult = (bool) Registry::getConfig()->getConfigParam('blShowNetPrice');
        $oUser = $this->getArticleUser();
        if ($oUser) {
            $blResult = $oUser->isPriceViewModeNetto();
        }

        return $blResult;
    }


    /**
     * Depending on view mode prepare oxPrice object
     *
     * @param bool $blCalculationModeNetto - if calculation mode netto - true
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    protected function getPriceObject($blCalculationModeNetto = null)
    {
        /** @var \OxidEsales\Eshop\Core\Price $oPrice */
        $oPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);

        if ($blCalculationModeNetto === null) {
            $blCalculationModeNetto = $this->isPriceViewModeNetto();
        }

        if ($blCalculationModeNetto) {
            $oPrice->setNettoPriceMode();
        } else {
            $oPrice->setBruttoPriceMode();
        }

        return $oPrice;
    }

    /**
     * Depending on view mode prepare price for viewing
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice price object
     *
     * @return double
     */
    protected function getPriceForView($oPrice)
    {
        if ($this->isPriceViewModeNetto()) {
            $dPrice = $oPrice->getNettoPrice();
        } else {
            $dPrice = $oPrice->getBruttoPrice();
        }

        return $dPrice;
    }


    /**
     * Depending on view mode prepare price before calculation
     *
     * @param double $dPrice                 - price
     * @param double $dVat                   - VAT
     * @param bool   $blCalculationModeNetto - if calculation mode netto - true
     *
     * @return double
     */
    protected function preparePrice($dPrice, $dVat, $blCalculationModeNetto = null)
    {
        if ($blCalculationModeNetto === null) {
            $blCalculationModeNetto = $this->isPriceViewModeNetto();
        }

        $oCurrency = Registry::getConfig()->getActShopCurrencyObject();

        $blEnterNetPrice = Registry::getConfig()->getConfigParam('blEnterNetPrice');
        if ($blCalculationModeNetto && !$blEnterNetPrice) {
            $dPrice = round(\OxidEsales\Eshop\Core\Price::brutto2Netto($dPrice, $dVat), $oCurrency->decimal);
        } elseif (!$blCalculationModeNetto && $blEnterNetPrice) {
            $dPrice = round(\OxidEsales\Eshop\Core\Price::netto2Brutto($dPrice, $dVat), $oCurrency->decimal);
        }

        return $dPrice;
    }


    /**
     * Return price suffix
     *
     * @return null
     */
    protected function getUserPriceSufix()
    {
        $sPriceSuffix = '';
        $oUser = $this->getArticleUser();

        if ($oUser) {
            if ($oUser->inGroup('oxidpricea')) {
                $sPriceSuffix = 'a';
            } elseif ($oUser->inGroup('oxidpriceb')) {
                $sPriceSuffix = 'b';
            } elseif ($oUser->inGroup('oxidpricec')) {
                $sPriceSuffix = 'c';
            }
        }

        return $sPriceSuffix;
    }

    /**
     * Return prepared price
     *
     * @return null
     */
    protected function getRawPrice()
    {
        $sPriceSuffix = $this->getUserPriceSufix();
        if ($sPriceSuffix === '') {
            $dPrice = $this->oxarticles__oxprice->value;
        } else {
            if (Registry::getConfig()->getConfigParam('blOverrideZeroABCPrices')) {
                $dPrice = ($this->{'oxarticles__oxprice' . $sPriceSuffix}->value != 0) ? $this->{'oxarticles__oxprice' . $sPriceSuffix}->value : $this->oxarticles__oxprice->value;
            } else {
                $dPrice = $this->{'oxarticles__oxprice' . $sPriceSuffix}->value;
            }
        }

        return $dPrice;
    }

    /**
     * Return variant min price
     *
     * @return null
     */
    protected function getVarMinRawPrice()
    {
        if ($this->_dVarMinPrice === null) {
            $dPrice = $this->getShopVarMinPrice();

            if (is_null($dPrice)) {
                $sPriceSuffix = $this->getUserPriceSufix();
                if ($sPriceSuffix === '') {
                    $dPrice = $this->oxarticles__oxvarminprice->value;
                } else {
                    $sSql = 'SELECT ';
                    if (Registry::getConfig()->getConfigParam('blOverrideZeroABCPrices')) {
                        $sSql .= 'MIN( IF(`oxprice' . $sPriceSuffix . '` = 0, `oxprice`, `oxprice' . $sPriceSuffix . '`) ) AS `varminprice` ';
                    } else {
                        $sSql .= 'MIN(`oxprice' . $sPriceSuffix . '`) AS `varminprice` ';
                    }

                    $sSql .= ' FROM ' . $this->getViewName(true) . '
                    WHERE ' . $this->getSqlActiveSnippet(true) . '
                        AND ( `oxparentid` = :oxparentid )';

                    $dPrice = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sSql, [
                        ':oxparentid' => $this->getId()
                    ]);
                }
            }

            $this->_dVarMinPrice = $dPrice;
        }

        return $this->_dVarMinPrice;
    }

    /**
     * Return variant max price
     *
     * @return null
     */
    protected function getVarMaxPrice()
    {
        if ($this->_dVarMaxPrice === null) {
            $dPrice = $this->getShopVarMaxPrice();

            if (is_null($dPrice)) {
                $sPriceSuffix = $this->getUserPriceSufix();
                if ($sPriceSuffix === '') {
                    $dPrice = $this->oxarticles__oxvarmaxprice->value;
                } else {
                    $sSql = 'SELECT ';
                    if (Registry::getConfig()->getConfigParam('blOverrideZeroABCPrices')) {
                        $sSql .= 'MAX( IF(`oxprice' . $sPriceSuffix . '` = 0, `oxprice`, `oxprice' . $sPriceSuffix . '`) ) AS `varmaxprice` ';
                    } else {
                        $sSql .= 'MAX(`oxprice' . $sPriceSuffix . '`) AS `varmaxprice` ';
                    }

                    $sSql .= ' FROM ' . $this->getViewName(true) . '
                        WHERE ' . $this->getSqlActiveSnippet(true) . '
                            AND ( `oxparentid` = :oxparentid )';

                    $dPrice = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sSql, [
                        ':oxparentid' => $this->getId()
                    ]);
                }
            }

            $this->_dVarMaxPrice = $dPrice;
        }

        return $this->_dVarMaxPrice;
    }

    /**
     * Place to hook to return variant min price if it might be different,
     * for example for subshops.
     *
     * @return double|null
     */
    protected function getShopVarMinPrice()
    {
        return null;
    }

    /**
     * Place to hook to return variant max price if it might be different,
     * for example for subshops.
     *
     * @return double|null
     */
    protected function getShopVarMaxPrice()
    {
        return null;
    }

    /**
     * Get data from db
     *
     * @param string $articleId id
     *
     * @return array
     */
    protected function loadFromDb($articleId)
    {
        $sSelect = $this->buildSelectString([$this->getViewName() . ".oxid" => $articleId]);

        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getRow($sSelect);
    }


    /**
     * Place to hook and change amount if it should be calculated by different logic,
     * for example VPE.
     *
     * @param double $amount Amount
     */
    public function checkForVpe($amount)
    {
    }

    /**
     * Set parent field value to child - variants in DB
     *
     * @return bool
     */
    protected function updateParentDependFields()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        foreach ($this->getCopyParentFields() as $sField) {
            $sValue = isset($this->$sField->value) ? $this->$sField->value : 0;
            $sSqlSets[] = '`' . str_replace('oxarticles__', '', $sField) . '` = ' . $oDb->quote($sValue);
        }

        $sSql = "UPDATE `oxarticles` SET ";
        $sSql .= implode(', ', $sSqlSets) . '';
        $sSql .= " WHERE `oxparentid` = :oxparentid";

        return $oDb->execute($sSql, [':oxparentid' => $this->getId()]);
    }

    /**
     * Returns array of fields which should not changed in variants
     *
     * @return array
     */
    protected function getCopyParentFields()
    {
        return $this->_aCopyParentField;
    }

    /**
     * Set parent field value to child - variants
     */
    protected function assignParentDependFields()
    {
        $sParent = $this->getParentArticle();
        if ($sParent) {
            foreach ($this->getCopyParentFields() as $sField) {
                $this->$sField = new \OxidEsales\Eshop\Core\Field($sParent->$sField->value);
            }
        }
    }

    /**
     * Saves values of sorting fields on article load.
     */
    protected function saveSortingFieldValuesOnLoad()
    {
        $aSortingFields = Registry::getConfig()->getConfigParam('aSortCols');
        $aSortingFields = !empty($aSortingFields) ? (array) $aSortingFields : [];

        foreach ($aSortingFields as $sField) {
            $sFullField = $this->getFieldLongName($sField);
            $this->_aSortingFieldsOnLoad[$sFullField] = $this->$sFullField->value;
        }
    }

    /**
     * Forms query to load variants.
     *
     * @param bool                      $blRemoveNotOrderables
     * @param bool                      $forceCoreTableUsage
     * @param \OxidEsales\Eshop\Application\Model\Article|\OxidEsales\Eshop\Application\Model\SimpleVariant $baseObject
     * @param string                    $sArticleTable
     *
     * @return string
     */
    protected function getLoadVariantsQuery($blRemoveNotOrderables, $forceCoreTableUsage, $baseObject, $sArticleTable)
    {
        return "select " . $baseObject->getSelectFields($forceCoreTableUsage) . " from $sArticleTable where " .
               $this->getActiveCheckQuery($forceCoreTableUsage) .
               $this->getVariantsQuery($blRemoveNotOrderables, $forceCoreTableUsage) .
               " order by $sArticleTable.oxsort";
    }

    /**
     * Set needed parameters to article list object like language.
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $baseObject          article list template object.
     * @param bool|null                              $forceCoreTableUsage if true forces core table use, default is
     *                                                                    false [optional]
     */
    protected function updateVariantsBaseObject($baseObject, $forceCoreTableUsage = null)
    {
        $baseObject->setLanguage($this->getLanguage());
    }

    /**
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $oManufacturer
     */
    protected function updateManufacturerBeforeLoading($oManufacturer)
    {
        $oManufacturer->setReadOnly(true);
    }

    protected function addSqlActiveRangeSnippet($query, $tableName): string
    {
        $dateUtils = Registry::getUtilsDate();
        $secondsToRoundForQueryCache = $this->getSecondsToRoundForQueryCache();
        $dateNow = $dateUtils->getRoundedRequestDateDBFormatted($secondsToRoundForQueryCache);
        $defaultDBDate = $dateUtils->formatDBDate('-');

        $activeToCondition = "$tableName.oxactivefrom <= '$dateNow' AND " .
            "$tableName.oxactivefrom != '$defaultDBDate' AND " .
            "$tableName.oxactiveto = '$defaultDBDate'";
        $activeFromToCondition = "$tableName.oxactivefrom <= '$dateNow' AND $tableName.oxactiveto >= '$dateNow'";

        $query = $query ? " $query or " : '';

        return " ( $query (($activeToCondition) OR ($activeFromToCondition)) )";
    }

    private function getAvailableStock(): int
    {
        return (int) ($this->_blNotBuyableParent
            ? $this->oxarticles__oxvarstock->value
            : $this->oxarticles__oxstock->value);
    }

    private function isLowStock(): bool
    {
        return $this->getAvailableStock() <= $this->getLowStockThreshold();
    }

    private function getLowStockThreshold(): int
    {
        return (int) ($this->oxarticles__oxlowstockactive->value ?
            $this->oxarticles__oxremindamount->value :
            Registry::getConfig()->getConfigParam('sStockWarningLimit'));
    }
}
