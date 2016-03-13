<?php

namespace OxidEsales\Eshop\Application\Model\Article;

class ListArticle extends \oxI18n implements \oxIUrl
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
    protected $_blUseLazyLoading = false;

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
     * @var \oxPrice
     */
    protected $_oPrice = null;


    /**
     * cached article variant min price
     *
     * @var double | null
     */
    protected $_dVarMinPrice = null;

    /**
     * cached article variant max price
     *
     * @var double | null
     */
    protected $_dVarMaxPrice = null;

    /**
     * caches article vat
     *
     * @var double | null
     */
    protected $_dArticleVat = null;

    /**
     * Persistent Parameter.
     *
     * @var array
     */
    protected $_aPersistParam = null;

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
     * If set true, then this object is on comparison list
     *
     * @var bool
     */
    protected $_blIsOnComparisonList = false;

    /**
     * user object
     *
     * @var \oxUser
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
     * $_fPricePerUnit is calculated from oxArticle::oxarticles__oxunitquantity->value
     * and from oxArticle::oxarticles__oxuniname->value. If either one of these values is empty then $_fPricePerUnit is not calculated.
     * Example: In case when product price is 10 EUR and product quantity is 0.5 (liters) then $_fPricePerUnit would be 20,00
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
     */
    protected $_oAttributeList = null;

    /**
     * Indicates whether the price is "From" price
     *
     * @var bool
     */
    protected $_blIsRangePrice = null;

    /**
     * Array containing references to already loaded parent articles, in order for variant to skip parent data loading
     *
     * @var array
     */
    static protected $_aLoadedParents;

    /**
     * Cached select lists array
     *
     * @var array
     */
    static protected $_aSelList;

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
     * @var \oxAmountPriceList
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
    protected $_aStdUrls = array();

    /**
     * Seo article urls for languages
     *
     * @var array
     */
    protected $_aSeoUrls = array();

    /**
     * Additional parameters to seo urls
     *
     * @var array
     */
    protected $_aSeoAddParams = array();

    /**
     * Additional parameters to std urls
     *
     * @var array
     */
    protected $_aStdAddParams = array();

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
    protected $_aSortingFieldsOnLoad = array();

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
    protected static $_aArticleManufacturers = array();

    /**
     * Articles vendor ids cache
     *
     * @var array
     */
    protected static $_aArticleVendors = array();

    /**
     * Articles category ids cache
     *
     * @var array
     */
    protected static $_aArticleCats = array();

    /**
     * Do not copy certain parent fields to variant
     *
     * @var array
     */
    protected $_aNonCopyParentFields = array(
        'oxarticles__oxinsert',
        'oxarticles__oxtimestamp',
        'oxarticles__oxnid',
        'oxarticles__oxid',
        'oxarticles__oxparentid'
    );

    /**
     * Override certain parent fields to variant
     *
     * @var array
     */
    protected $_aCopyParentField = array(
        'oxarticles__oxnonmaterial',
        'oxarticles__oxfreeshipping',
        'oxarticles__oxisdownloadable',
        'oxarticles__oxshowcustomagreement'
    );

    /**
     * Product long description field
     *
     * @var \oxField
     */
    protected $_oLongDesc = null;

    /**
     * Variant selections array
     *
     * @see getVariantSelections()
     *
     * @var array
     */
    protected $_aVariantSelections = array();

    /**
     * Array of product selections
     *
     * @var array
     */
    protected static $_aSelections = array();

    /**
     * Category instance cache
     *
     * @var array
     */
    protected static $_aCategoryCache = null;

    /**
     * stores if are stored any amount price
     *
     * @var bool
     */
    protected static $_blHasAmountPrice = null;

    /**
     * stores downloadable file list
     *
     * @var array|\oxList of oxArticleFile
     */
    protected $_aArticleFiles = null;

    /**
     * If admin can edit any field.
     *
     * @var bool
     */
    protected $_blCanUpdateAnyField = null;

    /**
     * Constructor, sets shop ID for article (oxconfig::getShopId()),
     * initiates parent constructor (parent::oxI18n()).
     *
     * @param array $aParams The array of names and values of oxArticle instance properties to be set on object instantiation
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
            $this->_assignParentFieldValue($sName);
        }

        return $this->$sName;
    }

    /**
     * @param \oxAmountPriceList $amountPriceList
     */
    public function setAmountPriceList($amountPriceList)
    {
        $this->_oAmountPriceList = $amountPriceList;
    }

    /**
     * @return \oxAmountPriceList
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
        return $this->_isInList();
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
        $sTable = $this->getViewName($blForceCoreTable);

        // check if article is still active
        $sQ = " $sTable.oxactive = 1 ";

        $sQ .= " and $sTable.oxhidden = 0 ";

        // enabled time range check ?
        if ($this->getConfig()->getConfigParam('blUseTimeCheck')) {
            $sDate = date('Y-m-d H:i:s', \oxRegistry::get("oxUtilsDate")->getTime());
            $sQ = "( $sQ or ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) ) ";
        }

        return $sQ;
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
        $myConfig = $this->getConfig();
        $sTable = $this->getViewName($blForceCoreTable);

        $sQ = "";

        //do not check for variants
        if ($myConfig->getConfigParam('blUseStock')) {
            $sQ = " and ( $sTable.oxstockflag != 2 or ( $sTable.oxstock + $sTable.oxvarstock ) > 0  ) ";
            //V #M513: When Parent article is not purchasable, it's visibility should be displayed in shop only if any of Variants is available.
            if (!$myConfig->getConfigParam('blVariantParentBuyable')) {
                $sTimeCheckQ = '';
                if ($myConfig->getConfigParam('blUseTimeCheck')) {
                    $sDate = date('Y-m-d H:i:s', \oxRegistry::get("oxUtilsDate")->getTime());
                    $sTimeCheckQ = " or ( art.oxactivefrom < '$sDate' and art.oxactiveto > '$sDate' )";
                }
                $sQ = " $sQ and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and ( art.oxactive = 1 $sTimeCheckQ ) and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";
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
        if ($this->getConfig()->getConfigParam('blUseStock')) {
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
     * Returns SQL select string with checks if items are available
     *
     * @param bool $blForceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    public function getSqlActiveSnippet($blForceCoreTable = null)
    {
        $sQ = $this->_createSqlActiveSnippet($blForceCoreTable);

        return "( $sQ ) ";
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $forceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    protected function _createSqlActiveSnippet($forceCoreTable)
    {
        // check if article is still active
        $sQ = $this->getActiveCheckQuery($forceCoreTable);

        // stock and variants check
        $sQ .= $this->getStockCheckQuery($forceCoreTable);

        return $sQ;
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
     * Checks whether article is inluded in comparison list
     *
     * @return bool
     */
    public function isOnComparisonList()
    {
        return $this->_blIsOnComparisonList;
    }

    /**
     * Returns price per unit
     *
     * @return string
     */
    public function getUnitPrice()
    {
        // Performance
        if (!$this->getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        $oPrice = null;
        if ((double) $this->getUnitQuantity() && $this->oxarticles__oxunitname->value) {
            $oPrice = clone $this->getPrice();
            $oPrice->divide((double) $this->getUnitQuantity());
        }

        return $oPrice;
    }

    /**
     * Returns article min price of variants
     *
     * @return oxPrice
     */
    public function getVarMinPrice()
    {
        if (!$this->getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        $oPrice = null;
        $dPrice = $this->_calculateVarMinPrice();

        $oPrice = $this->_getPriceObject();
        $oPrice->setPrice($dPrice);

        $this->_calculatePrice($oPrice);

        return $oPrice;
    }

    /**
     * Calculates lowest price of available article variants.
     *
     * @return double
     */
    protected function _calculateVarMinPrice()
    {
        $dPrice = $this->_getVarMinPrice();

        return $this->_preparePrice($dPrice, $this->getArticleVat());
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

            if ($this->_hasAnyVariant()) {
                $dPrice = $this->_getPrice();
                $dMinPrice = $this->_getVarMinPrice();
                $dMaxPrice = $this->_getVarMaxPrice();

                if ($dMinPrice != $dMaxPrice) {
                    $this->setRangePrice();
                } elseif (!$this->isParentNotBuyable() && $dMinPrice != $dPrice) {
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

        $this->oxarticles__oxnid = $this->oxarticles__oxid;

        // check for simple article.
        if ($this->_blSkipAssign) {
            return;
        }

        $this->_assignParentFieldValues();
        $this->_assignNotBuyableParent();

        // assign only for a first load time
        if (!$this->isLoaded()) {
            $this->_setShopValues($this);
        }

        $this->_assignStock();
        $this->_assignDynImageDir();
        $this->_assignComparisonListFlag();

        stopProfile('articleAssign');
    }

    /**
     * @param $article
     */
    protected function _setShopValues($article)
    {
    }

    /**
     * Loads object data from DB (object data ID must be passed to method).
     * Converts dates (oxArticle::oxarticles__oxinsert)
     * to international format (oxUtils.php \oxRegistry::get("oxUtilsDate")->formatDBDate(...)).
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

        $aData = $this->_loadData($sOXID);

        if ($aData) {
            $this->assign($aData);

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
    protected function _loadData($articleId)
    {
        return $this->_loadFromDb($articleId);
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
     * Returns variants selections lists array
     *
     * @param array  $aFilterIds    ids of active selections [optional]
     * @param string $sActVariantId active variant id [optional]
     * @param int    $iLimit        limit variant lists count (if non zero, return limited number of multidimensional variant selections)
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
                $aVariantSelections = oxNew("oxVariantHandler")->buildVariantSelections($this->oxarticles__oxvarname->getRawValue(),
                    $oVariants, $aFilterIds, $sActVariantId, $iLimit);

                if (!empty($oVariants) && empty($aVariantSelections['rawselections'])) {
                    $aVariantSelections = false;
                }
            }
            $this->_aVariantSelections[$iLimit] = $aVariantSelections;
        }

        return $this->_aVariantSelections[$iLimit];
    }

    /**
     * Collects and returns article variants.
     * Note: Only active variants are returned by this method. If you need full variant list use oxArticle::getAdminVariants()
     *
     * @param bool $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of stock
     * @param bool $blForceCoreTable      if true forces core table use, default is false [optional]
     *
     * @return array
     */
    public function getVariants($blRemoveNotOrderables = true, $blForceCoreTable = null)
    {
        return $this->_loadVariantList($this->_isInList(), $blRemoveNotOrderables, $blForceCoreTable);
    }

    /**
     * Returns T price
     *
     * @return \oxPrice
     */
    public function getTPrice()
    {
        if (!$this->getConfig()->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        // return cached result, since oPrice is created ONLY in this function [or function of EQUAL level]
        if ($this->_oTPrice !== null) {
            return $this->_oTPrice;
        }

        $oPrice = $this->_getPriceObject();

        $dBasePrice = $this->oxarticles__oxtprice->value;
        $dBasePrice = $this->_preparePrice($dBasePrice, $this->getArticleVat());

        $oPrice->setPrice($dBasePrice);

        $this->_applyVat($oPrice, $this->getArticleVat());
        $this->_applyCurrency($oPrice);

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
        if (\oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories()) {
            $oDb = \oxDb::getDb();
            $sO2CView = getViewName('oxobject2category', $this->getLanguage());
            $sViewName = getViewName('oxcategories', $this->getLanguage());
            $sSelect = "select 1 from $sO2CView as $sO2CView left join {$sViewName} on {$sViewName}.oxid = $sO2CView.oxcatnid
                         where $sO2CView.oxobjectid=" . $oDb->quote($this->getId()) . " and {$sViewName}.oxactive = 1 and {$sViewName}.oxskipdiscounts = '1' ";
            $this->_blSkipDiscounts = ($oDb->getOne($sSelect) == 1);
        }

        return $this->_blSkipDiscounts;
    }

    /**
     * Sets the current oxPrice object
     *
     * @param oxPrice $oPrice the new price object
     */
    public function setPrice(oxPrice $oPrice)
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
        $myConfig = $this->getConfig();
        if (!$myConfig->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        // GroupPrice or DB price ajusted by AmountPrice
        $dPrice = $this->_getModifiedAmountPrice($dAmount);

        return $dPrice;
    }

    /**
     * Modifies given amount price.
     *
     * @param int $amount
     *
     * @return double
     */
    protected function _getModifiedAmountPrice($amount)
    {
        $price = $this->_getAmountPrice($amount);

        return $price;
    }

    /**
     * Calculates and returns price of article (adds taxes and discounts).
     *
     * @param float|int $dAmount article amount.
     *
     * @return oxPrice
     */
    public function getPrice($dAmount = 1)
    {
        $myConfig = $this->getConfig();
        // Performance
        if (!$myConfig->getConfigParam('bl_perfLoadPrice') || !$this->_blLoadPrice) {
            return;
        }

        // return cached result, since oPrice is created ONLY in this function [or function of EQUAL level]
        if ($dAmount != 1 || $this->_oPrice === null) {
            // module
            $dBasePrice = $this->getBasePrice($dAmount);
            $dBasePrice = $this->_preparePrice($dBasePrice, $this->getArticleVat());

            $oPrice = $this->_getPriceObject();

            $oPrice->setPrice($dBasePrice);

            // price handling
            if (!$this->_blCalcPrice && $dAmount == 1) {
                return $this->_oPrice = $oPrice;
            }

            $this->_calculatePrice($oPrice);
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
     * @param oxUser $oUser user to set
     */
    public function setArticleUser($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * @return oxUser article user.
     */
    public function getArticleUser()
    {
        if ($this->_oUser) {
            return $this->_oUser;
        }

        return $this->getUser();
    }

    /**
     * Returns custom article VAT value if possible
     * By default value is taken from oxarticle__oxvat field
     *
     * @return double
     */
    public function getCustomVAT()
    {
        if (isset($this->oxarticles__oxvat->value)) {
            return $this->oxarticles__oxvat->value;
        }
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
        /** @var oxSeoEncoderArticle $oEncoder */
        $oEncoder = \oxRegistry::get("oxSeoEncoderArticle");
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
        if (!\oxRegistry::getUtils()->seoIsActive()) {
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
            $sUrl = $this->getConfig()->getShopUrl($iLang, false);
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
    public function getStdLink($iLang = null, $aParams = array())
    {
        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        if (!isset($this->_aStdUrls[$iLang])) {
            $this->_aStdUrls[$iLang] = $this->getBaseStdLink($iLang);
        }

        return \oxRegistry::get("oxUtilsUrl")->processUrl($this->_aStdUrls[$iLang], true, $aParams, $iLang);
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
        if (!$this->_isFieldEmpty("oxarticles__oxthumb")) {
            $sImgName = basename($this->oxarticles__oxthumb->value);
            $sDirname = "product/thumb/";
        } elseif (!$this->_isFieldEmpty("oxarticles__oxpic1")) {
            $sImgName = basename($this->oxarticles__oxpic1->value);
        }

        $sSize = $this->getConfig()->getConfigParam('sThumbnailsize');

        return \oxRegistry::get("oxPictureHandler")->getProductPicUrl($sDirname, $sImgName, $sSize, 0, $bSsl);
    }

    /**
     * Get parent article
     *
     * @return oxArticle
     */
    public function getParentArticle()
    {
        if (($sParentId = $this->oxarticles__oxparentid->value)) {
            $sIndex = $sParentId . "_" . $this->getLanguage();
            if (!isset(self::$_aLoadedParents[$sIndex])) {
                self::$_aLoadedParents[$sIndex] = oxNew('oxArticle');
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
     * Returns TRUE if product is variant, and false if not
     *
     * @return bool
     */
    public function isVariant()
    {
        return (bool) (isset($this->oxarticles__oxparentid) ? $this->oxarticles__oxparentid->value : false);
    }

    /**
     * Returns oxarticles__oxunitname value processed by oxLang::translateString()
     *
     * @return string
     */
    public function getUnitName()
    {
        if ($this->oxarticles__oxunitname->value) {
            return \oxRegistry::getLang()->translateString($this->oxarticles__oxunitname->value);
        }
    }

    /**
     * Loads and returns variants list.
     *
     * @param bool $loadSimpleVariants       if parameter $blSimple - list will be filled with oxSimpleVariant objects, else - oxArticle
     * @param bool $blRemoveNotOrderables    if true, removes from list not orderable articles, which are out of stock [optional]
     * @param bool|null $forceCoreTableUsage if true forces core table use, default is false [optional]
     *
     * @return array | oxsimplevariantlist | oxarticlelist
     */
    protected function _loadVariantList($loadSimpleVariants, $blRemoveNotOrderables = true, $forceCoreTableUsage = null)
    {
        $variants = array();
        if (($articleId = $this->getId())) {
            //do not load me as a parent later
            self::$_aLoadedParents[$articleId . "_" . $this->getLanguage()] = $this;

            $config = $this->getConfig();

            if (!$this->_blLoadVariants ||
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
                } else {
                    $this->_aVariants[$cacheKey] = &$variants;
                }
            } elseif (!$blRemoveNotOrderables) {
                if (isset($this->_aVariantsWithNotOrderables[$cacheKey])) {
                    return $this->_aVariantsWithNotOrderables[$cacheKey];
                } else {
                    $this->_aVariantsWithNotOrderables[$cacheKey] = &$variants;
                }
            }

            if (($this->_blHasVariants = $this->_hasAnyVariant($forceCoreTableUsage))) {

                //load simple variants for lists
                if ($loadSimpleVariants) {
                    $variants = oxNew('oxSimpleVariantList');
                    $variants->setParent($this);
                } else {
                    //loading variants
                    $variants = oxNew('oxArticleList');
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
                    $oMdVariants = oxNew("oxVariantHandler");
                    $this->_blHasMdVariants = $oMdVariants->isMdVariant(current($variants));
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
     * Calculates price of article (adds taxes, currency and discounts).
     *
     * @param oxPrice $oPrice price object
     * @param double  $dVat   vat value, optional, if passed, bypasses "bl_perfCalcVatOnlyForBasketOrder" config value
     *
     * @return oxPrice
     */
    protected function _calculatePrice($oPrice, $dVat = null)
    {
        // apply VAT only if configuration requires it
        if (isset($dVat) || !$this->getConfig()->getConfigParam('bl_perfCalcVatOnlyForBasketOrder')) {
            $this->_applyVAT($oPrice, isset($dVat) ? $dVat : $this->getArticleVat());
        }

        // apply currency
        $this->_applyCurrency($oPrice);
        // apply discounts
        if (!$this->skipDiscounts()) {
            $oDiscountList = \oxRegistry::get("oxDiscountList");
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
    protected function _hasAnyVariant($blForceCoreTable = null)
    {
        $blHas = false;
        if (($sId = $this->getId())) {
            if ($this->oxarticles__oxshopid->value == $this->getConfig()->getShopId()) {
                $blHas = (bool) $this->oxarticles__oxvarcount->value;
            } else {
                $sArticleTable = $this->getViewName($blForceCoreTable);
                $blHas = (bool) \oxDb::getDb()->getOne("select 1 from $sArticleTable where oxparentid='{$sId}'");
            }
        }

        return $blHas;
    }

    /**
     * get user Group A, B or C price, returns db price if user is not in groups
     *
     * @return double
     */
    protected function _getGroupPrice()
    {
        $sPriceSufix = $this->_getUserPriceSufix();
        $sVarName = "oxarticles__oxprice{$sPriceSufix}";
        $dPrice = $this->$sVarName->value;

        // #1437/1436C - added config option, and check for zero A,B,C price values
        if ($this->getConfig()->getConfigParam('blOverrideZeroABCPrices') && (double) $dPrice == 0) {
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
    protected function _getAmountPrice($amount = 1)
    {
        startProfile("_getAmountPrice");

        $dPrice = $this->_getGroupPrice();
        $oAmtPrices = $this->buildAmountPriceList();;
        foreach ($oAmtPrices as $oAmPrice) {
            if ($oAmPrice->oxprice2article__oxamount->value <= $amount
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
     * retrieve article VAT (cached)
     *
     * @return double
     */
    public function getArticleVat()
    {
        if (!isset($this->_dArticleVat)) {
            $this->_dArticleVat = \oxRegistry::get("oxVatSelector")->getArticleVat($this);
        }

        return $this->_dArticleVat;
    }

    /**
     * Applies VAT to article
     *
     * @param oxPrice $oPrice Price object
     * @param double  $dVat   VAT percent
     */
    protected function _applyVAT(\oxPrice $oPrice, $dVat)
    {
        startProfile(__FUNCTION__);
        $oPrice->setVAT($dVat);
        /** @var oxVatSelector $oVatSelector */
        $oVatSelector = \oxRegistry::get("oxVatSelector");
        if (($dVat = $oVatSelector->getArticleUserVat($this)) !== false) {
            $oPrice->setUserVat($dVat);
        }
        stopProfile(__FUNCTION__);
    }

    /**
     * Applies currency factor
     *
     * @param oxPrice $oPrice Price object
     * @param object  $oCur   Currency object
     */
    protected function _applyCurrency(\oxPrice $oPrice, $oCur = null)
    {
        if (!$oCur) {
            $oCur = $this->getConfig()->getActShopCurrencyObject();
        }

        $oPrice->multiply($oCur->rate);
    }


    /**
     * Generates SearchString for getCategory()
     *
     * @param string $sOXID            Article ID
     * @param bool   $blSearchPriceCat Whether to perform the search within price categories
     *
     * @return string
     */
    protected function _generateSearchStr($sOXID, $blSearchPriceCat = false)
    {

        $sCatView = getViewName('oxcategories', $this->getLanguage());
        $sO2CView = getViewName('oxobject2category');

        // we do not use lists here as we don't need this overhead right now
        if (!$blSearchPriceCat) {
            $sSelect = "select {$sCatView}.* from {$sO2CView} as oxobject2category left join {$sCatView} on
                         {$sCatView}.oxid = oxobject2category.oxcatnid
                         where oxobject2category.oxobjectid=" . \oxDb::getDb()->quote($sOXID) . " and {$sCatView}.oxid is not null ";
        } else {
            $sSelect = "select {$sCatView}.* from {$sCatView} where
                         '{$this->oxarticles__oxprice->value}' >= {$sCatView}.oxpricefrom and
                         '{$this->oxarticles__oxprice->value}' <= {$sCatView}.oxpriceto ";
        }

        return $sSelect;
    }

    /**
     * Collecting assigned to article amount-price list.
     *
     * @return oxAmountPriceList
     */
    protected function buildAmountPriceList()
    {
        if ($this->getAmountPriceList() === null) {
            /** @var oxAmountPriceList $oAmPriceList */
            $oAmPriceList = oxNew('oxAmountPriceList');
            $this->setAmountPriceList($oAmPriceList);

            if (!$this->skipDiscounts()) {
                //collecting assigned to article amount-price list
                $oAmPriceList->load($this);

                // prepare abs prices if currently having percentages
                $oBasePrice = $this->_getGroupPrice();
                foreach ($oAmPriceList as $oAmPrice) {
                    if ($oAmPrice->oxprice2article__oxaddperc->value) {
                        $oAmPrice->oxprice2article__oxaddabs = new \oxField(
                           \oxPrice::percent($oBasePrice, 100 - $oAmPrice->oxprice2article__oxaddperc->value),
                           \oxField::T_RAW
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
    protected function _isFieldEmpty($sFieldName)
    {
        $mValue = $this->$sFieldName->value;

        if (is_null($mValue)) {
            return true;
        }

        if ($mValue === '') {
            return true;
        }

        // certain fields with zero value treat as empty
        $aZeroValueFields = array('oxarticles__oxprice', 'oxarticles__oxvat', 'oxarticles__oxunitquantity');

        if (!$mValue && in_array($sFieldName, $aZeroValueFields)) {
            return true;
        }


        if (!strcmp($mValue, '0000-00-00 00:00:00') || !strcmp($mValue, '0000-00-00')) {
            return true;
        }

        $sFieldName = strtolower($sFieldName);

        if ($sFieldName == 'oxarticles__oxicon' && (strpos($mValue, "nopic_ico.jpg") !== false || strpos($mValue,
                    "nopic.jpg") !== false)
        ) {
            return true;
        }

        if (strpos($mValue, "nopic.jpg") !== false && ($sFieldName == 'oxarticles__oxthumb' || substr($sFieldName, 0,
                    17) == 'oxarticles__oxpic' || substr($sFieldName, 0, 18) == 'oxarticles__oxzoom')
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
     * @return null;
     */
    protected function _assignParentFieldValue($sFieldName)
    {
        if (!($oParentArticle = $this->getParentArticle())) {
            return;
        }

        $sCopyFieldName = $this->_getFieldLongName($sFieldName);

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
            if ($this->_isFieldEmpty($sCopyFieldName) && $this->_isImageField($sCopyFieldName) && $this->_hasMasterImage(1)) {
                return;
            }

            //COPY THE VALUE
            if ($this->_isFieldEmpty($sCopyFieldName)) {
                $this->$sCopyFieldName = clone $oParentArticle->$sCopyFieldName;
            }
        }
    }

    /**
     * Detects if field is an image field by field name
     *
     * @param string $sFieldName Field name
     *
     * @return bool.
     */
    protected function _isImageField($sFieldName)
    {
        $blIsImageField = (stristr($sFieldName, '_oxthumb') || stristr($sFieldName, '_oxicon') || stristr($sFieldName,
                '_oxzoom') || stristr($sFieldName, '_oxpic'));

        return $blIsImageField;
    }

    /**
     * Assigns parent field values to article
     */
    protected function _assignParentFieldValues()
    {
        startProfile('articleAssignParentInternal');
        if ($this->oxarticles__oxparentid->value) {
            // yes, we are in fact a variant
            if (!$this->isAdmin() || ($this->_blLoadParentData && $this->isAdmin())) {
                foreach ($this->_aFieldNames as $sFieldName => $sVal) {
                    $this->_assignParentFieldValue($sFieldName);
                }
            }
        }
        stopProfile('articleAssignParentInternal');
    }

    /**
     * if we have variants then depending on config option the parent may be non buyable
     */
    protected function _assignNotBuyableParent()
    {
        if (!$this->getConfig()->getConfigParam('blVariantParentBuyable') &&
            ($this->_blHasVariants || $this->oxarticles__oxvarstock->value || $this->oxarticles__oxvarcount->value)
        ) {
            $this->_blNotBuyableParent = true;
        }
    }

    /**
     * Assigns stock status to article
     */
    protected function _assignStock()
    {
        $myConfig = $this->getConfig();
        // -----------------------------------
        // stock
        // -----------------------------------

        // #1125 A. must round (using floor()) value taken from database and cast to int
        if (!$myConfig->getConfigParam('blAllowUnevenAmounts') && !$this->isAdmin()) {
            $this->oxarticles__oxstock = new \oxField((int) floor($this->oxarticles__oxstock->value));
        }
        //GREEN light
        $this->_iStockStatus = 0;

        // if we have flag /*1 or*/ 4 - we show always green light
        if ($myConfig->getConfigParam('blUseStock') && /*$this->oxarticles__oxstockflag->value != 1 && */
            $this->oxarticles__oxstockflag->value != 4
        ) {
            //ORANGE light
            $iStock = $this->oxarticles__oxstock->value;

            if ($this->_blNotBuyableParent) {
                $iStock = $this->oxarticles__oxvarstock->value;
            }


            if ($iStock <= $myConfig->getConfigParam('sStockWarningLimit') && $iStock > 0) {
                $this->_iStockStatus = 1;
            }

            //RED light
            if ($iStock <= 0) {
                $this->_iStockStatus = -1;
            }
        }


        // stock
        if ($myConfig->getConfigParam('blUseStock') && ($this->oxarticles__oxstockflag->value == 3 || $this->oxarticles__oxstockflag->value == 2)) {
            $iOnStock = $this->oxarticles__oxstock->value;
            if ($this->getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
                $iOnStock += $this->getSession()->getBasketReservations()->getReservedAmount($this->getId());
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
    protected function _assignDynImageDir()
    {
        $myConfig = $this->getConfig();

        $sThisShop = $this->oxarticles__oxshopid->value;

        $this->_sDynImageDir = $myConfig->getPictureUrl(null, false);
        $this->dabsimagedir = $myConfig->getPictureDir(false); //$sThisShop
        $this->nossl_dimagedir = $myConfig->getPictureUrl(null, false, false, null, $sThisShop); //$sThisShop
        $this->ssl_dimagedir = $myConfig->getPictureUrl(null, false, true, null, $sThisShop); //$sThisShop
    }

    /**
     * Adds a flag if article is on comparisonlist.
     */
    protected function _assignComparisonListFlag()
    {
        // #657 add a flag if article is on comparisonlist

        $aItems = \oxRegistry::getSession()->getVariable('aFiltcompproducts');
        if (isset($aItems[$this->getId()])) {
            $this->_blIsOnComparisonList = true;
        }
    }

    /**
     * Checks if article has uploaded master image for selected picture
     *
     * @param int $iIndex master picture index
     *
     * @return bool
     */
    protected function _hasMasterImage($iIndex)
    {
        $sPicName = basename($this->{"oxarticles__oxpic" . $iIndex}->value);

        if ($sPicName == "nopic.jpg" || $sPicName == "") {
            return false;
        }
        if ($this->isVariant() &&
            $this->getParentArticle() &&
            $this->getParentArticle()->{"oxarticles__oxpic" . $iIndex}->value == $this->{"oxarticles__oxpic" . $iIndex}->value
        ) {
            return false;
        }

        $sMasterPic = 'product/' . $iIndex . "/" . $sPicName;

        if ($this->getConfig()->getMasterPicturePath($sMasterPic)) {
            return true;
        }

        return false;
    }

    /**
     * Checks and return true if price view mode is netto
     *
     * @return bool
     */
    protected function _isPriceViewModeNetto()
    {
        $blResult = (bool) $this->getConfig()->getConfigParam('blShowNetPrice');
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
     * @return oxPice
     */
    protected function _getPriceObject($blCalculationModeNetto = null)
    {
        /** @var oxPrice $oPrice */
        $oPrice = oxNew('oxPrice');

        if ($blCalculationModeNetto === null) {
            $blCalculationModeNetto = $this->_isPriceViewModeNetto();
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
     * @param oxPrice $oPrice price object
     *
     * @return double
     */
    protected function _getPriceForView($oPrice)
    {
        if ($this->_isPriceViewModeNetto()) {
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
    protected function _preparePrice($dPrice, $dVat, $blCalculationModeNetto = null)
    {
        if ($blCalculationModeNetto === null) {
            $blCalculationModeNetto = $this->_isPriceViewModeNetto();
        }

        $oCurrency = $this->getConfig()->getActShopCurrencyObject();

        $blEnterNetPrice = $this->getConfig()->getConfigParam('blEnterNetPrice');
        if ($blCalculationModeNetto && !$blEnterNetPrice) {
            $dPrice = round(oxPrice::brutto2Netto($dPrice, $dVat), $oCurrency->decimal);
        } elseif (!$blCalculationModeNetto && $blEnterNetPrice) {
            $dPrice = round(oxPrice::netto2Brutto($dPrice, $dVat), $oCurrency->decimal);
        }

        return $dPrice;
    }

    /**
     * Return price suffix
     *
     * @return null
     */
    protected function _getUserPriceSufix()
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
    protected function _getPrice()
    {
        $sPriceSuffix = $this->_getUserPriceSufix();
        if ($sPriceSuffix === '') {
            $dPrice = $this->oxarticles__oxprice->value;
        } else {
            if ($this->getConfig()->getConfigParam('blOverrideZeroABCPrices')) {
                $dPrice = ($this->{oxarticles__oxprice . $sPriceSuffix}->value != 0) ? $this->{oxarticles__oxprice . $sPriceSuffix}->value : $this->oxarticles__oxprice->value;
            } else {
                $dPrice = $this->{oxarticles__oxprice . $sPriceSuffix}->value;
            }
        }

        return $dPrice;
    }


    /**
     * Return variant min price
     *
     * @return null
     */
    protected function _getVarMinPrice()
    {
        if ($this->_dVarMinPrice === null) {
            $dPrice = $this->_getShopVarMinPrice();

            if (is_null($dPrice)) {
                $sPriceSuffix = $this->_getUserPriceSufix();
                if ($sPriceSuffix === '') {
                    $dPrice = $this->oxarticles__oxvarminprice->value;
                } else {
                    $sSql = 'SELECT ';
                    if ($this->getConfig()->getConfigParam('blOverrideZeroABCPrices')) {
                        $sSql .= 'MIN( IF(`oxprice' . $sPriceSuffix . '` = 0, `oxprice`, `oxprice' . $sPriceSuffix . '`) ) AS `varminprice` ';
                    } else {
                        $sSql .= 'MIN(`oxprice' . $sPriceSuffix . '`) AS `varminprice` ';
                    }

                    $sSql .= ' FROM ' . $this->getViewName(true) . '
                    WHERE ' . $this->getSqlActiveSnippet(true) . '
                        AND ( `oxparentid` = ' . \oxDb::getDb()->quote($this->getId()) . ' )';

                    $dPrice = \oxDb::getDb()->getOne($sSql);
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
    protected function _getVarMaxPrice()
    {
        if ($this->_dVarMaxPrice === null) {
            $dPrice = $this->_getShopVarMaxPrice();

            if (is_null($dPrice)) {
                $sPriceSuffix = $this->_getUserPriceSufix();
                if ($sPriceSuffix === '') {
                    $dPrice = $this->oxarticles__oxvarmaxprice->value;
                } else {
                    $sSql = 'SELECT ';
                    if ($this->getConfig()->getConfigParam('blOverrideZeroABCPrices')) {
                        $sSql .= 'MAX( IF(`oxprice' . $sPriceSuffix . '` = 0, `oxprice`, `oxprice' . $sPriceSuffix . '`) ) AS `varmaxprice` ';
                    } else {
                        $sSql .= 'MAX(`oxprice' . $sPriceSuffix . '`) AS `varmaxprice` ';
                    }

                    $sSql .= ' FROM ' . $this->getViewName(true) . '
                        WHERE ' . $this->getSqlActiveSnippet(true) . '
                            AND ( `oxparentid` = ' . \oxDb::getDb()->quote($this->getId()) . ' )';

                    $dPrice = \oxDb::getDb()->getOne($sSql);
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
    protected function _getShopVarMinPrice()
    {
        return null;
    }

    /**
     * Place to hook to return variant max price if it might be different,
     * for example for subshops.
     *
     * @return double|null
     */
    protected function _getShopVarMaxPrice()
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
    protected function _loadFromDb($articleId)
    {
        $sSelect = $this->buildSelectString(array($this->getViewName() . ".oxid" => $articleId));

        $aData = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC)->getRow($sSelect);

        return $aData;
    }

    /**
     * Forms query to load variants.
     *
     * @param $blRemoveNotOrderables
     * @param $forceCoreTableUsage
     * @param $baseObject
     * @param $sArticleTable
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
     * @param oxBase $baseObject             article list template object.
     * @param bool|null $forceCoreTableUsage if true forces core table use, default is false [optional]
     */
    protected function updateVariantsBaseObject($baseObject, $forceCoreTableUsage = null)
    {
        $baseObject->setLanguage($this->getLanguage());
    }
}