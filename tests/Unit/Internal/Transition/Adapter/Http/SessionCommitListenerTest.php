<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http\SessionCommitListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class SessionCommitListenerTest extends TestCase
{
    public function tearDown(): void
    {
        Registry::set(Session::class, null);

        parent::tearDown();
    }

    public function testCommitsStartedSessionOnMainRequest(): void
    {
        $session = $this->createMock(Session::class);
        $session->method('isSessionStarted')->willReturn(true);
        $session->expects($this->once())->method('freeze');
        Registry::set(Session::class, $session);

        new SessionCommitListener()->onKernelResponse($this->createResponseEvent(HttpKernelInterface::MAIN_REQUEST));
    }

    public function testSkipsSessionThatWasNeverStarted(): void
    {
        $session = $this->createMock(Session::class);
        $session->method('isSessionStarted')->willReturn(false);
        $session->expects($this->never())->method('freeze');
        Registry::set(Session::class, $session);

        new SessionCommitListener()->onKernelResponse($this->createResponseEvent(HttpKernelInterface::MAIN_REQUEST));
    }

    public function testSkipsSubRequests(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->never())->method('freeze');
        Registry::set(Session::class, $session);

        new SessionCommitListener()->onKernelResponse($this->createResponseEvent(HttpKernelInterface::SUB_REQUEST));
    }

    private function createResponseEvent(int $requestType): ResponseEvent
    {
        return new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            $requestType,
            new Response()
        );
    }
}
