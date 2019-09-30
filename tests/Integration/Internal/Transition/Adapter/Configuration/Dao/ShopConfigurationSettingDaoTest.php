<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Dao;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopConfigurationSettingDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @dataProvider settingValueDataProvider
     */
    public function testSave(string $name, string $type, $value)
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName($name)
            ->setType($type)
            ->setValue($value);

        $settingDao->save($shopConfigurationSetting);

        $this->assertEquals(
            $shopConfigurationSetting,
            $settingDao->get($name, 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException
     */
    public function testGetNonExistentSetting()
    {
        $settingDao = $this->getConfigurationSettingDao();

        $settingDao->get('onExistentSetting', 1);
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     */
    public function testBackwardsCompatibility(string $name, string $type, $value)
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName($name)
            ->setType($type)
            ->setValue($value);

        $settingDao->save($shopConfigurationSetting);

        $this->assertSame(
            $settingDao->get($name, 1)->getValue(),
            Registry::getConfig()->getShopConfVar($name, 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException
     */
    public function testDelete()
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName('testDelete')
            ->setType('someType')
            ->setValue('value');

        $settingDao->save($shopConfigurationSetting);

        $settingDao->delete($shopConfigurationSetting);
        $settingDao->get('testDelete', 1);
    }

    public function testUpdate()
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName('testUpdate')
            ->setType('someType')
            ->setValue('firstSaving');

        $settingDao->save($shopConfigurationSetting);

        $shopConfigurationSetting->setValue('secondSaving');

        $settingDao->save($shopConfigurationSetting);

        $this->assertEquals(
            $shopConfigurationSetting,
            $settingDao->get('testUpdate', 1)
        );
    }

    public function testUpdateDoesNotCreateDuplicationsInDatabase()
    {
        $this->assertSame(
            0,
            $this->getRowCount('testDuplications', 1)
        );

        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName('testDuplications')
            ->setType('someType')
            ->setValue('firstSaving');

        $settingDao->save($shopConfigurationSetting);

        $this->assertSame(
            1,
            $this->getRowCount('testDuplications', 1)
        );

        $shopConfigurationSetting->setValue('secondSaving');

        $settingDao->save($shopConfigurationSetting);

        $this->assertSame(
            1,
            $this->getRowCount('testDuplications', 1)
        );
    }

    public function settingValueDataProvider()
    {
        return [
            [
                'string',
                'str',
                'testString',
            ],
            [
                'int',
                'int',
                1,
            ],
            [
                'float',
                'num',
                1.333,
            ],
            [
                'bool',
                'bool',
                true,
            ],
            [
                'array',
                'arr',
                [
                    'element'   => 'value',
                    'element2'  => 'value',
                ],
            ],
        ];
    }

    private function getConfigurationSettingDao(): ShopConfigurationSettingDaoInterface
    {
        return $this->get(ShopConfigurationSettingDaoInterface::class);
    }

    private function getRowCount(string $settingName, int $shopId): int
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('*')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->andWhere('oxmodule = ""')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $settingName,
            ]);

        return $queryBuilder->execute()->rowCount();
    }
}
