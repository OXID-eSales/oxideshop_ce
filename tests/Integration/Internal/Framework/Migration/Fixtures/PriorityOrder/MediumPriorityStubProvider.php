<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\PriorityOrder;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationPathProviderInterface;

class MediumPriorityStubProvider implements MigrationPathProviderInterface
{
    public function getMigrationConfigPath(): string
    {
        return __DIR__;
    }
}
