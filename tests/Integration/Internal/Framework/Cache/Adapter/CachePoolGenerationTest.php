<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Cache\Adapter;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\CachePoolGeneration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

#[Group('cache')]
final class CachePoolGenerationTest extends TestCase
{
    private string $cacheDirectory;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->cacheDirectory = Path::join(sys_get_temp_dir(), uniqid('cache-pool-generation-', true));
        $this->filesystem = new Filesystem();
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->cacheDirectory);
    }

    public function testRenewSwitchesShopToNewDirectory(): void
    {
        $generation = $this->createGeneration();
        $oldDirectory = $generation->getDirectory(1);

        $generation->renew(1);

        $this->assertNotSame($oldDirectory, $generation->getDirectory(1));
    }

    public function testRenewedDirectoryIsSharedWithOtherInstances(): void
    {
        $generation = $this->createGeneration();

        $generation->renew(1);

        $this->assertSame($generation->getDirectory(1), $this->createGeneration()->getDirectory(1));
    }

    public function testRenewRemovesOldGeneration(): void
    {
        $generation = $this->createGeneration();
        $oldDirectory = $generation->getDirectory(1);
        $this->givenCachedItemIn($oldDirectory);

        $generation->renew(1);

        $this->assertDirectoryDoesNotExist($oldDirectory);
    }

    public function testRenewKeepsPoolOfOtherShops(): void
    {
        $generation = $this->createGeneration();
        $this->givenCachedItemIn($generation->getDirectory(2));

        $generation->renew(1);

        $this->assertFileExists(Path::join($generation->getDirectory(2), 'item'));
    }

    public function testRenewLogsOldGenerationThatCannotBeRemoved(): void
    {
        $this->givenCachedItemIn($this->createGeneration()->getDirectory(1));
        $filesystem = $this->createStub(Filesystem::class);
        $filesystem->method('remove')->willThrowException(new IOException('locked'));
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        (new CachePoolGeneration($this->createContext(), $filesystem, $logger))->renew(1);
    }

    private function createGeneration(): CachePoolGeneration
    {
        return new CachePoolGeneration($this->createContext(), $this->filesystem, new NullLogger());
    }

    private function createContext(): BasicContextInterface
    {
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getCacheDirectory')->willReturn($this->cacheDirectory);

        return $context;
    }

    private function givenCachedItemIn(string $directory): void
    {
        $this->filesystem->dumpFile(Path::join($directory, 'item'), 'value');
    }
}
