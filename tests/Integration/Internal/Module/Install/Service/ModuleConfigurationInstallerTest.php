<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationInstallerTest extends TestCase
{
    use ContainerTrait;

    private $modulePath;
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    public function setUp()
    {
        $this->modulePath = realpath(__DIR__ . '/../../TestData/TestModule/');

        $this->projectConfigurationDao = $this->get(ProjectConfigurationDaoInterface::class);

        $this->prepareTestProjectConfiguration();

        parent::setUp();
    }

    public function testTransfer()
    {
        $transferringService = $this->get(ModuleConfigurationInstallerInterface::class);
        $transferringService->install($this->modulePath);

        $this->assertProjectConfigurationHasModuleConfigurationForAllShops();
    }

    public function testExtensionClassChainIsUpdatedAfterTransfer()
    {
        $transferringService = $this->get(ModuleConfigurationInstallerInterface::class);
        $transferringService->install($this->modulePath);

        $environmentConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration('prod');

        $shopConfigurationWithAlreadyExistentChain = $environmentConfiguration->getShopConfiguration(1);

        $this->assertSame(
            [
                'shopClass'             => [
                    'alreadyInstalledShopClass',
                    'anotherAlreadyInstalledShopClass',
                    'testModuleClassExtendsShopClass',
                ],
                'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
            ],
            $shopConfigurationWithAlreadyExistentChain->getChain(Chain::CLASS_EXTENSIONS)->getChain()
        );

        $shopConfigurationWithoutAlreadyExistentChain = $environmentConfiguration->getShopConfiguration(2);

        $this->assertSame(
            [
                'shopClass'             => [
                    'testModuleClassExtendsShopClass',
                ],
            ],
            $shopConfigurationWithoutAlreadyExistentChain->getChain(Chain::CLASS_EXTENSIONS)->getChain()
        );
    }

    private function assertProjectConfigurationHasModuleConfigurationForAllShops()
    {
        $environmentConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration('prod');

        foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
            $this->assertContains(
                'testModule',
                $shopConfiguration->getModuleIdsOfModuleConfigurations()
            );
        }
    }

    private function prepareTestProjectConfiguration()
    {
        $shopConfigurationWithChain = new ShopConfiguration();

        $chain = new Chain();
        $chain
            ->setName(Chain::CLASS_EXTENSIONS)
            ->setChain([
                'shopClass'             => ['alreadyInstalledShopClass', 'anotherAlreadyInstalledShopClass'],
                'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
            ]);

        $shopConfigurationWithChain->addChain($chain);

        $shopConfigurationWithoutChain = new ShopConfiguration();

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfigurationWithChain);
        $environmentConfiguration->addShopConfiguration(2, $shopConfigurationWithoutChain);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('prod', $environmentConfiguration);

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }
}
