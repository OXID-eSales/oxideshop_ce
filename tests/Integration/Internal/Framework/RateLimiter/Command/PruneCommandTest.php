<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Command;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Command\PruneCommand;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class PruneCommandTest extends TestCase
{
    use ContainerTrait;

    public function testRemovesExpiredBucketsAndKeepsLiveOnes(): void
    {
        $cacheDirectory = $this->prepareCacheDirectory();

        $this->createContainer();
        $this->compileContainer();
        $this->get(BasicContextInterface::class)->setCacheDirectory($cacheDirectory);
        $tester = new CommandTester($this->get(PruneCommand::class));

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertFileDoesNotExist($cacheDirectory . '/rate_limiter/A/B/expired-bucket');
        $this->assertFileExists($cacheDirectory . '/rate_limiter/C/D/live-bucket');
    }

    private function prepareCacheDirectory(): string
    {
        $cacheDirectory = sys_get_temp_dir() . '/rate-limiter-prune-' . uniqid('', true);
        mkdir($cacheDirectory . '/rate_limiter/A/B', 0777, true);
        mkdir($cacheDirectory . '/rate_limiter/C/D', 0777, true);
        file_put_contents($cacheDirectory . '/rate_limiter/A/B/expired-bucket', (time() - 60) . "\nexpired\ndata");
        file_put_contents($cacheDirectory . '/rate_limiter/C/D/live-bucket', (time() + 3600) . "\nlive\ndata");

        return $cacheDirectory;
    }
}
