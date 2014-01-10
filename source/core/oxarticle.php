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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

// defining supported link types
define( 'OXARTICLE_LINKTYPE_CATEGORY', 0 );
define( 'OXARTICLE_LINKTYPE_VENDOR', 1 );
define( 'OXARTICLE_LINKTYPE_MANUFACTURER', 2 );
define( 'OXARTICLE_LINKTYPE_PRICECATEGORY', 3 );
define( 'OXARTICLE_LINKTYPE_TAG', 4 );
define( 'OXARTICLE_LINKTYPE_RECOMM', 5 );

/**
 * Article manager.
 * Creates fully detailed article object, with such information as VAT,
 * discounts, etc.
 *
 * @package core
 */
class oxArticle extends oxI18n implements oxIArticle, oxIUrl
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxarticle';

    /**
     * Set $_blUseLazyLoading to true if you want to load only actually used fields not full objet, depending on views.
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
     * @var bool
     */
    protected $_blCalcPrice    = true;

    /**
     * Article oxprice object.
     * @var oxPrice
     */
    protected $_oPrice      = null;

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
    protected $_aPersistParam  = null;

    /**
     * Status of article - buyable/not buyable.
     *
     * @var bool
     */
    protected $_blNotBuyable   = false;

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
    protected $_blNotBuyableParent  = false;


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
     * @var bool
     */
    protected $_blIsOnComparisonList = false;

    /**
     * user object
     * @var oxUser
     */
    protected $_oUser = null;

    /**
     * Performance issue. Sometimes you want to load articles without calculating
     * correct discounts and prices etc.
     * @var bool
     */
    protected $_blLoadPrice = true;

    /**
     * If $_blSkipAbPrice is set to true, then "From price" is not calculated for this object.
     */
    protected $_blSkipAbPrice = false;

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
     * Attributes are loaded only when bl_perfLoadAttributes config option is set to true
     */
    protected $_oAttributeList = null;


    /**
     * Indicates whether the price is "Ab" price
     *
     * @var bool
     */
    protected $_blIsRangePrice = false;

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
     * @var oxList
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
     * Stardard/dynamic article urls for languages
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
     * @var duoble
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
    protected $_aNonCopyParentFields = array('oxarticles__oxinsert',
                                             'oxarticles__oxtimestamp',
                                             'oxarticles__oxnid',
                                             'oxarticles__oxid',
                                             'oxarticles__oxparentid');

    /**
     * Override certain parent fields to variant
     *
     * @var array
     */
    protected $_aCopyParentField = array('oxarticles__oxnonmaterial',
                                         'oxarticles__oxfreeshipping',
                                         //'oxarticles__oxremindactive',
                                         'oxarticles__oxisdownloadable');

    /**
     * Multidimensional variant tree structure
     *
     * @var OxMdVariant
     */
    protected $_oMdVariants = null;

    /**
     * Product long description field
     *
     * @var oxField
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
     * @var array
     */
    protected static $_aSelections = array();

    /**
     * Category instance cache
     * @var array
     */
    protected static $_aCategoryCache = null;

    /**
     * stores if are stored any amount price
     * @var bool
     */
    protected static $_blHasAmountPrice = null;

    /**
     * stores downloadable file list
     * @var array|oxList of oxArticleFile
     */
    protected $_aArticleFiles = null;


    /**
     * Class constructor, sets shop ID for article (oxconfig::getShopId()),
     * initiates parent constructor (parent::oxI18n()).
     *
     * @param array $aParams The array of names and values of oxArticle instance properties to be set on object instantiation
     *
     * @return null
     */
    public function __construct($aParams = null)
    {
        if ( $aParams && is_array($aParams)) {
            foreach ( $aParams as $sParam => $mValue) {
                $this->$sParam = $mValue;
            }
        }
        parent::__construct();
        $this->init( 'oxarticles' );

        $this->_blIsRangePrice = false;
    }

    /**
     * Magic getter, deals with deprecated values and long description which is loaded on demand.
     * Additionally it sets default value for unknown picture fields
     *
     * @param string $sName Variable name
     *
     * @return mixed
     */
    public function __get($sName)
    {
        $myUtils = oxUtils::getInstance();
        // deprecated since 2011.03.10, should be used getLongDescription() / getLongDesc()
        if ( strpos( $sName, 'oxarticles__oxlongdesc' ) === 0 ) {
            return $this->getLongDescription();
        }

        $this->$sName = parent::__get($sName);
        if ( $this->$sName ) {
            // since the field could have been loaded via lazyloading
            $this->_assignParentFieldValue($sName);
        }

        return $this->$sName;
    }

    /**
     * Sets article parameter
     *
     * @param string $sName  name of parameter to set
     * @param mixed  $sValue parameter value
     *
     * @return null
     */
    public function __set( $sName, $sValue )
    {
        // deprecated since 2011.03.14, should be used setArticleLongDesc()
        if ( strpos( $sName, 'oxarticles__oxlongdesc' ) === 0 ) {
            if ($this->_blEmployMultilanguage) {
                $sValue = ( $sValue instanceof oxField ) ? $sValue->getRawValue() : $sValue;
                $this->setArticleLongDesc( $sValue );
            } else {
                $this->$sName = $sValue;
            }
        } else {
            parent::__set( $sName, $sValue );
        }
    }

    /**
     * Sets object ID, additionally sets $this->oxarticles__oxnid field value
     *
     * @param string $sId New ID
     *
     * @return null
     */
    public function setId( $sId = null )
    {
        $sId = parent::setId( $sId );

        // TODO: in oxbase::setId make it to check if exists and update, not recreate, then delete this overload
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
    public function getActiveCheckQuery( $blForceCoreTable = null )
    {
        $sTable = $this->getViewName( $blForceCoreTable );

        // check if article is still active
        $sQ = " $sTable.oxactive = 1 ";

        // enabled time range check ?
        if ( $this->getConfig()->getConfigParam( 'blUseTimeCheck' ) ) {
            $sDate = date( 'Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime() );
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
    public function getStockCheckQuery( $blForceCoreTable = null )
    {
        $myConfig = $this->getConfig();
        $sTable = $this->getViewName( $blForceCoreTable );

        $sQ = "";

        //do not check for variants
        if ( $myConfig->getConfigParam( 'blUseStock' ) ) {
            $sQ = " and ( $sTable.oxstockflag != 2 or ( $sTable.oxstock + $sTable.oxvarstock ) > 0  ) ";
            //V #M513: When Parent article is not purchaseble, it's visibility should be displayed in shop only if any of Variants is available.
            if ( !$myConfig->getConfigParam( 'blVariantParentBuyable' ) ) {
                $sTimeCheckQ = '';
                if ( $myConfig->getConfigParam( 'blUseTimeCheck' ) ) {
                     $sDate = date( 'Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime() );
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
    public function getVariantsQuery( $blRemoveNotOrderables, $blForceCoreTable = null  )
    {
        $sTable = $this->getViewName( $blForceCoreTable );
        $sQ = " and $sTable.oxparentid = '".$this->getId()."' ";

        //checking if variant is active and stock status
        if ( $this->getConfig()->getConfigParam( 'blUseStock' ) ) {
            $sQ .= " and ( $sTable.oxstock > 0 or ( $sTable.oxstock <= 0 and $sTable.oxstockflag != 2 ";
            if ( $blRemoveNotOrderables ) {
                $sQ .= " and $sTable.oxstockflag != 3 ";
            }
            $sQ .= " ) ) ";
        }

        return $sQ;
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $blForceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    public function getSqlActiveSnippet( $blForceCoreTable = null )
    {
        $myConfig = $this->getConfig();

        // check if article is still active
        $sQ = $this->getActiveCheckQuery( $blForceCoreTable );

        // stock and variants check
        $sQ .= $this->getStockCheckQuery( $blForceCoreTable );


        return "( $sQ ) ";
    }

    /**
     * Assign condition setter. In case article assignment is skipped ($_blSkipAssign = true), it does not perform additional
     *
     * @param bool $blSkipAssign Whether to skip assign process for the article
     *
     * @return null
     */
    public function setSkipAssign($blSkipAssign)
    {
        $this->_blSkipAssign = $blSkipAssign;
    }

    /**
     * Disables article price loading. Should be called before assign(), or load()
     *
     * @return null
     */
    public function disablePriceLoad()
    {
        $this->_blLoadPrice = false;
    }

    /**
     * Enable article price loading, if disabled.
     *
     * @return null
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
     *
     * @return null
     */
    public function setItemKey($sItemKey)
    {
        $this->_sItemKey = $sItemKey;
    }

    /**
     * Disables/enables variant loading
     *
     * @param bool $blLoadVariants skip variant loading or not
     *
     * @return null
     */
    public function setNoVariantLoading( $blLoadVariants )
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
        if ($this->_blNotBuyableParent) {
            return false;
        }

        return !$this->_blNotBuyable;
    }

    /**
     * Get persistent parameters
     *
     * @return array
     */
    public function getPersParams()
    {
        return $this->_aPersistParam;
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
     *
     * @return null
     */
    public function setOnComparisonList( $blOnList )
    {
        $this->_blIsOnComparisonList = $blOnList;
    }

    /**
     * A setter for $_blLoadParentData (whether article parent info should be laoded fully) class variable
     *
     * @param bool $blLoadParentData Whether to load parent data
     *
     * @return null
     */
    public function setLoadParentData($blLoadParentData)
    {
        $this->_blLoadParentData = $blLoadParentData;
    }

    /**
     * Set _blSkipAbPrice value. If is set to true, then "From price" is not calculated for this object.
     *
     * @param bool $blSkipAbPrice Whether to skip "From" price loading
     *
     * @return null
     */
    public function setSkipAbPrice( $blSkipAbPrice = null )
    {
        $this->_blSkipAbPrice = $blSkipAbPrice;
    }

    /**
     * Returns an array of article object DB fields, without multilanguage.
     *
     * @deprecated since 20110826, used only in admin, should not be here
     *
     * @return array
     */
    public function getSearchableFields()
    {
        $aSkipFields = array("oxblfixedprice", "oxicon", "oxvarselect", "oxamitemid", "oxamtaskid", "oxpixiexport", "oxpixiexported") ;
        $aFields = array_diff( array_keys($this->_aFieldNames), $aSkipFields );

        return $aFields;
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
        switch ($sFieldName) {
            case "oxlongdesc":
            case "oxtags":
                return true;
        }

        return parent::isMultilingualField($sFieldName);
    }

    /**
     * Checks if article has visible status. Returns TRUE if its visible
     *
     * @return bool
     */
    public function isVisible()
    {

        // admin preview mode
        if ( ( $blCanPreview = oxUtils::getInstance()->canPreview() ) !== null ) {
            return $blCanPreview;
        }

        // active ?
        $sNow = date('Y-m-d H:i:s');
        if ( !$this->oxarticles__oxactive->value &&
             (  $this->oxarticles__oxactivefrom->value > $sNow ||
                $this->oxarticles__oxactiveto->value < $sNow
             )) {
            return false;
        }

        // stock flags
        if ( $this->getConfig()->getConfigParam( 'blUseStock' ) && $this->oxarticles__oxstockflag->value == 2) {
            $iOnStock = $this->oxarticles__oxstock->value + $this->oxarticles__oxvarstock->value;
            if ($this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )) {
                $iOnStock += $this->getSession()->getBasketReservations()->getReservedAmount($this->getId());
            }
            if ( $iOnStock <= 0 ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assigns to oxarticle object some base parameters/values (such as
     * detaillink, moredetaillink, etc).
     *
     * @param string $aRecord Array representing current field values
     *
     * @return null
     */
    public function assign( $aRecord)
    {
        startProfile('articleAssign');


        // load object from database
        parent::assign( $aRecord);

        $this->oxarticles__oxnid = $this->oxarticles__oxid;

        // check for simple article.
        if ($this->_blSkipAssign) {
            return;
        }

        $this->_assignParentFieldValues();
        $this->_assignNotBuyableParent();

        $this->_assignStock();
        startProfile('articleAssignPrices');
        $this->_assignPrices();
        stopProfile('articleAssignPrices');
        $this->_assignPersistentParam();
        $this->_assignDynImageDir();
        $this->_assignComparisonListFlag();
        $this->_assignAttributes();


        //$this->_seoAssign();

        stopProfile('articleAssign');
    }

    /**
     * Loads object data from DB (object data ID must be passed to method).
     * Converts dates (oxarticle::oxarticles__oxinsert, oxarticle::oxarticles__oxtimestamp)
     * to international format (oxutils.php oxUtilsDate::getInstance()->formatDBDate(...)).
     * Returns true if article was loaded successfully.
     *
     * @param string $oxID Article object ID
     *
     * @return bool
     */
    public function load( $oxID)
    {
        // A. #1325 resetting to avoid problems when reloading (details etc)
        $this->_blNotBuyableParent = false;

        $blRet = parent::load( $oxID);

        // convert date's to international format
        $this->oxarticles__oxinsert    = new oxField(oxUtilsDate::getInstance()->formatDBDate( $this->oxarticles__oxinsert->value));
        $this->oxarticles__oxtimestamp = new oxField(oxUtilsDate::getInstance()->formatDBDate( $this->oxarticles__oxtimestamp->value));

        return $blRet;
    }

    /**
     * Calculates and saves product rating average
     *
     * @param integer $iRating new rating value
     *
     * @return null
     */
    public function addToRatingAverage( $iRating )
    {
        $dOldRating = $this->oxarticles__oxrating->value;
        $dOldCnt    = $this->oxarticles__oxratingcnt->value;
        $this->oxarticles__oxrating->setValue(( $dOldRating * $dOldCnt + $iRating ) / ($dOldCnt + 1));
        $this->oxarticles__oxratingcnt->setValue($dOldCnt + 1);
        $dRating = ( $dOldRating * $dOldCnt + $iRating ) / ($dOldCnt + 1);
        $dRatingCnt = (int) ($dOldCnt + 1);
        // oxarticles.oxtimestamp = oxarticles.oxtimestamp to keep old timestamp value
        $oDb = oxDb::getDb();
        $oDb->execute( 'update oxarticles set oxarticles.oxrating = '.$dRating.',oxarticles.oxratingcnt = '.$dRatingCnt.', oxarticles.oxtimestamp = oxarticles.oxtimestamp where oxarticles.oxid = '.$oDb->quote( $this->getId() ) );
    }

    /**
     * Set product rating average
     *
     * @param integer $iRating new rating value
     *
     * @return null
     */
    public function setRatingAverage( $iRating )
    {
         $this->oxarticles__oxrating = new oxField( $iRating );
    }

    /**
     * Set product rating count
     *
     * @param integer $iRatingCnt new rating count
     *
     * @return null
     */
    public function setRatingCount( $iRatingCnt )
    {
         $this->oxarticles__oxratingcnt = new oxField( $iRatingCnt );
    }

    /**
     * Returns product rating average
     *
     * @param bool $blIncludeVariants - include variant ratings
     *
     * @return double
     */
    public function getArticleRatingAverage( $blIncludeVariants = false )
    {
        if ( !$blIncludeVariants ) {
            return round( $this->oxarticles__oxrating->value, 1);
        } else {
            $oRating = oxNew( 'oxRating' );
            return $oRating->getRatingAverage( $this->getId(), 'oxarticle', $this->_getVariantsIds() );
        }
    }

    /**
     * Returns product rating count
     *
     *@param bool $blIncludeVariants - include variant ratings
     *
     * @return double
     */
    public function getArticleRatingCount( $blIncludeVariants = false )
    {
        if ( !$blIncludeVariants ) {
            return $this->oxarticles__oxratingcnt->value;
        } else {
            $oRating = oxNew( 'oxRating' );
            return $oRating->getRatingCount( $this->getId(), 'oxarticle', $this->_getVariantsIds() );
        }
    }


    /**
     * Collects user written reviews about an article.
     *
     * @return oxList
     */
    public function getReviews()
    {
        $aIds = array($this->getId());

        if ( $this->oxarticles__oxparentid->value ) {
            $aIds[] = $this->oxarticles__oxparentid->value;
        }

        // showing variant reviews ..
        if ( $this->getConfig()->getConfigParam( 'blShowVariantReviews' ) ) {
            $aAdd = $this->_getVariantsIds();
            if (is_array($aAdd)) {
                $aIds = array_merge($aIds, $aAdd);
            }
        }

        $oReview = oxNew('oxreview');
        $oRevs = $oReview->loadList('oxarticle', $aIds);

        //if no review found, return null
        if ( $oRevs->count() < 1 ) {
            return null;
        }

        return $oRevs;
    }

    /**
     * Loads and returns array with crosselling information.
     *
     * @return array
     */
    public function getCrossSelling()
    {
        $oCrosslist = oxNew( "oxarticlelist");
        $oCrosslist->loadArticleCrossSell($this->oxarticles__oxid->value);
        if ( $oCrosslist->count() ) {
            return $oCrosslist;
        }
    }

    /**
     * Loads and returns array with accessoires information.
     *
     * @return array
     */
    public function getAccessoires()
    {
        $myConfig = $this->getConfig();

        // Performance
        if ( !$myConfig->getConfigParam( 'bl_perfLoadAccessoires' ) ) {
            return;
        }

        $oAcclist = oxNew( "oxarticlelist");
        $oAcclist->setSqlLimit( 0, $myConfig->getConfigParam( 'iNrofCrossellArticles' ));
        $oAcclist->loadArticleAccessoires($this->oxarticles__oxid->value);

        if ( $oAcclist->count()) {
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
        $myConfig = $this->getConfig();
        if ( !$myConfig->getConfigParam( 'bl_perfLoadSimilar' ) ) {
            return;
        }

        $sArticleTable = $this->getViewName();

        $sAttribs = '';
        $iCnt = 0;
        $this->_getAttribsString($sAttribs, $iCnt);

        if ( !$sAttribs) {
            return null;
        }

        $aList = $this->_getSimList($sAttribs, $iCnt);

        if ( count( $aList ) ) {
            uasort( $aList, 'cmpart');

            $sSearch = $this->_generateSimListSearchStr($sArticleTable, $aList);

            $oSimilarlist = oxNew( 'oxarticlelist' );
            $oSimilarlist->setSqlLimit( 0, $myConfig->getConfigParam( 'iNrofSimilarArticles' ));
            $oSimilarlist->selectString( $sSearch);

            return $oSimilarlist;
        }
    }

    /**
     * Loads and returns articles list, bought by same customer.
     *
     * @return array
     */
    public function getCustomerAlsoBoughtThisProducts()
    {
        // Performance
        $myConfig = $this->getConfig();
        if ( !$myConfig->getConfigParam( 'bl_perfLoadCustomerWhoBoughtThis' ) ) {
            return;
        }

        // selecting products that fits
        $sQ = $this->_generateSearchStrForCustomerBought();

        $oArticles = oxNew( 'oxarticlelist' );
        $oArticles->setSqlLimit( 0, $myConfig->getConfigParam( 'iNrofCustomerWhoArticles' ));
        $oArticles->selectString( $sQ );
        if ( $oArticles->count() ) {
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
        $myConfig = $this->getConfig();
        if ( !$myConfig->getConfigParam( 'bl_perfLoadPrice' ) || !$this->_blLoadPrice || !$this->_blCalcPrice || !$this->hasAmountPrice() ) {
            return array();
        }

        if ( $this->_oAmountPriceInfo === null ) {
            $this->_oAmountPriceInfo = array();
            if ( count( ( $oAmPriceList = $this->_getAmountPriceList() ) ) ) {
                $this->_oAmountPriceInfo = $this->_fillAmountPriceList( $oAmPriceList );
            }
        }
        return $this->_oAmountPriceInfo;
    }

    /**
     * Returns all selectlists this article has (used in basic theme and oxbasket)
     *
     * @param string $sKeyPrefix Optionall key prefix
     *
     * @return array
     */
    public function getSelectLists($sKeyPrefix = null)
    {
        //#1468C - more then one article in basket with different selectlist...
        //optionall function parameter $sKeyPrefix added, used only in basket.php
        $sKey = $this->getId();
        if ( isset( $sKeyPrefix ) ) {
            $sKey = $sKeyPrefix.'__'.$sKey;
        }
        if ( !isset( self::$_aSelList[$sKey] ) ) {
            $oDb = oxDb::getDb();
            $sSLViewName = getViewName( 'oxselectlist' );

            $sQ = "select {$sSLViewName}.* from oxobject2selectlist join {$sSLViewName} on $sSLViewName.oxid=oxobject2selectlist.oxselnid
                   where oxobject2selectlist.oxobjectid=%s order by oxobject2selectlist.oxsort";

            // all selectlists this article has
            $oLists = oxNew( 'oxlist' );
            $oLists->init( 'oxselectlist' );
            $oLists->selectString( sprintf( $sQ, $oDb->quote( $this->getId() ) ) );

            //#1104S if this is variant ant it has no selectlists, trying with parent
            if ( $oLists->count() == 0 && $this->oxarticles__oxparentid->value ) {
                $oLists->selectString( sprintf( $sQ, $oDb->quote( $this->oxarticles__oxparentid->value ) ) );
            }

            $dVat = 0;
            if ( $this->getPrice() != null ) {
                $dVat = $this->getPrice()->getVat();
            }

            $iCnt = 0;
            self::$_aSelList[$sKey] = array();
            foreach ( $oLists as $oSelectlist ) {
                self::$_aSelList[$sKey][$iCnt] = $oSelectlist->getFieldList( $dVat );
                self::$_aSelList[$sKey][$iCnt]['name'] = $oSelectlist->oxselectlist__oxtitle->value;
                $iCnt++;
            }
        }
        return self::$_aSelList[$sKey];
    }

    /**
     * Checks if parent has ANY variant assigned
     *
     * @param bool $blForceCoreTable force core table usage
     *
     * @return bool
     */
    protected function _hasAnyVariant( $blForceCoreTable = null )
    {
        $blHas = false;
        if ( ( $sId = $this->getId() ) ) {
            if ( $this->oxarticles__oxshopid->value == $this->getConfig()->getShopId() ) {
                $blHas = (bool) $this->oxarticles__oxvarcount->value;
            } else {
                $sArticleTable = $this->getViewName( $blForceCoreTable );
                $blHas = (bool) oxDb::getDb()->getOne( "select 1 from $sArticleTable where oxparentid='{$sId}'" );
            }

        }
        return $blHas;
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
    public function getVariantSelections( $aFilterIds = null, $sActVariantId = null, $iLimit = 0 )
    {

        $iLimit = (int) $iLimit;
        if ( !isset( $this->_aVariantSelections[$iLimit] ) ) {
            $aVariantSelections = false;
            if ( $this->oxarticles__oxvarcount->value ) {
                $oVariants = $this->getVariants( false );
                $aVariantSelections = oxNew( "oxVariantHandler" )->buildVariantSelections( $this->oxarticles__oxvarname->getRawValue(), $oVariants, $aFilterIds, $sActVariantId, $iLimit );

                if ( !empty($oVariants) && empty( $aVariantSelections['rawselections'] ) ) {
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
    public function getSelections( $iLimit = null, $aFilter = null )
    {
        $sId = $this->getId() . ( (int) $iLimit );
        if ( !array_key_exists( $sId, self::$_aSelections ) ) {

            $oDb = oxDb::getDb();
            $sSLViewName = getViewName( 'oxselectlist' );

            $sQ = "select {$sSLViewName}.* from oxobject2selectlist join {$sSLViewName} on $sSLViewName.oxid=oxobject2selectlist.oxselnid
                   where oxobject2selectlist.oxobjectid=%s order by oxobject2selectlist.oxsort";

            if ( ( $iLimit = (int) $iLimit ) ) {
                $sQ .= " limit $iLimit ";
            }

            // vat value for price
            $dVat = 0;
            if ( ( $oPrice = $this->getPrice() ) != null ) {
                $dVat = $oPrice->getVat();
            }

            // all selectlists this article has
            $oList = oxNew( 'oxlist' );
            $oList->init( 'oxselectlist' );
            $oList->getBaseObject()->setVat( $dVat );
            $oList->selectString( sprintf( $sQ, $oDb->quote( $this->getId() ) ) );

            //#1104S if this is variant and it has no selectlists, trying with parent
            if ( $oList->count() == 0 && $this->oxarticles__oxparentid->value ) {
                $oList->selectString( sprintf( $sQ, $oDb->quote( $this->oxarticles__oxparentid->value ) ) );
            }

            self::$_aSelections[$sId] = $oList->count() ? $oList : false;
        }

        if ( self::$_aSelections[$sId] ) {
            // marking active from filter
            $aFilter = ( $aFilter === null ) ? oxConfig::getParameter( "sel" ) : $aFilter;
            if ( $aFilter ) {
                $iSelIdx = 0;
                foreach ( self::$_aSelections[$sId] as $oSelection ) {
                    if ( isset( $aFilter[$iSelIdx] ) ) {
                        $oSelection->setActiveSelectionByIndex( $aFilter[$iSelIdx] );
                    }
                    $iSelIdx++;
                }
            }
        }

        return self::$_aSelections[$sId];
    }

    /**
     * Loads and returns variants list.
     *
     * @param bool $blSimple              If parameter $blSimple - list will be filled with oxSimpleVariant objects, else - oxArticle
     * @param bool $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of stock [optional]
     * @param bool $blForceCoreTable      if true forces core tabel use, default is false [optional]
     *
     * @return array | oxsimplevariantlist | oxarticlelist
     */
    protected function _loadVariantList( $blSimple, $blRemoveNotOrderables = true, $blForceCoreTable = null )
    {
        $oVariants = array();
        if ( ( $sId = $this->getId() ) ) {
            //do not load me as a parent later
            self::$_aLoadedParents[$sId] = $this;

            $myConfig = $this->getConfig();

            if ( !$this->_blLoadVariants ||
                ( !$this->isAdmin() && !$myConfig->getConfigParam( 'blLoadVariants') ) ||
                ( !$this->isAdmin() && !$this->oxarticles__oxvarcount->value ) ) {
                return $oVariants;
            }

            // cache
            $sCacheKey = $blSimple ? "simple" : "full";
            if ( $blRemoveNotOrderables ) {
                if ( isset( $this->_aVariants[$sCacheKey] ) ) {
                   return $this->_aVariants[$sCacheKey];
                } else {
                    $this->_aVariants[$sCacheKey] = & $oVariants;
                }
            } elseif ( !$blRemoveNotOrderables ) {
                if ( isset( $this->_aVariantsWithNotOrderables[$sCacheKey] ) ) {
                    return $this->_aVariantsWithNotOrderables[$sCacheKey];
                } else {
                    $this->_aVariantsWithNotOrderables[$sCacheKey] = & $oVariants;
                }
            }

            if ( ( $this->_blHasVariants = $this->_hasAnyVariant( $blForceCoreTable ) ) ) {

                //load simple variants for lists
                if ( $blSimple ) {
                    $oVariants = oxNew( 'oxsimplevariantlist' );
                    $oVariants->setParent( $this );
                } else {
                    //loading variants
                    $oVariants = oxNew( 'oxarticlelist' );
                    $oVariants->getBaseObject()->modifyCacheKey( '_variants' );
                }

                startProfile("selectVariants");
                $blUseCoreTable = (bool) $blForceCoreTable;
                $oBaseObject = $oVariants->getBaseObject();
                $oBaseObject->setLanguage( $this->getLanguage() );


                $sArticleTable = $this->getViewName( $blUseCoreTable );

                $sSelect = "select ".$oBaseObject->getSelectFields( $blUseCoreTable )." from $sArticleTable where " .
                           $this->getActiveCheckQuery( $blUseCoreTable ) .
                           $this->getVariantsQuery( $blRemoveNotOrderables, $blUseCoreTable ) .
                           " order by $sArticleTable.oxsort";

                $oVariants->selectString( $sSelect );

                //if this is multidimensional variants, make additional processing
                if ( $myConfig->getConfigParam( 'blUseMultidimensionVariants' ) ) {
                    $oMdVariants = oxNew( "oxVariantHandler" );
                    $this->_blHasMdVariants = $oMdVariants->isMdVariant( $oVariants->current() );
                }
                stopProfile("selectVariants");
            }

            //if we have variants then depending on config option the parent may be non buyable
            if ( !$myConfig->getConfigParam( 'blVariantParentBuyable' ) && $this->_blHasVariants ) {
                $this->_blNotBuyableParent = true;
            }

            //if we have variants, but all variants are incative means article may be non buyable (depends on config option)
            if ( !$myConfig->getConfigParam( 'blVariantParentBuyable' ) && count( $oVariants ) == 0 && $this->_blHasVariants ) {
                $this->_blNotBuyable = true;
            }
        }

        return $oVariants;
    }

    /**
     * Returns variant list (list contains oxArticle objects)
     *
     * @param bool $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of stock [optional]
     * @param bool $blForceCoreTable      if true forces core tabel use, default is false [optional]
     *
     * @return oxarticlelist
     */
    public function getFullVariants( $blRemoveNotOrderables = true, $blForceCoreTable = null )
    {
        return $this->_loadVariantList( false, $blRemoveNotOrderables, $blForceCoreTable );
    }

    /**
     * Collects and returns article variants.
     *
     * @param bool $blRemoveNotOrderables if true, removes from list not orderable articles, which are out of stock
     * @param bool $blForceCoreTable      if true forces core tabel use, default is false [optional]
     *
     * @return array
     */
    public function getVariants( $blRemoveNotOrderables = true, $blForceCoreTable = null  )
    {
        return $this->_loadVariantList( $this->_isInList(), $blRemoveNotOrderables, $blForceCoreTable );
    }

    /**
     * Simple way to get variants without quering oxarticle table first. This is basically used for lists.
     *
     * @return null
     */
    public function getSimpleVariants()
    {
        if ( $this->oxarticles__oxvarcount->value) {
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
    public function getAdminVariants( $sLanguage = null )
    {
        $oVariants = oxNew( 'oxarticlelist');
        if ( ( $sId = $this->getId() ) ) {

            $oBaseObj = $oVariants->getBaseObject();

            if ( is_null( $sLanguage ) ) {
                $oBaseObj->setLanguage( oxLang::getInstance()->getBaseLanguage() );
            } else {
                $oBaseObj->setLanguage( $sLanguage );
            }

            $sSql = "select * from ".$oBaseObj->getViewName()." where oxparentid = '{$sId}' order by oxsort ";
            $oVariants->selectString( $sSql );

            //if we have variants then depending on config option the parent may be non buyable
            if ( !$this->getConfig()->getConfigParam( 'blVariantParentBuyable' ) && ( $oVariants->count() > 0 ) ) {
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
     * @return oxcategory
     */
    public function getCategory()
    {
        $oCategory = oxNew( 'oxcategory' );
        $oCategory->setLanguage( $this->getLanguage() );

        // variant handling
        $sOXID = $this->getId();
        if ( isset( $this->oxarticles__oxparentid->value ) && $this->oxarticles__oxparentid->value ) {
            $sOXID = $this->oxarticles__oxparentid->value;
        }

        if ( $sOXID ) {
            // if the oxcategory instance of this article is not cached
            if ( !isset( $this->_aCategoryCache[ $sOXID ] ) ) {
                startPRofile( 'getCategory' );
                $oStr = getStr();
                $sWhere   = $oCategory->getSqlActiveSnippet();
                $sSelect  = $this->_generateSearchStr( $sOXID );
                $sSelect .= ( $oStr->strstr( $sSelect, 'where' )?' and ':' where ') . $sWhere . " order by oxobject2category.oxtime limit 1";

                // category not found ?
                if ( !$oCategory->assignRecord( $sSelect ) ) {

                    $sSelect  = $this->_generateSearchStr( $sOXID, true );
                    $sSelect .= ( $oStr->strstr( $sSelect, 'where' )?' and ':' where ') . $sWhere . " limit 1";

                    // looking for price category
                    if ( !$oCategory->assignRecord( $sSelect ) ) {
                        $oCategory = null;
                    }
                }
                // add the category instance to cache
                $this->_aCategoryCache[ $sOXID ] = $oCategory;
                stopPRofile( 'getCategory' );
            } else {
               // if the oxcategory instance is cached
               $oCategory = $this->_aCategoryCache[ $sOXID ];
            }
        }

        return $oCategory;
    }

    /**
     * Returns ID's of categories where this article is assigned
     *
     * @param bool $blActCats   select categories if all parents are active
     * @param bool $blSkipCache Whether to skip cache
     *
     * @return array
     */
    public function getCategoryIds( $blActCats = false, $blSkipCache = false )
    {
        $myConfig = $this->getConfig();
        if ( isset( self::$_aArticleCats[$this->getId()] ) && !$blSkipCache ) {
            return self::$_aArticleCats[$this->getId()];
        }

        // variant handling
        $sOXID = $this->getId();
        if (isset( $this->oxarticles__oxparentid->value) && $this->oxarticles__oxparentid->value) {
            $sOXID = $this->oxarticles__oxparentid->value;
        }

        // we do not use lists here as we dont need this overhead right now
        $sSql = $this->_getSelectCatIds( $sOXID, $blActCats );
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $rs = $oDb->select( $sSql );


        $aRet = array();

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $aRet[] = $rs->fields['oxcatnid'];
                $rs->moveNext();
            }
        }

        // adding price categories if such exists
        $sSql = $this->getSqlForPriceCategories();
        $oDb->setFetchMode( oxDb::FETCH_MODE_ASSOC );
        $rs = $oDb->select( $sSql );

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {

                if ( is_array( $rs->fields ) ) {
                   $rs->fields = array_change_key_case( $rs->fields, CASE_LOWER );
                }


                if ( !$aRet[$rs->fields['oxid']] ) {
                    $aRet[] = $rs->fields['oxid'];
                }
                $rs->moveNext();
            }
        }

        return self::$_aArticleCats[$this->getId()] = $aRet;
    }

    /**
     * Returns query for article categories select
     *
     * @param string $sOXID     article id
     * @param bool   $blActCats select categories if all parents are active
     *
     * @return string
     */
    protected function _getSelectCatIds( $sOXID, $blActCats = false )
    {
        $sO2CView = $this->_getObjectViewName('oxobject2category');
        $sCatView = $this->_getObjectViewName('oxcategories');
        $sSelect =  "select oxobject2category.oxcatnid as oxcatnid from $sO2CView as oxobject2category left join $sCatView as oxcategories on oxcategories.oxid = oxobject2category.oxcatnid ";
        $sSelect .= 'where oxobject2category.oxobjectid='.oxDb::getDb()->quote($sOXID).' and oxcategories.oxid is not null and oxcategories.oxactive = 1 ';
        if ( $blActCats ) {
            $sSelect .= "and oxcategories.oxhidden = 0 and (select count(cats.oxid) from $sCatView as cats where cats.oxrootid = oxcategories.oxrootid and cats.oxleft < oxcategories.oxleft and cats.oxright > oxcategories.oxright and ( cats.oxhidden = 1 or cats.oxactive = 0 ) ) = 0 ";
        }
        $sSelect .= 'order by oxobject2category.oxtime ';
        return $sSelect;
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
    public function getVendor( $blShopCheck = true )
    {
        if ( ( $sVendorId = $this->getVendorId() ) ) {
            $oVendor = oxNew( 'oxvendor' );
        } elseif ( !$blShopCheck && $this->oxarticles__oxvendorid->value ) {
                $oVendor = oxNew( 'oxi18n' );
                $oVendor->init('oxvendor');
                $oVendor->setReadOnly( true );
            $sVendorId = $this->oxarticles__oxvendorid->value;
        }
        if ( $sVendorId && $oVendor->load( $sVendorId ) && $oVendor->oxvendor__oxactive->value ) {

            //@deprecated in v.4.5.7, since 2012-02-15; config option removed bug #0003385
            if ( !$this->getConfig()->getConfigParam( 'bl_perfLoadVendorTree' ) ) {
                $oVendor->setReadOnly( true );
            }
            return $oVendor;
        }
        return null;
    }

    /**
     * Returns article object vendor ID. Result is cached into self::$_aArticleVendors
     *
     * @param bool $blForceReload reloads id even if it is cached
     *
     * @return string
     */
    public function getVendorId( $blForceReload = false )
    {
        $sVendorId = false;
        if ( $this->oxarticles__oxvendorid->value ) {
                $sVendorId = $this->oxarticles__oxvendorid->value;

        }
        return $sVendorId;
    }

    /**
     * Returns article object Manufacturer ID. Result is cached into self::$_aArticleManufacturers
     *
     * @param bool $blForceReload reloads id even if it is cached
     *
     * @return string
     */
    public function getManufacturerId( $blForceReload = false )
    {
        $sManufacturerId = false;
        if ( $this->oxarticles__oxmanufacturerid->value ) {

                $sManufacturerId = $this->oxarticles__oxmanufacturerid->value;

        }
        return $sManufacturerId;
    }

    /**
     * Returns current article Manufacturer object. If $blShopCheck = false, then
     * Manufacturer blReadOnly parameter will be set to true. If Manufacturer is
     * not assigned to current shop
     *
     * @param bool $blShopCheck Set false if shop check is not required (default is true)
     *
     * @return object
     */
    public function getManufacturer( $blShopCheck = true )
    {
            $oManufacturer = oxNew( 'oxmanufacturer' );;
        if ( !( $sManufacturerId = $this->getManufacturerId() ) &&
             !$blShopCheck && $this->oxarticles__oxmanufacturerid->value ) {
            $oManufacturer->setReadOnly( true );
            $sManufacturerId = $this->oxarticles__oxmanufacturerid->value;
        }

        if ( $sManufacturerId && $oManufacturer->load( $sManufacturerId ) ) {
            if ( !$this->getConfig()->getConfigParam( 'bl_perfLoadManufacturerTree' ) ) {
                $oManufacturer->setReadOnly( true );
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
    public function inCategory( $sCatNid)
    {
        return in_array( $sCatNid, $this->getCategoryIds());
    }

    /**
     * Checks if article is assigned to passed category (even checks
     * if this category is "price category"). Returns true on success.
     *
     * @param string $sCatId category ID
     *
     * @return bool
     */
    public function isAssignedToCategory( $sCatId )
    {
        // variant handling
        $sOXID = $this->getId();
        if ( isset( $this->oxarticles__oxparentid->value) && $this->oxarticles__oxparentid->value) {
            $sOXID = $this->oxarticles__oxparentid->value;
        }

        $oDb = oxDb::getDb();
        $sSelect = $this->_generateSelectCatStr( $sOXID, $sCatId);
        $sOXID = $oDb->getOne( $sSelect );
        // article is assigned to passed category!
        if ( isset( $sOXID) && $sOXID) {
            return true;
        }

        // maybe this category is price category ?
        if ( $this->getConfig()->getConfigParam( 'bl_perfLoadPrice' ) && $this->_blLoadPrice ) {
            $dPriceFromTo = $this->getPrice()->getBruttoPrice();
            if ( $dPriceFromTo > 0) {
                $sSelect = $this->_generateSelectCatStr( $sOXID, $sCatId, $dPriceFromTo);
                $sOXID = $oDb->getOne( $sSelect );
                // article is assigned to passed category!
                if ( isset( $sOXID) && $sOXID) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns T price
     *
     * @return oxPrice
     */
    public function getTPrice()
    {
        if ( !$this->getConfig()->getConfigParam( 'bl_perfLoadPrice' ) || !$this->_blLoadPrice ) {
            return;
        }
        // return cached result, since oPrice is created ONLY in this function [or function of EQUAL level]
        if ( $this->_oTPrice !== null ) {
            return $this->_oTPrice;
        }

        $this->_oTPrice = oxNew( 'oxPrice' );
        $this->_oTPrice->setPrice( $this->oxarticles__oxtprice->value );

        $this->_applyVat( $this->_oTPrice, $this->getArticleVat() );
        $this->_applyCurrency( $this->_oTPrice );

        return $this->_oTPrice;
    }

    /**
     * Checks if discount should be skipped for this article in basket. Returns true if yes.
     *
     * @return bool
     */
    public function skipDiscounts()
    {
        // allready loaded skip discounts config
        if ( $this->_blSkipDiscounts !== null ) {
            return $this->_blSkipDiscounts;
        }

        if ( $this->oxarticles__oxskipdiscounts->value ) {
            return true;
        }


        $this->_blSkipDiscounts = false;
        if ( oxDiscountList::getInstance()->hasSkipDiscountCategories() ) {

            $oDb = oxDb::getDb();
            $sO2CView  = getViewName( 'oxobject2category', $this->getLanguage() );
            $sViewName = getViewName( 'oxcategories', $this->getLanguage() );
            $sSelect =  "select 1 from $sO2CView as $sO2CView left join {$sViewName} on {$sViewName}.oxid = $sO2CView.oxcatnid
                         where $sO2CView.oxobjectid=".$oDb->quote( $this->getId() )." and {$sViewName}.oxactive = 1 and {$sViewName}.oxskipdiscounts = '1' ";
            $this->_blSkipDiscounts = ( $oDb->getOne( $sSelect ) == 1 );
        }

        return $this->_blSkipDiscounts;
    }

    /**
     * Sets the current oxPrice object
     *
     * @param oxPrice $oPrice the new price object
     *
     * @return null
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
    public function getBasePrice( $dAmount = 1 )
    {
        // override this function if you want e.g. different prices
        // for diff. usergroups.

        // Performance
        $myConfig = $this->getConfig();
        if( !$myConfig->getConfigParam( 'bl_perfLoadPrice' ) || !$this->_blLoadPrice )
            return;

        // GroupPrice or DB price ajusted by AmountPrice
        $dPrice = $this->_getAmountPrice( $dAmount );


        return $dPrice;
    }

    /**
     * Calculates and returns price of article (adds taxes and discounts).
     *
     * @param double $dAmount article amount
     *
     * @return oxPrice
     */
    public function getPrice( $dAmount = 1 )
    {
        $myConfig = $this->getConfig();
        // Performance
        if ( !$myConfig->getConfigParam( 'bl_perfLoadPrice' ) || !$this->_blLoadPrice ) {
            return;
        }

        // return cached result, since oPrice is created ONLY in this function [or function of EQUAL level]
        if ( $dAmount != 1 || $this->_oPrice === null ) {
            $oPrice = oxNew( 'oxPrice' );

            // get base
            $oPrice->setPrice( $this->getBasePrice( $dAmount ) );

            // price handling
            if ( !$this->_blCalcPrice && $dAmount == 1 ) {
                return $this->_oPrice = $oPrice;
            }

            $this->_calculatePrice( $oPrice );
            if ( $dAmount != 1 ) {
                return $oPrice;
            }

            $this->_oPrice = $oPrice;
        }
        return $this->_oPrice;
    }

    /**
     * Calculates price of article (adds taxes, currency and discounts).
     *
     * @param oxPrice $oPrice price object
     * @param double  $dVat   vat value, optional, if passed, bypasses "bl_perfCalcVatOnlyForBasketOrder" config value
     *
     * @return oxPrice
     */
    protected function _calculatePrice( $oPrice, $dVat = null )
    {
        // apply VAT only if configuration requires it
        if ( isset( $dVat ) || !$this->getConfig()->getConfigParam( 'bl_perfCalcVatOnlyForBasketOrder' ) ) {
            $this->_applyVAT( $oPrice, isset( $dVat ) ? $dVat : $this->getArticleVat() );
        }

        // apply currency
        $this->_applyCurrency( $oPrice );
        // apply discounts
        if ( !$this->skipDiscounts() ) {
            $oDiscountList = oxDiscountList::getInstance();
            $oDiscountList->applyDiscounts( $oPrice, $oDiscountList->getArticleDiscounts( $this, $this->getArticleUser() ) );
        }

        return $oPrice;
    }

    /**
     * sets article user
     *
     * @param oxUser $oUser user to set
     *
     * @return null
     */
    public function setArticleUser($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * return article user
     *
     * @return oxUser
     */
    public function getArticleUser()
    {
        if ($this->_oUser) {
            return $this->_oUser;
        }
        return $this->getUser();
    }

    /**
     * Creates, calculates and returns oxprice object for basket product.
     *
     * @param double $dAmount  Amount
     * @param string $aSelList Selection list
     * @param object $oBasket  User shopping basket object
     *
     * @return oxPrice
     */
    public function getBasketPrice( $dAmount, $aSelList, $oBasket )
    {
        $oUser = $oBasket->getBasketUser();
        $this->setArticleUser( $oUser );

        $oBasketPrice = oxNew( 'oxPrice' );

        // get base price
        $dBasePrice = $this->getBasePrice( $dAmount );

        // applying select list price
        $dBasePrice = $this->_modifySelectListPrice( $dBasePrice, $aSelList );

        // setting price
        $oBasketPrice->setPrice( $dBasePrice );

        $dVat = oxVatSelector::getInstance()->getBasketItemVat( $this, $oBasket );
        $this->_calculatePrice( $oBasketPrice, $dVat );

        // returning final price object
        return $oBasketPrice;
    }

    /**
     * Applies discoutns which are supposed to be applied on amounts greater than zero.
     * Returns applied discounts.
     *
     * @param oxPrice $oPrice     Old article price
     * @param array   $aDiscounts Discount array
     * @param amount  $dAmount    Amount in basket
     *
     * @deprecated use oxDiscountList::applyBasketDiscounts() instead
     *
     * @return array
     */
    public function applyBasketDiscounts(oxPrice $oPrice, $aDiscounts, $dAmount = 1)
    {
        $oDiscountList = oxDiscountList::getInstance();
        return $oDiscountList->applyBasketDiscounts( $oPrice, $aDiscounts, $dAmount );
    }

    /**
     * Deletes record and other information related to this article such as images from DB,
     * also removes variants. Returns true on success.
     *
     * @param string $sOXID Article id
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }
        if ( !$sOXID ) {
            return false;
        }


        // #2339 delete first variants before deleting parent product
        $this->_deleteVariantRecords( $sOXID );
        $this->load( $sOXID );
        $this->_deletePics();
        $this->_onChangeResetCounts( $sOXID, $this->oxarticles__oxvendorid->value, $this->oxarticles__oxmanufacturerid->value );

        // delete self
        parent::delete( $sOXID );

        $rs = $this->_deleteRecords( $sOXID );

        oxSeoEncoderArticle::getInstance()->onDeleteArticle($this);

        $this->onChange( ACTION_DELETE, $sOXID, $this->oxarticles__oxparentid->value );

        return $rs->EOF;
    }

    /**
     * reduce article stock. return the affected amount
     *
     * @param double $dAmount              amount to reduce
     * @param bool   $blAllowNegativeStock are negative stocks allowed?
     *
     * @return double
     */
    public function reduceStock($dAmount, $blAllowNegativeStock = false)
    {
        $this->beforeUpdate();

        $iStockCount = $this->oxarticles__oxstock->value - $dAmount;
        if (!$blAllowNegativeStock && ($iStockCount < 0)) {
            $dAmount += $iStockCount;
            $iStockCount = 0;
        }
        $this->oxarticles__oxstock = new oxField($iStockCount);

        $oDb = oxDb::getDb();
        $oDb->execute( 'update oxarticles set oxarticles.oxstock = '.$oDb->quote( $iStockCount ).' where oxarticles.oxid = '.$oDb->quote( $this->getId() ) );
        $this->onChange( ACTION_UPDATE_STOCK );
        return $dAmount;
    }

    /**
     * Recursive function. Updates quantity of sold articles.
     * Return true on success
     *
     * @param double $dAmount Number of articles sold
     *
     * @return bool
     */
    public function updateSoldAmount( $dAmount = 0 )
    {
        if ( !$dAmount ) {
            return;
        }

        $this->beforeUpdate();

        // article is not variant - should be updated current amount
        if ( !$this->oxarticles__oxparentid->value ) {
            //updating by SQL query, due to wrong behaviour if saving article using not admin mode
            $dAmount = (double) $dAmount;
            $oDb = oxDb::getDb();
            $rs = $oDb->execute( "update oxarticles set oxarticles.oxsoldamount = oxarticles.oxsoldamount + $dAmount where oxarticles.oxid = ".$oDb->quote($this->oxarticles__oxid->value));
        } elseif ( $this->oxarticles__oxparentid->value) {
            // article is variant - should be updated this article parent amount
            $oUpdateArticle = oxNewArticle( $this->oxarticles__oxparentid->value );
            $oUpdateArticle->updateSoldAmount( $dAmount );
        }

        $this->onChange( ACTION_UPDATE );

        return $rs;
    }

    /**
     * Disables reminder functionality for article
     *
     * @return bool
     */
    public function disableReminder()
    {
        $oDb = oxDb::getDb();
        return $oDb->execute( "update oxarticles set oxarticles.oxremindactive = 2 where oxarticles.oxid = ".$oDb->quote($this->oxarticles__oxid->value));
    }

    /**
     * Makes sure that image values (oxpic1 - oxpic12, oxthumb, oxicon) are only base name by striping
     * any dir information and leave only original file name and, saves long description
     * (oxArticle::_saveArtLongDesc()) finally save the object using parent::save() method.
     *
     * @return bool
     */
    public function save()
    {
        // @deprecated since 20110821. folders are no more written, getters must be user for urls
        $this->oxarticles__oxthumb = new oxField( basename( $this->oxarticles__oxthumb->value ), oxField::T_RAW );
        $this->oxarticles__oxicon  = new oxField( basename( $this->oxarticles__oxicon->value ), oxField::T_RAW );
        $iPicCount = $this->getConfig()->getConfigParam( 'iPicCount' );
        for ( $i = 1; $i <= $iPicCount; $i++ ) {
            $sFieldName = 'oxarticles__oxpic' . $i;
            if ( isset( $this->$sFieldName ) ) {
                $this->_setFieldData( $sFieldName, basename( $this->$sFieldName->value ), oxField::T_RAW );
            }
        }
        // @end deprecated

        if ( ( $blRet = parent::save() ) ) {
            // saving long descrition
            $this->_saveArtLongDesc();
        }

        return $blRet;
    }

    /**
     * Changes article variant to parent article
     *
     * @return null
     */
    public function resetParent()
    {
        $sParentId = $this->oxarticles__oxparentid;
        $this->oxarticles__oxparentid = new oxField( '', oxField::T_RAW );
        $this->_blAllowEmptyParentId = true;
        $this->save();
        $this->_blAllowEmptyParentId = false;

        if ( $sParentId !== '' ) {
            $this->onChange( ACTION_UPDATE, null, $sParentId );
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
        $myConfig = $this->getConfig();

        //initialize
        $blMorePic = false;
        $aArtPics  = array();
        $aArtIcons = array();
        $iActPicId = 1;
        $sActPic = $this->getPictureUrl( $iActPicId );

        if ( oxConfig::getParameter( 'actpicid' ) ) {
            $iActPicId = oxConfig::getParameter('actpicid');
        }

        $oStr = getStr();
        $iCntr = 0;
        $iPicCount = $myConfig->getConfigParam( 'iPicCount' );
        $blCheckActivePicId = true;

        for ( $i = 1; $i <= $iPicCount; $i++) {
            $sPicVal = $this->getPictureUrl( $i );
            $sIcoVal = $this->getIconUrl( $i );
            if ( !$oStr->strstr($sIcoVal, 'nopic_ico.jpg') && !$oStr->strstr($sIcoVal, 'nopic.jpg') &&
                 !$oStr->strstr($sPicVal, 'nopic_ico.jpg') && !$oStr->strstr($sPicVal, 'nopic.jpg') ) {
                if ($iCntr) {
                    $blMorePic = true;
                }
                $aArtIcons[$i]= $sIcoVal;
                $aArtPics[$i]= $sPicVal;
                $iCntr++;

                if ($iActPicId == $i) {
                    $sActPic = $sPicVal;
                    $blCheckActivePicId = false;
                }

            } else if ( $blCheckActivePicId && $iActPicId <= $i) {
                // if picture is empty, setting active pic id to next
                // picture
                $iActPicId++;
            }
        }

        $blZoomPic  = false;
        $aZoomPics = array();
        $iZoomPicCount = $myConfig->getConfigParam( 'iPicCount' );

        for ( $j = 1,$c = 1; $j <= $iZoomPicCount; $j++) {
            $sVal = $this->getZoomPictureUrl($j);

            if ( $sVal && !$oStr->strstr($sVal, 'nopic.jpg')) {
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

        $aPicGallery = array('ActPicID' => $iActPicId,
                             'ActPic' => $sActPic,
                             'MorePics' => $blMorePic,
                             'Pics' => $aArtPics,
                             'Icons' => $aArtIcons,
                             'ZoomPic' => $blZoomPic,
                             'ZoomPics' => $aZoomPics);

        return $aPicGallery;
    }

    /**
     * This function is triggered whenever article is saved or deleted or after the stock is changed.
     * Originally we need to update the oxstock for possible article parent in case parent is not buyable
     * Plus you may want to extend this function to update some extended information.
     * Call oxArticle::onChange($sAction, $sOXID) with ID parameter as static method when changes are executed over SQL.
     * (or use module class instead of oxArticle if such exists)
     *
     * @param string $sAction   Action constant
     * @param string $sOXID     Article ID
     * @param string $sParentID Parent ID
     *
     * @return null
     */
    public function onChange($sAction = null, $sOXID = null, $sParentID = null)
    {
        $myConfig = $this->getConfig();

        if (!isset($sOXID)) {
            if ( $this->getId()) {
                $sOXID = $this->getId();
            }
            if (!isset ($sOXID)) {
                $sOXID = $this->oxarticles__oxid->value;
            }
            if ($this->oxarticles__oxparentid->value) {
                $sParentID = $this->oxarticles__oxparentid->value;
            }
        }
        if (!isset($sOXID)) {
            return;
        }

        //if (isset($sOXID) && !$myConfig->blVariantParentBuyable && $myConfig->blUseStock)
        if ( $myConfig->getConfigParam( 'blUseStock' ) ) {
            //if article has variants then updating oxvarstock field
            //getting parent id
            if (!isset($sParentID)) {
                $oDb = oxDb::getDb();
                $sQ = 'select oxparentid from oxarticles where oxid = '.$oDb->quote($sOXID);
                $sParentID = $oDb->getOne( $sQ );
            }
            //if we have parent id then update stock
            if ($sParentID) {
                $this->_onChangeUpdateStock($sParentID);
            }
        }
        //if we have parent id then update count
        //update count even if blUseStock is not active
        if ($sParentID) {
            $this->_onChangeUpdateVarCount($sParentID);
        }

        $sId = ( $sParentID ) ? $sParentID : $sOXID;
        $this->_onChangeUpdateMinVarPrice( $sId );

        // reseting articles count cache if stock has changed and some
        // articles goes offline (M:1448)
        if ( $sAction === ACTION_UPDATE_STOCK ) {
            $this->_onChangeStockResetCount( $sOXID );
        }

    }

    /**
     * Returns custom article VAT value if possible
     * By default value is taken from oxarticle__oxvat field
     *
     * @return double
     */
    public function getCustomVAT()
    {
        if ( isset($this->oxarticles__oxvat->value) ) {
            return $this->oxarticles__oxvat->value;
        }
    }

    /**
     * Checks if stock configuration allows to buy user chosen amount $dAmount
     *
     * @param double $dAmount         buyable amount
     * @param double $dArtStockAmount stock amount
     *
     * @return mixed
     */
    public function checkForStock( $dAmount, $dArtStockAmount = 0 )
    {
        $myConfig = $this->getConfig();
        if ( !$myConfig->getConfigParam( 'blUseStock' ) ) {
            return true;
        }

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        // fetching DB info as its up-to-date
        $sQ = 'select oxstock, oxstockflag from oxarticles where oxid = '.$oDb->quote( $this->getId() );
        $rs = $oDb->select( $sQ );

        $iOnStock   = 0;
        $iStockFlag = 0;
        if ( $rs !== false && $rs->recordCount() > 0 ) {
            $iOnStock   = $rs->fields['oxstock'] - $dArtStockAmount;
            $iStockFlag = $rs->fields['oxstockflag'];

            // dodger : fremdlager is also always considered as on stock
            if ( $iStockFlag == 1 || $iStockFlag == 4) {
                return true;
            }
            if ( !$myConfig->getConfigParam( 'blAllowUnevenAmounts' ) ) {
                $iOnStock = floor( $iOnStock );
            }
        }
        if ($this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )) {
            $iOnStock += $this->getSession()->getBasketReservations()->getReservedAmount($this->getId());
        }
        if ( $iOnStock >= $dAmount ) {
            return true;
        } else {
            if ( $iOnStock > 0 ) {
                return $iOnStock;
            } else {
                $oEx = oxNew( 'oxArticleInputException' );
                $oEx->setMessage( 'EXCEPTION_ARTICLE_ARTICELNOTBUYABLE' );
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
                return false;
            }
        }
    }


    /**
     * Get article long description
     *
     * @param string $sOxid Article ID
     *
     * @return object $oField field object
     *
     * @deprecated since 2012-02-13 in version 4.6.0; use getLongDescription()
     */
    public function getArticleLongDesc( $sOxid = null )
    {
        return $this->getLongDescription();
    }

    /**
     * Get article long description
     *
     * @return object $oField field object
     */
    public function getLongDescription()
    {
        if ( $this->_oLongDesc === null ) {
            // initializing
            $this->_oLongDesc = new oxField();


            // choosing which to get..
            $sOxid = $this->getId();
            $sViewName = getViewName( 'oxartextends', $this->getLanguage() );

            $oDb = oxDb::getDb();
            $sDbValue = $oDb->getOne( "select oxlongdesc from {$sViewName} where oxid = " . $oDb->quote( $sOxid ) );

            if ( $sDbValue != false ) {
                $this->_oLongDesc->setValue( $sDbValue, oxField::T_RAW );
            } elseif ( $this->oxarticles__oxparentid->value ) {
                if ( !$this->isAdmin() || $this->_blLoadParentData ) {
                    $this->_oLongDesc->setValue( $this->getParentArticle()->getLongDescription()->getRawValue(), oxField::T_RAW );
                }
            }
        }
        return $this->_oLongDesc;
    }

    /**
     * set given value to object's oxlongdesc - also prepare it (parse throug smarty)
     *
     * @param string $sDbValue value to set
     *
     * @deprecated since 2011.03.10
     *
     * @return null
     */
    protected function _setLongDesc( $sDbValue )
    {
        $this->setArticleLongDesc( $sDbValue );
    }

    /**
     * get long description, parsed through smarty. should only be used by exports or so.
     * In templates use [{oxeval var=$oProduct->getLongDescription()->getRawValue()}]
     *
     * @return string
     */
    public function getLongDesc()
    {
        return oxUtilsView::getInstance()->parseThroughSmarty( $this->getLongDescription()->getRawValue(), $this->getId().$this->getLanguage() );
    }

    /**
     * Save article long description to oxartext table
     *
     * @param string $sDesc      description to set
     * @param string $sOrigValue base shop value, will be stored in oxField->orignalValue [optional]
     *
     * @return null
     */
    public function setArticleLongDesc( $sDesc, $sOrigValue = null )
    {

        // setting current value
        $this->_oLongDesc = new oxField( $sDesc, oxField::T_RAW );

        // setting original value?
        //deprecated since 2012-02-13 in v.4.5.7
        if ( $sOrigValue ) {
            $this->_oLongDesc->orignalValue = $sOrigValue;
        }
    }

    /**
     * Loads and returns attribute list associated with this article
     *
     * @return object
     */
    public function getAttributes()
    {
        if ( $this->_oAttributeList === null ) {
            $this->_oAttributeList = oxNew( 'oxattributelist' );
            $this->_oAttributeList->loadAttributes( $this->getId() );
        }

        return $this->_oAttributeList;
    }

    /**
     * Appends article seo url with additional request parameters
     *
     * @param string $sAddParams additional parameters which needs to be added to product url
     * @param int    $iLang      language id
     *
     * @return null
     */
    public function appendLink( $sAddParams, $iLang = null )
    {
        if ( $sAddParams ) {
            if ( $iLang === null ) {
                $iLang = $this->getLanguage();
            }

            $this->_aSeoAddParams[$iLang]  = isset( $this->_aSeoAddParams[$iLang] ) ? $this->_aSeoAddParams[$iLang] . "&amp;" : "";
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
    public function getBaseSeoLink( $iLang, $blMain = false )
    {
        $oEncoder = oxSeoEncoderArticle::getInstance();
        if ( !$blMain ) {
            return $oEncoder->getArticleUrl( $this, $iLang, $this->getLinkType() );
        }
        return $oEncoder->getArticleMainUrl( $this, $iLang );
    }

    /**
     * Gets article link
     *
     * @param int  $iLang  language id [optional]
     * @param bool $blMain force to return main url [optional]
     *
     * @return string
     */
    public function getLink( $iLang = null, $blMain = false  )
    {
        if ( !oxUtils::getInstance()->seoIsActive() ) {
            return $this->getStdLink( $iLang );
        }

        if ( $iLang === null ) {
            $iLang = $this->getLanguage();
        }

        $iLinkType = $this->getLinkType();
        if ( !isset( $this->_aSeoUrls[$iLang][$iLinkType] ) ) {
            $this->_aSeoUrls[$iLang][$iLinkType] = $this->getBaseSeoLink( $iLang, $blMain );
        }

        $sUrl = $this->_aSeoUrls[$iLang][$iLinkType];
        if ( isset($this->_aSeoAddParams[$iLang])) {
            $sUrl .= ( ( strpos( $sUrl.$this->_aSeoAddParams[$iLang], '?' ) === false ) ? '?' : '&amp;' ).$this->_aSeoAddParams[$iLang];
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
    public function getMainLink( $iLang = null )
    {
        return $this->getLink( $iLang, true );
    }

    /**
     * Resets details link
     *
     * @param int $iType type of link to load
     *
     * @return null
     */
    public function setLinkType( $iType )
    {
        // resetting detaisl link, to force new
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
     * Appends article dynemic url with additional request parameters
     *
     * @param string $sAddParams additional parameters which needs to be added to product url
     * @param int    $iLang      language id
     *
     * @return null
     */
    public function appendStdLink( $sAddParams, $iLang = null )
    {
        if ( $sAddParams ) {
            if ( $iLang === null ) {
                $iLang = $this->getLanguage();
            }

            $this->_aStdAddParams[$iLang]  = isset( $this->_aStdAddParams[$iLang] ) ? $this->_aStdAddParams[$iLang] . "&amp;" : "";
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
    public function getBaseStdLink( $iLang, $blAddId = true, $blFull = true )
    {
        $sUrl = '';
        if ( $blFull ) {
            //always returns shop url, not admin
            $sUrl = $this->getConfig()->getShopUrl( $iLang, false );
        }

        $sUrl .= "index.php?cl=details" . ( $blAddId ? "&amp;anid=".$this->getId() : "" );
        return $sUrl . ( isset( $this->_aStdAddParams[$iLang] ) ? "&amp;". $this->_aStdAddParams[$iLang] : "" );
    }

    /**
     * Returns standard URL to product
     *
     * @param int   $iLang   required language. optional
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink( $iLang = null, $aParams = array() )
    {
        if ( $iLang === null ) {
            $iLang = $this->getLanguage();
        }

        if ( !isset( $this->_aStdUrls[$iLang] ) ) {
            $this->_aStdUrls[$iLang] = $this->getBaseStdLink( $iLang );
        }

        return oxUtilsUrl::getInstance()->processUrl( $this->_aStdUrls[$iLang], true, $aParams, $iLang );
    }

    /**
     * Returns standard product Tag URL
     *
     * @param string $sTag tag
     *
     * @return string
     */
    public function getStdTagLink( $sTag )
    {
        $sStdTagLink = $this->getConfig()->getShopHomeURL( $this->getLanguage(), false );
        return $sStdTagLink . "cl=details&amp;anid=".$this->getId()."&amp;listtype=tag&amp;searchtag=".rawurlencode( $sTag );
    }

    /**
     * Returns article tags
     *
     * @return string;
     */
    public function getTags()
    {
        $oDb = oxDb::getDb();
        $sViewName = getViewName( "oxartextends", $this->getLanguage() );
        $sQ = "select oxtags from {$sViewName} where oxid = ".$oDb->quote( $this->getId() );
        $oTagCloud = oxNew('oxtagcloud');
        return $oTagCloud->trimTags( $oDb->getOne( $sQ ) );
    }

    /**
     * Saves article tags
     *
     * @param string $sTags article tag
     *
     * @return bool
     */
    public function saveTags($sTags)
    {
        //do not allow derived update
        if ( !$this->allowDerivedUpdate() ) {
            return false;
        }


        $oTagCloud = oxNew( 'oxtagcloud' );
        $oTagCloud->resetTagCache($this->getLanguage());
        $sTags = oxDb::getInstance()->escapeString( $oTagCloud->prepareTags( $sTags ) );
        $oDb = oxDb::getDb();

        $sTable = getLangTableName( 'oxartextends', $this->getLanguage() );
        $sLangSuffix = oxLang::getInstance()->getLanguageTag($this->getLanguage());
        $sQ = "insert into {$sTable} (oxid, oxtags$sLangSuffix) value (".$oDb->quote( $this->getId() ).", '{$sTags}')
               on duplicate key update oxtags$sLangSuffix = '{$sTags}'";
        return $oDb->execute( $sQ );
    }

    /**
     * Adds tag
     *
     * @param string $sTag new tag
     *
     * @return bool
     */
    public function addTag($sTag)
    {
        $oDb = oxDb::getDb();

        $oTagCloud = oxNew('oxtagcloud');
        $oTagCloud->resetTagCache();
        $sTag = $oTagCloud->prepareTags($sTag);
        $sTagSeparator = $this->getConfig()->getConfigParam('sTagSeparator');

        $sTable = getLangTableName( 'oxartextends', $this->getLanguage() );
        $sLangSuffix = oxLang::getInstance()->getLanguageTag($this->getLanguage());
        if ( $oDb->getOne( "select {$sTable}.OXTAGS$sLangSuffix from {$sTable} where {$sTable}.OXID = ".$oDb->quote( $this->getId() ) ) ) {
            $sTailTag = $sTagSeparator . $sTag;
        } else {
            $sTailTag = $sTag;
        }

        $sTag = oxDb::getInstance()->escapeString($sTag);
        $sTailTag = oxDb::getInstance()->escapeString($sTailTag);

        $sTag = oxDb::getInstance()->escapeString($sTag);
        $sTailTag = oxDb::getInstance()->escapeString($sTailTag);

        $sQ = "insert into {$sTable} ( {$sTable}.OXID, {$sTable}.OXTAGS$sLangSuffix) values (".$oDb->quote( $this->getId() ).", '{$sTag}')
                       ON DUPLICATE KEY update {$sTable}.OXTAGS$sLangSuffix = CONCAT(TRIM({$sTable}.OXTAGS$sLangSuffix), '$sTailTag') ";

        return $oDb->execute( $sQ );
    }

    /**
     * Return article media URL
     *
     * @return array
     */
    public function getMediaUrls()
    {
        if ( $this->_aMediaUrls === null ) {
            $this->_aMediaUrls = oxNew("oxlist");
            $this->_aMediaUrls->init("oxmediaurl");
            $this->_aMediaUrls->getBaseObject()->setLanguage( $this->getLanguage() );

            $sViewName = getViewName( "oxmediaurls", $this->getLanguage() );
            $sQ = "select * from {$sViewName} where oxobjectid = '".$this->getId()."'";
            $this->_aMediaUrls->selectString($sQ);
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
            if ( $this->getConfig()->getConfigParam( 'bl_perfLoadSelectLists' ) && $this->getConfig()->getConfigParam( 'bl_perfLoadSelectListsInAList' ) ) {
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
        if ( $this->_sMoreDetailLink == null ) {

            // and assign special article values
            $this->_sMoreDetailLink = $this->getConfig()->getShopHomeURL() . 'cl=moredetails';

            // not always it is okey, as not all the time active category is the same as primary article cat.
            if ( $sActCat = oxConfig::getParameter( 'cnid' ) ) {
                $this->_sMoreDetailLink .= '&amp;cnid='.$sActCat;
            }
            $this->_sMoreDetailLink .= '&amp;anid='.$this->getId();
            $this->_sMoreDetailLink = $this->_sMoreDetailLink;
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
        if ( $this->_sToBasketLink == null ) {
            $myConfig = $this->getConfig();

            if ( oxUtils::getInstance()->isSearchEngine() ) {
                $this->_sToBasketLink = $this->getLink();
            } else {
                // and assign special article values
                $this->_sToBasketLink = $myConfig->getShopHomeURL();

                // override some classes as these should never showup
                $sActClass = oxConfig::getParameter( 'cl' );
                if ( $sActClass == 'thankyou') {
                    $sActClass = 'basket';
                }
                $this->_sToBasketLink .= 'cl='.$sActClass;

                // this is not very correct
                if ( $sActCat = oxConfig::getParameter( 'cnid' ) ) {
                    $this->_sToBasketLink .= '&amp;cnid='.$sActCat;
                }

                $this->_sToBasketLink .= '&amp;fnc=tobasket&amp;aid='.$this->getId().'&amp;anid='.$this->getId();

                if ( $sTpl = basename( oxConfig::getParameter( 'tpl' ) ) ) {
                    $this->_sToBasketLink .= '&amp;tpl='.$sTpl;
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
     * Returns formated delivery date. If the date is not set ('0000-00-00') returns false.
     *
     * @return string | bool
     */
    public function getDeliveryDate()
    {
        if ( $this->oxarticles__oxdelivery->value != '0000-00-00') {
            return oxUtilsDate::getInstance()->formatDBDate( $this->oxarticles__oxdelivery->value);
        }
        return false;
    }

    /**
     * Returns rounded T price.
     *
     * @return double | bool
     */
    public function getFTPrice()
    {
        if ( $oPrice = $this->getTPrice() ) {
            if ( $oPrice->getBruttoPrice() ) {
                return oxLang::getInstance()->formatCurrency( oxUtils::getInstance()->fRound($oPrice->getBruttoPrice()));
            }
        }
    }

    /**
     * Returns formated product's price.
     *
     * @return double
     */
    public function getFPrice()
    {
        if ( $oPrice = $this->getPrice() ) {
            return $this->getPriceFromPrefix().oxLang::getInstance()->formatCurrency( $oPrice->getBruttoPrice() );
        }
    }

    /**
     * Resets oxremindactive status.
     * If remindActive status is 2, reminder is already sent.
     *
     * @return null
     */
    public function resetRemindStatus()
    {
        if ( $this->oxarticles__oxremindactive->value == 2 &&
            $this->oxarticles__oxremindamount->value <= $this->oxarticles__oxstock->value ) {
            $this->oxarticles__oxremindactive->value = 1;
        }
    }

    /**
     * Returns formated product's NETTO price.
     *
     * @return double
     */
    public function getFNetPrice()
    {
        if ( $oPrice = $this->getPrice() ) {
            return oxLang::getInstance()->formatCurrency( $oPrice->getNettoPrice() );
        }
    }

    /**
     * Returns formated price per unit (oxarticle::_assignPrices())
     *
     * @return string
     */
    public function getPricePerUnit()
    {
        return $this->_fPricePerUnit;
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
     *
     * @return null
     */
    public function setBuyableState( $blBuyable = false )
    {
        $this->_blNotBuyable = !$blBuyable;
    }

    /**
     * Returns variant lists of current product
     *
     * @deprecated
     * @see oxArticle::getVariants
     *
     * @return object
     */
    public function getVariantList()
    {
        return $this->getVariants();
    }

    /**
     * Sets selectlists of current product
     *
     * @param array $aSelList selectlist
     *
     * @return object
     */
    public function setSelectlist( $aSelList )
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
    public function getPictureUrl( $iIndex = 1 )
    {
        if ( $iIndex ) {
            $sImgName = false;
            if ( !$this->_isFieldEmpty( "oxarticles__oxpic".$iIndex ) ) {
                $sImgName = basename( $this->{"oxarticles__oxpic$iIndex"}->value );
            }

            $sSize = $this->getConfig()->getConfigParam( 'aDetailImageSizes' );
            return oxPictureHandler::getInstance()->getProductPicUrl( "product/{$iIndex}/", $sImgName, $sSize, 'oxpic'.$iIndex );
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
    public function getIconUrl( $iIndex = 0 )
    {
        $sImgName = false;
        $sDirname = "product/1/";
        if ( $iIndex && !$this->_isFieldEmpty( "oxarticles__oxpic{$iIndex}" ) ) {
            $sImgName = basename( $this->{"oxarticles__oxpic$iIndex"}->value );
            $sDirname = "product/{$iIndex}/";
        } elseif ( !$this->_isFieldEmpty( "oxarticles__oxicon" ) ) {
            $sImgName = basename( $this->oxarticles__oxicon->value );
            $sDirname = "product/icon/";
        } elseif ( !$this->_isFieldEmpty( "oxarticles__oxpic1" ) ) {
            $sImgName = basename( $this->oxarticles__oxpic1->value );
        }

        $sSize = $this->getConfig()->getConfigParam( 'sIconsize' );
        return oxPictureHandler::getInstance()->getProductPicUrl( $sDirname, $sImgName, $sSize, $iIndex );
    }

    /**
     * Returns article thumbnail picture url
     *
     * @param bool $bSsl wethere to force SSL
     *
     * @return string
     */
    public function getThumbnailUrl( $bSsl = null )
    {
        $sImgName = false;
        $sDirname = "product/1/";
        if ( !$this->_isFieldEmpty( "oxarticles__oxthumb" ) ) {
            $sImgName = basename( $this->oxarticles__oxthumb->value );
            $sDirname = "product/thumb/";
        } elseif ( !$this->_isFieldEmpty( "oxarticles__oxpic1" ) ) {
            $sImgName = basename( $this->oxarticles__oxpic1->value );
        }

        $sSize = $this->getConfig()->getConfigParam( 'sThumbnailsize' );
        return oxPictureHandler::getInstance()->getProductPicUrl( $sDirname, $sImgName, $sSize, 0, $bSsl );
    }

    /**
     * Returns article zoom picture url
     *
     * @param int $iIndex picture index
     *
     * @return string
     */
    public function getZoomPictureUrl( $iIndex = '' )
    {
        $iIndex = (int) $iIndex;
        if ( $iIndex > 0 && !$this->_isFieldEmpty( "oxarticles__oxpic".$iIndex ) ) {
            $sImgName = basename( $this->{"oxarticles__oxpic".$iIndex}->value );
            $sSize = $this->getConfig()->getConfigParam( "sZoomImageSize" );
            return oxPictureHandler::getInstance()->getProductPicUrl( "product/{$iIndex}/", $sImgName, $sSize, 'oxpic'.$iIndex );
        }
    }

    /**
     * Returns article file url
     *
     * @return string
     */
    public function getFileUrl()
    {
        return $this->getConfig()->getPictureUrl( 'media/' );
    }

    /**
     * Returns string prefix (like "ab") if needed or empty string.
     *
     * @return string
     */
    public function getPriceFromPrefix()
    {
        $sPricePrefix = '';
        if ( $this->_blIsRangePrice) {
            $sPricePrefix = oxLang::getInstance()->translateString('priceFrom').' ';
        }

        return $sPricePrefix;
    }

    /**
     * inserts article long description to artextends table
     *
     * @return null
     */
    protected function _saveArtLongDesc()
    {
        $myConfig = $this->getConfig();
        $sShopId = $myConfig->getShopID();
        if (in_array("oxlongdesc", $this->_aSkipSaveFields)) {
            return;
        }

        if ($this->_blEmployMultilanguage) {
            $sValue = $this->getLongDescription()->getRawValue();
            if ( $sValue !== null ) {
                $oArtExt = oxNew('oxI18n');
                $oArtExt->init('oxartextends');
                $oArtExt->setLanguage((int) $this->getLanguage());
                if (!$oArtExt->load($this->getId())) {
                    $oArtExt->setId($this->getId());
                }
                $oArtExt->oxartextends__oxlongdesc = new oxField($sValue, oxField::T_RAW);
                $oArtExt->save();
            }
        } else {
            $oArtExt = oxNew('oxI18n');
            $oArtExt->setEnableMultilang(false);
            $oArtExt->init('oxartextends');
            $aObjFields = $oArtExt->_getAllFields(true);
            if (!$oArtExt->load($this->getId())) {
                $oArtExt->setId($this->getId());
            }

            foreach ($aObjFields as $sKey => $sValue ) {
                if ( preg_match('/^oxlongdesc(_(\d{1,2}))?$/', $sKey) ) {
                    $sField = $this->_getFieldLongName($sKey);

                    if (isset($this->$sField)) {
                        $sLongDesc = null;
                        if ($this->$sField instanceof oxField) {
                            $sLongDesc = $this->$sField->getRawValue();
                        } elseif (is_object($this->$sField)) {
                            $sLongDesc = $this->$sField->value;
                        }
                        if (isset($sLongDesc)) {
                            $sAEField = $oArtExt->_getFieldLongName($sKey);
                            $oArtExt->$sAEField = new oxField($sLongDesc, oxField::T_RAW);
                        }
                    }
                }
            }
            $oArtExt->save();
        }
    }

    /**
     * Removes object data fields (oxarticles__oxtimestamp, oxarticles__oxparentid, oxarticles__oxinsert).
     *
     * @return null
     */
    protected function _skipSaveFields()
    {
        $myConfig = $this->getConfig();

        $this->_aSkipSaveFields = array();

        $this->_aSkipSaveFields[] = 'oxtimestamp';
       // $this->_aSkipSaveFields[] = 'oxlongdesc';
        $this->_aSkipSaveFields[] = 'oxinsert';

        if ( !$this->_blAllowEmptyParentId && (!isset( $this->oxarticles__oxparentid->value) || $this->oxarticles__oxparentid->value == '') ) {
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
    protected function _mergeDiscounts( $aDiscounts, $aItemDiscounts)
    {
        foreach ( $aItemDiscounts as $sKey => $oDiscount ) {
            // add prices of the same discounts
            if ( array_key_exists ($sKey, $aDiscounts) ) {
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
    protected function _getGroupPrice()
    {
        $dPrice = $this->oxarticles__oxprice->value;

        $oUser = $this->getArticleUser();
        if ( $oUser ) {
            if ( $oUser->inGroup( 'oxidpricea' ) ) {
                $dPrice = $this->oxarticles__oxpricea->value;
            } elseif ( $oUser->inGroup( 'oxidpriceb' ) ) {
                $dPrice = $this->oxarticles__oxpriceb->value;
            } elseif ( $oUser->inGroup( 'oxidpricec' ) ) {
                $dPrice = $this->oxarticles__oxpricec->value;
            }
        }

        // #1437/1436C - added config option, and check for zero A,B,C price values
        if ( $this->getConfig()->getConfigParam( 'blOverrideZeroABCPrices' ) && (double) $dPrice == 0 ) {
            $dPrice = $this->oxarticles__oxprice->value;
        }

        return $dPrice;
    }

    /**
     * Modifies article price depending on given amount.
     * Takes data from oxprice2article table.
     *
     * @param double $dAmount Basket amount
     *
     * @return bool | null
     */
    protected function _getAmountPrice($dAmount = 1)
    {
        $myConfig = $this->getConfig();

        startProfile( "_getAmountPrice" );

        $dPrice = $this->_getGroupPrice();
        $oAmtPrices = $this->_getAmountPriceList();
        foreach ($oAmtPrices as $oAmPrice) {
            if ($oAmPrice->oxprice2article__oxamount->value <= $dAmount
                    && $dAmount <= $oAmPrice->oxprice2article__oxamountto->value
                    && $dPrice > $oAmPrice->oxprice2article__oxaddabs->value ) {
                $dPrice = $oAmPrice->oxprice2article__oxaddabs->value;
            }
        }

        stopProfile( "_getAmountPrice" );
        return $dPrice;
    }

    /**
     * Modifies article price according to selected select list value
     *
     * @param double $dPrice      Modifyable price
     * @param array  $aChosenList Selection list array
     *
     * @return double
     */
    protected function _modifySelectListPrice( $dPrice, $aChosenList = null )
    {
        $myConfig = $this->getConfig();
        // #690
        if ( $myConfig->getConfigParam( 'bl_perfLoadSelectLists' ) && $myConfig->getConfigParam( 'bl_perfUseSelectlistPrice' ) ) {

            $aSelLists = $this->getSelectLists();

            foreach ( $aSelLists as $key => $aSel) {
                if ( isset( $aChosenList[$key]) && isset($aSel[$aChosenList[$key]] ) ) {
                    $oSel = $aSel[$aChosenList[$key]];
                    if ( $oSel->priceUnit =='abs' ) {
                        $dPrice += $oSel->price;
                    } elseif ( $oSel->priceUnit =='%' ) {
                        $dPrice += oxPrice::percent( $dPrice, $oSel->price );
                    }
                }
            }
        }
        return $dPrice;
    }


    /**
     * Fills amount price list object and sets amount price for article object
     *
     * @param object $oAmPriceList Amount (staffel) price list
     *
     * @return object
     */
    protected function _fillAmountPriceList($oAmPriceList)
    {
        $myConfig = $this->getConfig();
        $myUtils  = oxUtils::getInstance();

        //modifying price
        $oCur = $myConfig->getActShopCurrencyObject();

        $oUser = $this->getArticleUser();

        $oDiscountList = oxDiscountList::getInstance();
        $aDiscountList = $oDiscountList->getArticleDiscounts( $this, $oUser );

        $oLowestPrice = null;

        $dBasePrice = $this->_getGroupPrice();
        $oLang = oxLang::getInstance();

        $dArticleVat = null;
        if ( !$myConfig->getConfigParam( 'bl_perfCalcVatOnlyForBasketOrder' ) ) {
            $dArticleVat = $this->getArticleVat();
        }

        // trying to find lowest price value
        foreach ($oAmPriceList as $sId => $oItem) {
            $oItemPrice = oxNew( 'oxprice' );
            if ( $oItem->oxprice2article__oxaddabs->value) {
                $oItemPrice->setPrice( $oItem->oxprice2article__oxaddabs->value );
                $oDiscountList->applyDiscounts( $oItemPrice, $aDiscountList );
                $this->_applyCurrency( $oItemPrice, $oCur );
            } else {
                $oItemPrice->setPrice( $dBasePrice );
                $oItemPrice->subtractPercent( $oItem->oxprice2article__oxaddperc->value );
            }

            if (isset($dArticleVat)) {
                $this->_applyVAT($oItemPrice, $dArticleVat);
            }

            if (!$oLowestPrice) {
                $oLowestPrice = $oItemPrice;
            } elseif ($oLowestPrice->getBruttoPrice() > $oItemPrice->getBruttoPrice()) {
                $oLowestPrice = $oItemPrice;
            }

            $oAmPriceList[$sId]->fnetprice  = $oLang->formatCurrency( $myUtils->fRound($oItemPrice->getNettoPrice(), $oCur ) );
            $oAmPriceList[$sId]->fbrutprice = $oLang->formatCurrency( $myUtils->fRound($oItemPrice->getBruttoPrice(), $oCur ) );
        }

        $this->_dAmountPrice = $myUtils->fRound( $oLowestPrice->getBruttoPrice() );
        return $oAmPriceList;
    }

    /**
     * Collects and returns article variants ids.
     *
     * @return array
     */
    protected function _getVariantsIds()
    {
        $aSelect = array();
        if ( ( $sId = $this->getId() ) ) {
            $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
            $sQ = "select oxid from " . $this->getViewName( true ) . " where oxparentid = ".$oDb->quote( $sId )." and " .
                   $this->getSqlActiveSnippet( true ) . " order by oxsort";
            $oRs = $oDb->select( $sQ );
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while (!$oRs->EOF) {
                    $aSelect[] = reset( $oRs->fields );
                    $oRs->moveNext();
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
            $this->_dArticleVat = oxVatSelector::getInstance()->getArticleVat( $this );
        }
        return $this->_dArticleVat;
    }

    /**
     * Applies VAT to article
     *
     * @param oxPrice $oPrice Price object
     * @param double  $dVat   VAT percent
     *
     * @return null
     */
    protected function _applyVAT( oxPrice $oPrice, $dVat )
    {
        startProfile(__FUNCTION__);
        $oPrice->setVAT( $dVat );
        if ( ($dVat = oxVatSelector::getInstance()->getArticleUserVat($this)) !== false ) {
            $oPrice->setUserVat( $dVat );
        }
        stopProfile(__FUNCTION__);
    }

    /**
     * apply article and article use
     *
     * @param oxPrice $oPrice target price
     *
     * @return null
     */
    public function applyVats( oxPrice $oPrice )
    {
        $this->_applyVAT($oPrice, $this->getArticleVat() );
    }

    /**
     * Applies discounts which should be applied in general case (for 0 amount)
     *
     * @param oxprice $oPrice     Price object
     * @param array   $aDiscounts Discount list
     *
     * @deprecated use oxDiscountList::applyDiscounts() instead
     *
     * @return null
     */
    protected function _applyDiscounts( $oPrice, $aDiscounts )
    {
        $oDiscountList = oxDiscountList::getInstance();
        $oDiscountList->applyDiscounts( $oPrice, $aDiscounts );
    }

    /**
     * Applies discounts which should be applied in general case (for 0 amount)
     *
     * @param oxprice $oPrice Price object
     *
     * @return null
     */
    public function applyDiscountsForVariant( $oPrice )
    {
        // apply discounts
        if ( !$this->skipDiscounts() ) {
            $oDiscountList = oxDiscountList::getInstance();
            $oDiscountList->applyDiscounts( $oPrice, $oDiscountList->getArticleDiscounts( $this, $this->getArticleUser() ) );
        }
    }

    /**
     * Applies currency factor
     *
     * @param oxPrice $oPrice Price object
     * @param object  $oCur   Currency object
     *
     * @return null
     */
    protected function _applyCurrency(oxPrice $oPrice, $oCur = null )
    {
        if ( !$oCur ) {
            $oCur = $this->getConfig()->getActShopCurrencyObject();
        }

        $oPrice->multiply($oCur->rate);
    }


    /**
     * gets attribs string
     *
     * @param string &$sAttribs Attribute selection snippet
     * @param int    &$iCnt     The number of selected attributes
     *
     * @return null;
     */
    protected function _getAttribsString(&$sAttribs, &$iCnt)
    {
        // we do not use lists here as we dont need this overhead right now
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sSelect =  'select oxattrid from oxobject2attribute where oxobject2attribute.oxobjectid='.$oDb->quote( $this->getId() );
        $sAttribs = '';
        $blSep = false;
        $rs = $oDb->select( $sSelect);
        $iCnt = 0;
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                if ( $blSep) {
                    $sAttribs .= ' or ';
                }
                $sAttribs .= 't1.oxattrid = '.$oDb->quote($rs->fields['oxattrid']).' ';
                $blSep = true;
                $iCnt++;
                $rs->moveNext();
            }
        }
    }

    /**
     * Gets similar list.
     *
     * @param string $sAttribs Attribute selection snippet
     * @param int    $iCnt     Similar list article count
     *
     * @return array
     */
    protected function _getSimList($sAttribs, $iCnt)
    {
        $myConfig = $this->getConfig();
        $oDb      = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        // #523A
        $iAttrPercent = $myConfig->getConfigParam( 'iAttributesPercent' )/100;
        // 70% same attributes
        if ( !$iAttrPercent || $iAttrPercent < 0 || $iAttrPercent > 1) {
            $iAttrPercent = 0.70;
        }
        // #1137V iAttributesPercent = 100 doesnt work
        $iHitMin = ceil( $iCnt * $iAttrPercent );

        // we do not use lists here as we dont need this overhead right now
        $aList= array();
        $sSelect =  "select oxobjectid, count(*) as cnt from oxobject2attribute as t1 where
                    ( $sAttribs )
                    and t1.oxobjectid != ".$oDb->quote( $this->oxarticles__oxid->value )."
                    group by t1.oxobjectid having count(*) >= $iHitMin ";

        $rs = $oDb->selectLimit( $sSelect, 20, 0 );
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $oTemp = new stdClass();    // #663
                $oTemp->cnt = $rs->fields['cnt'];
                $oTemp->id  = $rs->fields['oxobjectid'];
                $aList[] = $oTemp;
                $rs->moveNext();
            }
        }
        return $aList;
    }

    /**
     * Generates search string for similar list.
     *
     * @param string $sArticleTable Article table name
     * @param array  $aList         A list of original articles
     *
     * @return string
     */
    protected function _generateSimListSearchStr($sArticleTable, $aList)
    {
        $myConfig = $this->getConfig();
        $sFieldList = $this->getSelectFields();
        $sSearch = "select $sFieldList from $sArticleTable where ".$this->getSqlActiveSnippet()."  and $sArticleTable.oxissearch = 1 and $sArticleTable.oxid in ( ";
        $blSep = false;
        $iCnt = 0;
        $oDb = oxDb::getDb();
        foreach ( $aList as $oTemp) {
            if ( $blSep) {
                $sSearch .= ',';
            }
            $sSearch .= $oDb->quote($oTemp->id);
            $blSep = true;
            if ( $iCnt >= $myConfig->getConfigParam( 'iNrofSimilarArticles' ) ) {
                break;
            }
            $iCnt++;
        }

        //#1741T
        //$sSearch .= ") and $sArticleTable.oxparentid = '' ";
        $sSearch .= ') ';

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
    protected function _generateSearchStr($sOXID, $blSearchPriceCat = false )
    {

        $sCatView = getViewName( 'oxcategories', $this->getLanguage() );
        $sO2CView = getViewName( 'oxobject2category' );

        // we do not use lists here as we dont need this overhead right now
        if ( !$blSearchPriceCat ) {
            $sSelect  = "select {$sCatView}.* from {$sO2CView} as oxobject2category left join {$sCatView} on
                         {$sCatView}.oxid = oxobject2category.oxcatnid
                         where oxobject2category.oxobjectid=".oxDb::getDb()->quote($sOXID)." and {$sCatView}.oxid is not null ";
        } else {
            $sSelect  = "select {$sCatView}.* from {$sCatView} where
                         '{$this->oxarticles__oxprice->value}' >= {$sCatView}.oxpricefrom and
                         '{$this->oxarticles__oxprice->value}' <= {$sCatView}.oxpriceto ";
        }
        return $sSelect;
    }

    /**
     * Generates SQL select string for getCustomerAlsoBoughtThisProduct
     *
     * @return string
     */
    protected function _generateSearchStrForCustomerBought()
    {
        $sArtTable = $this->getViewName();
        $sOrderArtTable = getViewName( 'oxorderarticles' );

        // fetching filter params
        $sIn = " '{$this->oxarticles__oxid->value}' ";
        if ( $this->oxarticles__oxparentid->value ) {

            // adding article parent
            $sIn .= ", '{$this->oxarticles__oxparentid->value}' ";
            $sParentIdForVariants = $this->oxarticles__oxparentid->value;

        } else {
            $sParentIdForVariants = $this->getId();
        }

        // adding variants
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oRs = $oDb->select( "select oxid from {$sArtTable} where oxparentid = ".$oDb->quote($sParentIdForVariants)." and oxid != ".$oDb->quote($this->oxarticles__oxid->value) );
        if ( $oRs != false && $oRs->recordCount() > 0) {
            while ( !$oRs->EOF ) {
                $sIn .= ", ".$oDb->quote(current( $oRs->fields ))." ";
                $oRs->moveNext();
            }
        }

        $iLimit = (int) $this->getConfig()->getConfigParam( 'iNrofCustomerWhoArticles' );
        $iLimit = $iLimit?( $iLimit * 10 ): 50;

        // building sql (optimized)
        $sQ = "select distinct {$sArtTable}.* from (
                   select d.oxorderid as suborderid from {$sOrderArtTable} as d use index ( oxartid ) where d.oxartid in ( {$sIn} ) limit {$iLimit}
               ) as suborder
               left join {$sOrderArtTable} force index ( oxorderid ) on suborder.suborderid = {$sOrderArtTable}.oxorderid
               left join {$sArtTable} on {$sArtTable}.oxid = {$sOrderArtTable}.oxartid
               where {$sArtTable}.oxid not in ( {$sIn} )
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' ) and ".$this->getSqlActiveSnippet();

        /* non optimized, but could be used if index forcing is not supported
        // building sql
        $sQ = "select distinct {$sArtTable}.* from {$sOrderArtTable}, {$sArtTable} where {$sOrderArtTable}.oxorderid in (
                   select {$sOrderArtTable}.oxorderid from {$sOrderArtTable} where {$sOrderArtTable}.oxartid in ( {$sIn} )
               ) and {$sArtTable}.oxid = {$sOrderArtTable}.oxartid and {$sArtTable}.oxid not in ( {$sIn} )
               and ( {$sArtTable}.oxissearch = 1 or {$sArtTable}.oxparentid <> '' )
               and ".$this->getSqlActiveSnippet();
        */

        return $sQ;
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
    protected function _generateSelectCatStr($sOXID, $sCatId, $dPriceFromTo = false)
    {
        $sCategoryView = getViewName('oxcategories');
        $sO2CView = getViewName('oxobject2category');

        $oDb    = oxDb::getDb();
        $sOXID  = $oDb->quote($sOXID);
        $sCatId = $oDb->quote($sCatId);

        if (!$dPriceFromTo) {
            $sSelect  = "select oxobject2category.oxcatnid from $sO2CView as oxobject2category ";
            $sSelect .= "left join $sCategoryView as oxcategories on oxcategories.oxid = oxobject2category.oxcatnid ";
            $sSelect .= "where oxobject2category.oxcatnid=$sCatId and oxobject2category.oxobjectid=$sOXID ";
            $sSelect .= "and oxcategories.oxactive = 1 order by oxobject2category.oxtime ";
        } else {
            $dPriceFromTo = $oDb->quote($dPriceFromTo);
            $sSelect  = "select oxcategories.oxid from $sCategoryView as oxcategories where ";
            $sSelect .= "oxcategories.oxid=$sCatId and $dPriceFromTo >= oxcategories.oxpricefrom and ";
            $sSelect .= "$dPriceFromTo <= oxcategories.oxpriceto ";
        }
        return $sSelect;
    }

    /**
     * Collecting assigned to article amount-price list
     *
     * @return oxList
     */
    protected function _getAmountPriceList()
    {
        if ( $this->_oAmountPriceList === null ) {
            $this->_oAmountPriceList = array();
            if ( !$this->skipDiscounts() ) {
                $myConfig = $this->getConfig();
                $sArtId   = $this->getId();

                // #1690C - Scale prices and variants
                if ( !$this->isAdmin() && $myConfig->getConfigParam( 'blVariantInheritAmountPrice' ) && $this->oxarticles__oxparentid->value ) {
                    $sArtId = $this->oxarticles__oxparentid->value;
                }

                //collecting assigned to article amount-price list
                $oAmPriceList = oxNew( 'oxlist' );
                $oAmPriceList->init( 'oxbase', 'oxprice2article' );

                $sShopID = $myConfig->getShopID();
                if ( $myConfig->getConfigParam( 'blMallInterchangeArticles' ) ) {
                    $sShopSelect = '1';
                } else {
                    $sShopSelect = " oxshopid =  '$sShopID' ";
                }

                $oAmPriceList->selectString( "select * from oxprice2article where oxartid = " . oxDb::getDb()->quote( $sArtId ) . " and $sShopSelect order by oxamount ");

                // prepare abs prices if currently having percentages
                $oBasePrice = $this->_getGroupPrice();
                foreach ( $oAmPriceList as $oAmPrice ) {
                    if ( $oAmPrice->oxprice2article__oxaddperc->value ) {
                        $oAmPrice->oxprice2article__oxaddabs = new oxField(oxPrice::percent( $oBasePrice, 100 - $oAmPrice->oxprice2article__oxaddperc->value ), oxField::T_RAW );
                    }
                }

                $this->_oAmountPriceList = $oAmPriceList;
            }
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
    protected function _isFieldEmpty( $sFieldName )
    {
        $mValue = $this->$sFieldName->value;

        if ( is_null( $mValue ) ) {
            return true;
        }

        if ( $mValue === '' ) {
            return true;
        }

        // certain fields with zero value treat as empty
        $aZeroValueFields = array('oxarticles__oxprice', 'oxarticles__oxvat', 'oxarticles__oxunitquantity');

        if (!$mValue && in_array( $sFieldName, $aZeroValueFields ) ) {
            return true;
        }


        if (!strcmp($mValue, '0000-00-00 00:00:00') || !strcmp($mValue, '0000-00-00')) {
            return true;
        }

        $sFieldName = strtolower($sFieldName);

        if ( $sFieldName == 'oxarticles__oxicon' && ( strpos($mValue, "nopic_ico.jpg") !== false || strpos($mValue, "nopic.jpg") !== false ) ) {
            return true;
        }

        if ( strpos($mValue, "nopic.jpg") !== false && ($sFieldName == 'oxarticles__oxthumb' || substr($sFieldName, 0, 17) == 'oxarticles__oxpic' || substr($sFieldName, 0, 18) == 'oxarticles__oxzoom') ) {
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

        // assigning only theese which parent article has
        if ( $oParentArticle->$sCopyFieldName != null ) {

            // only overwrite database values
            if ( substr( $sCopyFieldName, 0, 12) != 'oxarticles__') {
                return;
            }

            //do not copy certain fields
            if (in_array($sCopyFieldName, $this->_aNonCopyParentFields)) {
                return;
            }

            //COPY THE VALUE
            // assigning images from parent only if variant has no master image (#1807)
            if ( stristr($sCopyFieldName, '_oxthumb') || stristr($sCopyFieldName, '_oxicon') ) {
                if ( $this->_isFieldEmpty( $sCopyFieldName ) && !$this->_hasMasterImage( 1 ) ) {
                    $this->$sCopyFieldName = clone $oParentArticle->$sCopyFieldName;
                }
            } elseif ( stristr($sCopyFieldName, '_oxzoom') ) {
                // for zoom images checking master image with specified index
                // assign from parent only if no pictures to variant are added
                $iIndex = (int) str_ireplace( "oxarticles__oxzoom", "", $sFieldName );
                if ( $this->_isFieldEmpty( $sCopyFieldName ) && !$this->_hasMasterImage( $iIndex ) && !$this->_hasMasterImage( 1 ) ) {
                    $this->$sCopyFieldName = clone $oParentArticle->$sCopyFieldName;
                }
            } elseif ( stristr($sCopyFieldName, '_oxpicsgenerated') && $this->{$sCopyFieldName}->value == 0 ) {
                // if no pics generated for variants, load all from
                $this->$sCopyFieldName = clone $oParentArticle->$sCopyFieldName;
            } elseif ($this->_isFieldEmpty($sCopyFieldName) || in_array( $sCopyFieldName, $this->_aCopyParentField ) ) {
                $this->$sCopyFieldName = clone $oParentArticle->$sCopyFieldName;
            }
        }
    }

    /**
     * Get parent article
     *
     * @return oxArticle
     */
    public function getParentArticle()
    {
        if ( ( $sParentId = $this->oxarticles__oxparentid->value ) ) {
            $sIndex = $sParentId . "_" . $this->getLanguage();
            if ( !isset( self::$_aLoadedParents[$sIndex] ) ) {
                self::$_aLoadedParents[$sIndex] = oxNew( 'oxarticle' );
                self::$_aLoadedParents[$sIndex]->_blSkipAbPrice  = true;
                self::$_aLoadedParents[$sIndex]->_blLoadPrice    = false;
                self::$_aLoadedParents[$sIndex]->_blLoadVariants = false;
                self::$_aLoadedParents[$sIndex]->loadInLang( $this->getLanguage(), $sParentId );
            }
            return self::$_aLoadedParents[$sIndex];
        }
    }

    /**
     * Get parent article
     *
     * @deprecated since version 4.2
     *
     * @return oxArticle
     */
    protected function _getParentAricle()
    {
        return $this->getParentArticle();
    }

    /**
     * Assigns parent field values to article
     *
     * @return null;
     */
    protected function _assignParentFieldValues()
    {
        startProfile('articleAssignParentInternal');
        if ( $this->oxarticles__oxparentid->value ) {
            // yes, we are in fact a variant
            if ( !$this->isAdmin() || ( $this->_blLoadParentData && $this->isAdmin() ) ) {
                foreach ( $this->_aFieldNames as $sFieldName => $sVal ) {
                    $this->_assignParentFieldValue( $sFieldName );
                }
            }
        }
        stopProfile('articleAssignParentInternal');
    }

    /**
     * if we have variants then depending on config option the parent may be non buyable
     *
     * @return null
     */
    protected function _assignNotBuyableParent()
    {
        if ( !$this->getConfig()->getConfigParam( 'blVariantParentBuyable' ) &&
             ($this->_blHasVariants || $this->oxarticles__oxvarstock->value || $this->oxarticles__oxvarcount->value )) {
            $this->_blNotBuyableParent = true;

        }
    }

    /**
     * Assigns stock status to article
     *
     * @return null
     */
    protected function _assignStock()
    {
        $myConfig = $this->getConfig();
        // -----------------------------------
        // stock
        // -----------------------------------

        // #1125 A. must round (using floor()) value taken from database and cast to int
        if (!$myConfig->getConfigParam( 'blAllowUnevenAmounts' ) && !$this->isAdmin() ) {
            $this->oxarticles__oxstock = new oxField((int) floor($this->oxarticles__oxstock->value));
        }
        //GREEN light
        $this->_iStockStatus = 0;

        // if we have flag /*1 or*/ 4 - we show always green light
        if ( $myConfig->getConfigParam( 'blUseStock' ) && /*$this->oxarticles__oxstockflag->value != 1 && */ $this->oxarticles__oxstockflag->value != 4) {
            //ORANGE light
            $iStock = $this->oxarticles__oxstock->value;

            if ($this->_blNotBuyableParent) {
                $iStock = $this->oxarticles__oxvarstock->value;
            }


            if ( $iStock <= $myConfig->getConfigParam( 'sStockWarningLimit' ) && $iStock > 0) {
                $this->_iStockStatus = 1;
            }

            //RED light
            if ($iStock <= 0) {
                $this->_iStockStatus = -1;
            }
        }


        // stock
        if ( $myConfig->getConfigParam( 'blUseStock' ) && ($this->oxarticles__oxstockflag->value == 3 || $this->oxarticles__oxstockflag->value == 2)) {
            $iOnStock = $this->oxarticles__oxstock->value;
            if ($this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )) {
                $iOnStock += $this->getSession()->getBasketReservations()->getReservedAmount($this->getId());
            }
            if ($iOnStock <= 0) {
                $this->setBuyableState( false );
            }
        }

        //exceptional handling for variant parent stock:
        if ($this->_blNotBuyable && $this->oxarticles__oxvarstock->value ) {
            $this->setBuyableState( true );
            //but then at least setting notBuaybleParent to true
            $this->_blNotBuyableParent = true;
        }

        //special treatment for lists when blVariantParentBuyable config option is set to false
        //then we just hide "to basket" button.
        //if variants are not loaded in the list and this article has variants and parent is not buyable then this article is not buyable
        if ( !$myConfig->getConfigParam( 'blVariantParentBuyable' ) && !$myConfig->getConfigParam( 'blLoadVariants' ) && $this->oxarticles__oxvarstock->value) {
            $this->setBuyableState( false );
        }

        //setting to non buyable when variant list is empty (for example not loaded or inactive) and $this is non buyable parent
        if (!$this->_blNotBuyable && $this->_blNotBuyableParent && $this->oxarticles__oxvarcount->value == 0) {
            $this->setBuyableState( false );
        }
    }

    /**
     * Assigns prices to article
     *
     * @return null
     */
    protected function _assignPrices()
    {
        $myConfig = $this->getConfig();

        // Performance
        if ( !$myConfig->getConfigParam( 'bl_perfLoadPrice' ) || !$this->_blLoadPrice ) {
            return;
        }

        //price per unit handling
        if ((double) $this->oxarticles__oxunitquantity->value && $this->oxarticles__oxunitname->value) {
            // compute price
            $dPrice = $this->getPrice()->getBruttoPrice();
            $oCur = $myConfig->getActShopCurrencyObject();
            $this->_fPricePerUnit = oxLang::getInstance()->formatCurrency($dPrice / (double) $this->oxarticles__oxunitquantity->value, $oCur);
        }

        //getting min and max prices of variants
        if ( $this->_hasAnyVariant() ) {
            $this->_applyRangePrice();
        }
    }

    /**
     * assigns persistent param to article
     *
     * @return null;
     */
    protected function _assignPersistentParam()
    {
        // Persistent Parameter Handling
        $aPersParam     = oxSession::getVar( 'persparam');
        if ( isset( $aPersParam) && isset( $aPersParam[$this->getId()])) {
            $this->_aPersistParam = $aPersParam[$this->getId()];
        }
    }

    /**
     * assigns dynimagedir to article
     *
     * @return null;
     */
    protected function _assignDynImageDir()
    {
        $myConfig = $this->getConfig();

        $sThisShop = $this->oxarticles__oxshopid->value;

        $this->_sDynImageDir   = $myConfig->getPictureUrl( null, false );
        $this->dabsimagedir    = $myConfig->getPictureDir( false ); //$sThisShop
        $this->nossl_dimagedir = $myConfig->getPictureUrl( null, false, false, null, $sThisShop ); //$sThisShop
        $this->ssl_dimagedir   = $myConfig->getPictureUrl( null, false, true, null, $sThisShop ); //$sThisShop
    }

    /**
     * Adds a flag if article is on comparisonlist.
     *
     * @return null;
     */
    protected function _assignComparisonListFlag()
    {
        // #657 add a flag if article is on comparisonlist

        $aItems = oxSession::getVar('aFiltcompproducts');
        if ( isset( $aItems[$this->getId()])) {
            $this->_blIsOnComparisonList = true;
        }
    }

    /**
     * Assigns atttibutes to article
     *
     * @return null;
     */
    protected function _assignAttributes()
    {
        //#1029T load attributes
        //#1078S removed check for module "Produktvergleich"
        if ( $this->getConfig()->getConfigParam( 'bl_perfLoadAttributes' ) ) {
            $this->getAttributes();
        }
    }


    /**
     * Sets article creation date
     * (oxarticle::oxarticles__oxinsert). Then executes parent method
     * parent::_insert() and returns insertion status.
     *
     * @return bool
     */
    protected function _insert()
    {
        // set oxinsert
        $sNow = date('Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime());
        $this->oxarticles__oxinsert    = new oxField( $sNow );
        $this->oxarticles__oxtimestamp = new oxField( $sNow );
        if ( !is_object($this->oxarticles__oxsubclass) || $this->oxarticles__oxsubclass->value == '') {
            $this->oxarticles__oxsubclass = new oxField('oxarticle');
        }

        return parent::_insert();
    }

    /**
     * Executes oxarticle::_skipSaveFields() and updates article information
     *
     * @return bool
     */
    protected function _update()
    {

        $this->_skipSaveFields();

        $myConfig = $this->getConfig();


        return parent::_update();
    }

    /**
     * Updates article variants oxremindactive field, as variants inherit this setting from parent
     *
     * @return null
     */
    public function updateVariantsRemind()
    {
        // check if it is parent article
        if ( !$this->isVariant() && $this->_hasAnyVariant()) {
            $oDb = oxDb::getDb();
            $sOxId = $oDb->quote($this->getId());
            $sOxShopId = $oDb->quote($this->getShopId());
            $iRemindActive = $oDb->quote($this->oxarticles__oxremindactive->value);
            $sUpdate = "
                update oxarticles
                    set oxremindactive = $iRemindActive
                    where oxparentid = $sOxId and
                          oxshopid = $sOxShopId
            ";
            $oDb->execute( $sUpdate );
        }
    }

    /**
     * Deletes records in database
     *
     * @param string $sOXID Article ID
     *
     * @return int
     */
    protected function _deleteRecords($sOXID)
    {
        $oDb = oxDb::getDb();

        $sOXID = $oDb->quote($sOXID);

        //remove other records
        $sDelete = 'delete from oxobject2article where oxarticlenid = '.$sOXID.' or oxobjectid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxobject2attribute where oxobjectid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxobject2category where oxobjectid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxobject2selectlist where oxobjectid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxprice2article where oxartid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxreviews where oxtype="oxarticle" and oxobjectid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxratings where oxobjectid = '.$sOXID.' ';
        $rs = $oDb->execute( $sDelete );

        $sDelete = 'delete from oxaccessoire2article where oxobjectid = '.$sOXID.' or oxarticlenid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        //#1508C - deleting oxobject2delivery entries added
        $sDelete = 'delete from oxobject2delivery where oxobjectid = '.$sOXID.' and oxtype=\'oxarticles\' ';
        $oDb->execute( $sDelete);

        $sDelete = 'delete from oxartextends where oxid = '.$sOXID.' ';
        $oDb->execute( $sDelete);

        //delete the record
        foreach ( $this->_getLanguageSetTables( "oxartextends" ) as $sSetTbl ) {
            $oDb->execute( "delete from $sSetTbl where oxid = {$sOXID}" );
        }

        $sDelete = 'delete from oxactions2article where oxartid = '.$sOXID.' ';
        $rs = $oDb->execute( $sDelete );

        $sDelete = 'delete from oxobject2list where oxobjectid = '.$sOXID.' ';
        $rs = $oDb->execute( $sDelete );


        return $rs;
    }

    /**
     * Deletes variant records
     *
     * @param string $sOXID Article ID
     *
     * @return null
     */
    protected function _deleteVariantRecords( $sOXID )
    {
        if ( $sOXID ) {
            $oDb = oxDb::getDb();
            //collect variants to remove recursively
            $sQ = 'select oxid from '.$this->getViewName().' where oxparentid = '.$oDb->quote( $sOXID );
            $rs = $oDb->select( $sQ, false, false );
            if ($rs != false && $rs->recordCount() > 0) {
                while (!$rs->EOF) {
                    $this->delete( $rs->fields[0] );
                    $rs->moveNext();
                }
            }
        }
    }

    /**
     * Resets cache and article count in vendor and category
     *
     * @param string $sOxid reset article id
     *
     * @deprecated since Jan 21, 2009
     *
     * @return null
     */
    protected function _resetCacheAndArticleCount( $sOxid )
    {
        $this->_onChangeResetCounts( $sOxid, $this->oxarticles__oxvendorid->value, $this->oxarticles__oxmanufacturerid->value );
    }

    /**
     * Delete pics
     *
     * @return null
     */
    protected function _deletePics()
    {
        $myUtilsPic = oxUtilsPic::getInstance();
        $myConfig   = $this->getConfig();
        $oPictureHandler = oxPictureHandler::getInstance();

        //deleting custom main icon
        $oPictureHandler->deleteMainIcon( $this );

        //deleting custom thumbnail
        $oPictureHandler->deleteThumbnail( $this );

        $sAbsDynImageDir = $myConfig->getPictureDir(false);

        // deleting master image and all generated images
        $iPicCount = $myConfig->getConfigParam( 'iPicCount' );
        for ( $i = 1; $i <= $iPicCount; $i++ ) {
            $oPictureHandler->deleteArticleMasterPicture( $this, $i );
        }
    }

    /**
     * Resets category and vendor counts. This method is supposed to be called on article change triger.
     *
     * @param string $sOxid           object to reset id ID
     * @param string $sVendorId       Vendor ID
     * @param string $sManufacturerId Manufacturer ID
     *
     * @return null
     */
    protected function _onChangeResetCounts( $sOxid, $sVendorId = null, $sManufacturerId = null )
    {

        $myUtilsCount = oxUtilsCount::getInstance();

        if ( $sVendorId ) {
            $myUtilsCount->resetVendorArticleCount( $sVendorId );
        }

        if ( $sManufacturerId ) {
            $myUtilsCount->resetManufacturerArticleCount( $sManufacturerId );
        }

        //also reseting category counts
        $oDb = oxDb::getDb();
        $sQ = "select oxcatnid from oxobject2category where oxobjectid = ".$oDb->quote($sOxid);
        $oRs = $oDb->select( $sQ, false, false );
        if ( $oRs !== false && $oRs->recordCount() > 0) {
            while ( !$oRs->EOF ) {
                $myUtilsCount->resetCatArticleCount( $oRs->fields[0] );
                $oRs->moveNext();
            }
        }
    }

    /**
     * Updates article stock. This method is supposed to be called on article change triger.
     *
     * @param string $sParentID product parent id
     *
     * @return null
     */
    protected function _onChangeUpdateStock( $sParentID )
    {
        if ( $sParentID ) {
            $oDb = oxDb::getDb();
            $sParentIdQuoted = $oDb->quote($sParentID);
            $sQ = 'select oxstock, oxvendorid, oxmanufacturerid from oxarticles where oxid = '.$sParentIdQuoted;
            $rs = $oDb->select( $sQ, false, false );
            $iOldStock = $rs->fields[0];
            $iVendorID = $rs->fields[1];
            $iManufacturerID = $rs->fields[2];

            $sQ = 'select sum(oxstock) from '.$this->getViewName(true).' where oxparentid = '.$sParentIdQuoted.' and '. $this->getSqlActiveSnippet( true ).' and oxstock > 0 ';
            $iStock = (float) $oDb->getOne( $sQ, false, false );

            $sQ = 'update oxarticles set oxvarstock = '.$iStock.' where oxid = '.$sParentIdQuoted;
            $oDb->execute( $sQ );

            //now lets update category counts
            //first detect stock status change for this article (to or from 0)
            if ( $iStock < 0 ) {
                $iStock = 0;
            }
            if ( $iOldStock < 0 ) {
                $iOldStock = 0;
            }
            if ( $this->oxarticles__oxstockflag->value == 2 && $iOldStock xor $iStock ) {
                //means the stock status could be changed (oxstock turns from 0 to 1 or from 1 to 0)
                // so far we leave it like this but later we could move all count resets to one or two functions
                $this->_onChangeResetCounts( $sParentID, $iVendorID, $iManufacturerID );
            }
        }
    }

    /**
     * Resets article count cache when stock value is zero and article goes offline.
     *
     * @param string $sOxid product id
     *
     * @return null
     */
    protected function _onChangeStockResetCount( $sOxid )
    {
        $myConfig = $this->getConfig();

        if ( $myConfig->getConfigParam( 'blUseStock' ) && $this->oxarticles__oxstockflag->value == 2 &&
           ( $this->oxarticles__oxstock->value + $this->oxarticles__oxvarstock->value ) <= 0 ) {

               $this->_onChangeResetCounts( $sOxid, $this->oxarticles__oxvendorid->value, $this->oxarticles__oxmanufacturerid->value );
        }
    }

    /**
     * Updates variant count. This method is supposed to be called on article change triger.
     *
     * @param string $sParentID Parent ID
     *
     * @return null
     */
    protected function _onChangeUpdateVarCount( $sParentID )
    {
        if ( $sParentID ) {
            $oDb = oxDb::getDb();
            $sParentIdQuoted = $oDb->quote( $sParentID );
            $sQ = "select count(*) as varcount from oxarticles where oxparentid = {$sParentIdQuoted}";
            $iVarCount = (int) $oDb->getOne( $sQ, false, false );

            $sQ = "update oxarticles set oxvarcount = {$iVarCount} where oxid = {$sParentIdQuoted}";
            $oDb->execute( $sQ );
        }
    }

    /**
     * Updates variant min price. This method is supposed to be called on article change triger.
     *
     * @param string $sParentID Parent ID
     *
     * @return null
     */
    protected function _onChangeUpdateMinVarPrice( $sParentID )
    {
        if ( $sParentID ) {
            $oDb = oxDb::getDb();
            $sParentIdQuoted = $oDb->quote($sParentID);
            //#M0000883 (Sarunas)
            $sQ = 'select min(oxprice) as varminprice from '.$this->getViewName(true).' where '.$this->getSqlActiveSnippet(true).' and (oxparentid = '.$sParentIdQuoted.')';
            $dVarMinPrice = $oDb->getOne( $sQ, false, false );

            $dParentPrice = $oDb->getOne( "select oxprice from oxarticles where oxid = $sParentIdQuoted ", false, false );

            $blParentBuyable =  $this->getConfig()->getConfigParam( 'blVariantParentBuyable' );

            if ($dVarMinPrice) {
                if ($blParentBuyable) {
                    $dVarMinPrice = min($dVarMinPrice, $dParentPrice);
                }

            } else {
                $dVarMinPrice = $dParentPrice;
            }

            if ( $dVarMinPrice ) {
                $sQ = 'update oxarticles set oxvarminprice = '.$dVarMinPrice.' where oxid = '.$sParentIdQuoted;
                $oDb->execute($sQ);
            }
        }
    }


    /**
     * Returns minimum brut price from all (already loaded) variants and if aplicable parent article
     *
     * @return null;
     */
    protected function _applyRangePrice()
    {
        //#buglist_413 if bl_perfLoadPriceForAddList variant price shouldn't be loaded too
        if ( !$this->getConfig()->getConfigParam( 'bl_perfLoadPrice' ) || !$this->_blLoadPrice ) {
            return;
        }

        $this->_blIsRangePrice = false;

        // if parent is buyable - do not apply range price calcculations
        if ($this->_blSkipAbPrice || !$this->_blNotBuyableParent) {
            return;
        }

        if ( $this->isParentNotBuyable() && !$this->getConfig()->getConfigParam( 'blLoadVariants' )) {
            //#2509 we cannot force brutto price here, as netto price can be added to DB
            // $this->getPrice()->setBruttoPriceMode();
            $dPrice = $this->oxarticles__oxvarminprice->value;
            $this->getPrice()->setPrice($dPrice);
            $this->_blIsRangePrice = true;
            $this->_calculatePrice( $this->getPrice() );
            return;
        }

        $aPrices = array();

        if (!$this->_blNotBuyableParent) {
            $aPrices[] = $this->getPrice()->getBruttoPrice();
        }

        $aVariants = $this->getVariants(false);

        if (count($aVariants)) {
            foreach ($aVariants as $sKey => $oVariant) {
                $aPrices[] = $oVariant->getPrice()->getBruttoPrice();
            }
        }

        if ( count( $aPrices ) ) {
            $dMinPrice = min( $aPrices );
            $dMaxPrice = max( $aPrices );
        }

        if ($this->_blNotBuyableParent && isset($dMinPrice) && $dMinPrice == $dMaxPrice) {
            $this->getPrice()->setBruttoPriceMode();
            $this->getPrice()->setPrice($dMinPrice);
        }

        if (isset($dMinPrice) && $dMinPrice != $dMaxPrice) {
            $this->getPrice()->setBruttoPriceMode();
            $this->getPrice()->setPrice($dMinPrice);
            $this->_blIsRangePrice = true;
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
    public function getProductParentId()
    {
        return $this->oxarticles__oxparentid->value;
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
    public function isVariant()
    {
        return (bool) ( isset( $this->oxarticles__oxparentid ) ? $this->oxarticles__oxparentid->value : false );
    }

    /**
     * Returns TRUE if product is multidimensional variant, and false if not
     *
     * @return bool
     */
    public function isMdVariant()
    {
        $oMdVariant = oxNew( "oxVariantHandler" );

        return $oMdVariant->isMdVariant($this);
    }

    /**
     * get Sql for loading price categories which include this article
     *
     * @param string $sFields fields to load from oxcategories
     *
     * @return string
     */
    public function getSqlForPriceCategories($sFields = '')
    {
        if (!$sFields) {
            $sFields = 'oxid';
        }
        $sSelectWhere = "select $sFields from ".$this->_getObjectViewName('oxcategories')." where";
        $sQuotedPrice = oxDb::getDb()->quote( $this->oxarticles__oxprice->value );
        return  "$sSelectWhere oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= $sQuotedPrice and oxpriceto >= $sQuotedPrice"
               ." union $sSelectWhere oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= $sQuotedPrice"
               ." union $sSelectWhere oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= $sQuotedPrice";
    }

    /**
     * Checks if artickle is assigned to price category $sCatNID.
     *
     * @param string $sCatNid Price category ID
     *
     * @return bool
     */
    public function inPriceCategory( $sCatNid )
    {
        $oDb = oxDb::getDb();

        $sQuotedPrice = $oDb->quote( $this->oxarticles__oxprice->value );
        $sQuotedCnid = $oDb->quote( $sCatNid );
        return (bool) $oDb->getOne(
            "select 1 from ".$this->_getObjectViewName('oxcategories')." where oxid=$sQuotedCnid and"
           ."(   (oxpricefrom != 0 and oxpriceto != 0 and oxpricefrom <= $sQuotedPrice and oxpriceto >= $sQuotedPrice)"
           ." or (oxpricefrom != 0 and oxpriceto = 0 and oxpricefrom <= $sQuotedPrice)"
           ." or (oxpricefrom = 0 and oxpriceto != 0 and oxpriceto >= $sQuotedPrice)"
           .")"
        );
    }

    /**
     * Returns multidimensional variant structure
     *
     * @return OxMdVariants
     */
    public function getMdVariants()
    {
        if ( $this->_oMdVariants ) {
            return $this->_oMdVariants;
        }

        $oParentArticle = $this->getParentArticle();
        if ( $oParentArticle ) {
            $oVariants = $oParentArticle->getVariants();
        } else {
            $oVariants = $this->getVariants();
        }

        $oVariantHandler = oxNew( "oxVariantHandler" );
        $this->_oMdVariants = $oVariantHandler->buildMdVariants( $oVariants, $this->getId() );

        return $this->_oMdVariants;
    }

    /**
     * Returns first level variants from multidimensional variants list
     *
     * @return OxMdVariants
     */
    public function getMdSubvariants()
    {
        return $this->getMdVariants()->getMdSubvariants();
    }

    /**
     * Checks if article has uplodaded master image for selected picture
     *
     * @param int $iIndex master picture index
     *
     * @return bool
     */
    protected function _hasMasterImage( $iIndex )
    {
        $sPicName = basename($this->{"oxarticles__oxpic" . $iIndex}->value);

        if ( $sPicName == "nopic.jpg" || $sPicName == "" ) {
            return false;
        }
        if ( $this->isVariant() && $this->getParentArticle()->{"oxarticles__oxpic".$iIndex}->value == $this->{"oxarticles__oxpic".$iIndex}->value ) {
            return false;
        }

        $sMasterPic = 'product/'.$iIndex . "/" . $sPicName;

        if ( $this->getConfig()->getMasterPicturePath( $sMasterPic ) ) {
            return true;
        }

        return false;
    }

    /**
     * Return article picture file name
     *
     * @param string $sFieldName article picture field name
     * @param int    $iIndex     article picture index
     *
     * @return string
     */
    public function getPictureFieldValue( $sFieldName, $iIndex = null )
    {
        if ( $sFieldName ) {
            $sFieldName = "oxarticles__" . $sFieldName . $iIndex;
            return $this->$sFieldName->value;
        }
    }

    /**
     * Get master zoom picture url
     *
     * @param int $iIndex picture index
     *
     * @return string
     */
    public function getMasterZoomPictureUrl( $iIndex )
    {
        $sPicUrl  = false;
        $sPicName = basename( $this->{"oxarticles__oxpic" . $iIndex}->value );

        if ( $sPicName && $sPicName != "nopic.jpg" ) {
            $sPicUrl = $this->getConfig()->getPictureUrl( "master/product/" . $iIndex . "/" . $sPicName );
            if ( !$sPicUrl || basename( $sPicUrl ) == "nopic.jpg" ) {
                $sPicUrl = false;
            }
        }

        return $sPicUrl;
    }

    /**
     * Returns oxarticles__oxunitname value processed by oxLang::translateString()
     *
     * @return string
     */
    public function getUnitName()
    {
        if ( $this->oxarticles__oxunitname->value ) {
            return oxLang::getInstance()->translateString( $this->oxarticles__oxunitname->value );
        }
    }

     /**
     * Return article downloadable file list (oxlist of oxfile)
     *
     * @param bool $blAddFromParent - return with parent files if not buyable
     *
     * @return null|oxList of oxFile
     */
    public function getArticleFiles( $blAddFromParent=false )
    {
        if ( $this->_aArticleFiles === null) {

            $this->_aArticleFiles = false;

            $sQ = "SELECT * FROM `oxfiles` WHERE `oxartid` = '".$this->getId()."'";

            if ( !$this->getConfig()->getConfigParam( 'blVariantParentBuyable' ) && $blAddFromParent ) {
                $sQ .= " OR `oxartId` = '". $this->oxarticles__oxparentid->value . "'";
            }

            $oArticleFiles = oxNew("oxlist");
            $oArticleFiles->init("oxfile");
            $oArticleFiles->selectString( $sQ );
            $this->_aArticleFiles  = $oArticleFiles;

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
     * Checks if article has amount price
     *
     * @return bool
     */
    public function hasAmountPrice()
    {
        if ( self::$_blHasAmountPrice === null ) {

            self::$_blHasAmountPrice = false;

            $oDb = oxDb::getDb();
            $sQ = "SELECT 1 FROM `oxprice2article` LIMIT 1";

            if ( $oDb->getOne( $sQ ) ) {
                self::$_blHasAmountPrice = true;
            }
        }

        return self::$_blHasAmountPrice;
    }
}
