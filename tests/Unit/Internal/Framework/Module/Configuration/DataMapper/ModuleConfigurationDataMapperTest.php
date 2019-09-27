<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassesWithoutNamespaceDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassExtensionsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ControllersDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\EventsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\SmartyPluginDirectoriesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplatesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
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
            'path'        => 'relativePath',
            'version'     => '7.0',
            'configured'  => true,
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
            ClassesWithoutNamespaceDataMapper::MAPPING_KEY => [
                'myvendor_mymodule_file1' => 'path/to/file1.php'
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

    private function removeKeysWithoutAssignedDataMapper(array $configurationData) : array
    {
        unset($configurationData['keyWithoutDataMapperAssigned']);
        return $configurationData;
    }

    /**
     * @dataProvider moduleConfigurationDataProvider
     *
     * @param array                                  $data
     * @param ModuleConfigurationDataMapperInterface $dataMapper
     */
    public function testToDataAndFromData(array $data, ModuleConfigurationDataMapperInterface $dataMapper)
    {

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration = $dataMapper->fromData($moduleConfiguration, $data);

        $this->assertEquals(
            $data,
            $dataMapper->toData($moduleConfiguration)
        );
    }

    public function moduleConfigurationDataProvider()
    {
        return [
            [
                'data' => [
                    ClassesWithoutNamespaceDataMapper::MAPPING_KEY => [
                        'myvendor_mymodule_file1' => 'path/to/file1.php',
                        'myvendor_mymodule_file2' => 'path/to/file2.php',
                    ]
                ],
                'dataMapper' => new ClassesWithoutNamespaceDataMapper()

            ],
            [
                'data' => [
                    ClassExtensionsDataMapper::MAPPING_KEY => [
                        'shopClass1' => 'moduleClass1',
                        'shopClass2' => 'moduleClass2'
                    ]
                ],
                'dataMapper' => new ClassExtensionsDataMapper()

            ],
            [
                'data' => [
                    ControllersDataMapper::MAPPING_KEY => [
                        'controller1' => \MyVendor\MyController\Controller1::class,
                        'controller2' => \MyVendor\MyController\Controller2::class
                    ]
                ],
                'dataMapper' => new ControllersDataMapper()

            ],
            [
                'data' => [
                    EventsDataMapper::MAPPING_KEY => [
                            'onActivate'   => 'MyEvents::onActivate',
                            'onDeactivate' => 'MyEvents::onDeactivate'
                    ]
                ],
                'dataMapper' => new EventsDataMapper()

            ],
            [
                'data' => [
                    ModuleSettingsDataMapper::MAPPING_KEY => [
                        'testEmptyBoolConfig' => [
                            'group' => 'settingsEmpty',
                            'type' => 'bool',
                            'value' => 'false'
                        ],
                        'testFilledAArrConfig' => [
                            'group' => 'settingsFilled',
                            'type' => 'aarr',
                            'value' => ['key1' => 'option1', 'key2' => 'option2']
                        ]
                    ]
                ],
                'dataMapper' => new ModuleSettingsDataMapper()

            ],
            [
                'data' => [
                    SmartyPluginDirectoriesDataMapper::MAPPING_KEY => [
                        'Smarty/PluginDirectory1WithMetadataVersion21',
                        'Smarty/PluginDirectory2WithMetadataVersion21'
                    ]
                ],
                'dataMapper' => new SmartyPluginDirectoriesDataMapper()

            ],
            [
                'data' => [
                    TemplateBlocksDataMapper::MAPPING_KEY => [
                        [
                            'template' => 'page/checkout/basket.tpl',
                            'block' => 'basket_btn_next_top',
                            'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'
                        ],
                        [
                            'template' => 'page/checkout/basket.tpl',
                            'block' => 'basket_btn_next_bottom',
                            'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'
                        ],
                    ]
                ],
                'dataMapper' => new TemplateBlocksDataMapper()

            ],
            [
                'data' => [
                    TemplatesDataMapper::MAPPING_KEY => [
                            'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                            'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl'
                    ]
                ],
                'dataMapper' => new TemplatesDataMapper()

            ]
        ];
    }
}
