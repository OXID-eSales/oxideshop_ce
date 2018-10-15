<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Setup;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleActivationServiceTest extends TestCase
{
    use ContainerTrait;

    public function testActivation()
    {
        $projectConfigurationDao = $this->get(ProjectConfigurationDaoInterface::class);
        $projectConfigurationDao->persistConfiguration($this->getTestProjectConfiguration());

        $moduleActivationService = $this->get(ModuleActivationServiceInterface::class);
        $moduleActivationService->activate('testModuleConfiguration', 1);
    }

    private function getTestProjectConfiguration(): ProjectConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleConfiguration')
            ->setState('active');

        $moduleConfiguration->setSetting(
            new ModuleSetting('path', 'somePath')
        )
        ->setSetting(
            new ModuleSetting('version', 'v2.1')
        )
        ->setSetting(new ModuleSetting(
            'controllers',
            [
                'originalClassNamespace' => 'moduleClassNamespace',
                'otherOriginalClassNamespace' => 'moduleClassNamespace',
            ]
        ))
        ->setSetting(new ModuleSetting(
            'templates',
            [
                'originalTemplate' => 'moduleTemplate',
                'otherOriginalTemplate' => 'moduleTemplate',
            ]
        ))
        ->setSetting(new ModuleSetting(
            'smartyPluginDirectories',
            [
                'firstSmartyDirectory',
                'secondSmartyDirectory',
            ]
        ))
        ->setSetting(new ModuleSetting(
            'blocks',
            [
                [
                    'block'     => 'testBlock',
                    'position'  => '3',
                    'theme'     => 'flow_theme',
                    'template'  => 'extendedTemplatePath',
                    'file'      => 'filePath',
                ],
            ]
        ))
        ->setSetting(new ModuleSetting(
            'extend',
            [
                'originalClassNamespace' => 'moduleClassNamespace',
                'otherOriginalClassNamespace' => 'moduleClassNamespace',
            ]
        ))
        ->setSetting(new ModuleSetting(
            ModuleSetting::SHOP_MODULE_SETTING,
            [
                [
                    'group' => 'frontend',
                    'name'  => 'sGridRow',
                    'type'  => 'str',
                    'value' => 'row',
                ],
            ]
        ))
        /**
        ->setSetting(new ModuleSetting(
            'events',
            [
                'onActivate' => 'ModuleClass::onActivate',
                'onDeactivate' => 'ModuleClass::onDeactivate',
            ]
        ))
         */;

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->setModuleConfiguration('testModuleConfiguration', $moduleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->setShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('testProject');
        $projectConfiguration->setEnvironmentConfiguration('dev', $environmentConfiguration);

        return $projectConfiguration;
    }
}
