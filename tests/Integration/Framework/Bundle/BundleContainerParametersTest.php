<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Framework\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Tests\Integration\Framework\Bundle\TestBundle\TestBundle;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @internal
 */
class BundleContainerParametersTest extends TestCase
{
    private SymfonyContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->buildContainerWithTestBundle();
    }

    public function testKernelBundlesParameterContainsTestBundle(): void
    {
        $bundleMap = $this->container->getParameter('kernel.bundles');

        $this->assertArrayHasKey('TestBundle', $bundleMap);
        $this->assertSame(TestBundle::class, $bundleMap['TestBundle']);
    }

    public function testKernelBundlesMetadataContainsTestBundlePath(): void
    {
        $metadata = $this->container->getParameter('kernel.bundles_metadata');

        $this->assertArrayHasKey('TestBundle', $metadata);
        $this->assertSame(dirname((new TestBundle())->getPath()), dirname($metadata['TestBundle']['path']));
    }

    public function testKernelBundlesMetadataContainsTestBundleNamespace(): void
    {
        $metadata = $this->container->getParameter('kernel.bundles_metadata');

        $this->assertSame(
            'OxidEsales\EshopCommunity\Tests\Integration\Framework\Bundle\TestBundle',
            $metadata['TestBundle']['namespace']
        );
    }

    public function testKernelBundlesIsEmptyWithNoBundles(): void
    {
        $container = $this->buildContainerWithoutBundles();

        $this->assertSame([], $container->getParameter('kernel.bundles'));
        $this->assertSame([], $container->getParameter('kernel.bundles_metadata'));
    }

    private function buildContainerWithTestBundle(): SymfonyContainerBuilder
    {
        return (new ContainerBuilder($this->createContextWithBundles([TestBundle::class])))->getContainer();
    }

    private function buildContainerWithoutBundles(): SymfonyContainerBuilder
    {
        return (new ContainerBuilder($this->createContextWithBundles([])))->getContainer();
    }

    private function createContextWithBundles(array $bundleClasses): BasicContextStub
    {
        $bundlesYaml = $this->writeTempBundlesYaml($bundleClasses);
        $context = new BasicContextStub();
        $context->setBundlesConfigFilePath($bundlesYaml);
        return $context;
    }

    private function writeTempBundlesYaml(array $bundleClasses): string
    {
        $path = sys_get_temp_dir() . '/oxid_test_bundles_' . uniqid() . '.yaml';
        $lines = ['bundles:'];
        foreach ($bundleClasses as $class) {
            $lines[] = '  ' . $class . ': { all: true }';
        }
        file_put_contents($path, implode("\n", $lines) . "\n");
        return $path;
    }
}
