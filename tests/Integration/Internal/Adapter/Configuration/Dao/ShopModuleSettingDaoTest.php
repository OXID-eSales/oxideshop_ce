<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Adapter\Configuration\Dao;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopModuleSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopModuleSetting;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopModuleSettingDaoTest extends TestCase
{
    /**
     * @dataProvider settingValueDataProvider
     */
    public function testSettingSaving(string $name, $value)
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting = new ShopModuleSetting(
            'testModuleId',
            1,
            $name,
            $value
        );

        $settingDao->save($shopModuleSetting);

        $this->assertEquals(
            $shopModuleSetting,
            $settingDao->get($name, 'testModuleId', 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException
     */
    public function testGetNonExistentSetting()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $settingDao->get('onExistentSetting', 'moduleId', 1);
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     */
    public function testSettingSavingCompatibility(string $name, $value)
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting = new ShopModuleSetting(
            'testModuleId',
            1,
            $name,
            $value
        );

        $settingDao->save($shopModuleSetting);

        $this->assertEquals(
            $value,
            Registry::getConfig()->getShopConfVar($name, 1, 'testModuleId')
        );
    }

    public function settingValueDataProvider(): array
    {
        return [
            [
                'string',
                'testString',
            ],
            [
                'int',
                1,
            ],
            [
                'bool',
                true,
            ],
            [
                'array',
                [
                    'element'   => 'value',
                    'element2'  => 'value',
                ]
            ],
        ];
    }

    private function getShopModuleSettingDao()
    {
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $settingDaoDefinition = $container->getDefinition(ShopModuleSettingDaoInterface::class);
        $settingDaoDefinition->setPublic(true);

        $container->setDefinition(
            ShopModuleSettingDaoInterface::class,
            $settingDaoDefinition
        );

        $container->compile();

        return $container->get(ShopModuleSettingDaoInterface::class);
    }
}
