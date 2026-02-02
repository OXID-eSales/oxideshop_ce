<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleContainerExtension;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\PrependableBundle;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\PrependableExtension;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\TestBundle;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\TestBundleExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @internal
 */
class BundleContainerExtensionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TestBundle::resetState();
        TestBundleExtension::resetState();
        PrependableExtension::resetState();
    }

    public function testInitializeBundlesRegistersExtension(): void
    {
        $container = new SymfonyContainerBuilder();
        $bundle = new TestBundle();
        $bundleExtension = new BundleContainerExtension();

        $bundleExtension->initializeBundles($container, [$bundle]);

        $this->assertTrue($container->hasExtension('test_bundle'));
    }

    public function testInitializeBundlesCallsBuild(): void
    {
        $container = new SymfonyContainerBuilder();
        $bundle = new TestBundle();
        $bundleExtension = new BundleContainerExtension();

        $bundleExtension->initializeBundles($container, [$bundle]);

        $this->assertTrue(TestBundle::$buildCalled);
    }

    public function testLoadExtensionConfigsQueuesExtension(): void
    {
        $container = new SymfonyContainerBuilder();
        $bundle = new TestBundle();
        $bundleExtension = new BundleContainerExtension();

        $bundleExtension->initializeBundles($container, [$bundle]);
        $bundleExtension->loadExtensionConfigs($container, [$bundle]);

        $extensionConfigs = $container->getExtensionConfig('test_bundle');
        $this->assertIsArray($extensionConfigs);
    }

    public function testInitializeBundlesWithEmptyArray(): void
    {
        $container = new SymfonyContainerBuilder();
        $bundleExtension = new BundleContainerExtension();

        $bundleExtension->initializeBundles($container, []);

        $this->assertFalse($container->hasParameter('oxid.bundles'));
    }

    public function testLoadExtensionConfigsCallsPrependOnPrependableExtensions(): void
    {
        $container = new SymfonyContainerBuilder();
        $bundle = new PrependableBundle();
        $bundleExtension = new BundleContainerExtension();

        $bundleExtension->initializeBundles($container, [$bundle]);
        $bundleExtension->loadExtensionConfigs($container, [$bundle]);

        $this->assertTrue(PrependableExtension::$prependCalled);
    }

    public function testPrependSetsParameter(): void
    {
        $container = new SymfonyContainerBuilder();
        $bundle = new PrependableBundle();
        $bundleExtension = new BundleContainerExtension();

        $bundleExtension->initializeBundles($container, [$bundle]);
        $bundleExtension->loadExtensionConfigs($container, [$bundle]);

        $this->assertTrue($container->hasParameter('test_prepend.prepended'));
    }
}
