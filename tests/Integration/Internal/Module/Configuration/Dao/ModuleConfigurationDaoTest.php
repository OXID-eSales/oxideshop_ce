<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    protected function setUp()
    {
        $this->prepareProjectConfiguration();

        parent::setUp();
    }

    public function testSaving()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setPath('somePath');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($moduleConfiguration, 1);

        $this->assertEquals(
            $moduleConfiguration,
            $dao->get('testId', 1)
        );
    }

    private function prepareProjectConfiguration()
    {
        $shopConfiguration = new ShopConfiguration();

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('prod', $environmentConfiguration);

        $dao = $this->get(ProjectConfigurationDaoInterface::class);

        $dao->persistConfiguration($projectConfiguration);
    }
}
