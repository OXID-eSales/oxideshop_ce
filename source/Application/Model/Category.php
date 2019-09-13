<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxRegistry;
use oxField;

/**
 * Category manager.
 * Collects category information (articles, etc.), performs insertion/deletion
 * of categories nodes. By recursion methods are set structure of category.
 *
 */
class Category extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel implements \OxidEsales\Eshop\Core\Contract\IUrl
{
    /**
     * Subcategories array.
     *
     * @var array
     */
    protected $_aSubCats = [];

    /**
     * Content category array.
     *
     * @var array
     */
    protected $_aContentCats = [];

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxcategory';

    /**
     * number of articles in the current category
     *
     * @var int
     */
    protected $_iNrOfArticles;

    /**
     * visibility of a category
     *
     * @var int
     */
    protected $_blIsVisible;

    /**
     * expanded state of a category
     *
     * @var int
     */
    protected $_blExpanded;

    /**
     * visibility of a category
     *
     * @var int
     */
    protected $_blHasSubCats;

    /**
     * has visible sub categories state of a category
     *
     * @var int
     */
    protected $_blHasVisibleSubCats;

    /**
     * Marks that current object is managed by SEO
     *
     * @var bool
     */
    protected $_blIsSeoObject = true;

    /**
     * Set $_blUseLazyLoading to true if you want to load only actually used fields not full object, depending on views.
     *
     * @var bool
     */
    protected $_blUseLazyLoading = false;

    /**
     * Dyn image dir
     *
     * @var string
     */
    protected $_sDynImageDir = null;

    /**
     * Top category marker
     *
     * @var bool
     */
    protected $_blTopCategory = null;

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
     * Category attributes cache
     *
     * @var array
     */
    protected static $_aCatAttributes = [];

