<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\ShopModuleSetting;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSetting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopModuleSettingDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @dataProvider settingValueDataProvider
     */
    public function testSave(string $name, string $type, $value)
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting = new ShopModuleSetting();
        $shopModuleSetting
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setName($name)
            ->setType($type)
            ->setValue($value)
            ->setConstraints([
                'first',
                'second',
                'third',
            ])
            ->setGroupName('testGroup')
            ->setPositionInGroup(5);

        $settingDao->save($shopModuleSetting);

        $this->assertEquals(
            $shopModuleSetting,
            $settingDao->get($name, 'testModuleId', 1)
        );
    }

    public function testSaveSeveralSettings()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting1 = new ShopModuleSetting();
        $shopModuleSetting1
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setName('first')
            ->setType('arr')
            ->setValue('first')
            ->setConstraints([
                'first',
                'second',
                'third',
            ])
            ->setGroupName('testGroup')
            ->setPositionInGroup(5);

        $settingDao->save($shopModuleSetting1);

        $shopModuleSetting2 = new ShopModuleSetting();
        $shopModuleSetting2
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setName('second')
            ->setType('int')
            ->setValue('second')
            ->setConstraints([
                '1',
                '2',
                '3',
            ])
            ->setGroupName('testGroup')
            ->setPositionInGroup(5);

        $settingDao->save($shopModuleSetting2);

        $this->assertEquals(
            $shopModuleSetting1,
            $settingDao->get('first', 'testModuleId', 1)
        );

        $this->assertEquals(
            $shopModuleSetting2,
            $settingDao->get('second', 'testModuleId', 1)
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
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException
     */
    public function testDelete()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting = new ShopModuleSetting();
        $shopModuleSetting
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setName('testDelete')
            ->setType('some')
            ->setValue('some');

        $settingDao->save($shopModuleSetting);

        $settingDao->delete($shopModuleSetting);
        $settingDao->get('testDelete', 'testModuleId', 1);
    }

    public function testUpdate()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting = new ShopModuleSetting();
        $shopModuleSetting
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setName('testUpdate')
            ->setType('some')
            ->setValue('valueBeforeUpdate');

        $settingDao->save($shopModuleSetting);

        $shopModuleSetting->setValue('valueAfterUpdate');

        $settingDao->save($shopModuleSetting);

        $this->assertEquals(
            $shopModuleSetting,
            $settingDao->get('testUpdate', 'testModuleId', 1)
        );
    }

    public function testUpdateDoesNotCreateDuplicationsInDatabase()
    {
        $moduleId = 'testModuleId';
        $settingName = 'testSettingName';

        $this->assertSame(0, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(0, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));

        $shopModuleSetting = new ShopModuleSetting();
        $shopModuleSetting
            ->setModuleId($moduleId)
            ->setShopId(1)
            ->setName($settingName)
            ->setType('some')
            ->setValue('valueBeforeUpdate');

        $settingDao = $this->getShopModuleSettingDao();
        $settingDao->save($shopModuleSetting);

        $this->assertSame(1, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(1, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));

        $shopModuleSetting->setValue('valueAfterUpdate');
        $settingDao->save($shopModuleSetting);

        $this->assertSame(1, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(1, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     */
    public function testBackwardsCompatibility(string $name, string $type, $value)
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting = new ShopModuleSetting();
        $shopModuleSetting
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setName($name)
            ->setType($type)
            ->setValue($value);

        $settingDao->save($shopModuleSetting);

        $this->assertSame(
            $settingDao->get($name, 'testModuleId', 1)->getValue(),
            Registry::getConfig()->getShopConfVar($name, 1, 'module:testModuleId')
        );
    }

    public function settingValueDataProvider(): array
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
                ]
            ],
        ];
    }

    private function getShopModuleSettingDao()
    {
        return $this->get(ShopModuleSettingDaoInterface::class);
    }

    private function getOxConfigTableRowCount(string $settingName, int $shopId, string $moduleId): int
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('*')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->andWhere('oxmodule = :moduleId')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $settingName,
                'moduleId'  => 'module:' . $moduleId,
            ]);

        return $queryBuilder->execute()->rowCount();
    }

    private function getOxDisplayConfigTableRowCount(string $settingName, string $moduleId): int
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('*')
            ->from('oxconfigdisplay')
            ->andWhere('oxcfgvarname = :name')
            ->andWhere('oxcfgmodule = :moduleId')
            ->setParameters([
                'name'      => $settingName,
                'moduleId'  => 'module:' . $moduleId,
            ]);

        return $queryBuilder->execute()->rowCount();
    }
}
