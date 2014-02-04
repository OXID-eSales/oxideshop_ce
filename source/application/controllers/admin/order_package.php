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
 * Admin order package manager.
 * Collects order package information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders.
 * @package admin
 */
class Order_Package extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), fetches order info from DB,
     * passes it to Smarty engine and returns name of template file.
     * "order_package.tpl"
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        parent::render();

        $aOrders = oxNew('oxlist');
        $aOrders->init('oxorder');
        $aOrders->selectString( "select * from oxorder where oxorder.oxsenddate = '0000-00-00 00:00:00' and oxorder.oxshopid = '".$myConfig->getShopId()."' order by oxorder.oxorderdate asc limit 5000" );

        $this->_aViewData['resultset'] = $aOrders;

        return "order_package.tpl";
    }
}
