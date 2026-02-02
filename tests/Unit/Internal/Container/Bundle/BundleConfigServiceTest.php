<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\BundleConfigService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\TestBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
class BundleConfigServiceTest extends TestCase
{
    private string $tempDir;
    private string $bundlesConfigFile;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . '/oxid_bundle_config_test_' . uniqid();
        $this->filesystem->mkdir($this->tempDir);
        $this->bundlesConfigFile = $this->tempDir . '/bundles.yaml';
    }

    protected function tearDown(): void
    {
        if ($this->filesystem->exists($this->tempDir)) {
            $this->filesystem->remove($this->tempDir);
        }
        parent::tearDown();
    }

    public function testGetBundlesReturnsEmptyArrayWhenNoConfig(): void
    {
        $service = $this->createService();

        $bundles = $service->getBundles();

        $this->assertSame([], $bundles);
    }

    public function testGetBundlesReturnsConfiguredBundles(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => true],
            ],
        ]);
        $service = $this->createService();

        $bundles = $service->getBundles();

        $this->assertArrayHasKey(TestBundle::class, $bundles);
        $this->assertSame(['all' => true], $bundles[TestBundle::class]);
    }

    public function testAddBundle(): void
    {
        $service = $this->createService();

        $service->addBundle(TestBundle::class, ['all' => true]);

        $bundles = $service->getBundles();
        $this->assertArrayHasKey(TestBundle::class, $bundles);
        $this->assertSame(['all' => true], $bundles[TestBundle::class]);
    }

    public function testAddBundleWithDefaultOptions(): void
    {
        $service = $this->createService();

        $service->addBundle(TestBundle::class);

        $bundles = $service->getBundles();
        $this->assertSame(['all' => true], $bundles[TestBundle::class]);
    }

    public function testAddBundleWithEnvironments(): void
    {
        $service = $this->createService();

        $service->addBundle(TestBundle::class, ['environments' => ['dev', 'test']]);

        $bundles = $service->getBundles();
        $this->assertSame(['environments' => ['dev', 'test']], $bundles[TestBundle::class]);
    }

    public function testRemoveBundle(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => true],
            ],
        ]);
        $service = $this->createService();

        $service->removeBundle(TestBundle::class);

        $this->assertFalse($service->hasBundle(TestBundle::class));
    }

    public function testRemoveNonExistentBundleDoesNothing(): void
    {
        $service = $this->createService();

        $service->removeBundle(TestBundle::class);

        $this->assertFalse($service->hasBundle(TestBundle::class));
    }

    public function testHasBundle(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => true],
            ],
        ]);
        $service = $this->createService();

        $this->assertTrue($service->hasBundle(TestBundle::class));
        $this->assertFalse($service->hasBundle('NonExistent\\Bundle'));
    }

    public function testUpdateBundle(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => true],
            ],
        ]);
        $service = $this->createService();

        $service->updateBundle(TestBundle::class, ['environments' => ['prod']]);

        $bundles = $service->getBundles();
        $this->assertSame(['environments' => ['prod']], $bundles[TestBundle::class]);
    }

    public function testUpdateNonExistentBundleThrowsException(): void
    {
        $service = $this->createService();

        $this->expectException(\RuntimeException::class);

        $service->updateBundle('NonExistent\\Bundle', ['all' => true]);
    }

    public function testAddBundleCreatesDirectoryIfNotExists(): void
    {
        $this->filesystem->remove($this->tempDir);
        $service = $this->createService();

        $service->addBundle(TestBundle::class);

        $this->assertTrue($this->filesystem->exists($this->bundlesConfigFile));
    }

    public function testMultipleBundleOperations(): void
    {
        $service = $this->createService();

        $service->addBundle(TestBundle::class, ['all' => true]);
        $service->addBundle('Another\\Bundle', ['environments' => ['dev']]);

        $bundles = $service->getBundles();
        $this->assertCount(2, $bundles);

        $service->removeBundle(TestBundle::class);
        $bundles = $service->getBundles();
        $this->assertCount(1, $bundles);
        $this->assertArrayHasKey('Another\\Bundle', $bundles);
    }

    private function createService(): BundleConfigService
    {
        $context = $this->createMock(BasicContextInterface::class);
        $context->method('getBundlesConfigFilePath')->willReturn($this->bundlesConfigFile);
        return new BundleConfigService($context, $this->filesystem);
    }

    private function writeConfig(array $config): void
    {
        file_put_contents($this->bundlesConfigFile, Yaml::dump($config, 3, 2));
    }
}
