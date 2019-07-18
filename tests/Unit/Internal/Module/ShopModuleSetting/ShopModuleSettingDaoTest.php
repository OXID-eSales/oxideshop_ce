<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setting;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Database\TransactionServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingDao;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SettingDaoTest extends TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testRollbackTransactionOnSave()
    {
        $queryBuilderFactory = $this->getMockBuilder(QueryBuilderFactoryInterface::class)->getMock();
        $queryBuilderFactory
            ->method('create')
            ->willThrowException(new \Exception());

        $transactionService = $this->getMockBuilder(TransactionServiceInterface::class)->getMock();
        $transactionService
            ->expects($this->once())
            ->method('rollback');

        $shopModuleSettingDao = new SettingDao(
            $queryBuilderFactory,
            $this->getMockBuilder(ContextInterface::class)->getMock(),
            $this->getMockBuilder(ShopSettingEncoderInterface::class)->getMock(),
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock(),
            $transactionService
        );

        $shopModuleSettingDao->save(new Setting());
    }
}
