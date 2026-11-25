<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\CacheControlListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CacheControlListenerTest extends TestCase
{
    private const DEFAULT_POLICY = 'no-store, no-cache, must-revalidate';
    private const DEFAULT_POLICY_AS_SENT = 'must-revalidate, no-cache, no-store, private';

    #[DataProvider('responseWithoutOwnCachePolicyDataProvider')]
    public function testAppliesDefaultPolicyToResponseWithoutOwnCachePolicy(Response $response): void
    {
        $event = $this->createResponseEvent(new Request(), $response);

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame(self::DEFAULT_POLICY_AS_SENT, $event->getResponse()->headers->get('Cache-Control'));
    }

    public static function responseWithoutOwnCachePolicyDataProvider(): array
    {
        return [
            'plain response' => [new Response()],
            'permanent redirect' => [new RedirectResponse('https://shop.example/target', 301)],
            'no-cache' => [new Response(headers: ['Cache-Control' => 'no-cache'])],
        ];
    }

    #[DataProvider('ownCachePolicyDataProvider')]
    public function testKeepsOwnCachePolicy(Response $response, string $expectedCacheControl): void
    {
        $event = $this->createResponseEvent(new Request(), $response);

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame($expectedCacheControl, $event->getResponse()->headers->get('Cache-Control'));
    }

    public static function ownCachePolicyDataProvider(): array
    {
        return [
            'max-age' => [
                new Response(headers: ['Cache-Control' => 'public, max-age=3600']),
                'max-age=3600, public',
            ],
            's-maxage' => [new Response(headers: ['Cache-Control' => 's-maxage=60']), 's-maxage=60'],
            'expires' => [
                new Response(headers: ['Expires' => 'Thu, 01 Jan 2099 00:00:00 GMT']),
                'private, must-revalidate',
            ],
            'etag' => [new Response(headers: ['ETag' => '"v1"']), 'no-cache, private'],
            'last-modified' => [
                new Response(headers: ['Last-Modified' => 'Thu, 01 Jan 2026 00:00:00 GMT']),
                'private, must-revalidate',
            ],
            'public' => [new Response(headers: ['Cache-Control' => 'public']), 'public'],
            'no-store' => [new Response(headers: ['Cache-Control' => 'no-store']), 'no-store, private'],
        ];
    }

    public function testAppliesNoPolicyWhenDefaultPolicyIsEmpty(): void
    {
        $event = $this->createResponseEvent(new Request(), new Response());

        (new CacheControlListener(''))->onKernelResponse($event);

        $this->assertSame('no-cache, private', $event->getResponse()->headers->get('Cache-Control'));
    }

    public function testKeepsCachePolicyOfCacheAttribute(): void
    {
        $response = new Response();
        $response->setPrivate();
        $event = $this->createResponseEvent($this->createRequestWithCacheAttribute(), $response);

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame('private', $event->getResponse()->headers->get('Cache-Control'));
    }

    #[DataProvider('statusIgnoredByCacheAttributeDataProvider')]
    public function testAppliesDefaultPolicyToStatusIgnoredByCacheAttribute(int $statusCode): void
    {
        $event = $this->createResponseEvent($this->createRequestWithCacheAttribute(), new Response('', $statusCode));

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame(self::DEFAULT_POLICY_AS_SENT, $event->getResponse()->headers->get('Cache-Control'));
    }

    public static function statusIgnoredByCacheAttributeDataProvider(): array
    {
        return [
            'forbidden' => [Response::HTTP_FORBIDDEN],
            'too many requests' => [Response::HTTP_TOO_MANY_REQUESTS],
            'server error' => [Response::HTTP_INTERNAL_SERVER_ERROR],
            'service unavailable' => [Response::HTTP_SERVICE_UNAVAILABLE],
        ];
    }

    public function testIgnoresSubRequests(): void
    {
        $event = new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame('no-cache, private', $event->getResponse()->headers->get('Cache-Control'));
    }

    #[DataProvider('userSpecificResponseDataProvider')]
    public function testMakesPublicUserSpecificResponsePrivate(Request $request, Response $response): void
    {
        $event = $this->createResponseEvent($request, $response);

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame('max-age=600, private', $event->getResponse()->headers->get('Cache-Control'));
    }

    public static function userSpecificResponseDataProvider(): array
    {
        $responseWithCookie = new Response(headers: ['Cache-Control' => 'public, max-age=600, s-maxage=600']);
        $responseWithCookie->headers->setCookie(Cookie::create('sid', 'session-of-user-a'));

        return [
            'cookie' => [new Request(), $responseWithCookie],
            'started session' => [
                new Request(attributes: ['_session_started' => true]),
                new Response(headers: ['Cache-Control' => 'public, max-age=600, s-maxage=600']),
            ],
        ];
    }

    public function testKeepsPrivateResponseWithCookie(): void
    {
        $response = new Response(headers: ['Cache-Control' => 'private, max-age=600']);
        $response->headers->setCookie(Cookie::create('some-cookie', 'some-value'));
        $event = $this->createResponseEvent(new Request(), $response);

        (new CacheControlListener(self::DEFAULT_POLICY))->onKernelResponse($event);

        $this->assertSame('max-age=600, private', $event->getResponse()->headers->get('Cache-Control'));
    }

    private function createRequestWithCacheAttribute(): Request
    {
        $request = new Request();
        $request->attributes->set('_cache', [new Cache(public: false)]);

        return $request;
    }

    private function createResponseEvent(Request $request, Response $response): ResponseEvent
    {
        return new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );
    }
}
