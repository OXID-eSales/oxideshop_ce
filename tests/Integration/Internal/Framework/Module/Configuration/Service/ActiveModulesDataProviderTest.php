<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ActiveModulesDataProviderTest extends TestCase
{
    use ContainerTrait;

    private $activeModuleId = 'activeModuleId';
    private $activeModulePath = 'some-path-active';
    private $activeModuleSource = 'some-source-active';
    private $inactiveModuleId = 'inActiveModuleId';
    private $inactiveModulePath = 'some-path-inactive';
    private $inactiveModuleSource = 'some-source-inactive';
    /** @var BasicContext */
    private $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new BasicContext();
        $this->prepareTestShopConfiguration();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    public function testGetModuleIds(): void
    {
        $this->assertSame(
            [$this->activeModuleId],
            $this->get(ActiveModulesDataProviderInterface::class)->getModuleIds()
        );
    }

    public function testGetModulePathsWillReturnSourcePathForActiveModule(): void
    {
        $countOfActivatedModules = 1;

        $paths = $this->get(ActiveModulesDataProviderInterface::class)->getModulePaths();

        $this->assertCount($countOfActivatedModules, $paths);
        $path = $paths[array_key_first($paths)];
        $this->assertStringContainsString($this->activeModuleSource, $path);
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setPath($this->activeModulePath)
            ->setModuleSource($this->activeModuleSource);

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setPath($this->inactiveModulePath)
            ->setModuleSource($this->inactiveModuleSource);

        /** @var ShopConfigurationDaoInterface $dao */
        $dao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfiguration = $dao->get(1);
        $shopConfiguration
            ->addModuleConfiguration($activeModule)
            ->addModuleConfiguration($inactiveModule);

        $dao->save($shopConfiguration, $this->context->getDefaultShopId());

        $this->get(ModuleActivationServiceInterface::class)->activate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function cleanUpTestData(): void
    {
        $this->get(ModuleActivationServiceInterface::class)->deactivate($this->activeModuleId, $this->context->getDefaultShopId());
    }
}
