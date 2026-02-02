<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * A test bundle for unit testing bundle integration.
 *
 * @internal
 */
class TestBundle extends Bundle
{
    public static bool $buildCalled = false;
    public static bool $bootCalled = false;
    public static bool $shutdownCalled = false;

    public static function resetState(): void
    {
        self::$buildCalled = false;
        self::$bootCalled = false;
        self::$shutdownCalled = false;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        self::$buildCalled = true;
    }

    public function boot(): void
    {
        parent::boot();
        self::$bootCalled = true;
    }

    public function shutdown(): void
    {
        parent::shutdown();
        self::$shutdownCalled = true;
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new TestBundleExtension();
    }
}
