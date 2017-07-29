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

namespace OxidEsales\EshopCommunity\Core;

/**
 * Static class mostly containing static methods which are supposed to be called before the full framework initialization
 */
class Oxid
{

    /**
     * Executes main shop controller
     *
     * @static
     *
     * @return void
     */
    public static function run()
    {
        /** @var ShopControl $shopControl */
        $shopControl = oxNew(\OxidEsales\Eshop\Core\ShopControl::class);

        return $shopControl->start();
    }

    /**
     * Executes shop widget controller
     *
     * @static
     *
     * @return void
     */
    public static function runWidget()
    {
        /** @var WidgetControl $widgetControl */
        $widgetControl = oxNew(\OxidEsales\Eshop\Core\WidgetControl::class);

        return $widgetControl->start();
    }
}
