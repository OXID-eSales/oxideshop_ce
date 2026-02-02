<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\BundleTemplateDirectoryResolver;
use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class BundleTemplateDirectoryResolverTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/oxid_tpl_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
        parent::tearDown();
    }

    public function testReturnsEmptyArrayWhenNoBundlesMetadata(): void
    {
        $resolver = new BundleTemplateDirectoryResolver([]);

        $this->assertSame([], $resolver->getTemplateDirectories());
    }

    public function testReturnsDirectoryForBundleWithResourcesTemplates(): void
    {
        $templateDir = $this->tempDir . '/Resources/templates';
        mkdir($templateDir, 0777, true);

        $resolver = new BundleTemplateDirectoryResolver([
            'TestBundle' => ['path' => $this->tempDir, 'namespace' => 'TestNs'],
        ]);

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertInstanceOf(NamespacedDirectory::class, $directories[0]);
        $this->assertSame('TestBundle', $directories[0]->getNamespace());
        $this->assertSame($templateDir, $directories[0]->getDirectory());
    }

    public function testReturnsDirectoryForBundleWithResourcesViews(): void
    {
        $viewsDir = $this->tempDir . '/Resources/views';
        mkdir($viewsDir, 0777, true);

        $resolver = new BundleTemplateDirectoryResolver([
            'TestBundle' => ['path' => $this->tempDir, 'namespace' => 'TestNs'],
        ]);

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertSame($viewsDir, $directories[0]->getDirectory());
    }

    public function testPreferResourcesTemplatesOverResourcesViews(): void
    {
        mkdir($this->tempDir . '/Resources/templates', 0777, true);
        mkdir($this->tempDir . '/Resources/views', 0777, true);

        $resolver = new BundleTemplateDirectoryResolver([
            'TestBundle' => ['path' => $this->tempDir, 'namespace' => 'TestNs'],
        ]);

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(1, $directories);
        $this->assertSame($this->tempDir . '/Resources/templates', $directories[0]->getDirectory());
    }

    public function testSkipsBundleWithNoTemplateDirectory(): void
    {
        $resolver = new BundleTemplateDirectoryResolver([
            'TestBundle' => ['path' => $this->tempDir, 'namespace' => 'TestNs'],
        ]);

        $this->assertSame([], $resolver->getTemplateDirectories());
    }

    public function testHandlesMultipleBundles(): void
    {
        $dir1 = $this->tempDir . '/bundle1';
        $dir2 = $this->tempDir . '/bundle2';
        mkdir($dir1 . '/Resources/templates', 0777, true);
        mkdir($dir2 . '/templates', 0777, true);

        $resolver = new BundleTemplateDirectoryResolver([
            'Bundle1' => ['path' => $dir1, 'namespace' => 'Ns1'],
            'Bundle2' => ['path' => $dir2, 'namespace' => 'Ns2'],
        ]);

        $directories = $resolver->getTemplateDirectories();

        $this->assertCount(2, $directories);
        $this->assertSame('Bundle1', $directories[0]->getNamespace());
        $this->assertSame('Bundle2', $directories[1]->getNamespace());
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
