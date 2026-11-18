<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;

class UnknownClassProbingActiveModulesDataProviderBridge implements ActiveModulesDataProviderBridgeInterface
{
    public function __construct()
    {
        class_exists('Vendor\NotInstalledPackage\SomeClass');
    }

    public function getModuleIds(): array
    {
        return [];
    }

    public function getModulePaths(): array
    {
        return [];
    }

    public function getControllers(): array
    {
        return [];
    }

    public function getClassExtensions(): array
    {
        return [];
    }
}
