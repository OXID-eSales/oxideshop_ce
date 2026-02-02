<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Test bundle extension for unit testing.
 *
 * @internal
 */
class TestBundleExtension extends Extension
{
    public static bool $loadCalled = false;

    public static function resetState(): void
    {
        self::$loadCalled = false;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        self::$loadCalled = true;
        $container->setParameter('test_bundle.loaded', true);
    }

    public function getAlias(): string
    {
        return 'test_bundle';
    }
}
