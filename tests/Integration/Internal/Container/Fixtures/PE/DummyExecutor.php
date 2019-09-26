<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Fixtures\PE;

class DummyExecutor
{
    public function execute()
    {
        return 'Service overwriting for PE!';
    }
}
