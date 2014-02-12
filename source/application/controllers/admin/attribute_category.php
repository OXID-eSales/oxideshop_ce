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
 * Admin category main attributes manager.
 * There is possibility to change attribute description, assign categories to
 * this attribute, etc.
 * Admin Menu: Manage Products -> Attributes -> Gruppen.
 * @package admin
 */
class Attribute_Category extends oxAdminDetails
{
    /**
     * Loads Attribute categories info, passes it to Smarty engine and
     * returns name of template file "attribute_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

            $aListAllIn = array();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oAttr = oxNew( "oxattribute" );
            $oAttr->load( $soxId);
            $this->_aViewData["edit"] =  $oAttr;
        }

        if ( oxConfig::getParameter("aoc") ) {
            $oAttributeCategoryAjax = oxNew( 'attribute_category_ajax' );
            $this->_aViewData['oxajax'] = $oAttributeCategoryAjax->getColumns();

            return "popups/attribute_category.tpl";
        }
        return "attribute_category.tpl";
    }
}
