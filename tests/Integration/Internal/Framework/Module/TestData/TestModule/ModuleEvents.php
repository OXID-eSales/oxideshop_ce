<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\TestData\TestModule;

/**
 * @internal
 */
class ModuleEvents
{
    public static function onActivate(): void
    {
        echo 'Method onActivate was called';
    }

    public static function onDeactivate(): void
    {
        echo 'Method onDeactivate was called';
    }
}
