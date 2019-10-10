<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setting;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SettingDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @dataProvider settingValueDataProvider
     *
     * @param string $name
     * @param string $type
     * @param        $value
     */
    public function testSave(string $name, string $type, $value): void
    {
        $settingDao = $this->getSettingDao();

        $shopModuleSetting = new Setting();
        $shopModuleSetting
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

        $settingDao->save($shopModuleSetting, 'testModuleId', 1);

        $this->assertEquals(
            $shopModuleSetting,
            $settingDao->get($name, 'testModuleId', 1)
        );
    }

    public function testSaveSeveralSettings(): void
    {
        $settingDao = $this->getSettingDao();

        $shopModuleSetting1 = new Setting();
        $shopModuleSetting1
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

        $settingDao->save($shopModuleSetting1, 'testModuleId', 1);

        $shopModuleSetting2 = new Setting();
        $shopModuleSetting2
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

        $settingDao->save($shopModuleSetting2, 'testModuleId', 1);

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
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException
     */
    public function testGetSettingNotExistingInOxConfigTableThrowsException(): void
    {
        $settingDao = $this->getSettingDao();

        $settingDao->get('onExistentSetting', 'moduleId', 1);
    }

    public function testGetSettingNotExistingInOxConfigdisplayTableReturnsSettingFromOxconfigTable()
    {
        $shopModuleSetting = new Setting();
        $shopModuleSetting
            ->setName('third')
            ->setType('arr')
            ->setValue('third')
            ->setGroupName('testGroup')
            ->setPositionInGroup(5);

        $shopModuleSettingFromOxConfig = clone($shopModuleSetting);
        $shopModuleSettingFromOxConfig
            ->setGroupName('')
            ->setPositionInGroup(0);

        $this->saveDataToOxConfigTable($shopModuleSetting, 'testModuleId', 1);

        $settingDao = $this->getSettingDao();
        $this->assertEquals($shopModuleSettingFromOxConfig, $settingDao->get('third', 'testModuleId', 1));
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException
     */
    public function testDelete()
    {
        $settingDao = $this->getSettingDao();

        $shopModuleSetting = new Setting();
        $shopModuleSetting
            ->setName('testDelete')
            ->setType('some')
            ->setValue('some');

        $settingDao->save($shopModuleSetting, 'testModuleId', 1);

        $settingDao->delete($shopModuleSetting, 'testModuleId', 1);
        $settingDao->get('testDelete', 'testModuleId', 1);
    }

    public function testUpdate(): void
    {
        $settingDao = $this->getSettingDao();

        $shopModuleSetting = new Setting();
        $shopModuleSetting
            ->setName('testUpdate')
            ->setType('some')
            ->setValue('valueBeforeUpdate');

        $settingDao->save($shopModuleSetting, 'testModuleId', 1);

        $shopModuleSetting->setValue('valueAfterUpdate');

        $settingDao->save($shopModuleSetting, 'testModuleId', 1);

        $this->assertEquals(
            $shopModuleSetting,
            $settingDao->get('testUpdate', 'testModuleId', 1)
        );
    }

    public function testUpdateDoesNotCreateDuplicationsInDatabase(): void
    {
        $moduleId = 'testModuleId';
        $settingName = 'testSettingName';

        $this->assertSame(0, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(0, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));

        $shopModuleSetting = new Setting();
        $shopModuleSetting
            ->setName($settingName)
            ->setType('some')
            ->setValue('valueBeforeUpdate');

        $settingDao = $this->getSettingDao();
        $settingDao->save($shopModuleSetting, $moduleId, 1);

        $this->assertSame(1, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(1, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));

        $shopModuleSetting->setValue('valueAfterUpdate');
        $settingDao->save($shopModuleSetting, $settingName, 1);

        $this->assertSame(1, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(1, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     *
     * @param string $name
     * @param string $type
     * @param        $value
     */
    public function testBackwardsCompatibility(string $name, string $type, $value): void
    {
        $settingDao = $this->getSettingDao();

        $shopModuleSetting = new Setting();
        $shopModuleSetting
            ->setName($name)
            ->setType($type)
            ->setValue($value);

        $settingDao->save($shopModuleSetting, 'testModuleId', 1);

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

    private function getSettingDao(): SettingDaoInterface
    {
        return $this->get(SettingDaoInterface::class);
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

    private function saveDataToOxConfigTable(Setting $shopModuleSetting, string $moduleId, int $shopId)
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);
        $shopSettingEncoder = $this->get(ShopSettingEncoderInterface::class);
        $queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);
        $context = $this->get(ContextInterface::class);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxconfig')
            ->values([
                'oxid'          => ':id',
                'oxmodule'      => ':moduleId',
                'oxshopid'      => ':shopId',
                'oxvarname'     => ':name',
                'oxvartype'     => ':type',
                'oxvarvalue'    => 'encode(:value, :key)',
            ])
            ->setParameters([
                'id'        => $shopAdapter->generateUniqueId(),
                'moduleId'  => $this->getPrefixedModuleId($moduleId),
                'shopId'    => $shopId,
                'name'      => $shopModuleSetting->getName(),
                'type'      => $shopModuleSetting->getType(),
                'value'     => $shopSettingEncoder->encode(
                    $shopModuleSetting->getType(),
                    $shopModuleSetting->getValue()
                ),
                'key'       => $context->getConfigurationEncryptionKey(),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param string $moduleId
     * @return string
     */
    private function getPrefixedModuleId(string $moduleId): string
    {
        return 'module:' . $moduleId;
    }
}
