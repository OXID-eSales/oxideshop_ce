<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class BackwardsCompatibilityClassMapProvider
{
    public function getMap(): array
    {
        $classMap = include __DIR__ . \DIRECTORY_SEPARATOR . 'BackwardsCompatibilityClassMap.php';

        return $classMap;
    }
}
