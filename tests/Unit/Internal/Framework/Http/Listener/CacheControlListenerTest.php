<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\CacheControlListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CacheControlListenerTest extends TestCase
{
    public function testAppliesDefaultPolicyWhenResponseHasNoExplicitCaching(): void
    {
        $event = $this->createResponseEvent(new Response());

        (new CacheControlListener('no-store, no-cache, must-revalidate'))->onKernelResponse($event);

        $this->assertSame(
            'must-revalidate, no-cache, no-store, private',
            $event->getResponse()->headers->get('Cache-Control')
        );
    }

    public function testKeepsExplicitlySetCachePolicy(): void
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(3600);
        $event = $this->createResponseEvent($response);

        (new CacheControlListener('no-store'))->onKernelResponse($event);

        $this->assertStringContainsString('max-age=3600', (string) $event->getResponse()->headers->get('Cache-Control'));
        $this->assertStringContainsString('public', (string) $event->getResponse()->headers->get('Cache-Control'));
    }

    public function testDisabledWhenPolicyEmpty(): void
    {
        $event = $this->createResponseEvent(new Response());

        (new CacheControlListener(''))->onKernelResponse($event);

        $this->assertSame('no-cache, private', $event->getResponse()->headers->get('Cache-Control'));
    }

    private function createResponseEvent(Response $response): ResponseEvent
    {
        return new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );
    }
}
