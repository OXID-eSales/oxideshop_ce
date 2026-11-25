<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Cache\Adapter;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\CachePoolGenerationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\PhpFilesTagAwareAdapterFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Cache\ItemInterface;

#[Group('cache')]
final class PhpFilesTagAwareAdapterFactoryTest extends TestCase
{
    private string $directory;
    private PhpFilesTagAwareAdapterFactory $factory;

    protected function setUp(): void
    {
        $this->directory = Path::join(sys_get_temp_dir(), uniqid('cache-pool-', true));
        $generation = $this->createStub(CachePoolGenerationInterface::class);
        $generation->method('getDirectory')->willReturn($this->directory);
        $this->factory = new PhpFilesTagAwareAdapterFactory($generation);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testCreateKeepsTagVersionsOutOfOpcache(): void
    {
        $this->storeTaggedItem('value');

        $plainTagVersionFiles = (new Finder())->files()->in(Path::join($this->directory, 'tags'))->notContains('<?php');

        $this->assertCount(1, $plainTagVersionFiles);
    }

    public function testCreateSeesTagInvalidationOfOtherInstance(): void
    {
        $this->storeTaggedItem('old');

        $this->factory->create(1)->invalidateTags(['some-tag']);

        $this->assertSame('new', $this->storeTaggedItem('new'));
    }

    private function storeTaggedItem(string $value): string
    {
        return $this->factory->create(1)->get('some-key', static function (ItemInterface $item) use ($value): string {
            $item->tag('some-tag');

            return $value;
        });
    }
}
