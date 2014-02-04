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
 * Article interface
 *
 * @package core
 */
interface oxIArticle
{
    /**
     * Checks if stock configuration allows to buy user chosen amount $dAmount
     *
     * @param double $dAmount         buyable amount
     * @param double $dArtStockAmount stock amount
     *
     * @return mixed
     */
    public function checkForStock( $dAmount, $dArtStockAmount = 0 );

    /**
     * Returns all selectlists this article has.
     *
     * @param string $sKeyPrefix Optionall key prefix
     *
     * @return array
     */
    public function getSelectLists( $sKeyPrefix = null );

    /**
     * Creates, calculates and returns oxprice object for basket product.
     *
     * @param double $dAmount  Amount
     * @param string $aSelList Selection list
     * @param object $oBasket  User shopping basket object
     *
     * @return oxPrice
     */
    public function getBasketPrice( $dAmount, $aSelList, $oBasket );

    /**
     * Checks if discount should be skipped for this article in basket. Returns true if yes.
     *
     * @return bool
     */
    public function skipDiscounts();

    /**
     * Returns ID's of categories. where this article is assigned
     *
     * @param bool $blActCats   select categories if all parents are active
     * @param bool $blSkipCache Whether to skip cache
     *
     * @return array
     */
    public function getCategoryIds( $blActCats = false, $blSkipCache = false );

    /**
     * Calculates and returns price of article (adds taxes and discounts).
     *
     * @return oxPrice
     */
    public function getPrice();

    /**
     * Returns product id (oxid)
     *
     * @return string
     */
    public function getProductId();

    /**
     * Returns base article price from database
     *
     * @param double $dAmount article amount. Default is 1
     *
     * @return double
     */
    public function getBasePrice( $dAmount = 1 );

    /**
     * Returns product parent id (oxparentid)
     *
     * @return string
     */
    public function getProductParentId();

    /**
     * Returns true if object is derived from oxorderarticle class
     *
     * @return bool
     */
    public function isOrderArticle();

}
