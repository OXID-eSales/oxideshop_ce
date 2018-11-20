<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Validator\SettingValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationDataMapperTest extends TestCase
{
    public function testMapping()
    {
        $configurationData = [
            'id'          => 'moduleId',
            'title'       => 'title',
            'description' => [
                'de' => 'description de',
                'en' => 'description en',
            ],
            'lang'        => 'en',
            'thumbnail'   => 'logo.png',
            'author'      => 'author',
            'url'         => 'http://example.com',
            'email'       => 'test@example.com',
            'settings'    => [
                'version'   => '1.0',
                'path'      => 'relativePath',
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
