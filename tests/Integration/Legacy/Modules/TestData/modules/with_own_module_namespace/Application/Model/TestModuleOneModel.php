<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_own_module_namespace\Application\Model;

class TestModuleOneModel
{
    public function getInfo(): string
    {
        return 'TestModuleOneModel info';
    }
}
