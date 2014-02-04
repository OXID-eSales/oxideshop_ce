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
 * Discount list manager.
 * Organizes list of discount objects.
 *
 * @package model
 */
class oxDiscountList extends oxList
{
    /**
     * Discount list inst.
     *
     * @var oxdiscountlist
     */
    static protected $_instance = null;

    /**
     * Discount user id
     *
     * @var string User ID
     */
    protected $_sUserId = null;

    /**
     * Forced list reload marker
     *
     * @var bool
     */
    protected $_blReload = true;


    /**
    * If any shops category has "skip discounts" status this parameter value will be true
    *
    * @var bool
    */
    protected $_hasSkipDiscountCategories = null;

    /**
     * Class Constructor
     *
     * @param string $sObjectsInListName Associated list item object type
     */
    public function __construct( $sObjectsInListName = 'oxdiscount' )
    {
        parent::__construct( 'oxdiscount' );
    }

    /**
     * Returns discount list instance
     *
     * @deprecated since v5.0 (2012-08-10); Use Registry getter instead - oxRegistry::get("oxDiscountList");
     *
     * @return oxDiscountList
     */
    static public function getInstance()
    {
        return oxRegistry::get("oxDiscountList");
    }

    /**
     * Initializes current state discount list
     * For iterating through the list, use getArray() on the list,
     * as iterating on object itself can cause concurrency problems.
     *
     * @param object $oUser user object (optional)
     *
     * @return array
     */
    protected function _getList( $oUser = null )
    {
        $sUserId = $oUser?$oUser->getId():'';

        if ( $this->_blReload || $sUserId !== $this->_sUserId ) {
            // loading list
            $this->selectString( $this->_getFilterSelect( $oUser ) );

            // setting list proterties
            $this->_blReload = false;    // reload marker
            $this->_sUserId  = $sUserId; // discount list user id
        }

        // resetting array pointer
        $this->rewind();

        return $this;
    }

    /**
     * Returns user country id for for discount selection
     *
     * @param oxuser $oUser oxuser object
     *
     * @return string
     */
    public function getCountryId( $oUser )
    {
        $sCountryId = null;
        if ( $oUser ) {
            $sCountryId = $oUser->getActiveCountry();
        }

        return $sCountryId;
    }

    /**
     * Used to force discount list reload
     *
     * @return null
     */
    public function forceReload()
    {
        $this->_blReload = true;
    }

