<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SessionCommitListenerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.force_session_start', true);
    }

    public function testFreezesStartedSessionOnMainResponse(): void
    {
        Registry::getSession()->start();
        Registry::getSession()->setVariable('some-key', 'some-value');

        $this->dispatchResponseEvent(HttpKernelInterface::MAIN_REQUEST, new Response());

        $this->assertFalse(Registry::getSession()->isSessionStarted());
        session_start();
        $this->assertSame('some-value', $_SESSION['some-key'] ?? null);
    }

    public function testKeepsSessionOpenOnSubRequestResponse(): void
    {
        Registry::getSession()->start();

        $this->dispatchResponseEvent(HttpKernelInterface::SUB_REQUEST, new Response());

        $this->assertTrue(Registry::getSession()->isSessionStarted());
    }

    public function testDoesNotCreateSessionWhenNoneWasUsed(): void
    {
        Registry::set(Session::class, null);

        $this->dispatchResponseEvent(HttpKernelInterface::MAIN_REQUEST, new Response());

        $this->assertFalse(Registry::instanceExists(Session::class));
    }

    public function testMakesPublicResponseOfStartedSessionPrivate(): void
    {
        Registry::getSession()->start();
        $response = new Response(headers: ['Cache-Control' => 'public, max-age=600']);

        $this->dispatchResponseEvent(HttpKernelInterface::MAIN_REQUEST, $response);

        $this->assertSame('max-age=600, private', $response->headers->get('Cache-Control'));
    }

    public function testKeepsPublicResponseWithoutStartedSession(): void
    {
        $response = new Response(headers: ['Cache-Control' => 'public, max-age=600']);

        $this->dispatchResponseEvent(HttpKernelInterface::MAIN_REQUEST, $response);

        $this->assertSame('max-age=600, public', $response->headers->get('Cache-Control'));
    }

    public function testKernelMakesPublicCacheAttributeResponseOfStartedSessionPrivate(): void
    {
        Registry::getSession()->start();
        $request = new Request();
        $request->attributes->set(
            '_controller',
            #[Cache(public: true, maxage: 600)] static fn(): Response => new Response()
        );

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('max-age=600, private', $response->headers->get('Cache-Control'));
    }

    private function dispatchResponseEvent(int $requestType, Response $response): void
    {
        $this->get(EventDispatcherInterface::class)->dispatch(
            new ResponseEvent($this->createStub(HttpKernelInterface::class), new Request(), $requestType, $response),
            KernelEvents::RESPONSE
        );
    }
}
