<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Configuration\Dao;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDao;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ShopConfigurationSettingDaoTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());

        parent::tearDown();
    }

    #[DataProvider('settingValueDataProvider')]
    public function testSave(string $name, string $type, string|int|float|bool|array $value): void
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

    public function testGetNonExistentSetting(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
        $settingDao = $this->getConfigurationSettingDao();

        $this->expectException(EntryDoesNotExistDaoException::class);
        $settingDao->get('onExistentSetting', 1);
    }

    public function testDelete(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
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

    public function testUpdate(): void
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

    public function testUpdateDoesNotCreateDuplicationsInDatabase(): void
    {
        $this->assertSame(
            0,
            $this->getRowCount()
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
            $this->getRowCount()
        );

        $shopConfigurationSetting->setValue('secondSaving');

        $settingDao->save($shopConfigurationSetting);

        $this->assertSame(
            1,
            $this->getRowCount()
        );
    }

    public function testGetDoesNotReturnCachedReference(): void
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName('cloning_test')
            ->setType('str')
            ->setValue('initial');

        $settingDao->save($shopConfigurationSetting);

        $first = $settingDao->get('cloning_test', 1);
        $first->setValue('changed');

        $second = $settingDao->get('cloning_test', 1);

        $this->assertSame('initial', $second->getValue());
    }

    public function testGetUsesDatabaseOnlyOnceForSameSetting(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $queryBuilder
            ->expects($this->once())
            ->method('fetchAssociative')
            ->willReturn([
                'type' => 'str',
                'value' => 'test',
                'name' => 'test',
            ]);

        $queryBuilderFactory = $this->createMock(QueryBuilderFactoryInterface::class);
        $queryBuilderFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($queryBuilder);

        $encoder = $this->createStub(ShopSettingEncoderInterface::class);

        $eventDispatcher = $this->createStub(EventDispatcherInterface::class);

        $settingDao = new ShopConfigurationSettingDao(
            $queryBuilderFactory,
            $encoder,
            $eventDispatcher
        );

        $settingDao->get('test', 1);
        $settingDao->get('test', 1);
    }

    public static function settingValueDataProvider(): array
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

    private function getRowCount(): int
    {
        return $this
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->select('*')
            ->from('oxconfig')
            ->where('oxshopid = "1"')
            ->andWhere('oxvarname = "testDuplications"')
            ->andWhere('oxmodule = ""')
            ->executeQuery()
            ->rowCount();
    }
}
