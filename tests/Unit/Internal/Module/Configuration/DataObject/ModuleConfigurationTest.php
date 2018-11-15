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
        $moduleConfiguration->addSetting($setting);

        $this->assertSame(
            $setting,
            $moduleConfiguration->getSetting('testSetting')
        );
    }

    public function testConfigurationHasSetting()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $this->assertFalse($moduleConfiguration->hasSetting('testSetting'));

        $moduleConfiguration->addSetting(
            new ModuleSetting('testSetting', [])
        );

        $this->assertTrue($moduleConfiguration->hasSetting('testSetting'));
    }

    public function testConfigurationHasClassExtension()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $moduleConfiguration->addSetting(
            new ModuleSetting(
                ModuleSetting::CLASS_EXTENSIONS,
                [
                    'extendedClassNamespace' => 'expectedExtensionNamespace',
                ]
            )
        );

        $this->assertTrue(
            $moduleConfiguration->hasClassExtension('expectedExtensionNamespace')
        );
    }

    public function testConfigurationDoesNotHaveClassExtension()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $this->assertFalse(
            $moduleConfiguration->hasClassExtension('expectedExtensionNamespace')
        );

        $moduleConfiguration->addSetting(
            new ModuleSetting(
                ModuleSetting::CLASS_EXTENSIONS,
                [
                    'extendedClassNamespace' => 'anotherExtensionNamespace',
                ]
            )
        );

        $this->assertFalse(
            $moduleConfiguration->hasClassExtension('expectedExtensionNamespace')
        );
    }
}
