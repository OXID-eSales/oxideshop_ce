<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfigurationDataMapper;
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
            'path'        => 'relativePath',
            'version'     => '7.0',
            'autoActive'  => true,
            'title'       => ['en' => 'title'],
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
                'templates' => [
                    'shopTemplate' => 'moduleTemplate',
                ],
                'extend'    => [
                    'shopClass' => 'moduleClass',
                ],
            ],
        ];

        $moduleConfigurationDataMapper = new ModuleConfigurationDataMapper();

        $moduleConfiguration = $moduleConfigurationDataMapper->fromData($configurationData);

        $this->assertEquals(
            $configurationData,
            $moduleConfigurationDataMapper->toData($moduleConfiguration)
        );
    }
}
