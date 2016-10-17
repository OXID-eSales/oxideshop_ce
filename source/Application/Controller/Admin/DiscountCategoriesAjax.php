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

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxDb;
use oxField;

/**
 * Class manages discount categories
 */
class DiscountCategoriesAjax extends \ajaxListComponent
{
    /** If this discount id comes from request, it means that new discount should be created. */
    const NEW_DISCOUNT_ID = "-1";

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        // field , table, visible, multilanguage, id
        'container1' => array(
            array('oxtitle', 'oxcategories', 1, 1, 0),
            array('oxdesc', 'oxcategories', 1, 1, 0),
            array('oxid', 'oxcategories', 0, 0, 0),
            array('oxid', 'oxcategories', 0, 0, 1)
        ),
         'container2' => array(
             array('oxtitle', 'oxcategories', 1, 1, 0),
             array('oxdesc', 'oxcategories', 1, 1, 0),
             array('oxid', 'oxcategories', 0, 0, 0),
             array('oxid', 'oxobject2discount', 0, 0, 1),
             array('oxid', 'oxcategories', 0, 0, 1)
         ),
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oDb = oxDb::getDb();
        $oConfig = $this->getConfig();
        $sId = $oConfig->getRequestParameter('oxid');
        $sSynchId = $oConfig->getRequestParameter('synchoxid');

        $sCategoryTable = $this->_getViewName('oxcategories');

        // category selected or not ?
        if (!$sId) {
            $sQAdd = " from {$sCategoryTable}";
        } else {
            $sQAdd = " from oxobject2discount, {$sCategoryTable} " .
                     "where {$sCategoryTable}.oxid=oxobject2discount.oxobjectid " .
                     " and oxobject2discount.oxdiscountid = " . $oDb->quote($sId) .
                     " and oxobject2discount.oxtype = 'oxcategories' ";
        }

        if ($sSynchId && $sSynchId != $sId) {
            // performance
            $sSubSelect = " select {$sCategoryTable}.oxid from oxobject2discount, {$sCategoryTable} " .
                          "where {$sCategoryTable}.oxid=oxobject2discount.oxobjectid " .
                          " and oxobject2discount.oxdiscountid = " . $oDb->quote($sSynchId) .
                          " and oxobject2discount.oxtype = 'oxcategories' ";
            if (stristr($sQAdd, 'where') === false) {
                $sQAdd .= ' where ';
            } else {
                $sQAdd .= ' and ';
            }
            $sQAdd .= " {$sCategoryTable}.oxid not in ( $sSubSelect ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes selected category (categories) from discount list
     */
    public function removeDiscCat()
    {
        $config = $this->getConfig();
        $categoryIds = $this->_getActionIds('oxobject2discount.oxid');

        if ($config->getRequestParameter('all')) {
            $query = $this->_addFilter("delete oxobject2discount.* " . $this->_getQuery());
            oxDb::getDb()->Execute($query);

        } elseif (is_array($categoryIds)) {
            $chosenCategories = implode(", ", oxDb::getDb()->quoteArray($categoryIds));
            $query = "delete from oxobject2discount where oxobject2discount.oxid in (" . $chosenCategories . ") ";
            oxDb::getDb()->Execute($query);
        }
    }

    /**
     * Adds selected category (categories) to discount list
     */
    public function addDiscCat()
    {
        $config = $this->getConfig();
        $categoryIds = $this->_getActionIds('oxcategories.oxid');
        $discountId = $config->getRequestParameter('synchoxid');

        if ($config->getRequestParameter('all')) {
            $categoryTable = $this->_getViewName('oxcategories');
            $categoryIds = $this->_getAll($this->_addFilter("select $categoryTable.oxid " . $this->_getQuery()));
        }
        if ($discountId && $discountId != self::NEW_DISCOUNT_ID && is_array($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $this->addCategoryToDiscount($discountId, $categoryId);
            }
        }
    }

    /**
     * Adds category to discounts list.
     *
     * @param string $discountId
     * @param string $categoryId
     */
    protected function addCategoryToDiscount($discountId, $categoryId)
    {
        $object2Discount = oxNew("oxBase");
        $object2Discount->init('oxobject2discount');
        $object2Discount->oxobject2discount__oxdiscountid = new oxField($discountId);
        $object2Discount->oxobject2discount__oxobjectid = new oxField($categoryId);
        $object2Discount->oxobject2discount__oxtype = new oxField("oxcategories");

        $object2Discount->save();
    }
}
