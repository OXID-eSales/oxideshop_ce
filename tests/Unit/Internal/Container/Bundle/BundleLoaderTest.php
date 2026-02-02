<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleLoader;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\TestBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
class BundleLoaderTest extends TestCase
{
    private string $tempDir;
    private string $bundlesConfigFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/oxid_bundle_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
        $this->bundlesConfigFile = $this->tempDir . '/bundles.yaml';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->bundlesConfigFile)) {
            unlink($this->bundlesConfigFile);
        }
        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
        parent::tearDown();
    }

    public function testLoadBundlesReturnsEmptyArrayWhenConfigFileNotExists(): void
    {
        $context = $this->createContextMock('/non/existing/path/bundles.yaml');
        $loader = new BundleLoader($context);

        $bundles = $loader->loadBundles();

        $this->assertSame([], $bundles);
    }

    public function testLoadBundlesReturnsEmptyArrayWhenNoBundlesConfigured(): void
    {
        $this->writeConfig(['bundles' => []]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $bundles = $loader->loadBundles();

        $this->assertSame([], $bundles);
    }

    public function testLoadBundlesReturnsBundleInstances(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $bundles = $loader->loadBundles();

        $this->assertCount(1, $bundles);
        $this->assertInstanceOf(TestBundle::class, $bundles[0]);
    }

    public function testLoadBundlesWithAllEnvironments(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $bundles = $loader->loadBundles('prod');

        $this->assertCount(1, $bundles);
    }

    public function testLoadBundlesWithSpecificEnvironment(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['dev' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $bundlesDev = $loader->loadBundles('dev');
        $bundlesProd = $loader->loadBundles('prod');

        $this->assertCount(1, $bundlesDev);
        $this->assertCount(0, $bundlesProd);
    }

    public function testLoadBundlesWithAllFalseDisablesBundle(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['all' => false],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $this->assertCount(0, $loader->loadBundles('prod'));
        $this->assertCount(0, $loader->loadBundles('dev'));
    }

    public function testLoadBundlesWithMultipleEnvironments(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['dev' => true, 'test' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $this->assertCount(1, $loader->loadBundles('dev'));
        $this->assertCount(1, $loader->loadBundles('test'));
        $this->assertCount(0, $loader->loadBundles('prod'));
    }

    public function testLoadBundlesWithNoEnvironmentFilterLoadsAll(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => ['dev' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $bundles = $loader->loadBundles(null);

        $this->assertCount(1, $bundles);
    }

    public function testLoadBundlesWithEmptyOptionsDisablesBundle(): void
    {
        $this->writeConfig([
            'bundles' => [
                TestBundle::class => [],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $this->assertCount(0, $loader->loadBundles('prod'));
    }

    public function testLoadBundlesThrowsExceptionForNonExistentClass(): void
    {
        $this->writeConfig([
            'bundles' => [
                'NonExistent\\Bundle\\Class' => ['all' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $this->expectException(\RuntimeException::class);

        $loader->loadBundles();
    }

    public function testLoadBundlesThrowsExceptionForInvalidBundleClass(): void
    {
        $this->writeConfig([
            'bundles' => [
                \stdClass::class => ['all' => true],
            ],
        ]);
        $context = $this->createContextMock($this->bundlesConfigFile);
        $loader = new BundleLoader($context);

        $this->expectException(\RuntimeException::class);

        $loader->loadBundles();
    }

    private function createContextMock(string $bundlesConfigPath): BasicContextInterface
    {
        $context = $this->createMock(BasicContextInterface::class);
        $context->method('getBundlesConfigFilePath')->willReturn($bundlesConfigPath);
        return $context;
    }

    private function writeConfig(array $config): void
    {
        file_put_contents($this->bundlesConfigFile, Yaml::dump($config));
    }
}
