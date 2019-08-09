<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Common\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class ShopConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    public function testSave(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);

        $module = new ModuleConfiguration();
        $module
            ->setId('test')
            ->setPath('test');

        $shopConfigurationWithModule = new ShopConfiguration();
        $shopConfigurationWithModule->addModuleConfiguration($module);
        $shopConfigurationDao->save($shopConfigurationWithModule, 1);

        $shopConfiguration = new ShopConfiguration();
        $shopConfigurationDao->save($shopConfiguration, 2);

        $this->assertEquals(
            $shopConfigurationWithModule,
            $shopConfigurationDao->get(1)
        );

        $this->assertEquals(
            $shopConfiguration,
            $shopConfigurationDao->get(2)
        );
    }

    public function testGetAlwaysReturnsTheSameObjectIfConfigurationWasNotChanged(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);

        $shopConfiguration = $shopConfigurationDao->get(1);

        $this->assertSame(
            $shopConfiguration,
            $shopConfigurationDao->get(1)
        );
    }

    public function testGetAll(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);

        $this->assertEquals(
            new ShopConfiguration(),
            $shopConfigurationDao->get(1)
        );

        $shopConfigurationDao->save(new ShopConfiguration(), 3);

        $this->assertEquals(
            [
                1 => new ShopConfiguration(),
                3 => new ShopConfiguration(),
            ],
            $shopConfigurationDao->getAll()
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testWithIncorrectNode(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);

        $yamlStorage = $this->get(FileStorageFactoryInterface::class)->create(
            Path::join(
                $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory(),
                'shops/1.yaml'
            )
        );

        $yamlStorage->save(['incorrectKey']);

        $shopConfigurationDao->get(1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ShopConfigurationNotFoundException
     */
    public function testGetIncorrectShopId(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);
        $shopConfigurationDao->save(new ShopConfiguration(), 2);
        $shopConfigurationDao->save(new ShopConfiguration(), 3);

        $shopConfigurationDao->get(99);
    }

    public function testGetCorrectShopId(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);

        $shopConfiguration = $shopConfigurationDao->get(1);

        $this->assertSame(
            $shopConfiguration,
            $shopConfigurationDao->get(1)
        );
    }

    public function testEnvironmentShopConfigurationFileOverwritesShopConfiguration(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();

        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);

        $setting = new Setting();
        $setting
            ->setName('settingToOverwrite')
            ->setValue('value')
            ->setType('int');

        $module = new ModuleConfiguration();
        $module
            ->setId('testModule')
            ->setPath('test')
            ->addModuleSetting($setting);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($module);
        $shopConfigurationDao->save($shopConfiguration, 1);

        $this->assertSame(
            'overwrittenValue',
            $shopConfigurationDao
                ->get(1)
                ->getModuleConfiguration('testModule')
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );
    }

    private function prepareTestEnvironmentShopConfigurationFile(): void
    {
        $fileStorageFactory = $this->get(FileStorageFactoryInterface::class);
        $storage = $fileStorageFactory->create(
            $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory() . '/environment/1.yaml'
        );

        $storage->save([
            'modules' => [
                'testModule' => [
                    ModuleSettingsDataMapper::MAPPING_KEY => [
                        'settingToOverwrite' => [
                            'value' => 'overwrittenValue',
                        ]
                    ]
                ]
            ]
        ]);
    }
}
