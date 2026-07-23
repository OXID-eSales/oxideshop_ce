<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\SecurityHeadersListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class SecurityHeadersListenerTest extends TestCase
{
    public function testAppliesConfiguredHeaders(): void
    {
        $event = $this->createResponseEvent(new Response());

        (new SecurityHeadersListener([
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
        ]))->onKernelResponse($event);

        $this->assertSame('SAMEORIGIN', $event->getResponse()->headers->get('X-Frame-Options'));
        $this->assertSame('nosniff', $event->getResponse()->headers->get('X-Content-Type-Options'));
    }

    public function testDoesNotOverwriteExistingHeader(): void
    {
        $response = new Response();
        $response->headers->set('X-Frame-Options', 'DENY');
        $event = $this->createResponseEvent($response);

        (new SecurityHeadersListener(['X-Frame-Options' => 'SAMEORIGIN']))->onKernelResponse($event);

        $this->assertSame('DENY', $event->getResponse()->headers->get('X-Frame-Options'));
    }

    public function testSkipsHeadersConfiguredEmpty(): void
    {
        $event = $this->createResponseEvent(new Response());

        (new SecurityHeadersListener(['X-Frame-Options' => '']))->onKernelResponse($event);

        $this->assertFalse($event->getResponse()->headers->has('X-Frame-Options'));
    }

    public function testIgnoresSubRequests(): void
    {
        $event = new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        (new SecurityHeadersListener(['X-Frame-Options' => 'SAMEORIGIN']))->onKernelResponse($event);

        $this->assertFalse($event->getResponse()->headers->has('X-Frame-Options'));
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
