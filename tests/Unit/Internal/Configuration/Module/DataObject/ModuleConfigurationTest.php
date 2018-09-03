<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataObject;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;
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
        $moduleConfiguration->setModuleSetting('testSetting', $setting);

        $this->assertSame(
            $setting,
            $moduleConfiguration->getModuleSetting('testSetting')
        );
    }

    public function testConfigurationHasSetting()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $this->assertFalse($moduleConfiguration->hasSetting('testSetting'));

        $moduleConfiguration->setModuleSetting(
            'testSetting',
            new ModuleSetting('testSetting', [])
        );

        $this->assertTrue($moduleConfiguration->hasSetting('testSetting'));
    }
}
