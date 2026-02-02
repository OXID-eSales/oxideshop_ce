<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Test extension that implements PrependExtensionInterface.
 *
 * @internal
 */
class PrependableExtension extends Extension implements PrependExtensionInterface
{
    public static bool $prependCalled = false;
    public static bool $loadCalled = false;

    public static function resetState(): void
    {
        self::$prependCalled = false;
        self::$loadCalled = false;
    }

    public function prepend(ContainerBuilder $container): void
    {
        self::$prependCalled = true;
        $container->setParameter('test_prepend.prepended', true);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        self::$loadCalled = true;
        $container->setParameter('test_prepend.loaded', true);
    }

    public function getAlias(): string
    {
        return 'test_prepend';
    }
}
