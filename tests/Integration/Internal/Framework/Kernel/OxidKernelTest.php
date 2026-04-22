<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\Service\GreetingServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestKernel;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[RunTestsInSeparateProcesses]
final class OxidKernelTest extends TestCase
{
    private TestKernel $kernel;

    protected function setUp(): void
    {
        $cacheDir = (new TestKernel('test', true))->getCacheDir();
        if (is_dir($cacheDir)) {
            $this->removeDir($cacheDir);
        }
        $this->kernel = new TestKernel('test', true);
        $this->kernel->boot();
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }

    protected function tearDown(): void
    {
        $this->kernel->shutdown();
    }

    public function testKernelBoots(): void
    {
        $this->assertNotNull($this->kernel->getContainer());
    }

    public function testFrameworkBundleRegistered(): void
    {
        $this->assertArrayHasKey('FrameworkBundle', $this->kernel->getBundles());
    }

    public function testTestBundleRegistered(): void
    {
        $this->assertArrayHasKey('TestBundle', $this->kernel->getBundles());
    }

    public function testCompilerPassExecuted(): void
    {
        $this->assertTrue(
            $this->kernel->getContainer()->hasParameter('test_bundle.compiler_pass_executed')
        );
    }

    public function testBundleServiceAutowired(): void
    {
        $service = $this->kernel->getContainer()->get(GreetingServiceInterface::class);
        $this->assertSame('Hello, World!', $service->greet('World'));
    }

    public function testRouteAttributeRegistered(): void
    {
        $router = $this->kernel->getContainer()->get('router');
        $route = $router->getRouteCollection()->get('test_hello');
        $this->assertNotNull($route);
        $this->assertSame('/api/test-bundle/hello/{name}', $route->getPath());
    }

    public function testControllerReturnsJsonResponse(): void
    {
        $request = Request::create('/api/test-bundle/hello/OXID');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('json', $response->headers->get('Content-Type'));

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Hello, OXID!', $data['message']);
    }

    public function testPostRequestEchosBody(): void
    {
        $request = Request::create('/api/test-bundle/echo', 'POST', [], [], [], ['CONTENT_TYPE' => 'text/plain'], 'test-payload');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('test-payload', $data['body']);
    }

    public function testRequestListenerFires(): void
    {
        $request = Request::create('/api/test-bundle/hello/Test');
        $this->kernel->handle($request);

        $this->assertTrue($request->attributes->get('test_bundle.request_listener_fired'));
    }

    public function testResponseListenerAddsHeader(): void
    {
        $request = Request::create('/api/test-bundle/hello/Test');
        $response = $this->kernel->handle($request);

        $this->assertSame('active', $response->headers->get('X-Test-Bundle'));
    }

    public function testResponseListenerFiresForLegacyCatchAllRoute(): void
    {
        $request = Request::create('/some/unmatched/legacy/path');
        $response = $this->kernel->handle($request);

        $this->assertSame(
            'active',
            $response->headers->get('X-Test-Bundle'),
            'kernel.response must fire for LegacyController-dispatched responses'
        );
    }

    public function testExceptionHandledGracefully(): void
    {
        $kernel = new TestKernel('prod', false);
        $kernel->boot();
        $request = Request::create('/api/test-bundle/error');
        $response = $kernel->handle($request);
        $kernel->shutdown();

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testKernelSecretConfigured(): void
    {
        $this->assertTrue($this->kernel->getContainer()->hasParameter('kernel.secret'));
        $this->assertNotEmpty($this->kernel->getContainer()->getParameter('kernel.secret'));
    }

    public function testHttpKernelServiceExists(): void
    {
        $this->assertTrue($this->kernel->getContainer()->has('http_kernel'));
    }

    public function testRouterServiceExists(): void
    {
        $this->assertTrue($this->kernel->getContainer()->has('router'));
    }

    public function testLegacyControllerRouteExists(): void
    {
        $router = $this->kernel->getContainer()->get('router');
        $route = $router->getRouteCollection()->get('legacy');
        $this->assertNotNull($route);
        $this->assertSame('/{path}', $route->getPath());
    }

    public function testTwigBundleRegistered(): void
    {
        $this->assertArrayHasKey('TwigBundle', $this->kernel->getBundles());
    }

    public function testTwigBundleLoadsTemplates(): void
    {
        $request = Request::create('/api/test-bundle/page/TwigTest');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Hello, TwigTest!', $response->getContent());
    }

    public function testBundleTemplateRendering(): void
    {
        $request = Request::create('/api/test-bundle/page/OXID');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Hello, OXID!', $response->getContent());
        $this->assertStringContainsString('Rendered by TestBundle', $response->getContent());
    }

    public function testTemplateInheritance(): void
    {
        $request = Request::create('/api/test-bundle/inherited/World');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('<title>World - Test</title>', $response->getContent());
        $this->assertStringContainsString('Hello, World!', $response->getContent());
    }
}
