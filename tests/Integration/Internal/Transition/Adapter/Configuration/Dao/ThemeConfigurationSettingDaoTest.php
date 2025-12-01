<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Configuration\Dao;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ThemeConfigurationSettingDao;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ThemeConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ThemeConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationSettingDaoTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    private const THEME_ID = 'apex';

    protected function setUp(): void
    {
        parent::setUp();

        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());
    }

    protected function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());

        parent::tearDown();
    }

    #[DataProvider('settingValueDataProvider')]
    public function testSave(string $name, string $type, string|int|float|bool|array $value): void
    {
        $settingDao = $this->getConfigurationSettingDao();

        $themeConfigurationSetting = $this->createSetting($name, $type, $value);

        $settingDao->save($themeConfigurationSetting);

        $this->assertEquals(
            $themeConfigurationSetting,
            $settingDao->get($name, 1, self::THEME_ID)
        );
    }

    public function testGetNonExistentSetting(): void
    {
        $settingDao = $this->getConfigurationSettingDao();

        $this->expectException(EntryDoesNotExistDaoException::class);
        $settingDao->get('nonExisting', 1, self::THEME_ID);
    }

    public function testDelete(): void
    {
        $settingDao = $this->getConfigurationSettingDao();
        $themeConfigurationSetting = $this->createSetting('testDelete', 'str', 'value');

        $settingDao->save($themeConfigurationSetting);

        $settingDao->delete($themeConfigurationSetting);

        $this->expectException(EntryDoesNotExistDaoException::class);
        $settingDao->get('testDelete', 1, self::THEME_ID);
    }

    public function testUpdate(): void
    {
        $settingDao = $this->getConfigurationSettingDao();
        $themeConfigurationSetting = $this->createSetting('testUpdate', 'str', 'first');

        $settingDao->save($themeConfigurationSetting);

        $themeConfigurationSetting->setValue('second');

        $settingDao->save($themeConfigurationSetting);

        $this->assertEquals(
            $themeConfigurationSetting,
            $settingDao->get('testUpdate', 1, self::THEME_ID)
        );
    }

    public function testUpdateDoesNotCreateDuplicationsInDatabase(): void
    {
        $this->assertSame(0, $this->getRowCount());

        $settingDao = $this->getConfigurationSettingDao();
        $themeConfigurationSetting = $this->createSetting('testDuplications', 'str', 'first');

        $settingDao->save($themeConfigurationSetting);

        $this->assertSame(1, $this->getRowCount());

        $themeConfigurationSetting->setValue('second');

        $settingDao->save($themeConfigurationSetting);

        $this->assertSame(1, $this->getRowCount());
    }

    public function testGetDoesNotReturnCachedReference(): void
    {
        $settingDao = $this->getConfigurationSettingDao();
        $themeConfigurationSetting = $this->createSetting('cloning_test', 'str', 'initial');

        $settingDao->save($themeConfigurationSetting);

        $first = $settingDao->get('cloning_test', 1, self::THEME_ID);
        $first->setValue('changed');

        $second = $settingDao->get('cloning_test', 1, self::THEME_ID);

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

        $encoder = $this->createMock(ShopSettingEncoderInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $settingDao = new ThemeConfigurationSettingDao(
            $queryBuilderFactory,
            $encoder,
            $eventDispatcher
        );

        $settingDao->get('test', 1, self::THEME_ID);
        $settingDao->get('test', 1, self::THEME_ID);
    }

    public static function settingValueDataProvider(): array
    {
        return [
            ['string', 'str', 'value'],
            ['int', 'int', 1],
            ['float', 'num', 1.23],
            ['bool', 'bool', true],
            ['array', 'arr', ['key' => 'value']],
        ];
    }

    private function createSetting(string $name, string $type, string|int|float|bool|array $value): ThemeConfigurationSetting
    {
        $setting = new ThemeConfigurationSetting();
        $setting
            ->setShopId(1)
            ->setThemeId(self::THEME_ID)
            ->setName($name)
            ->setType($type)
            ->setValue($value);

        return $setting;
    }

    private function getConfigurationSettingDao(): ThemeConfigurationSettingDaoInterface
    {
        return $this->get(ThemeConfigurationSettingDaoInterface::class);
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
            ->andWhere('oxmodule = "theme:' . self::THEME_ID . '"')
            ->executeQuery()
            ->rowCount();
    }
}
