<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Adapter\Configuration;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\ShopConfigurationSettingDaoInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopConfigurationSettingDaoTest extends TestCase
{
    /**
     * @dataProvider settingValueDataProvider
     */
    public function testSettingSaving(string $name, $value)
    {
        $settingDao = $this->getConfigurationSettingDao();

        $settingDao->save(
            $name,
            $value,
            1
        );

        $this->assertSame(
            $value,
            $settingDao->get($name, 1)
        );
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     */
    public function testSettingSavingCompatibility(string $name, $value)
    {
        $settingDao = $this->getConfigurationSettingDao();

        $settingDao->save(
            $name,
            $value,
            1
        );

        $this->assertEquals(
            $value,
            Registry::getConfig()->getShopConfVar($name, 1)
        );
    }

    public function settingValueDataProvider()
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

    private function getConfigurationSettingDao()
    {
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $settingDaoDefinition = $container->getDefinition(ShopConfigurationSettingDaoInterface::class);
        $settingDaoDefinition->setPublic(true);

        $container->setDefinition(
            ShopConfigurationSettingDaoInterface::class,
            $settingDaoDefinition
        );

        $container->compile();

        return $container->get(ShopConfigurationSettingDaoInterface::class);
    }
}
