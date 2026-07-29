<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\FileSystem;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ConfiguredShopIdProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ConfiguredShopIdProviderTest extends TestCase
{
    private string $shopsDirectory;

    public function testGetShopIdsReturnsEmptyListWhenNoShopIsConfigured(): void
    {
        $this->createShopsDirectory([]);

        $this->assertSame([], $this->createProvider()->getShopIds());
    }

    public function testGetShopIdsReturnsConfiguredShopsSorted(): void
    {
        $this->createShopsDirectory(['1' => [], '10' => [], '2' => []]);

        $this->assertSame([1, 2, 10], $this->createProvider()->getShopIds());
    }

    public function testGetShopIdsIgnoresNonDirectoryEntriesAndNonNumericNames(): void
    {
        $this->createShopsDirectory(['2' => [], 'not-a-shop' => [], '3' => 'a file, not a directory']);

        $this->assertSame([2], $this->createProvider()->getShopIds());
    }

    private function createProvider(): ConfiguredShopIdProvider
    {
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getDefaultShopId')->willReturn(1);
        $context->method('getShopConfigurationDirectory')->willReturnCallback(
            fn (int $shopId): string => Path::join($this->shopsDirectory, (string)$shopId)
        );

        return new ConfiguredShopIdProvider($context, new Filesystem());
    }

    private function createShopsDirectory(array $structure): void
    {
        $root = vfsStream::setup('configuration', null, $structure === [] ? [] : ['shops' => $structure]);
        $this->shopsDirectory = $root->url() . '/shops';
    }
}
