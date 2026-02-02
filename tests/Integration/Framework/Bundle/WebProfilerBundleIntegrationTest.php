<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Framework\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

/**
 * @internal
 */
class WebProfilerBundleIntegrationTest extends TestCase
{
    private SymfonyContainerBuilder $container;
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/oxid_web_profiler_test_' . uniqid();
        mkdir($this->tempDir . '/var/configuration', 0777, true);

        $bundlesYaml = $this->tempDir . '/bundles.yaml';
        file_put_contents(
            $bundlesYaml,
            'bundles:' . "\n" . '  ' . WebProfilerBundle::class . ': { all: true }' . "\n"
        );

        file_put_contents($this->tempDir . '/var/configuration/routes.yaml', implode("\n", [
            '_wdt:',
            "    resource: '@WebProfilerBundle/Resources/config/routing/wdt.xml'",
            '    prefix: /_wdt',
            '_profiler:',
            "    resource: '@WebProfilerBundle/Resources/config/routing/profiler.xml'",
            '    prefix: /_profiler',
        ]));

        $context = new BasicContextStub();
        $context->setBundlesConfigFilePath($bundlesYaml);
        $context->setShopRootPath($this->tempDir);

        $symfonyContainer = (new ContainerBuilder($context))->getContainer();
        $this->registerWebProfilerStubs($symfonyContainer);
        $symfonyContainer->compile();
        $this->container = $symfonyContainer;
    }

    public function testWebProfilerBundleIsRegistered(): void
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        $this->assertArrayHasKey('WebProfilerBundle', $bundles);
        $this->assertSame(WebProfilerBundle::class, $bundles['WebProfilerBundle']);
    }

    public function testProfilerRoutesAreRegistered(): void
    {
        $match = $this->buildMatcher()->match('/_profiler/search');

        $this->assertSame('_profiler_search', $match['_route']);
    }

    public function testWdtRouteIsRegistered(): void
    {
        $match = $this->buildMatcher()->match('/_wdt/abc123');

        $this->assertSame('_wdt', $match['_route']);
        $this->assertSame('abc123', $match['token']);
    }

    public function testWebProfilerTemplateDirectoryIsResolved(): void
    {
        $metadata = $this->container->getParameter('kernel.bundles_metadata');

        $this->assertArrayHasKey('WebProfilerBundle', $metadata);
        $this->assertDirectoryExists($metadata['WebProfilerBundle']['path'] . '/Resources/views');
    }

    private function registerWebProfilerStubs(SymfonyContainerBuilder $container): void
    {
        $container->register('request_stack', \Symfony\Component\HttpFoundation\RequestStack::class);
        $container->register('router', \OxidEsales\EshopCommunity\Internal\Framework\Api\NullUrlGenerator::class);
        $container->register('error_handler.error_renderer.html', \Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer::class)
            ->setArguments([
                $container->getParameter('kernel.debug'),
                $container->getParameter('kernel.charset'),
                null,
                $container->getParameter('kernel.project_dir'),
                '',
                null,
            ]);
        $container->setParameter('data_collector.templates', []);
        $container->setParameter('debug.file_link_format', '');
    }

    private function buildMatcher(): CompiledUrlMatcher
    {
        return new CompiledUrlMatcher(
            $this->container->getParameter('oxid.routes'),
            new RequestContext()
        );
    }
}
