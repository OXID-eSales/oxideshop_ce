<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class contains OXID eShop version.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ShopVersion
{
    /**
     * @return string
     */
    public static function getVersion()
    {
        return '6.0.0-rc.2';
    }
}
