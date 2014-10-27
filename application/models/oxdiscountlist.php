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
 */
class oxDiscountList extends oxList
{

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
     * @return null
     */
    public function __construct()
    {
        parent::__construct('oxdiscount');
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
    protected function _getList($oUser = null)
    {
        $sUserId = $oUser ? $oUser->getId() : '';

        if ($this->_blReload || $sUserId !== $this->_sUserId) {
            // loading list
            $this->selectString($this->_getFilterSelect($oUser));

            // setting list proterties
            $this->_blReload = false; // reload marker
            $this->_sUserId = $sUserId; // discount list user id
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
    public function getCountryId($oUser)
    {
        $sCountryId = null;
        if ($oUser) {
            $sCountryId = $oUser->getActiveCountry();
        }

        return $sCountryId;
    }

    /**
     * Used to force discount list reload
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
    protected function _getFilterSelect($oUser)
    {
        $oBaseObject = $this->getBaseObject();

        $sTable = $oBaseObject->getViewName();
        $sQ = "select " . $oBaseObject->getSelectFields() . " from $sTable ";
        $sQ .= "where " . $oBaseObject->getSqlActiveSnippet() . ' ';


        // defining initial filter parameters
        $sUserId = null;
        $sGroupIds = null;
        $sCountryId = $this->getCountryId($oUser);
        $oDb = oxDb::getDb();

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser) {

            // user ID
            $sUserId = $oUser->getId();

            // user group ids
            foreach ($oUser->getUserGroups() as $oGroup) {
                if ($sGroupIds) {
                    $sGroupIds .= ', ';
                }
                $sGroupIds .= $oDb->quote($oGroup->getId());
            }
        }

        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $sCountrySql = $sCountryId ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' and oxobject2discount.OXOBJECTID=" . $oDb->quote($sCountryId) . ")" : '0';
        $sUserSql = $sUserId ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' and oxobject2discount.OXOBJECTID=" . $oDb->quote($sUserId) . ")" : '0';
        $sGroupSql = $sGroupIds ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' and oxobject2discount.OXOBJECTID in ($sGroupIds) )" : '0';

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
    public function getArticleDiscounts($oArticle, $oUser = null)
    {
        $aList = array();
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->isForArticle($oArticle)) {
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
    public function getBasketItemDiscounts($oArticle, $oBasket, $oUser = null)
    {
        $aList = array();
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->isForBasketItem($oArticle) && $oDiscount->isForBasketAmount($oBasket)) {
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
    public function getBasketDiscounts($oBasket, $oUser = null)
    {
        $aList = array();
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->isForBasket($oBasket)) {
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
    public function getBasketItemBundleDiscounts($oArticle, $oBasket, $oUser = null)
    {
        $aList = array();
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->isForBundleItem($oArticle, $oBasket) && $oDiscount->isForBasketAmount($oBasket)) {
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
    public function getBasketBundleDiscounts($oBasket, $oUser = null)
    {
        $aList = array();
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->isForBundleBasket($oBasket)) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    /**
     * Checks if any category has "skip discounts" status
     *
     * @return bool
     */
    public function hasSkipDiscountCategories()
    {
        if ($this->_hasSkipDiscountCategories === null || $this->_blReload) {
            $sViewName = getViewName('oxcategories');
            $sQ = "select 1 from {$sViewName} where {$sViewName}.oxactive = 1 and {$sViewName}.oxskipdiscounts = '1' ";

            $this->_hasSkipDiscountCategories = (bool) oxDb::getDb()->getOne($sQ);
        }

        return $this->_hasSkipDiscountCategories;
    }
}
