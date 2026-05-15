<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Cache\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Command\ClearCacheCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[Group('cache')]
final class ClearCacheCommandTest extends TestCase
{
    public function testClearCacheTriggersRegularAndTemplatesCleaners(): void
    {
        $command = new ClearCacheCommand(
            $this->getContainerCacheMock(),
            $this->getContextMock(),
            $this->getShopCacheCleanerMock()
        );

        $exitCode = $command->run(
            $this->createStub(InputInterface::class),
            $this->createStub(OutputInterface::class),
        );

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    private function getContainerCacheMock(): ContainerCacheInterface
    {
        $containerCacheMock = $this->createMock(ContainerCacheInterface::class);
        $containerCacheMock->expects($this->once())->method('invalidate');

        return $containerCacheMock;
    }

    private function getContextMock(): ContextInterface
    {
        $contextMock = $this->createMock(ContextInterface::class);
        $contextMock->expects($this->once())->method('getAllShopIds')->willReturn([1]);

        return $contextMock;
    }

    private function getShopCacheCleanerMock(): ShopCacheCleanerInterface
    {
        $shopCacheCleanerMock = $this->createMock(ShopCacheCleanerInterface ::class);
        $shopCacheCleanerMock->expects($this->once())->method('clearAll');

        return $shopCacheCleanerMock;
    }
}
