<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\TerminableInterface;

final class HttpKernelServiceTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testKernelServiceIsTerminable(): void
    {
        $this->assertInstanceOf(TerminableInterface::class, $this->get(HttpKernelInterface::class));
    }

    public function testRunsTheControllerFromRequestAttributes(): void
    {
        $request = $this->requestWithController(static fn(): Response => new Response('from-controller'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('from-controller', $response->getContent());
    }

    public function testDeliversRedirectResponseForRedirectExceptionFromTheController(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new RedirectException('https://shop.example/target', 302);
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('https://shop.example/target', $response->headers->get('Location'));
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testSetsHtmlContentTypeOnResponseWithoutOne(): void
    {
        $request = $this->requestWithController(static fn(): Response => new Response('some-content'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function testStripsBodyFromResponseToHeadRequest(): void
    {
        $request = Request::create('/', 'HEAD');
        $request->attributes->set('_controller', static fn(): Response => new Response('some-content'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('', $response->getContent());
    }

    public function testCookiesQueuedDuringHandlingGetSecureDefaultOnHttpsRequest(): void
    {
        $this->get(EventDispatcherInterface::class)->addListener(
            KernelEvents::RESPONSE,
            static function (ResponseEvent $event): void {
                $event->getResponse()->headers->setCookie(Cookie::create('some-cookie', 'some-value'));
            }
        );
        $request = Request::create('https://anything.local/');
        $request->attributes->set('_controller', static fn(): Response => new Response('some-content'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        [$cookie] = $response->headers->getCookies();
        $this->assertTrue($cookie->isSecure());
    }

    private function requestWithController(callable $controller): Request
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);

        return $request;
    }
}
