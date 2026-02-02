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
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

/**
 * @internal
 */
class BundleRoutingTest extends TestCase
{
    private SymfonyContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $symfonyContainer = (new ContainerBuilder($this->createContextWithTestBundle()))->getContainer();
        $symfonyContainer->compile();
        $this->container = $symfonyContainer;
    }

    public function testAttributeRouteIsRegistered(): void
    {
        $matcher = $this->buildMatcher();

        $match = $matcher->match('/test-bundle/hello');

        $this->assertSame('test_bundle_hello', $match['_route']);
    }

    public function testYamlRouteIsRegistered(): void
    {
        $matcher = $this->buildMatcher();

        $match = $matcher->match('/test-bundle/yaml-route');

        $this->assertSame('test_bundle_yaml_route', $match['_route']);
    }

    private function buildMatcher(): CompiledUrlMatcher
    {
        $compiledRoutes = $this->container->getParameter('oxid.routes');
        return new CompiledUrlMatcher($compiledRoutes, new RequestContext());
    }

    private function createContextWithTestBundle(): BasicContextStub
    {
        $bundlesYaml = sys_get_temp_dir() . '/oxid_test_bundles_' . uniqid() . '.yaml';
        file_put_contents($bundlesYaml, "bundles:\n  " . TestBundle::class . ": { all: true }\n");
        $context = new BasicContextStub();
        $context->setBundlesConfigFilePath($bundlesYaml);
        return $context;
    }
}
