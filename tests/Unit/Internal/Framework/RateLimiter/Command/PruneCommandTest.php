<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Command;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Command\PruneCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PruneCommandTest extends TestCase
{
    public function testPrunesTheCachePool(): void
    {
        $cachePool = $this->createMock(PruneableInterface::class);
        $cachePool->expects($this->once())->method('prune')->willReturn(true);
        $command = new PruneCommand($cachePool);

        $exitCode = $command->run(
            $this->createStub(InputInterface::class),
            $this->createStub(OutputInterface::class)
        );

        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
