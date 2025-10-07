<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ModulesDataProviderTest extends TestCase
{
    use ContainerTrait;

    private string $activeModuleId = 'activeModuleId';
    private string $activeModulePath = 'some-path-active';
    private string $activeModuleSource = 'some-source-active';
    private string $inactiveModuleId = 'inActiveModuleId';
    private string $inactiveModulePath = 'some-path-inactive';
    private string $inactiveModuleSource = 'some-source-inactive';

    private BasicContext $context;

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
        self::assertSame(
            [$this->activeModuleId, $this->inactiveModuleId],
            $this->get(ModulesDataProviderInterface::class)->getModuleIds()
        );
    }

    public function testGetModulePathsWillReturnSourcePath(): void
    {
        self::assertEquals(
            [
                Path::join($this->context->getShopRootPath(), $this->activeModuleSource),
                Path::join($this->context->getShopRootPath(), $this->inactiveModuleSource)
            ],
            $this->get(ModulesDataProviderInterface::class)->getModulePaths()
        );
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setModuleSource($this->activeModuleSource);

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setModuleSource($this->inactiveModuleSource);

        /** @var ShopConfigurationDaoInterface $dao */
        $dao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfiguration = $dao->get(1);
        $shopConfiguration
            ->addModuleConfiguration($activeModule)
            ->addModuleConfiguration($inactiveModule);

        $dao->save($shopConfiguration, $this->context->getDefaultShopId());

        $this->get(ModuleActivationServiceInterface::class)
            ->activate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function cleanUpTestData(): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->deactivate($this->activeModuleId, $this->context->getDefaultShopId());
    }
}
