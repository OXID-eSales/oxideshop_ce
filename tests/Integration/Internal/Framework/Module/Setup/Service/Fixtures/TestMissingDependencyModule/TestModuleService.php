<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service\Fixtures\TestMissingDependencyModule;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service\Fixtures\TestAnotherDependentModule\TestAnotherModuleService;

/**
 * @internal
 */
class TestModuleService extends TestAnotherModuleService
{
    public function doSomething(): bool
    {
        return false;
    }
}