    /**
     * Creates discount list filter SQL to load current state discount list
     *
     * @param object $oUser user object
     *
     * @return string
     */
    protected function _getFilterSelect( $oUser )
    {
        $oBaseObject = $this->getBaseObject();

        $sTable = $oBaseObject->getViewName();
        $sQ  = "select ".$oBaseObject->getSelectFields()." from $sTable ";
        $sQ .= "where ".$oBaseObject->getSqlActiveSnippet().' ';


        // defining initial filter parameters
        $sUserId    = null;
        $sGroupIds  = null;
        $sCountryId = $this->getCountryId( $oUser );
        $oDb = oxDb::getDb();

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ( $oUser ) {

            // user ID
            $sUserId = $oUser->getId();

            // user group ids
            foreach ( $oUser->getUserGroups() as $oGroup ) {
                if ( $sGroupIds ) {
                    $sGroupIds .= ', ';
                }
                $sGroupIds .= $oDb->quote( $oGroup->getId() );
            }
        }

        $sUserTable    = getViewName( 'oxuser' );
        $sGroupTable   = getViewName( 'oxgroups' );
        $sCountryTable = getViewName( 'oxcountry' );

        $sCountrySql = $sCountryId?"EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' and oxobject2discount.OXOBJECTID=".$oDb->quote( $sCountryId ).")":'0';
        $sUserSql    = $sUserId   ?"EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' and oxobject2discount.OXOBJECTID=".$oDb->quote( $sUserId ). ")":'0';
        $sGroupSql   = $sGroupIds ?"EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' and oxobject2discount.OXOBJECTID in ($sGroupIds) )":'0';

        $sQ .= "and (
            select
                if(EXISTS(select 1 from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1),
                        $sCountrySql,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1),
                        $sUserSql,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1),
                        $sGroupSql,
                        1)
            )";

        return $sQ;
    }

    /**
     * Returns array of discounts that can be globally (transparently) applied
     *
     * @param object $oArticle article object
     * @param object $oUser    oxuser object (optional)
     *
     * @return array
     */
    public function getArticleDiscounts( $oArticle, $oUser = null )
    {
        $aList = array();
        $aDiscList = $this->_getList( $oUser )->getArray();
        foreach ( $aDiscList as $oDiscount ) {
            if ( $oDiscount->isForArticle( $oArticle ) ) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    /**
     * Returns array of discounts that can be applied for individual basket item
     *
     * @param mixed  $oArticle article object or article id (according to needs)
     * @param object $oBasket  array of basket items containing article id, amount and price
     * @param object $oUser    user object (optional)
     *
     * @return array
     */
    public function getBasketItemDiscounts( $oArticle, $oBasket, $oUser = null )
    {
        $aList = array();
        $aDiscList = $this->_getList( $oUser )->getArray();
        foreach ( $aDiscList as $oDiscount ) {
            if ( $oDiscount->isForBasketItem( $oArticle ) && $oDiscount->isForBasketAmount( $oBasket ) ) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    /**
     * Returns array of discounts that can be applied for whole basket
     *
     * @param object $oBasket basket
     * @param object $oUser   user object (optional)
     *
     * @return array
     */
    public function getBasketDiscounts( $oBasket, $oUser = null )
    {
        $aList = array();
        $aDiscList = $this->_getList( $oUser )->getArray();
        foreach ( $aDiscList as $oDiscount ) {
            if ( $oDiscount->isForBasket( $oBasket ) ) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    /**
     * Returns array of bundle discounts that can be applied for whole basket
     *
     * @param object $oArticle article object
     * @param object $oBasket  basket
     * @param object $oUser    user object (optional)
     *
     * @return array
     */
    public function getBasketItemBundleDiscounts( $oArticle, $oBasket, $oUser = null )
    {
        $aList = array();
        $aDiscList = $this->_getList( $oUser )->getArray();
        foreach ( $aDiscList as $oDiscount ) {
            if ( $oDiscount->isForBundleItem( $oArticle, $oBasket ) && $oDiscount->isForBasketAmount($oBasket) ) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    /**
     * Returns array of basket bundle discounts
     *
     * @param oxbasket $oBasket oxbasket object
     * @param oxuser   $oUser   oxuser object (optional)
     *
     * @return array
     */
    public function getBasketBundleDiscounts( $oBasket, $oUser = null )
    {
        $aList = array();
        $aDiscList = $this->_getList( $oUser )->getArray();
        foreach ( $aDiscList as $oDiscount ) {
            if ( $oDiscount->isForBundleBasket( $oBasket ) ) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    /**
     * Applies discounts which should be applied in general case (for 0 amount)
     *
     * @param oxprice $oPrice     Price object
     * @param array   $aDiscounts Discount list
     *
     * @deprecated since v5.0 (2012-09-14); use oxPrice class  discount calculation methods;
     *
     * @return null
     */
    public function applyDiscounts( $oPrice, $aDiscounts )
    {
        reset( $aDiscounts );
        while ( list( , $oDiscount ) = each( $aDiscounts ) ) {
            $oDiscount->applyDiscount( $oPrice );
        }
    }

    /**
     * Applies discounts which are supposed to be applied on amounts greater than zero.
     * Returns applied discounts.
     *
     * @param oxPrice $oPrice     Old article price
     * @param array   $aDiscounts Discount array
     * @param amount  $dAmount    Amount in basket
     *
     * @deprecated since v5.0 (2012-09-14); use oxPrice class  discount calculation methods;
     *
     * @return array
     */
    public function applyBasketDiscounts( oxPrice $oPrice, $aDiscounts, $dAmount = 1 )
    {
        $aDiscLog = array();
        reset( $aDiscounts );

        // price object to correctly perform calculations
        $dOldPrice = $oPrice->getPrice();

        while (list( , $oDiscount ) = each( $aDiscounts ) ) {
            $oDiscount->applyDiscount( $oPrice );
            $dNewPrice = $oPrice->getPrice();

            if ( !isset( $aDiscLog[$oDiscount->getId()] ) ) {
                $aDiscLog[$oDiscount->getId()] = $oDiscount->getSimpleDiscount();
            }

            $aDiscLog[$oDiscount->getId()]->dDiscount += $dOldPrice - $dNewPrice;
            $aDiscLog[$oDiscount->getId()]->dDiscount *= $dAmount;
            $dOldPrice = $dNewPrice;
        }
        return $aDiscLog;
    }


    /**
     * Checks if any category has "skip discounts" status
     *
     * @return bool
     */
    public function hasSkipDiscountCategories()
    {
        if ( $this->_hasSkipDiscountCategories === null  || $this->_blReload ) {
            $sViewName = getViewName( 'oxcategories' );
            $sQ = "select 1 from {$sViewName} where {$sViewName}.oxactive = 1 and {$sViewName}.oxskipdiscounts = '1' ";

            $this->_hasSkipDiscountCategories = (bool) oxDb::getDb()->getOne( $sQ );
        }
        return $this->_hasSkipDiscountCategories;
    }
}
