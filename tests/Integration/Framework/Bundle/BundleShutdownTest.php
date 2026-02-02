<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Framework\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
class BundleShutdownTest extends TestCase
{
    public function testSetContainerNullIsCalledOnShutdown(): void
    {
        $bundle = $this->createMock(BundleInterface::class);
        $container = new Container();

        $manager = new BundleManager();
        $manager->boot($container, [$bundle]);

        $bundle->expects($this->once())
            ->method('setContainer')
            ->with(null);

        $manager->shutdown();
    }

    public function testShutdownCallsSetContainerNullAfterShutdown(): void
    {
        $callOrder = [];

        $bundle = $this->createMock(BundleInterface::class);
        $bundle->method('shutdown')->willReturnCallback(function () use (&$callOrder) {
            $callOrder[] = 'shutdown';
        });
        $bundle->method('setContainer')->willReturnCallback(function ($arg) use (&$callOrder) {
            if ($arg === null) {
                $callOrder[] = 'setContainer(null)';
            }
        });

        $manager = new BundleManager();
        $manager->boot(new Container(), [$bundle]);
        $manager->shutdown();

        $this->assertSame(['shutdown', 'setContainer(null)'], $callOrder);
    }
}
