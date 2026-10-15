<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationTest extends TestCase
{
    public function testNewConfigurationHasEmptyIdAndSource(): void
    {
        $configuration = new ThemeConfiguration();

        $this->assertSame('', $configuration->getId());
        $this->assertSame('', $configuration->getSource());
    }

    public function testNewSettingHasEmptyNameAndType(): void
    {
        $setting = new Setting();

        $this->assertSame('', $setting->getName());
        $this->assertSame('', $setting->getType());
    }
}
