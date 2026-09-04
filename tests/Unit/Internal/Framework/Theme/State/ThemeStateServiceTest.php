<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateService;
use PHPUnit\Framework\TestCase;

final class ThemeStateServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testIsActiveReturnsTrueForActivatedTheme(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $dao->method('get')->willReturn((new ThemeConfiguration())->setId('active')->setActivated(true));

        $this->assertTrue((new ThemeStateService($dao))->isActive('active', self::SHOP_ID));
    }

    public function testIsActiveReturnsFalseForUninstalledTheme(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);

        $this->assertFalse((new ThemeStateService($dao))->isActive('unknown', self::SHOP_ID));
    }
}
