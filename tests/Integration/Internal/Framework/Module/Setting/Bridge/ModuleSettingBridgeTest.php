<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ModuleSettingBridgeTest extends TestCase
{
    use ContainerTrait;

    public function setUp()
    {
        $modulePath = realpath(__DIR__ . '/../../TestData/TestModule/');

        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($modulePath, 'targetPath');

        parent::setUp();
    }

    public function testSave(): void
    {
        $bridge = $this->get(ModuleSettingBridgeInterface::class);
        $newValue = ['some new setting'];

        $bridge->save('setting', $newValue, 'test-module');

        $configurationDao = $this->get(ModuleConfigurationDaoInterface::class);
        $configuration = $configurationDao->get('test-module', 1);
        $this->assertSame($newValue, $configuration->getModuleSetting('setting')->getValue());

        $settingsDao = $this->get(SettingDaoInterface::class);
        $this->assertSame($newValue, $settingsDao->get('setting', 'test-module', 1)->getValue());
    }

    public function testGet(): void
    {
        $defaultModuleSettingValue = ['Preis', 'Hersteller'];
        $bridge = $this->get(ModuleSettingBridgeInterface::class);
        $this->assertSame($defaultModuleSettingValue, $bridge->get('setting', 'test-module'));
    }
}
