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
 * Admin article categories order manager.
 * There is possibility to change category sorting.
 * Admin Menu: Manage Products -> Categories -> Order.
 * @package admin
 */
class Category_Order extends oxAdminDetails
{
    /**
     * Loads article category ordering info, passes it to Smarty
     * engine and returns name of template file "category_order.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew( 'oxcategory' );

        // resetting
        oxSession::setVar( 'neworder_sess', null );

        $soxId = $this->getEditObjectId();

        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oCategory->load( $soxId );

            //Disable editing for derived items
            if ( $oCategory->isDerived() ) {
                $this->_aViewData['readonly'] = true;
            }
        }
        if ( oxConfig::getParameter("aoc") ) {
            $oCategoryOrderAjax = oxNew( 'category_order_ajax' );
            $this->_aViewData['oxajax'] = $oCategoryOrderAjax->getColumns();

            return "popups/category_order.tpl";
        }
        return "category_order.tpl";
    }
}
