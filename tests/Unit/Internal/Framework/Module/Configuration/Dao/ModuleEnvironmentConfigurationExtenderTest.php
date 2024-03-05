<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentWithOrphanSettingEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleEnvironmentConfigurationExtender;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ModuleEnvironmentConfigurationExtenderTest extends TestCase
{
    use ProphecyTrait;

    private ModuleEnvironmentConfigurationDaoInterface|ObjectProphecy $environmentDao;
    private ModuleEnvironmentConfigurationExtender $environmentExtension;
    private ObjectProphecy|EventDispatcherInterface $eventDispatcher;
    private int $shopId = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->environmentDao = $this->prophesize(ModuleEnvironmentConfigurationDaoInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->environmentExtension = new ModuleEnvironmentConfigurationExtender(
            $this->environmentDao->reveal(),
            $this->eventDispatcher->reveal()
        );
    }

    public function testWithEmptyEnvironment(): void
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();

        $environmentConfiguration = [];
        $this->environmentDao->get('testId', $this->shopId)->willReturn($environmentConfiguration);

        $result = $this->environmentExtension->extend($moduleConfiguration, $this->shopId);

        $this->assertEquals($this->getTestModuleConfiguration(), $result);
    }

    public function testWithEnvironment(): void
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();

        $environmentConfiguration = [
            'moduleSettings' => [
                'testSetting' => [
                    'group' => 'envGroup',
                    'value' => 'envValue',
                    'constraints' => ['env'],
                    'position' => 5,
                ],
            ],
        ];
        $this->environmentDao->get('testId', $this->shopId)->willReturn($environmentConfiguration);

        $expectedConfiguration = $this->getTestModuleConfiguration();
        $setting = $expectedConfiguration->getModuleSetting('testSetting');
        $setting
            ->setValue('envValue')
            ->setConstraints(['env'])
            ->setPositionInGroup(5)
            ->setGroupName('envGroup');

        $result = $this->environmentExtension->extend($moduleConfiguration, $this->shopId);

        $this->assertEquals($expectedConfiguration, $result);
    }

    public function testWithNonExistentSettingInEnvironment(): void
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();

        $environmentConfiguration = [
            'moduleSettings' => [
                'nonExistent' => [
                    'group' => 'envGroup',
                    'value' => 'envValue',
                    'constraints' => ['env'],
                    'position' => 5,
                ],
            ],
        ];
        $event = new ShopEnvironmentWithOrphanSettingEvent(
            $this->shopId,
            'testId',
            'nonExistent'
        );
        $this->environmentDao->get('testId', $this->shopId)->willReturn($environmentConfiguration);
        $this->eventDispatcher
            ->dispatch($event)
            ->willReturnArgument();

        $result = $this->environmentExtension->extend($moduleConfiguration, $this->shopId);

        $this->assertEquals($this->getTestModuleConfiguration(), $result);
    }

    private function getTestModuleConfiguration(): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testId');

        $setting = new Setting();
        $setting
            ->setName('testSetting')
            ->setValue('originalValue')
            ->setGroupName('originalGroup')
            ->setConstraints(['123'])
            ->setPositionInGroup(0);

        $moduleConfiguration->addModuleSetting($setting);

        return $moduleConfiguration;
    }
}
