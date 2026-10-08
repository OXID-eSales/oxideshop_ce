<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\Setting;
use PHPUnit\Framework\TestCase;

final class SettingTest extends TestCase
{
    public function testNewSettingHasEmptyNameAndType(): void
    {
        $setting = new Setting();

        $this->assertSame('', $setting->getName());
        $this->assertSame('', $setting->getType());
    }
}
