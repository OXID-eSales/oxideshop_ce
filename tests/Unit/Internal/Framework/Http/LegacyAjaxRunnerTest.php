<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\LegacyAjaxRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;

final class LegacyAjaxRunnerTest extends TestCase
{
    public function testRunsTheGivenController(): void
    {
        $runner = new LegacyAjaxRunner(
            new HttpKernel(
                new EventDispatcher(),
                new ContainerControllerResolver(new Container()),
                new RequestStack(),
                new ArgumentResolver(),
                handleAllThrowables: true
            ),
            Request::create('/any/path')
        );

        ob_start();
        $runner->runController(static fn(): Response => new Response('legacy-ajax-controller'));
        $sentContent = (string) ob_get_clean();

        $this->assertSame('legacy-ajax-controller', $sentContent);
    }
}
