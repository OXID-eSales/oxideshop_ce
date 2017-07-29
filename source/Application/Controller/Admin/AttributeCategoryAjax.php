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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use Exception;

/**
 * Class manages category attributes
 */
class AttributeCategoryAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array('container1' => array( // field , table,         visible, multilanguage, ident
        array('oxtitle', 'oxcategories', 1, 1, 0),
        array('oxdesc', 'oxcategories', 1, 1, 0),
        array('oxid', 'oxcategories', 0, 0, 0),
        array('oxid', 'oxcategories', 0, 0, 1)
    ),
                                 'container2' => array(
                                     array('oxtitle', 'oxcategories', 1, 1, 0),
                                     array('oxdesc', 'oxcategories', 1, 1, 0),
                                     array('oxid', 'oxcategories', 0, 0, 0),
                                     array('oxid', 'oxcategory2attribute', 0, 0, 1),
                                     array('oxid', 'oxcategories', 0, 0, 1)
                                 ),
                                 'container3' => array(
                                     array('oxtitle', 'oxattribute', 1, 1, 0),
                                     array('oxsort', 'oxcategory2attribute', 1, 0, 0),
                                     array('oxid', 'oxcategory2attribute', 0, 0, 1)
                                 )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sCatTable = $this->_getViewName('oxcategories');
        $sDiscountId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchDiscountId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sDiscountId) {
            $sQAdd = " from {$sCatTable} where {$sCatTable}.oxshopid = '" . $myConfig->getShopId() . "' ";
            $sQAdd .= " and {$sCatTable}.oxactive = '1' ";
        } else {
            $sQAdd = " from {$sCatTable} left join oxcategory2attribute " .
                     "on {$sCatTable}.oxid=oxcategory2attribute.oxobjectid " .
                     " where oxcategory2attribute.oxattrid = " . $oDb->quote($sDiscountId) .
                     " and {$sCatTable}.oxshopid = '" . $myConfig->getShopId() . "' " .
                     " and {$sCatTable}.oxactive = '1' ";
        }

        if ($sSynchDiscountId && $sSynchDiscountId != $sDiscountId) {
            $sQAdd .= " and {$sCatTable}.oxid not in ( select {$sCatTable}.oxid " .
                      "from {$sCatTable} left join oxcategory2attribute " .
                      "on {$sCatTable}.oxid=oxcategory2attribute.oxobjectid " .
                      " where oxcategory2attribute.oxattrid = " . $oDb->quote($sSynchDiscountId) .
                      " and {$sCatTable}.oxshopid = '" . $myConfig->getShopId() . "' " .
                      " and {$sCatTable}.oxactive = '1' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes category from Attributes list
     */
    public function removeCatFromAttr()
    {
        $aChosenCat = $this->_getActionIds('oxcategory2attribute.oxid');

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxcategory2attribute.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenCat)) {
            $sChosenCategories = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenCat));
            $sQ = "delete from oxcategory2attribute where oxcategory2attribute.oxid in (" . $sChosenCategories . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }

        $this->resetContentCache();
    }

    /**
     * Adds category to Attributes list
     *
     * @throws Exception
     */
    public function addCatToAttr()
    {
        $aAddCategory = $this->_getActionIds('oxcategories.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        $oAttribute = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);
        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sCatTable = $this->_getViewName('oxcategories');
            $aAddCategory = $this->_getAll($this->_addFilter("select $sCatTable.oxid " . $this->_getQuery()));
        }

        if ($oAttribute->load($soxId) && is_array($aAddCategory)) {
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->startTransaction();
            try {
                $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
                foreach ($aAddCategory as $sAdd) {
                    $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                    $oNewGroup->init("oxcategory2attribute");
                    $sOxSortField = 'oxcategory2attribute__oxsort';
                    $sObjectIdField = 'oxcategory2attribute__oxobjectid';
                    $sAttributeIdField = 'oxcategory2attribute__oxattrid';
                    $sOxIdField = 'oxattribute__oxid';
                    $oNewGroup->$sObjectIdField = new \OxidEsales\Eshop\Core\Field($sAdd);
                    $oNewGroup->$sAttributeIdField = new \OxidEsales\Eshop\Core\Field($oAttribute->$sOxIdField->value);
                    $sSql = "select max(oxsort) + 1 from oxcategory2attribute where oxobjectid = '$sAdd' ";

                    $oNewGroup->$sOxSortField = new \OxidEsales\Eshop\Core\Field(( int ) $database->getOne($sSql));
                    $oNewGroup->save();
                }
            } catch (Exception $exception) {
                \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->rollbackTransaction();
                throw $exception;
            }
        }
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->commitTransaction();

        $this->resetContentCache();
    }
}
