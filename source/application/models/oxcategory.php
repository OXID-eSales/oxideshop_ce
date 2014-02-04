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
 * Category manager.
 * Collects category information (articles, etc.), performs insertion/deletion
 * of categories nodes. By recursion methods are set structure of category.
 *
 * @package model
 */
class oxCategory extends oxI18n implements oxIUrl
{
    /**
     * Subcategories array.
     * @var array
     */
    protected $_aSubCats = array();

    /**
     * Content category array.
     * @var array
     */
    protected $_aContentCats = array();

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
    protected $_aStdUrls = array();

    /**
     * Seo article urls for languages
     *
     * @var array
     */
    protected $_aSeoUrls = array();

    /**
     * Category attributes cache
     * @var array
     */
    protected static $_aCatAttributes = array();

    /**
     * Parent category object container.
     *
     * @var oxCategory
     */
    protected $_oParent = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxcategories' );
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
    public function __get( $sName )
    {
        switch ( $sName ) {
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
    protected function _loadFromDb( $sOXID )
    {
        $sSelect = $this->buildSelectString( array( $this->getViewName().".oxid" => $sOXID ));
        $aData = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getRow( $sSelect );
        return $aData;
    }

     /**
     * Load category data
     *
     * @param string $sOXID id
     *
     * @return null
     */
    public function load( $sOXID )
    {

            $aData = $this->_loadFromDb( $sOXID );

        if ( $aData ) {
            $this->assign( $aData );
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
    public function assign( $dbRecord )
    {
        $this->_iNrOfArticles = null;
        return parent::assign( $dbRecord );
    }

    /**
     * Delete empty categories, returns true on success.
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$this->getId() ) {
            $this->load( $sOXID );
        }

        $sOXID = isset( $sOXID ) ? $sOXID : $this->getId();


        $myConfig = $this->getConfig();
        $oDb      = oxDb::getDb();
        $blRet    = false;

        if ( $this->oxcategories__oxright->value == ($this->oxcategories__oxleft->value+1) ) {
            $myUtilsPic = oxRegistry::get("oxUtilsPic");
            $sDir = $myConfig->getPictureDir(false);

            // only delete empty categories
            // #1173M - not all pic are deleted, after article is removed
            $myUtilsPic->safePictureDelete( $this->oxcategories__oxthumb->value, $sDir . oxRegistry::get("oxUtilsFile")->getImageDirByType('TC'), 'oxcategories', 'oxthumb' );
            $myUtilsPic->safePictureDelete( $this->oxcategories__oxicon->value, $sDir . oxRegistry::get("oxUtilsFile")->getImageDirByType('CICO'), 'oxcategories', 'oxicon' );
            $myUtilsPic->safePictureDelete( $this->oxcategories__oxpromoicon->value, $sDir . oxRegistry::get("oxUtilsFile")->getImageDirByType('PICO'), 'oxcategories', 'oxpromoicon' );

            $sAdd = " and oxshopid = '" . $this->getShopId() . "' ";

            $oDb->execute( "UPDATE oxcategories SET OXLEFT = OXLEFT - 2
                            WHERE  OXROOTID = ".$oDb->quote($this->oxcategories__oxrootid->value)."
                            AND OXLEFT >   ".((int) $this->oxcategories__oxleft->value).$sAdd );

            $oDb->execute( "UPDATE oxcategories SET OXRIGHT = OXRIGHT - 2
                            WHERE  OXROOTID = ".$oDb->quote($this->oxcategories__oxrootid->value)."
                            AND OXRIGHT >   ".((int) $this->oxcategories__oxright->value).$sAdd );

            // delete entry
            $blRet = parent::delete( $sOXID );

            $sOxidQuoted = $oDb->quote( $sOXID );
            // delete links to articles
            $oDb->execute( "delete from oxobject2category where oxobject2category.oxcatnid=$sOxidQuoted ");

            // #657 ADDITIONAL delete links to attributes
            $oDb->execute( "delete from oxcategory2attribute where oxcategory2attribute.oxobjectid=$sOxidQuoted ");

            // A. removing assigned:
            // - deliveries
            $oDb->execute( "delete from oxobject2delivery where oxobject2delivery.oxobjectid=$sOxidQuoted ");
            // - discounts
            $oDb->execute( "delete from oxobject2discount where oxobject2discount.oxobjectid=$sOxidQuoted ");

            oxRegistry::get("oxSeoEncoderCategory")->onDeleteCategory($this);
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
     *
     * @return null
     */
    public function setSubCats( $aCats )
    {
        $this->_aSubCats = $aCats;

        foreach ( $aCats as $oCat ) {

            // keeping ref. to parent
            $oCat->setParentCategory( $this );

            if ( $oCat->getIsVisible() ) {
                $this->setHasVisibleSubCats( true );
            }
        }
    }

    /**
     * sets a single category, handles sorting and parent hasVisibleSubCats
     *
     * @param oxCategory $oCat the category
     * @param string     $sKey (optional, default=null)  the key for that category, without a key, the category is just added to the array
     *
     * @return null
     */
    public function setSubCat( $oCat, $sKey=null )
    {
        if ( $sKey ) {
            $this->_aSubCats[$sKey] = $oCat;
        } else {
            $this->_aSubCats[] = $oCat;
        }

        // keeping ref. to parent
        $oCat->setParentCategory( $this );

        if ( $oCat->getIsVisible() ) {
            $this->setHasVisibleSubCats( true );
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
     *
     * @return null
     */
    public function setContentCats( $aContent )
    {
        $this->_aContentCats = $aContent;
    }

    /**
     * sets a single category
     *
     * @param oxCategory $oContent the category
     * @param string     $sKey     optional, the key for that category, without a key, the category is just added to the array
     *
     * @return null
     */
    public function setContentCat( $oContent, $sKey=null )
    {
        if ( $sKey ) {
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

        if ( !isset($this->_iNrOfArticles)
          && !$this->isAdmin()
          && (
                 $myConfig->getConfigParam( 'bl_perfShowActionCatArticleCnt' )
              || $myConfig->getConfigParam('blDontShowEmptyCategories')
             ) ) {

            if ( $this->isPriceCategory() ) {
                $this->_iNrOfArticles = oxRegistry::get("oxUtilsCount")->getPriceCatArticleCount( $this->getId(), $this->oxcategories__oxpricefrom->value, $this->oxcategories__oxpriceto->value );
            } else {
                $this->_iNrOfArticles = oxRegistry::get("oxUtilsCount")->getCatArticleCount( $this->getId() );
            }
        }

        return (int)$this->_iNrOfArticles;
    }

    /**
     * sets the number or articles in category
     *
     * @param int $iNum category product count setter
     *
     * @return null
     */
    public function setNrOfArticles( $iNum )
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
        if (!isset( $this->_blIsVisible ) ) {

            if ( $this->getConfig()->getConfigParam( 'blDontShowEmptyCategories' ) ) {
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
     *
     * @return null
     */
    public function setIsVisible( $blVisible )
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
        if ( $this->_sDynImageDir === null ) {
            $sThisShop = $this->oxcategories__oxshopid->value;
            $this->_sDynImageDir = $this->getConfig()->getPictureUrl( null, false, null, null, $sThisShop);
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
    public function getBaseSeoLink( $iLang, $iPage = 0 )
    {
        $oEncoder = oxRegistry::get("oxSeoEncoderCategory");
        if ( !$iPage ) {
            return $oEncoder->getCategoryUrl( $this, $iLang );
        }
        return $oEncoder->getCategoryPageUrl( $this, $iPage, $iLang );
    }

    /**
     * returns the url of the category
     *
     * @param int $iLang language id
     *
     * @return string
     */
    public function getLink( $iLang = null )
    {
        if ( !oxRegistry::getUtils()->seoIsActive() ||
             ( isset( $this->oxcategories__oxextlink ) && $this->oxcategories__oxextlink->value ) ) {
            return $this->getStdLink( $iLang );
        }

        if ( $iLang === null ) {
            $iLang = $this->getLanguage();
        }

        if ( !isset( $this->_aSeoUrls[$iLang] ) ) {
            $this->_aSeoUrls[$iLang] = $this->getBaseSeoLink( $iLang );
        }
        return $this->_aSeoUrls[$iLang];
    }

    /**
     * sets the url of the category
     *
     * @param string $sLink category url
     *
     * @return null
     */
    public function setLink( $sLink )
    {
        $iLang = $this->getLanguage();
        if ( oxRegistry::getUtils()->seoIsActive() ) {
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
    public function getSqlActiveSnippet( $blForceCoreTable = null )
    {
        $sQ  = parent::getSqlActiveSnippet( $blForceCoreTable );

        $sTable = $this->getViewName($blForceCoreTable);
        $sQ .= ( strlen( $sQ )? ' and ' : '' ) . " $sTable.oxhidden = '0' ";


        return "( $sQ ) ";
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
    public function getBaseStdLink( $iLang, $blAddId = true, $blFull = true )
    {
        if ( isset( $this->oxcategories__oxextlink ) && $this->oxcategories__oxextlink->value ) {
            return  $this->oxcategories__oxextlink->value;
        }

        $sUrl = '';
        if ( $blFull ) {
            //always returns shop url, not admin
            $sUrl = $this->getConfig()->getShopUrl( $iLang, false );
        }

        //always returns shop url, not admin
        return $sUrl . "index.php?cl=alist" . ( $blAddId ? "&amp;cnid=".$this->getId() : "" );
    }

    /**
     * Returns standard URL to category
     *
     * @param int   $iLang   language
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink( $iLang = null, $aParams = array() )
    {
        if ( isset( $this->oxcategories__oxextlink ) && $this->oxcategories__oxextlink->value ) {
            return  oxRegistry::get("oxUtilsUrl")->processUrl( $this->oxcategories__oxextlink->value, false );
        }

        if ( $iLang === null ) {
            $iLang = $this->getLanguage();
        }

        if ( !isset( $this->_aStdUrls[$iLang] ) ) {
            $this->_aStdUrls[$iLang] = $this->getBaseStdLink( $iLang );
        }

        return oxRegistry::get("oxUtilsUrl")->processUrl( $this->_aStdUrls[$iLang], true, $aParams, $iLang );
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
     *
     * @return null
     */
    public function setExpanded( $blExpanded )
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
        if ( !isset( $this->_blHasSubCats ) ) {
            $this->_blHasSubCats = $this->oxcategories__oxright->value > $this->oxcategories__oxleft->value + 1 ;
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
        if ( !isset( $this->_blHasVisibleSubCats ) ) {
            $this->_blHasVisibleSubCats = false;
        }

        return $this->_blHasVisibleSubCats;
    }

    /**
     * sets the state of has visible sub categories for the category
     *
     * @param bool $blHasVisibleSubcats marker if category has visible subcategories
     *
     * @return null
     */
    public function setHasVisibleSubCats( $blHasVisibleSubcats )
    {
        if ( $blHasVisibleSubcats && !$this->_blHasVisibleSubCats ) {
            unset( $this->_blIsVisible );
            if ($this->_oParent instanceof oxCategory) {
                $this->_oParent->setHasVisibleSubCats( true );
            }
        }
        $this->_blHasVisibleSubCats = $blHasVisibleSubcats;
    }

    /**
     * Loads and returns attribute list associated with this category
     *
     * @return array
     */
    public function getAttributes()
    {
        $sActCat = $this->getId();

        $sKey = md5( $sActCat . serialize( oxSession::getVar( 'session_attrfilter' ) ) );
        if ( !isset( self::$_aCatAttributes[$sKey] ) ) {
            $oAttrList = oxNew( "oxAttributeList" );
            $oAttrList->getCategoryAttributes( $sActCat, $this->getLanguage() );
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
    public function getCatInLang( $oActCategory = null )
    {
        $oCategoryInDefaultLanguage= oxNew( "oxCategory" );
        if ( $this->isPriceCategory() ) {
            // get it in base language
            $oCategoryInDefaultLanguage= oxNew( "oxCategory" );
            $oCategoryInDefaultLanguage->loadInLang( 0, $this->getId());
        } else {
            $oCategoryInDefaultLanguage= oxNew( "oxCategory" );
            $oCategoryInDefaultLanguage->loadInLang( 0, $oActCategory->getId());
        }
        return $oCategoryInDefaultLanguage;
    }

    /**
     * Set parent category object for internal usage only.
     *
     * @param oxCategory $oCategory parent category object
     *
     * @return null
     */
    public function setParentCategory( $oCategory )
    {
        $this->_oParent = $oCategory;
    }

    /**
     * Returns parent category object for current category (if it is available).
     *
     * @return oxCategory
     */
    public function getParentCategory()
    {
        $oCat = null;

        // loading only if parent id is not rootid
        if ( $this->oxcategories__oxparentid->value && $this->oxcategories__oxparentid->value != 'oxrootid' ) {

            // checking if object itself has ref to parent
            if ( $this->_oParent ) {
                $oCat = $this->_oParent;
            } else {
                $oCat = oxNew( 'oxCategory' );
                if ( !$oCat->load( $this->oxcategories__oxparentid->value ) ) {
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
        if ( !isset( $sCategoryId ) ) {
            return;
        }
        $oDb = oxDb::getDb();

        return $oDb->getOne( 'select oxrootid from '.getViewName('oxcategories').' where oxid = ' . $oDb->quote( $sCategoryId ) );
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
            if ( $this->assignRecord( $sSelect ) ) {
                return  true;
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



        if ( $this->oxcategories__oxparentid->value != "oxrootid") {
            // load parent
            $oParent = oxNew( "oxCategory" );
            //#M317 check if parent is loaded
            if ( !$oParent->load( $this->oxcategories__oxparentid->value) ) {
                return false;
            }

            $sAdd = " and oxshopid = '" . $this->getShopId() . "' ";

            // update existing nodes
            $oDb = oxDb::getDb();
            $oDb->execute( "UPDATE oxcategories SET OXLEFT = OXLEFT + 2
                            WHERE  OXROOTID = ".$oDb->quote($oParent->oxcategories__oxrootid->value)."
                            AND OXLEFT >   ".((int) $oParent->oxcategories__oxright->value)."
                            AND OXRIGHT >= ".((int) $oParent->oxcategories__oxright->value).$sAdd);


            $oDb->execute( "UPDATE oxcategories SET OXRIGHT = OXRIGHT + 2
                            WHERE  OXROOTID = ".$oDb->quote($oParent->oxcategories__oxrootid->value)."
                            AND OXRIGHT >= ".((int) $oParent->oxcategories__oxright->value).$sAdd );

            if ( !$this->getId() ) {
                $this->setId();
            }

            $this->oxcategories__oxrootid = new oxField($oParent->oxcategories__oxrootid->value, oxField::T_RAW);
            $this->oxcategories__oxleft = new oxField($oParent->oxcategories__oxright->value, oxField::T_RAW);
            $this->oxcategories__oxright = new oxField($oParent->oxcategories__oxright->value + 1, oxField::T_RAW);
            return parent::_insert();
        } else {
            // root entry
            if ( !$this->getId() ) {
                $this->setId();
            }

            $this->oxcategories__oxrootid = new oxField($this->getId(), oxField::T_RAW);
            $this->oxcategories__oxleft = new oxField(1, oxField::T_RAW);
            $this->oxcategories__oxright = new oxField(2, oxField::T_RAW);
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

        $oDb = oxDb::getDb();
        $sOldParentID = $oDb->getOne( "select oxparentid from oxcategories where oxid = ".$oDb->quote( $this->getId() ), false, false );

        if ( $this->_blIsSeoObject && $this->isAdmin() ) {
            oxRegistry::get("oxSeoEncoderCategory")->markRelatedAsExpired($this);
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
        if ( $this->oxcategories__oxparentid->value != $sOldParentID) {
            $sOldParentLeft = $this->oxcategories__oxleft->value;
            $sOldParentRight = $this->oxcategories__oxright->value;

            $iTreeSize = $sOldParentRight-$sOldParentLeft+1;

            $sNewRootID = $oDb->getOne( "select oxrootid from oxcategories where oxid = ".$oDb->quote($this->oxcategories__oxparentid->value), false, false);

            //If empty rootID, we set it to categorys oxid
            if ( $sNewRootID == "") {
                //echo "<br>* ) Creating new root tree ( {$this->_sOXID} )";
                $sNewRootID = $this->getId();
            }

            $sNewParentLeft = $oDb->getOne( "select oxleft from oxcategories where oxid = ".$oDb->quote($this->oxcategories__oxparentid->value), false, false);

            //if(!$sNewParentLeft){
                //the current node has become root node, (oxrootid == "oxrootid")
            //    $sNewParentLeft = 0;
            //}

            $iMoveAfter = $sNewParentLeft+1;

            //New parentid can not be set to it's child
            if ($sNewParentLeft > $sOldParentLeft && $sNewParentLeft < $sOldParentRight && $this->oxcategories__oxrootid->value == $sNewRootID) {
                //echo "<br>* ) Can't asign category to it's child";

                //Restoring old parentid, stoping further actions
                $sRestoreOld = "UPDATE oxcategories SET OXPARENTID = ".$oDb->quote($sOldParentID)." WHERE oxid = ".$oDb->quote($this->getId());
                $oDb->execute( $sRestoreOld );
                return false;
            }

            //Old parent will be shifted too, if it is in the same tree
            if ($sOldParentLeft > $iMoveAfter && $this->oxcategories__oxrootid->value == $sNewRootID) {
                $sOldParentLeft += $iTreeSize;
                $sOldParentRight += $iTreeSize;
            }

            $iDelta = $iMoveAfter-$sOldParentLeft;

            //echo "Size=$iTreeSize, NewStart=$iMoveAfter, delta=$iDelta";

            $sAddOld = " and oxshopid = '" . $this->getShopId() . "' and OXROOTID = ".$oDb->quote($this->oxcategories__oxrootid->value).";";
            $sAddNew = " and oxshopid = '" . $this->getShopId() . "' and OXROOTID = ".$oDb->quote($sNewRootID).";";

            //Updating everything after new position
            $oDb->execute( "UPDATE oxcategories SET OXLEFT = (OXLEFT + ".$iTreeSize.") WHERE OXLEFT >= ".$iMoveAfter.$sAddNew );
            $oDb->execute( "UPDATE oxcategories SET OXRIGHT = (OXRIGHT + ".$iTreeSize.") WHERE OXRIGHT >= ".$iMoveAfter.$sAddNew );
            //echo "<br>1.) + $iTreeSize, >= $iMoveAfter";

            $sChangeRootID = "";
            if ($this->oxcategories__oxrootid->value != $sNewRootID) {
                //echo "<br>* ) changing root IDs ( {$this->oxcategories__oxrootid->value} -> {$sNewRootID} )";
                $sChangeRootID = ", OXROOTID=".$oDb->quote($sNewRootID);
            }

            //Updating subtree
            $oDb->execute( "UPDATE oxcategories SET OXLEFT = (OXLEFT + ".$iDelta."), OXRIGHT = (OXRIGHT + ".$iDelta.") ".$sChangeRootID." WHERE OXLEFT >= ".$sOldParentLeft." AND OXRIGHT <= ".$sOldParentRight.$sAddOld );
            //echo "<br>2.) + $iDelta, >= $sOldParentLeft and <= $sOldParentRight";

            //Updating everything after old position
            $oDb->execute( "UPDATE oxcategories SET OXLEFT = (OXLEFT - ".$iTreeSize.") WHERE OXLEFT >=   ".($sOldParentRight+1).$sAddOld );
            $oDb->execute( "UPDATE oxcategories SET OXRIGHT = (OXRIGHT - ".$iTreeSize.") WHERE OXRIGHT >=   ".($sOldParentRight+1).$sAddOld );
            //echo "<br>3.) - $iTreeSize, >= ".($sOldParentRight+1);
        }

        if ( $blRes && $this->_blIsSeoObject && $this->isAdmin() ) {
            oxRegistry::get("oxSeoEncoderCategory")->markRelatedAsExpired($this);
        }

        return $blRes;
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT)
    {
        //preliminary quick check saves 3% of execution time in category lists by avoiding redundant strtolower() call
        if ($sFieldName[2] == 'l' || $sFieldName[2] == 'L' || (isset($sFieldName[16]) && ($sFieldName[16] == 'l' || $sFieldName[16] == 'L') ) ) {
            if ('oxlongdesc' === strtolower($sFieldName) || 'oxcategories__oxlongdesc' === strtolower($sFieldName)) {
                $iDataType = oxField::T_RAW;
            }
        }
        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }


    /**
     * Returns category icon picture url if exist, false - if not
     *
     * @return mixed
     */
    public function getIconUrl()
    {
        if ( ( $sIcon = $this->oxcategories__oxicon->value ) ) {
            $oConfig = $this->getConfig();
            $sSize = $oConfig->getConfigParam( 'sCatIconsize' );
            if ( !isset( $sSize ) ) {
                $sSize = $oConfig->getConfigParam( 'sIconsize' );
            }

            return oxRegistry::get("oxPictureHandler")->getPicUrl( "category/icon/", $sIcon, $sSize );
        }
    }

    /**
     * Returns category thumbnail picture url if exist, false - if not
     *
     * @return mixed
     */
    public function getThumbUrl()
    {
        if ( ( $sIcon = $this->oxcategories__oxthumb->value ) ) {
            $sSize = $this->getConfig()->getConfigParam( 'sCatThumbnailsize' );
            return oxRegistry::get("oxPictureHandler")->getPicUrl( "category/thumb/", $sIcon, $sSize );
        }
    }

    /**
     * Returns category promotion icon picture url if exist, false - if not
     *
     * @return mixed
     */
    public function getPromotionIconUrl()
    {
        if ( ( $sIcon = $this->oxcategories__oxpromoicon->value ) ) {
            $sSize = $this->getConfig()->getConfigParam( 'sCatPromotionsize' );
            return oxRegistry::get("oxPictureHandler")->getPicUrl( "category/promo_icon/", $sIcon, $sSize );
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
    public function getPictureUrlForType( $sPicName, $sPicType )
    {
        if ( $sPicName ) {
            return $this->getPictureUrl() . $sPicType . '/' . $sPicName;
        } else {
            return false;
        }
    }

    /**
     * Returns true is category parent id is 'oxrootid'
     *
     * @return bool
     */
    public function isTopCategory()
    {
        if ( $this->_blTopCategory == null ) {
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
        return (bool) ( $this->oxcategories__oxpricefrom->value || $this->oxcategories__oxpriceto->value );
    }

    /**
     * Returns long description, parsed through smarty. should only be used by exports or so.
     * In templates use [{oxeval var=$oCategory->oxcategories__oxlongdesc->getRawValue()}]
     *
     * @return string
     */
    public function getLongDesc()
    {
        if ( isset( $this->oxcategories__oxlongdesc ) && $this->oxcategories__oxlongdesc instanceof oxField ) {
           return oxRegistry::get("oxUtilsView")->parseThroughSmarty( $this->oxcategories__oxlongdesc->getRawValue(), $this->getId().$this->getLanguage() );
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
}
