<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Adapter;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use PHPUnit\Framework\TestCase;

class ShopAdapterTest extends TestCase
{
    public function testGetModuleFullPathReturnsPathWithModuleIdIfModulePathNotExisting()
    {
        $shopAdapter = new ShopAdapter();

        $this->assertContains(
            'moduleId',
            $shopAdapter->getModuleFullPath('moduleId')
        );
    }
}
