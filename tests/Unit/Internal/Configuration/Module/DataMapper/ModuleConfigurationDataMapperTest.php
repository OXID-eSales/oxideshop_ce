<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator\SettingValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationDataMapperTest extends TestCase
{
    public function testMapping()
    {
        $configurationData = [
            'id'        => 'moduleId',
            'state'     => 'active',
            'version'   => '1.0',
            'path'      => 'relativePath',
            'settings'  => [
                'templates' => [
                    'shopTemplate' => 'moduleTemplate',
                ],
                'extend'    => [
                    'shopClass' => 'moduleClass',
                ],
            ],
        ];

        $settingsValidator = $this->getMockBuilder(SettingValidatorInterface::class)->getMock();
        $moduleConfigurationDataMapper = new ModuleConfigurationDataMapper($settingsValidator);

        $moduleConfiguration = $moduleConfigurationDataMapper->fromData($configurationData);

        $this->assertEquals(
            $configurationData,
            $moduleConfigurationDataMapper->toData($moduleConfiguration)
        );
    }
}