    /**
     * Parent category object container.
     *
     * @var \OxidEsales\Eshop\Application\Model\Category
     */
    protected $_oParent = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxcategories');
    }

    /**
     * Gets default sorting value
     *
     * @return string
     */
    public function getDefaultSorting()
    {
        return $this->oxcategories__oxdefsort->value;
    }

    /**
     * Gets default sorting mode value
     *
     * @return string
     */
    public function getDefaultSortingMode()
    {
        return $this->oxcategories__oxdefsortmode->value;
    }

    /**
     * Extra getter to guarantee compatibility with templates
     *
     * @param string $sName name of variable to get
     *
     * @return string
     */
    public function __get($sName)
    {
        switch ($sName) {
            case 'aSubCats':
                return $this->_aSubCats;
                break;
            case 'aContent':
                return $this->_aContentCats;
                break;
            case 'iArtCnt':
                return $this->getNrOfArticles();
                break;
            case 'isVisible':
                return $this->getIsVisible();
                break;
            case 'expanded':
                return $this->getExpanded();
                break;
            case 'hasSubCats':
                return $this->getHasSubCats();
                break;
            case 'hasVisibleSubCats':
                return $this->getHasVisibleSubCats();
                break;
            case 'openlink':
            case 'closelink':
            case 'link':
                //case 'toListLink':
                //case 'noparamlink':
                return $this->getLink();
                break;
            case 'dimagedir':
                return $this->getPictureUrl();
                break;
        }
        return parent::__get($sName);
    }

    /**
     * Get data from db
     *
     * @param string $sOXID id
     *
     * @return array
     */
    protected function _loadFromDb($sOXID)
    {
        $sSelect = $this->buildSelectString(["`{$this->getViewName()}`.`oxid`" => $sOXID]);
        $aData = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getRow($sSelect);

        return $aData;
    }

    /**
     * Load category data
     *
     * @param string $sOXID id
     *
     * @return bool
     */
    public function load($sOXID)
    {
        $aData = $this->_loadFromDb($sOXID);

        if ($aData) {
            $this->assign($aData);
            $this->_isLoaded = true;
            return true;
        }

        return false;
    }

    /**
     * Loads and assigns object data from DB.
     *
     * @param mixed $dbRecord database record array
     *
     * @return null
     */
    public function assign($dbRecord)
    {
        $this->_iNrOfArticles = null;

        //clear seo urls
        $this->_aSeoUrls = [];

        return parent::assign($dbRecord);
    }

    /**
     * Delete empty categories, returns true on success.
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (!$this->getId()) {
            $this->load($sOXID);
        }

        $sOXID = isset($sOXID) ? $sOXID : $this->getId();

        $myConfig = $this->getConfig();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $blRet = false;

        if ($this->oxcategories__oxright->value == ($this->oxcategories__oxleft->value + 1)) {
            $myUtilsPic = \OxidEsales\Eshop\Core\Registry::getUtilsPic();
            $sDir = $myConfig->getPictureDir(false);

            // only delete empty categories
            // #1173M - not all pic are deleted, after article is removed
            $myUtilsPic->safePictureDelete($this->oxcategories__oxthumb->value, $sDir . \OxidEsales\Eshop\Core\Registry::getUtilsFile()->getImageDirByType('TC'), 'oxcategories', 'oxthumb');
            $myUtilsPic->safePictureDelete($this->oxcategories__oxicon->value, $sDir . \OxidEsales\Eshop\Core\Registry::getUtilsFile()->getImageDirByType('CICO'), 'oxcategories', 'oxicon');
            $myUtilsPic->safePictureDelete($this->oxcategories__oxpromoicon->value, $sDir . \OxidEsales\Eshop\Core\Registry::getUtilsFile()->getImageDirByType('PICO'), 'oxcategories', 'oxpromoicon');

            $query = "UPDATE oxcategories SET OXLEFT = OXLEFT - 2
                      WHERE OXROOTID = :oxrootid AND
                            OXLEFT > :oxleft AND
                            OXSHOPID = :oxshopid";
            $oDb->execute($query, [
                ':oxrootid' => $this->oxcategories__oxrootid->value,
                ':oxleft' => (int) $this->oxcategories__oxleft->value,
                ':oxshopid' => $this->getShopId()
            ]);

            $query = "UPDATE oxcategories SET OXRIGHT = OXRIGHT - 2
                      WHERE OXROOTID = :oxrootid AND
                            OXRIGHT > :oxright AND
                            OXSHOPID = :oxshopid";
            $oDb->execute($query, [
                ':oxrootid' => $this->oxcategories__oxrootid->value,
                ':oxright' => (int) $this->oxcategories__oxright->value,
                ':oxshopid' => $this->getShopId()
            ]);

            // delete entry
            $blRet = parent::delete($sOXID);

            // delete links to articles
            $oDb->execute("delete from oxobject2category where oxobject2category.oxcatnid = :oxid", [
                ':oxid' => $sOXID
            ]);

            // #657 ADDITIONAL delete links to attributes
            $oDb->execute("delete from oxcategory2attribute where oxcategory2attribute.oxobjectid = :oxid", [
                ':oxid' => $sOXID
            ]);

            // A. removing assigned:
            // - deliveries
            $oDb->execute("delete from oxobject2delivery where oxobject2delivery.oxobjectid = :oxid", [
                ':oxid' => $sOXID
            ]);
            // - discounts
            $oDb->execute("delete from oxobject2discount where oxobject2discount.oxobjectid = :oxid", [
                ':oxid' => $sOXID
            ]);

            \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class)->onDeleteCategory($this);
        }

        return $blRet;
    }

    /**
     * returns the sub category array
     *
     * @return array
     */
    public function getSubCats()
    {
        return $this->_aSubCats;
    }

    /**
     * returns a specific sub category
     *
     * @param string $sKey the key of the category
     *
     * @return object
     */
    public function getSubCat($sKey)
    {
        return $this->_aSubCats[$sKey];
    }

    /**
     * Sets an array of sub categories, also handles parent hasVisibleSubCats
     *
     * @param array $aCats array of categories
     */
    public function setSubCats($aCats)
    {
        $this->_aSubCats = $aCats;

        foreach ($aCats as $oCat) {
            // keeping ref. to parent
            $oCat->setParentCategory($this);

            if ($oCat->getIsVisible()) {
                $this->setHasVisibleSubCats(true);
            }
        }
    }

    /**
     * sets a single category, handles sorting and parent hasVisibleSubCats
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCat the category
     * @param string                                       $sKey (optional, default=null)  the key for that category,
     *                                                           without a key, the category is just added to the array
     */
    public function setSubCat($oCat, $sKey = null)
    {
        if ($sKey) {
            $this->_aSubCats[$sKey] = $oCat;
        } else {
            $this->_aSubCats[] = $oCat;
        }

        // keeping ref. to parent
        $oCat->setParentCategory($this);

        if ($oCat->getIsVisible()) {
            $this->setHasVisibleSubCats(true);
        }
    }

    /**
     * returns the content category array
     *
     * @return array
     */
    public function getContentCats()
    {
        return $this->_aContentCats;
    }

    /**
     * Sets an array of content categories
     *
     * @param array $aContent array of content
     */
    public function setContentCats($aContent)
    {
        $this->_aContentCats = $aContent;
    }

    /**
     * sets a single category
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oContent the category
     * @param string                                       $sKey     optional, the key for that category,
     *                                                               without a key, the category is just added to the array
     */
    public function setContentCat($oContent, $sKey = null)
    {
        if ($sKey) {
            $this->_aContentCats[$sKey] = $oContent;
        } else {
            $this->_aContentCats[] = $oContent;
        }
    }

    /**
     * returns number or articles in category
     *
     * @return integer
     */
    public function getNrOfArticles()
    {
        $myConfig = $this->getConfig();

        if (!isset($this->_iNrOfArticles)
            && !$this->isAdmin()
            && (
                $myConfig->getConfigParam('bl_perfShowActionCatArticleCnt')
                || $myConfig->getConfigParam('blDontShowEmptyCategories')
            )
        ) {
            if ($this->isPriceCategory()) {
                $this->_iNrOfArticles = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getPriceCatArticleCount($this->getId(), $this->oxcategories__oxpricefrom->value, $this->oxcategories__oxpriceto->value);
            } else {
                $this->_iNrOfArticles = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getCatArticleCount($this->getId());
            }
        }

        return (int) $this->_iNrOfArticles;
    }

    /**
     * sets the number or articles in category
     *
     * @param int $iNum category product count setter
     */
    public function setNrOfArticles($iNum)
    {
        $this->_iNrOfArticles = $iNum;
    }

    /**
     * returns the visibility of a category, handles hidden and empty categories
     *
     * @return bool
     */
    public function getIsVisible()
    {
        if (!isset($this->_blIsVisible)) {
            if ($this->getConfig()->getConfigParam('blDontShowEmptyCategories')) {
                $blEmpty = ($this->getNrOfArticles() < 1) && !$this->getHasVisibleSubCats();
            } else {
                $blEmpty = false;
            }

            $this->_blIsVisible = !($blEmpty || $this->oxcategories__oxhidden->value);
        }

        return $this->_blIsVisible;
    }

    /**
     * sets the visibility of a category
     *
     * @param bool $blVisible category visibility status setter
     */
    public function setIsVisible($blVisible)
    {
        $this->_blIsVisible = $blVisible;
    }

    /**
     * Returns dyn image dir
     *
     * @return string
     */
    public function getPictureUrl()
    {
        if ($this->_sDynImageDir === null) {
            $sThisShop = $this->oxcategories__oxshopid->value;
            $this->_sDynImageDir = $this->getConfig()->getPictureUrl(null, false, null, null, $sThisShop);
        }

        return $this->_sDynImageDir;
    }

    /**
     * Returns raw category seo url
     *
     * @param int $iLang language id
     * @param int $iPage page number [optional]
     *
     * @return string
     */
    public function getBaseSeoLink($iLang, $iPage = 0)
    {
        $oEncoder = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class);
        if (!$iPage) {
            return $oEncoder->getCategoryUrl($this, $iLang);
        }

        return $oEncoder->getCategoryPageUrl($this, $iPage, $iLang);
    }

    /**
     * returns the url of the category
     *
     * @param int $iLang language id
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        if (!\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive() ||
            (isset($this->oxcategories__oxextlink) && $this->oxcategories__oxextlink->value)
        ) {
            return $this->getStdLink($iLang);
        }

        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        if (!isset($this->_aSeoUrls[$iLang])) {
            $this->_aSeoUrls[$iLang] = $this->getBaseSeoLink($iLang);
        }

        return $this->_aSeoUrls[$iLang];
    }

    /**
     * sets the url of the category
     *
     * @param string $sLink category url
     */
    public function setLink($sLink)
    {
        $iLang = $this->getLanguage();
        if (\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive()) {
            $this->_aSeoUrls[$iLang] = $sLink;
        } else {
            $this->_aStdUrls[$iLang] = $sLink;
        }
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
        $sQ = parent::getSqlActiveSnippet($blForceCoreTable);

        $sTable = $this->getViewName($blForceCoreTable);
        $sQ .= (strlen($sQ) ? ' and ' : '') . " $sTable.oxhidden = '0' ";
        $sQ .= $this->getAdditionalSqlFilter($blForceCoreTable);

        return "( $sQ ) ";
    }

    /**
     * Additional SQL conditions for selecting articles snippet
     *
     * @param bool $forceCoreTable
     * @return string
     */
    protected function getAdditionalSqlFilter($forceCoreTable)
    {
        return '';
    }

    /**
     * Returns base dynamic url: shopUrl/index.php?cl=details
     *
     * @param int  $iLang   language id
     * @param bool $blAddId add current object id to url or not
     * @param bool $blFull  return full including domain name [optional]
     *
     * @return string
     */
    public function getBaseStdLink($iLang, $blAddId = true, $blFull = true)
    {
        if (isset($this->oxcategories__oxextlink) && $this->oxcategories__oxextlink->value) {
            return $this->oxcategories__oxextlink->value;
        }

        $sUrl = '';
        if ($blFull) {
            //always returns shop url, not admin
            $sUrl = $this->getConfig()->getShopUrl($iLang, false);
        }

        //always returns shop url, not admin
        return $sUrl . "index.php?cl=alist" . ($blAddId ? "&amp;cnid=" . $this->getId() : "");
    }

    /**
     * Returns standard URL to category
     *
     * @param int   $iLang   language
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink($iLang = null, $aParams = [])
    {
        if (isset($this->oxcategories__oxextlink) && $this->oxcategories__oxextlink->value) {
            return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($this->oxcategories__oxextlink->value, true);
        }

        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        if (!isset($this->_aStdUrls[$iLang])) {
            $this->_aStdUrls[$iLang] = $this->getBaseStdLink($iLang);
        }

        return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($this->_aStdUrls[$iLang], true, $aParams, $iLang);
    }

    /**
     * returns the expanded state of the category
     *
     * @return bool
     */
    public function getExpanded()
    {
        return $this->_blExpanded;
    }

    /**
     * set the expanded state of the category
     *
     * @param bool $blExpanded expanded status setter
     */
    public function setExpanded($blExpanded)
    {
        $this->_blExpanded = $blExpanded;
    }

    /**
     * returns if a category has sub categories
     *
     * @return bool
     */
    public function getHasSubCats()
    {
        if (!isset($this->_blHasSubCats)) {
            $this->_blHasSubCats = $this->oxcategories__oxright->value > $this->oxcategories__oxleft->value + 1;
        }

        return $this->_blHasSubCats;
    }

    /**
     * returns if a category has visible sub categories
     *
     * @return bool
     */
    public function getHasVisibleSubCats()
    {
        if (!isset($this->_blHasVisibleSubCats)) {
            $this->_blHasVisibleSubCats = false;
        }

        return $this->_blHasVisibleSubCats;
    }

    /**
     * sets the state of has visible sub categories for the category
     *
     * @param bool $blHasVisibleSubcats marker if category has visible subcategories
     */
    public function setHasVisibleSubCats($blHasVisibleSubcats)
    {
        if ($blHasVisibleSubcats && !$this->_blHasVisibleSubCats) {
            unset($this->_blIsVisible);
            if ($this->_oParent instanceof \OxidEsales\Eshop\Application\Model\Category) {
                $this->_oParent->setHasVisibleSubCats(true);
            }
        }
        $this->_blHasVisibleSubCats = $blHasVisibleSubcats;
    }

    /**
     * Loads and returns attribute list associated with this category
     *
     * @return oxAttributeList
     */
    public function getAttributes()
    {
        $sActCat = $this->getId();

        $sKey = md5($sActCat . serialize(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter')));
        if (!isset(self::$_aCatAttributes[$sKey])) {
            $oAttrList = oxNew(\OxidEsales\Eshop\Application\Model\AttributeList::class);
            $oAttrList->getCategoryAttributes($sActCat, $this->getLanguage());
            self::$_aCatAttributes[$sKey] = $oAttrList;
        }

        return self::$_aCatAttributes[$sKey];
    }

    /**
     * Loads and returns category in base language
     *
     * @param object $oActCategory active category
     *
     * @return object
     */
    public function getCatInLang($oActCategory = null)
    {
        $oCategoryInDefaultLanguage = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($this->isPriceCategory()) {
            // get it in base language
            $oCategoryInDefaultLanguage->loadInLang(0, $this->getId());
        } else {
            $oCategoryInDefaultLanguage->loadInLang(0, $oActCategory->getId());
        }

        return $oCategoryInDefaultLanguage;
    }

    /**
     * Set parent category object for internal usage only.
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $oCategory parent category object
     */
    public function setParentCategory($oCategory)
    {
        $this->_oParent = $oCategory;
    }

    /**
     * Returns parent category object for current category (if it is available).
     *
     * @return \OxidEsales\Eshop\Application\Model\Category
     */
    public function getParentCategory()
    {
        $oCat = null;

        // loading only if parent id is not rootid
        if ($this->oxcategories__oxparentid->value && $this->oxcategories__oxparentid->value != 'oxrootid') {
            // checking if object itself has ref to parent
            if ($this->_oParent) {
                $oCat = $this->_oParent;
            } else {
                $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                if (!$oCat->load($this->oxcategories__oxparentid->value)) {
                    $oCat = null;
                } else {
                    $this->_oParent = $oCat;
                }
            }
        }

        return $oCat;
    }

    /**
     * Returns root category id of a child category
     *
     * @param string $sCategoryId category id
     *
     * @return integer
     */
    public static function getRootId($sCategoryId)
    {
        if (!isset($sCategoryId)) {
            return;
        }
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        return $oDb->getOne('select oxrootid from ' . getViewName('oxcategories') . ' where oxid = :oxid', [
            ':oxid' => $sCategoryId
        ]);
    }

    /**
     * Before assigning the record from SQL it checks for viewable rights
     *
     * @param string $sSelect SQL select
     *
     * @return bool
     */
    public function assignViewableRecord($sSelect)
    {
        if ($this->assignRecord($sSelect)) {
            return true;
        }

        return false;
    }

    /**
     * Inserts new category (and updates existing node oxLeft amd oxRight accordingly). Returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        if ($this->oxcategories__oxparentid->value != "oxrootid") {
            // load parent
            $oParent = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
            //#M317 check if parent is loaded
            if (!$oParent->load($this->oxcategories__oxparentid->value)) {
                return false;
            }

            // update existing nodes
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $query = "UPDATE oxcategories SET OXLEFT = OXLEFT + 2
                      WHERE OXROOTID = :oxrootid AND
                            OXLEFT > :oxleft AND
                            OXRIGHT >= :oxright AND
                            OXSHOPID = :oxshopid ";
            $oDb->execute($query, [
                ':oxrootid' => $oParent->oxcategories__oxrootid->value,
                ':oxleft' => (int) $oParent->oxcategories__oxright->value,
                ':oxright' => (int) $oParent->oxcategories__oxright->value,
                ':oxshopid' => $this->getShopId()
            ]);

            $query = "UPDATE oxcategories SET OXRIGHT = OXRIGHT + 2
                      WHERE OXROOTID = :oxrootid AND
                            OXRIGHT >= :oxright AND
                            OXSHOPID = :oxshopid";
            $oDb->execute($query, [
                ':oxrootid' => $oParent->oxcategories__oxrootid->value,
                ':oxright' => (int) $oParent->oxcategories__oxright->value,
                ':oxshopid' => $this->getShopId()
            ]);

            if (!$this->getId()) {
                $this->setId();
            }

            $this->oxcategories__oxrootid = new \OxidEsales\Eshop\Core\Field($oParent->oxcategories__oxrootid->value, \OxidEsales\Eshop\Core\Field::T_RAW);
            $this->oxcategories__oxleft = new \OxidEsales\Eshop\Core\Field($oParent->oxcategories__oxright->value, \OxidEsales\Eshop\Core\Field::T_RAW);
            $this->oxcategories__oxright = new \OxidEsales\Eshop\Core\Field($oParent->oxcategories__oxright->value + 1, \OxidEsales\Eshop\Core\Field::T_RAW);

            return parent::_insert();
        } else {
            // root entry
            if (!$this->getId()) {
                $this->setId();
            }

            $this->oxcategories__oxrootid = new \OxidEsales\Eshop\Core\Field($this->getId(), \OxidEsales\Eshop\Core\Field::T_RAW);
            $this->oxcategories__oxleft = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
            $this->oxcategories__oxright = new \OxidEsales\Eshop\Core\Field(2, \OxidEsales\Eshop\Core\Field::T_RAW);

            return parent::_insert();
        }
    }

    /**
     * Updates category tree, returns true on success.
     *
     * @return bool
     */
    protected function _update()
    {
        $this->setUpdateSeo(true);
        $this->_setUpdateSeoOnFieldChange('oxtitle');

        // Function is called from inside a transaction in Category::save (see ESDEV-3804 and ESDEV-3822).
        // No need to explicitly force master here.
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sOldParentID = $database->getOne("select oxparentid from oxcategories where oxid = :oxid", [
            ':oxid' => $this->getId()
        ]);

        if ($this->_blIsSeoObject && $this->isAdmin()) {
            \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class)->markRelatedAsExpired($this);
        }

        $blRes = parent::_update();

        // #872C - need to update category tree oxleft and oxright values (nested sets),
        // then sub trees are moved inside one root, or to another root.
        // this is done in 3 basic steps
        // 1. increase oxleft and oxright values of target root tree by $iTreeSize, where oxleft>=$iMoveAfter , oxright>=$iMoveAfter
        // 2. modify current subtree, we want to move by adding $iDelta to it's oxleft and oxright,  where oxleft>=$sOldParentLeft and oxright<=$sOldParentRight values,
        //    in this step we also modify rootid's if they were changed
        // 3. decreasing oxleft and oxright values of current root tree, where oxleft >= $sOldParentRight+1 , oxright >= $sOldParentRight+1

        // did we change position in tree ?
        if ($this->oxcategories__oxparentid->value != $sOldParentID) {
            $sOldParentLeft = $this->oxcategories__oxleft->value;
            $sOldParentRight = $this->oxcategories__oxright->value;

            $iTreeSize = $sOldParentRight - $sOldParentLeft + 1;

            $sNewRootID = $database->getOne("select oxrootid from oxcategories where oxid = :oxid", [
                ':oxid' => $this->oxcategories__oxparentid->value
            ]);

            //If empty rootID, we set it to categorys oxid
            if ($sNewRootID == "") {
                //echo "<br>* ) Creating new root tree ( {$this->_sOXID} )";
                $sNewRootID = $this->getId();
            }
            $sNewParentLeft = $database->getOne("select oxleft from oxcategories where oxid = :oxid", [
                ':oxid' => $this->oxcategories__oxparentid->value
            ]);

            //if(!$sNewParentLeft){
            //the current node has become root node, (oxrootid == "oxrootid")
            //    $sNewParentLeft = 0;
            //}

            $iMoveAfter = $sNewParentLeft + 1;

            //New parentid can not be set to it's child
            if ($sNewParentLeft > $sOldParentLeft && $sNewParentLeft < $sOldParentRight && $this->oxcategories__oxrootid->value == $sNewRootID) {
                //echo "<br>* ) Can't asign category to it's child";

                //Restoring old parentid, stoping further actions
                $sRestoreOld = "UPDATE oxcategories SET OXPARENTID = :oxparentid WHERE oxid = :oxid";
                $database->execute($sRestoreOld, [
                    ':oxparentid' => $sOldParentID,
                    ':oxid' => $this->getId()
                ]);

                return false;
            }

            //Old parent will be shifted too, if it is in the same tree
            if ($sOldParentLeft > $iMoveAfter && $this->oxcategories__oxrootid->value == $sNewRootID) {
                $sOldParentLeft += $iTreeSize;
                $sOldParentRight += $iTreeSize;
            }

            $iDelta = $iMoveAfter - $sOldParentLeft;

            //echo "Size=$iTreeSize, NewStart=$iMoveAfter, delta=$iDelta";

            $sAddOld = " and oxshopid = '" . $this->getShopId() . "' and OXROOTID = " . $database->quote($this->oxcategories__oxrootid->value) . ";";
            $sAddNew = " and oxshopid = '" . $this->getShopId() . "' and OXROOTID = " . $database->quote($sNewRootID) . ";";

            //Updating everything after new position
            $params = [':treeSize' => $iTreeSize, ':offset' => $iMoveAfter];
            $database->execute("UPDATE oxcategories SET OXLEFT = (OXLEFT + :treeSize) WHERE OXLEFT >= :offset" . $sAddNew, $params);
            $database->execute("UPDATE oxcategories SET OXRIGHT = (OXRIGHT + :treeSize) WHERE OXRIGHT >= :offset" . $sAddNew, $params);
            //echo "<br>1.) + $iTreeSize, >= $iMoveAfter";

            $sChangeRootID = "";
            if ($this->oxcategories__oxrootid->value != $sNewRootID) {
                //echo "<br>* ) changing root IDs ( {$this->oxcategories__oxrootid->value} -> {$sNewRootID} )";
                $sChangeRootID = ", OXROOTID=" . $database->quote($sNewRootID);
            }

            //Updating subtree
            $query = "UPDATE oxcategories SET OXLEFT = (OXLEFT + :delta), OXRIGHT = (OXRIGHT + :delta) " . $sChangeRootID .
                     "WHERE OXLEFT >= :oxleft AND OXRIGHT <= :oxright" . $sAddOld;
            $database->execute($query, [
                ':delta' => $iDelta,
                ':oxleft' => $sOldParentLeft,
                ':oxright' => $sOldParentRight
            ]);
            //echo "<br>2.) + $iDelta, >= $sOldParentLeft and <= $sOldParentRight";

            //Updating everything after old position
            $params = [':treeSize' => $iTreeSize, ':offset' => $sOldParentRight + 1];
            $database->execute("UPDATE oxcategories SET OXLEFT = (OXLEFT - :treeSize) WHERE OXLEFT >= :offset" . $sAddOld, $params);
            $database->execute("UPDATE oxcategories SET OXRIGHT = (OXRIGHT - :treeSize) WHERE OXRIGHT >= :offset" . $sAddOld, $params);
            //echo "<br>3.) - $iTreeSize, >= ".($sOldParentRight+1);
        }

        if ($blRes && $this->_blIsSeoObject && $this->isAdmin()) {
            \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class)->markRelatedAsExpired($this);
        }

        return $blRes;
    }

    /**
     * Sets data field value
     *
     * @param string $fieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $value     value of data field
     * @param int    $dataType  field type
     *
     * @return null
     */
    protected function _setFieldData($fieldName, $value, $dataType = \OxidEsales\Eshop\Core\Field::T_TEXT)
    {
        //preliminary quick check saves 3% of execution time in category lists by avoiding redundant strtolower() call
        $fieldNameIndex2 = $fieldName[2];
        if ($fieldNameIndex2 === 'l' || $fieldNameIndex2 === 'L' || (isset($fieldName[16]) && ($fieldName[16] == 'l' || $fieldName[16] == 'L'))) {
            $loweredFieldName = strtolower($fieldName);
            if ('oxlongdesc' === $loweredFieldName || 'oxcategories__oxlongdesc' === $loweredFieldName) {
                $dataType = \OxidEsales\Eshop\Core\Field::T_RAW;
            }
        }

        return parent::_setFieldData($fieldName, $value, $dataType);
    }

    /**
     * Returns category icon picture url if exist, false - if not
     *
     * @return mixed
     */
    public function getIconUrl()
    {
        if (($sIcon = $this->oxcategories__oxicon->value)) {
            $oConfig = $this->getConfig();
            $sSize = $oConfig->getConfigParam('sCatIconsize');
            if (!isset($sSize)) {
                $sSize = $oConfig->getConfigParam('sIconsize');
            }

            return \OxidEsales\Eshop\Core\Registry::getPictureHandler()->getPicUrl("category/icon/", $sIcon, $sSize);
        }
    }

    /**
     * Returns category thumbnail picture url if exist, false - if not
     *
     * @return mixed
     */
    public function getThumbUrl()
    {
        if (($sIcon = $this->oxcategories__oxthumb->value)) {
            $sSize = $this->getConfig()->getConfigParam('sCatThumbnailsize');

            return \OxidEsales\Eshop\Core\Registry::getPictureHandler()->getPicUrl("category/thumb/", $sIcon, $sSize);
        }
    }

    /**
     * Returns category promotion icon picture url if exist, false - if not
     *
     * @return mixed
     */
    public function getPromotionIconUrl()
    {
        if (($sIcon = $this->oxcategories__oxpromoicon->value)) {
            $sSize = $this->getConfig()->getConfigParam('sCatPromotionsize');

            return \OxidEsales\Eshop\Core\Registry::getPictureHandler()->getPicUrl("category/promo_icon/", $sIcon, $sSize);
        }
    }

    /**
     * Returns category picture url if exist, false - if not
     *
     * @param string $sPicName picture name
     * @param string $sPicType picture type related with picture dir: icon - icon; 0 - image
     *
     * @return mixed
     */
    public function getPictureUrlForType($sPicName, $sPicType)
    {
        if ($sPicName) {
            return $this->getPictureUrl() . $sPicType . '/' . $sPicName;
        } else {
            return false;
        }
    }

    /**
     * Returns true if category parentid is 'oxrootid'
     *
     * @return bool
     */
    public function isTopCategory()
    {
        if ($this->_blTopCategory == null) {
            $this->_blTopCategory = $this->oxcategories__oxparentid->value == 'oxrootid';
        }

        return $this->_blTopCategory;
    }

    /**
     * Returns true if current category is price type ( ( oxpricefrom || oxpriceto ) > 0 )
     *
     * @return bool
     */
    public function isPriceCategory()
    {
        return (bool) ($this->oxcategories__oxpricefrom->value || $this->oxcategories__oxpriceto->value);
    }

    /**
     * Returns long description, parsed through smarty. should only be used by exports or so.
     * In templates use [{oxeval var=$oCategory->oxcategories__oxlongdesc->getRawValue()}]
     *
     * @return string
     */
    public function getLongDesc()
    {
        if (isset($this->oxcategories__oxlongdesc) && $this->oxcategories__oxlongdesc instanceof \OxidEsales\Eshop\Core\Field) {
            /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
            $oUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();
            return $oUtilsView->parseThroughSmarty($this->oxcategories__oxlongdesc->getRawValue(), $this->getId() . $this->getLanguage(), null, true);
        }
    }

    /**
     * Returns short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->oxcategories__oxdesc->value;
    }

    /**
     * Returns category title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->oxcategories__oxtitle->value;
    }

    /**
     * Gets one field from all of subcategories.
     * Default is set to 'OXID'
     *
     * @param string $sField field to be retrieved from each subcategory
     * @param string $sOXID  Cetegory ID
     *
     * @return array
     */
    public function getFieldFromSubCategories($sField = 'OXID', $sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }
        if (!$sOXID) {
            return false;
        }

        $sTable = $this->getViewName();
        $sField = "`{$sTable}`.`{$sField}`";
        $sSql = "SELECT $sField FROM `{$sTable}` WHERE `OXROOTID` = :oxrootid AND `OXPARENTID` != 'oxrootid'";
        $aResult = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getCol($sSql, [
            ':oxrootid' => $sOXID
        ]);

        return $aResult;
    }
}
