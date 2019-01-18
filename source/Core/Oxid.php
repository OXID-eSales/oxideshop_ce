<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
