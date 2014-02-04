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
 * Shop list manager.
 * Organizes list of shop objects.
 *
 * @package model
 */
class oxShopList extends oxList
{
    /**
     * Class constructor, sets callback so that Shopowner is able to add any information to the article.
     *
     * @param string $sObjectsInListName Object name (oxShop)
     *
     * @return null
     */
    public function __construct( $sObjectsInListName = 'oxshop')
    {
        return parent::__construct( 'oxshop');
    }

    /**
     * Loads all shops to list
     *
     * @return null
     */
    public function getAll()
    {
        $this->selectString( 'SELECT `oxshops`.* FROM `oxshops`' );
    }

}
