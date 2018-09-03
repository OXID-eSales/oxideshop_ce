<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationToShopConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationToShopConfigurationDataMapperTest extends TestCase
{
    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\UnsupportedMethodException
     */
    public function testFromDataMethodIsNotSupported()
    {
        $mapper = new ModuleConfigurationToShopConfigurationDataMapper();
        $mapper->fromData([]);
    }

    public function testToDataMapping()
    {
        $mapper = new ModuleConfigurationToShopConfigurationDataMapper();
        $moduleConfiguration = $this->getModuleConfiguration();

        $this->assertEquals(
            [
                'aModulePaths'          => ['testModuleConfiguration' => 'testModuleConfigurationPath'],
                'aModuleVersions'       => ['testModuleConfiguration' => 'v0.0.7'],
                'aModuleEvents'         => [
                    'testModuleConfiguration' => [
                        'onActivate'    => 'ModuleClass::onActivate',
                        'onDeactivate'  => 'ModuleClass::onDeactivate',
                    ]
                ],
                'aModuleControllers'    => [
                    'testModuleConfiguration' => [
                        'originalClassNamespace'        => 'moduleClassNamespace',
                        'otherOriginalClassNamespace'   => 'moduleClassNamespace',
                    ]
                ],
                'aModuleTemplates'    => [
                    'testModuleConfiguration' => [
                        'originalTemplate'        => 'moduleTemplate',
                        'otherOriginalTemplate'   => 'moduleTemplate',
                    ]
                ],
                'aModuleExtensions'    => [
                    'testModuleConfiguration' => [
                        'originalClassNamespace'        => 'moduleClassNamespace',
                        'otherOriginalClassNamespace'   => 'moduleClassNamespace',
                    ]
                ],
                'moduleSmartyPluginDirectories'    => [
                    'testModuleConfiguration' => [
                        'firstSmartyDirectory',
                        'secondSmartyDirectory',
                    ]
                ],
            ],
            $mapper->toData($moduleConfiguration)
        );
    }

    private function getModuleConfiguration(): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleConfiguration')
            ->setPath('testModuleConfigurationPath')
            ->setVersion('v0.0.7')
            ->setModuleSetting('events', new ModuleSetting(
                'events',
                [
                    'onActivate'    => 'ModuleClass::onActivate',
                    'onDeactivate'  => 'ModuleClass::onDeactivate',
                ]
            ))
            ->setModuleSetting('controllers', new ModuleSetting(
                'controllers',
                [
                    'originalClassNamespace'        => 'moduleClassNamespace',
                    'otherOriginalClassNamespace'   => 'moduleClassNamespace',
                ]
            ))
            ->setModuleSetting('templates', new ModuleSetting(
                'templates',
                [
                    'originalTemplate'        => 'moduleTemplate',
                    'otherOriginalTemplate'   => 'moduleTemplate',
                ]
            ))
            ->setModuleSetting('extend', new ModuleSetting(
                'extend',
                [
                    'originalClassNamespace'        => 'moduleClassNamespace',
                    'otherOriginalClassNamespace'   => 'moduleClassNamespace',
                ]
            ))
            ->setModuleSetting('smartyPluginDirectories', new ModuleSetting(
                'smartyPluginDirectories',
                [
                    'firstSmartyDirectory',
                    'secondSmartyDirectory',
                ]
            ));

        return $moduleConfiguration;
    }
}
