<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\without_own_module_namespace\Application\Model;

class TestModuleTwoModel
{
    public function getInfo(): string
    {
        return 'TestModuleTwoModel info';
    }
}
