<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

final class TemplateCacheServiceTest extends TestCase
{
    use ContainerTrait;

    private string $templateCachePath;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->templateCachePath = $this->get(BasicContextInterface::class)->getTemplateCacheDirectory();

        $this->clearTemplateCache();
        $this->populateTemplateCache();

        parent::setUp();
    }

    public function testInvalidateTemplateCache(): void
    {
        $this->assertNotCount(0, \glob($this->selectAllCacheFiles()));

        $this->get(TemplateCacheServiceInterface::class)->invalidateTemplateCache();

        self::assertCount(0, \glob($this->selectAllCacheFiles()));
    }

    private function clearTemplateCache(): void
    {
        $this->filesystem->remove($this->selectAllCacheFiles());
    }

    private function selectAllCacheFiles(): string
    {
        return "$this->templateCachePath/*";
    }

    private function populateTemplateCache(): void
    {
        $numberOfTestFiles = 3;
        $this->filesystem->mkdir($this->templateCachePath);
        for ($i = 0; $i < $numberOfTestFiles; $i++) {
            $this->filesystem->touch(
                Path::join(
                    $this->templateCachePath,
                    uniqid('template-file-', true)
                )
            );
        }
    }
}
