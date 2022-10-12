<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

interface ModuleDependencyServiceInterface
{
    public function getModuleId(): string;

    public function getDependencies(): array;
}
