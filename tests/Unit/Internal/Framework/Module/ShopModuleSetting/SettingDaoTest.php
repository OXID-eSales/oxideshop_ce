<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\ShopModuleSetting;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\TransactionServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDao;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class SettingDaoTest extends TestCase
{
    use ContainerTrait;

    public function testRollbackTransactionOnSave(): void
    {
        $this->expectException(\Exception::class);

        $queryBuilderFactory = $this->getMockBuilder(QueryBuilderFactoryInterface::class)->getMock();
        $queryBuilderFactory
            ->method('create')
            ->willThrowException(new \Exception());

        $transactionService = $this->getMockBuilder(TransactionServiceInterface::class)->getMock();
        $transactionService
            ->expects($this->once())
            ->method('rollback');

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $shopModuleSettingDao = new SettingDao(
            $queryBuilderFactory,
            $this->getMockBuilder(ContextInterface::class)->getMock(),
            $this->getMockBuilder(ShopSettingEncoderInterface::class)->getMock(),
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock(),
            $transactionService,
            $eventDispatcher
        );

        $shopModuleSettingDao->save(new Setting(), '', 1);
    }

    public function testDispatchEventOnSave()
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(SettingChangedEvent::class),
                $this->stringContains(SettingChangedEvent::NAME)
            );

        $shopModuleSettingDao = new SettingDao(
            $this->get(QueryBuilderFactoryInterface::class),
            $this->get(ContextInterface::class),
            $this->get(ShopSettingEncoderInterface::class),
            $this->get(ShopAdapterInterface::class),
            $this->getMockBuilder(TransactionServiceInterface::class)->getMock(),
            $eventDispatcher
        );

        $moduleSetting = new Setting();
        $moduleSetting->setName('module_param')->setType('str')->setValue('module_value');

        $shopModuleSettingDao->save($moduleSetting, 'phpunit_module_id', 1);
    }
}
