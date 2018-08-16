<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator\SettingValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationDataMapperTest extends TestCase
{
    public function testBaseFieldsMappingFromData()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setState('active')
            ->setVersion('1.0');

        $settingsValidator = $this->getMockBuilder(SettingValidatorInterface::class)->getMock();
        $moduleConfigurationDataMapper = new ModuleConfigurationDataMapper($settingsValidator);

        $this->assertEquals(
            $moduleConfiguration,
            $moduleConfigurationDataMapper->fromData(
                [
                    'state'     => 'active',
                    'version'   => '1.0',
                ]
            )
        );
    }

    public function testSettingsMappingFromData()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setState('active')
            ->setVersion('1.0');

        $moduleConfiguration->setModuleSetting(
            'templates',
            new ModuleSetting('templates', ['shopTemplate' => 'moduleTemplate'])
        );

        $moduleConfiguration->setModuleSetting(
            'extend',
            new ModuleSetting('extend', ['shopClass' => 'moduleClass'])
        );

        $settingsValidator = $this->getMockBuilder(SettingValidatorInterface::class)->getMock();
        $moduleConfigurationDataMapper = new ModuleConfigurationDataMapper($settingsValidator);

        $this->assertEquals(
            $moduleConfiguration,
            $moduleConfigurationDataMapper->fromData(
                [
                    'state'     => 'active',
                    'version'   => '1.0',
                    'settings'  => [
                        'templates' => [
                            'shopTemplate' => 'moduleTemplate',
                        ],
                        'extend'    => [
                            'shopClass' => 'moduleClass',
                        ],
                    ],
                ]
            )
        );
    }
}
