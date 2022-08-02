<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use Psr\Container\ContainerInterface;
use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Basket;

/**
 * @internal
 */
class ModuleListTest extends UnitTestCase
{
    public function setup(): void
    {
        $this->getContainer()
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->getContainer()
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
    }

    public function testGetDisabledModuleClasses(): void
    {
        $notActiveModuleId = 'with_class_extensions';
        $this->installModule($notActiveModuleId);

        $this->assertSame(
            [
                'with_class_extensions/ModuleArticle',
            ],
            oxNew(ModuleList::class)->getDisabledModuleClasses()
        );
    }

    public function testCleanup(): void
    {
        $activeModuleId = 'with_metadata_v21';
        $this->installModule($activeModuleId);
        $this->activateModule($activeModuleId);

        $moduleList = $this
            ->getMockBuilder(ModuleList::class)
            ->setMethods(['getDeletedExtensions'])
            ->getMock();

        $moduleList
            ->method('getDeletedExtensions')
            ->willReturn(
                [
                    'with_metadata_v21' => 'someExtension',
                ]
            );

        $moduleList->cleanup();

        $moduleActivationBridge = $this->getContainer()->get(ModuleActivationBridgeInterface::class);

        $this->assertFalse(
            $moduleActivationBridge->isActive('with_metadata_v21', 1)
        );
    }

    public function testGetDeletedExtensionsWithMissingExtensions(): void
    {
        $moduleId = 'InvalidNamespaceModule';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);


        $module = oxNew(Module::class);
        $module->load($moduleId);

        $this->assertSame(
            [
                $moduleId => [
                    'extensions' => [
                        'OxidEsales\Eshop\Application\Model\Article' => ['OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\InvalidNamespaceModule1\Model\NonExistentFile'],
                    ]
                ],
            ],
            oxNew(ModuleList::class)->getDeletedExtensions()
        );
    }

    public function testGetModuleExtensionsWithMultipleExtensions()
    {
        $extensions = [
            'OxidEsales\Eshop\Application\Model\Article' => [
                'with_multiple_extensions/articleExtension1',
                'with_multiple_extensions/articleExtension2',
                'with_multiple_extensions/articleExtension3',
            ],
            Order::class => [
                'with_multiple_extensions/oxOrder'
            ],
            Basket::class => [
                'with_multiple_extensions/basketExtension'
            ]
        ];

        $this->installModule('with_multiple_extensions');
        $this->activateModule('with_multiple_extensions');

        $this->assertSame($extensions, oxNew(ModuleList::class)->getModuleExtensions('with_multiple_extensions'));
    }

    public function testGetModuleExtensionsWithNoExtensions(): void
    {
        $this->installModule('with_metadata_v21');
        $this->assertSame([], oxNew(ModuleList::class)->getModuleExtensions('with_metadata_v21'));
    }

    private function installModule(string $id): void
    {
        $package = new OxidEshopPackage(__DIR__ . '/Fixtures/' . $id);

        $this->getContainer()->get(ModuleInstallerInterface::class)
            ->install($package);
    }

    private function activateModule(string $id): void
    {
        $this->getContainer()->get(ModuleActivationBridgeInterface::class)->activate($id, 1);
    }

    private function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
