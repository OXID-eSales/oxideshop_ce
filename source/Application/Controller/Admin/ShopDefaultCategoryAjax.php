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
use oxField;

/**
 * Class controls article assignment to attributes
 */
class ShopDefaultCategoryAjax extends \ajaxListComponent
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
    )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oCat = oxNew('oxCategory');
        $oCat->setLanguage(oxRegistry::getConfig()->getRequestParameter('editlanguage'));

        $sCategoriesTable = $oCat->getViewName();

        return " from $sCategoriesTable where " . $oCat->getSqlActiveSnippet();
    }

    /**
     * Removing article from corssselling list
     */
    public function unassignCat()
    {
        $sShopId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $oShop = oxNew('oxShop');
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new oxField('');
            $oShop->save();
        }
    }

    /**
     * Adding article to corssselling list
     */
    public function assignCat()
    {
        $sChosenCat = oxRegistry::getConfig()->getRequestParameter('oxcatid');
        $sShopId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $oShop = oxNew('oxShop');
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new oxField($sChosenCat);
            $oShop->save();
        }
    }
}
