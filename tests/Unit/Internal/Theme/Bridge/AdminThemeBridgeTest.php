<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Theme\Bridge;

use OxidEsales\EshopCommunity\Internal\Theme\Bridge\AdminThemeBridge;

class AdminThemeBridgeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetActiveTheme()
    {
        $this->assertSame('admin', (new AdminThemeBridge('admin'))->getActiveTheme());
    }
}
