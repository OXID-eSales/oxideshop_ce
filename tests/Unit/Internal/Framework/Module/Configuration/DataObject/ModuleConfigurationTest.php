<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleSettingNotFountException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationTest extends TestCase
{
    public function testAddModuleSetting()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $setting = new Setting();
        $setting
            ->setName('testSetting')
            ->setValue([]);
        $moduleConfiguration->addModuleSetting($setting);

        $this->assertSame(
            $setting,
            $moduleConfiguration->getModuleSetting('testSetting')
        );
    }

    public function testConfigurationHasSetting()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $this->assertFalse($moduleConfiguration->hasModuleSetting('testSetting'));

        $setting = new Setting();
        $setting
            ->setName('testSetting')
            ->setValue([]);
        $moduleConfiguration->addModuleSetting($setting);

        $this->assertTrue($moduleConfiguration->hasModuleSetting('testSetting'));
    }

    public function testConfigurationHasClassExtension()
    {
        $moduleConfiguration = new ModuleConfiguration();

        $moduleConfiguration->addClassExtension(
            new ClassExtension(
                'extendedClassNamespace',
                'expectedExtensionNamespace'
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

        $moduleConfiguration->addClassExtension(
            new ClassExtension(
                'extendedClassNamespace',
                'anotherExtensionNamespace'
            )
        );

        $this->assertFalse(
            $moduleConfiguration->hasClassExtension('expectedExtensionNamespace')
        );
    }

    public function testGetModuleSettingWhenSettingNotFound(): void
    {
        $this->expectException(ModuleSettingNotFountException::class);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->getModuleSetting('nonExistingSetting');
    }
}
