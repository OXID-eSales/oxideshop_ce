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
 * Admin article main discount manager.
 * There is possibility to change discount name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 * @package admin
 */
class Discount_Articles extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates discount category tree,
     * passes data to Smarty engine and returns name of template file "discount_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if ( $soxId != '-1' && isset( $soxId)) {
            // load object
            $oDiscount = oxNew( 'oxdiscount' );
            $oDiscount->load( $soxId );
            $this->_aViewData['edit'] = $oDiscount;

            //disabling derived items
            if ($oDiscount->isDerived())
                $this->_aViewData['readonly'] =  true;

            // generating category tree for artikel choose select list
            $this->_createCategoryTree( "artcattree" );
        }

        $iAoc = oxConfig::getParameter("aoc");
        if ( $iAoc == 1 ) {
            $oDiscountArticlesAjax = oxNew( 'discount_articles_ajax' );
            $this->_aViewData['oxajax'] = $oDiscountArticlesAjax->getColumns();

            return "popups/discount_articles.tpl";
        } elseif ( $iAoc == 2 ) {
            $oDiscountCategoriesAjax = oxNew( 'discount_categories_ajax' );
            $this->_aViewData['oxajax'] = $oDiscountCategoriesAjax->getColumns();

            return "popups/discount_categories.tpl";
        }
        return 'discount_articles.tpl';
    }
}
