<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassExtensionsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ControllersDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\EventsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\SmartyPluginDirectoriesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplatesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationDataMapperTest extends TestCase
{
    use ContainerTrait;

    public function testMapping()
    {
        $configurationData = [
            'id'          => 'moduleId',
            'moduleSource' => 'relativePath',
            'version'     => '7.0',
            'activated'   => true,
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
            'keyWithoutDataMapperAssigned' => [
                'subkey' => 'subvalue'
            ],
            ClassExtensionsDataMapper::MAPPING_KEY => [
                'shopClass' => 'moduleClass',
            ],
            ControllersDataMapper::MAPPING_KEY => [
                'controller1' => \MyVendor\MyController\Controller1::class,
            ],
            EventsDataMapper::MAPPING_KEY => [
                'onActivate'   => 'MyEvents::onActivate'
            ],
            ModuleSettingsDataMapper::MAPPING_KEY => [
                'name' => [
                    'group'         => 'name',
                    'type'          => 'type',
                    'value'         => true,
                    'position'      => 4,
                    'constraints'   => [1, 2],
                ]
            ],
            SmartyPluginDirectoriesDataMapper::MAPPING_KEY => [
                'Smarty/PluginDirectory1WithMetadataVersion21',
            ],
            TemplateBlocksDataMapper::MAPPING_KEY => [
                [
                    'template' => 'page/checkout/basket.tpl',
                    'block' => 'basket_btn_next_top',
                    'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'
                ],
            ],
            TemplatesDataMapper::MAPPING_KEY => [
                'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
            ]
        ];

        $moduleConfigurationDataMapper = $this->get(ModuleConfigurationDataMapperInterface::class);

        $moduleConfiguration = new ModuleConfiguration();

        $moduleConfiguration = $moduleConfigurationDataMapper->fromData($moduleConfiguration, $configurationData);

        $this->assertEquals(
            $this->removeKeysWithoutAssignedDataMapper($configurationData),
            $moduleConfigurationDataMapper->toData($moduleConfiguration)
        );
    }

    private function removeKeysWithoutAssignedDataMapper(array $configurationData): array
    {
        unset($configurationData['keyWithoutDataMapperAssigned']);
        return $configurationData;
    }
}
