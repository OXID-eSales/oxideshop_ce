<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
final class ModuleConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    protected function setUp(): void
    {
        $this->prepareProjectConfiguration();

        parent::setUp();
    }

    public function testSaving(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setModuleSource('test');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($moduleConfiguration, 1);

        $this->assertEquals(
            $moduleConfiguration,
            $dao->get('testId', 1)
        );
    }

    public function testGetAllOrdersConfigurationsById(): void
    {
        $aModuleConfiguration = new ModuleConfiguration();
        $aModuleConfiguration
            ->setId('aTestId')
            ->setModuleSource('test');

        $bModuleConfiguration = new ModuleConfiguration();
        $bModuleConfiguration
            ->setId('bTestId')
            ->setModuleSource('test');

        $cModuleConfiguration = new ModuleConfiguration();
        $cModuleConfiguration
            ->setId('cTestId')
            ->setModuleSource('test');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($bModuleConfiguration, 1);
        $dao->save($cModuleConfiguration, 1);
        $dao->save($aModuleConfiguration, 1);

        $this->assertSame(
            [
                'aTestId',
                'bTestId',
                'cTestId',
            ],
            array_keys($dao->getAll(1))
        );
    }



    public function testDeleteAll(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setModuleSource('test');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($moduleConfiguration, 1);

        $dao->deleteAll(1);

        $this->assertEquals([], $dao->getAll(1));
    }

    public function testGetAlwaysReturnsTheSameObjectIfConfigurationWasNotChanged(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setModuleSource('test');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($moduleConfiguration, 1);

        $configuration = $dao->get('testId', 1);

        $this->assertSame(
            $configuration,
            $dao->get('testId', 1)
        );
    }

    public function testExists(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setModuleSource('test');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($moduleConfiguration, 1);

        $this->assertTrue(
            $dao->exists('testId', 1)
        );

        $this->assertFalse(
            $dao->exists('nonExistentModule', 1)
        );
    }

    public function testWithIncorrectNode(): void
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setModuleSource('test');

        $dao = $this->get(ModuleConfigurationDaoInterface::class);
        $dao->save($moduleConfiguration, 1);

        $yamlStorage = $this->get(FileStorageFactoryInterface::class)->create(
            Path::join(
                $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory(),
                'shops/1/modules/testId.yaml'
            )
        );

        $yamlStorage->save(['incorrectKey']);

        $this->expectException(InvalidConfigurationException::class);
        $dao->get('testId', 1);
    }

    private function prepareProjectConfiguration(): void
    {
        $this->get(ShopConfigurationDaoInterface::class)->save(
            new ShopConfiguration(),
            1
        );
    }
}
