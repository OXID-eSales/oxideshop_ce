<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass\RoutePass;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\ReservedRoutePathException;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass\Fixtures\ApiController;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass\Fixtures\IndexScriptPathController;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass\Fixtures\OptionalPathParameterController;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass\Fixtures\RootPathController;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass\Fixtures\SlugController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

final class RoutePassTest extends TestCase
{
    public function testProcessCompilesRoutesOfPublicServices(): void
    {
        $container = $this->containerWithPublicService(ApiController::class);

        new RoutePass()->process($container);

        $this->assertSame(
            [ApiController::class, 'show'],
            $this->matcher($container)->match('/api/items/7')['_controller']
        );
    }

    public function testProcessIgnoresRoutesOfPrivateServices(): void
    {
        $container = new ContainerBuilder();
        $container->register(ApiController::class, ApiController::class);
        new RoutePass()->process($container);

        $this->expectException(ResourceNotFoundException::class);

        $this->matcher($container)->match('/api/items/7');
    }

    #[DataProvider('controllerMatchingShopEntryPointDataProvider')]
    public function testProcessRejectsRouteMatchingShopEntryPoint(string $controllerClass): void
    {
        $this->expectException(ReservedRoutePathException::class);

        new RoutePass()->process($this->containerWithPublicService($controllerClass));
    }

    public static function controllerMatchingShopEntryPointDataProvider(): array
    {
        return [
            'root path' => [RootPathController::class],
            'index.php' => [IndexScriptPathController::class],
            'optional parameter on root path' => [OptionalPathParameterController::class],
        ];
    }

    public function testProcessCompilesRouteWithPathParameter(): void
    {
        $container = $this->containerWithPublicService(SlugController::class);

        new RoutePass()->process($container);

        $this->assertSame(SlugController::class, $this->matcher($container)->match('/landing-page')['_controller'][0]);
    }

    private function containerWithPublicService(string $class): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->register($class, $class)->setPublic(true);

        return $container;
    }

    private function matcher(ContainerBuilder $container): CompiledUrlMatcher
    {
        return new CompiledUrlMatcher($container->getParameter('oxid.routes'), new RequestContext());
    }
}
