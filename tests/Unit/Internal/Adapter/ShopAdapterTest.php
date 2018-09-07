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
    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Adapter\Exception\ModuleNotLoadableException
     */
    public function testInvalidateCacheThrowsExceptionOnNonExistentModuleId()
    {
        $shopAdapter = new ShopAdapter();
        $shopAdapter->invalidateModuleCache(uniqid('test', false));
    }
}
