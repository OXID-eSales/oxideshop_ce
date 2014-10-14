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
 * Admin article main delivery manager.
 * There is possibility to change delivery name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 * @package admin
 */
class Delivery_Articles extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates delivery category tree,
     * passes data to Smarty engine and returns name of template file "delivery_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();

        if ( $soxId != "-1" && isset( $soxId)) {
            $this->_createCategoryTree( "artcattree");

            // load object
            $oDelivery = oxNew( "oxdelivery" );
            $oDelivery->load( $soxId);
            $this->_aViewData["edit"] =  $oDelivery;

            //Disable editing for derived articles
            if ($oDelivery->isDerived())
               $this->_aViewData['readonly'] = true;
        }

        $iAoc = oxConfig::getParameter("aoc");
        if ( $iAoc == 1 ) {
            $oDeliveryArticlesAjax = oxNew( 'delivery_articles_ajax' );
            $this->_aViewData['oxajax'] = $oDeliveryArticlesAjax->getColumns();

            return "popups/delivery_articles.tpl";
        } elseif ( $iAoc == 2 ) {
            $oDeliveryCategoriesAjax = oxNew( 'delivery_categories_ajax' );
            $this->_aViewData['oxajax'] = $oDeliveryCategoriesAjax->getColumns();

            return "popups/delivery_categories.tpl";
        }

        return "delivery_articles.tpl";
    }
}
