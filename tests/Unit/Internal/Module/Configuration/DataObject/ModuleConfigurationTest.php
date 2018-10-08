<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationTest extends TestCase
{
    public function testAddModuleSetting()
    {
        $setting = new ModuleSetting('testSetting', []);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setSetting($setting);

        $this->assertSame(
            $setting,
            $moduleConfiguration->getSetting('testSetting')
        );
    }

    public function testConfigurationHasSetting()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $this->assertFalse($moduleConfiguration->hasSetting('testSetting'));

        $moduleConfiguration->setSetting(
            new ModuleSetting('testSetting', [])
        );

        $this->assertTrue($moduleConfiguration->hasSetting('testSetting'));
    }
}
