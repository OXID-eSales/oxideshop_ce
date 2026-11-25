<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator\FileTypeNotAllowedException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Cookie\ResponseCookieQueueInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\AccessDeniedException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponseFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

final class HttpKernelServiceTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.debug_mode', false);
    }

    public function testRoutesTheRequestToTheControllerOfTheMatchingRoute(): void
    {
        $this->useRoutedControllerFixture();

        $response = $this->get(HttpKernelInterface::class)->handle(Request::create('/kernel-routing/7'));

        $this->assertSame('routed-body id=7', $response->getContent());
    }

    public function testAnswersWrongMethodOnRouteWithMethodNotAllowed(): void
    {
        $this->useRoutedControllerFixture();
        $request = Request::create('/kernel-routing/7', 'POST', server: ['HTTP_ACCEPT' => 'application/json']);
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response('fallback-body'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
    }

    public function testAnswersUnroutedRequestWithoutFallbackWithNotFound(): void
    {
        $request = Request::create('/', server: ['HTTP_ACCEPT' => 'application/json']);

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testRunsTheFallbackControllerForTheRootPath(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response('fallback-body'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testDeliversRedirectResponseForRedirectExceptionFromTheController(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new RedirectException('https://shop.example/target', 302);
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('https://shop.example/target', $response->headers->get('Location'));
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testAnswersRoutingExceptionWithJsonProblem(): void
    {
        $response = $this->handleJsonRequestThrowing(new RoutingException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testAnswersWrongMethodWithJsonProblemAndAllowedMethods(): void
    {
        $response = $this->handleJsonRequestThrowing(new MethodNotAllowedHttpException(['GET']));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
    }

    public function testAnswersServerErrorsOfHtmlClientsWithTheOfflinePage(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new RuntimeException('boom');
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame($this->getOfflinePage(), $response->getContent());
        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function testAnswersOfflineShopWithServiceUnavailable(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new ShopOfflineException();
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
        $this->assertSame($this->getOfflinePage(), $response->getContent());
    }

    public function testAnswersAccessDeniedWithForbidden(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new AccessDeniedException();
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertStringContainsString('The server returned a "403 Forbidden".', $response->getContent());
        $this->assertStringNotContainsString($this->getOfflinePage(), $response->getContent());
    }

    public function testAnswersForbiddenFileTypeWithBadRequest(): void
    {
        $response = $this->handleJsonRequestThrowing(new FileTypeNotAllowedException());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testAnswersTooManyRequestsOfHtmlClientsWithTheGenericErrorPage(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new TooManyRequestsHttpException(45);
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertSame('45', $response->headers->get('Retry-After'));
        $this->assertStringContainsString(
            'The server returned a "429 Too Many Requests".',
            $response->getContent()
        );
    }

    #[DataProvider('loggedExceptionDataProvider')]
    public function testLogsExceptionWithMappedLevel(string $exceptionClass, array $arguments, string $level): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('log')->with($level);
        $this->useLogger($logger);

        $this->handleJsonRequestThrowing(new $exceptionClass(...$arguments));
    }

    public static function loggedExceptionDataProvider(): array
    {
        return [
            'routing exception' => [RoutingException::class, [], LogLevel::INFO],
            'not found' => [NotFoundHttpException::class, [], LogLevel::INFO],
            'shop offline' => [ShopOfflineException::class, [], LogLevel::INFO],
            'access denied' => [AccessDeniedException::class, [], LogLevel::INFO],
            'file type not allowed' => [FileTypeNotAllowedException::class, [], LogLevel::WARNING],
            'too many requests' => [TooManyRequestsHttpException::class, [], LogLevel::INFO],
            'method not allowed' => [MethodNotAllowedHttpException::class, [['GET']], LogLevel::ERROR],
            'server error' => [RuntimeException::class, [], LogLevel::CRITICAL],
        ];
    }

    public function testDoesNotLogRedirects(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('log');
        $this->useLogger($logger);

        $this->handleJsonRequestThrowing(new RedirectException('https://shop.example/target'));
    }

    public function testAddsExceptionDetailsToJsonProblemsInDebugMode(): void
    {
        $this->setParameter('oxid_esales.debug_mode', true);

        $response = $this->handleJsonRequestThrowing(new RuntimeException('boom'));

        $problem = json_decode($response->getContent(), true);
        $this->assertSame('boom', $problem['detail']);
        $this->assertSame(RuntimeException::class, $problem['class']);
        $this->assertNotEmpty($problem['trace']);
    }

    public function testShowsTheExceptionPageForNotFoundInDebugMode(): void
    {
        $this->setParameter('oxid_esales.debug_mode', true);
        $request = $this->requestWithController(static function (): Response {
            throw new RoutingException('Controller "nothing" cannot be resolved');
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame(
            rawurlencode('Controller "nothing" cannot be resolved'),
            $response->headers->get('X-Debug-Exception')
        );
    }

    public function testAppliesTheCacheAttributeOfTheController(): void
    {
        $this->withoutActiveSession();
        $request = $this->requestWithController(
            #[Cache(public: true, maxage: 600)] static fn(): Response => new Response()
        );

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('max-age=600, public', $response->headers->get('Cache-Control'));
    }

    public function testMakesPublicCacheAttributeResponseWithQueuedCookiePrivate(): void
    {
        $this->withoutActiveSession();
        $cookieQueue = $this->get(ResponseCookieQueueInterface::class);
        $request = $this->requestWithController(
            #[Cache(public: true, maxage: 600, smaxage: 600)] static function () use ($cookieQueue): Response {
                $cookieQueue->add(Cookie::create('sid', 'session-of-user-a'));

                return new Response();
            }
        );

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('max-age=600, private', $response->headers->get('Cache-Control'));
    }

    public function testAppliesTheDefaultCachePolicyToControllerWithoutCacheAttribute(): void
    {
        $request = $this->requestWithController(static fn(): Response => new Response());

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testAppliesDefaultCachePolicyToPermanentRedirect(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new RedirectException('https://shop.example/target', 301);
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertSame('must-revalidate, no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testKeepsRevalidationForControllerDeclaringAnEtag(): void
    {
        $request = $this->requestWithController(#[Cache(etag: "'v1'")] static fn(): Response => new Response());

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('"' . hash('sha256', 'v1') . '"', $response->getEtag());
        $this->assertSame('no-cache, private', $response->headers->get('Cache-Control'));
    }

    public function testAddsQueuedCookiesAsSecureToHttpsResponse(): void
    {
        $request = Request::create('https://shop.local/');
        $request->attributes->set('_controller', function (): Response {
            $this->get(ResponseCookieQueueInterface::class)->add(Cookie::create('some-cookie', 'some-value'));

            return new Response();
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertTrue($response->headers->getCookies()[0]->isSecure());
    }

    public function testDoesNotLeakQueuedCookiesIntoNextRequest(): void
    {
        $this->get(ResponseCookieQueueInterface::class)->add(Cookie::create('stale-cookie', 'some-value'));
        $request = $this->requestWithController(static fn(): Response => new Response());

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame([], $response->headers->getCookies());
    }

    public function testAddsSecurityHeadersToResponse(): void
    {
        $request = $this->requestWithController(static fn(): Response => new Response());

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
    }

    private function handleJsonRequestThrowing(Throwable $throwable): Response
    {
        $request = Request::create('/nothing', 'GET', server: ['HTTP_ACCEPT' => 'application/json']);
        $request->attributes->set('_controller', static function () use ($throwable): Response {
            throw $throwable;
        });

        return $this->get(HttpKernelInterface::class)->handle($request);
    }

    private function useLogger(LoggerInterface $logger): void
    {
        $this->createContainerWithoutDebugMode();
        $this->replaceService(LoggerInterface::class, $logger);
        $this->replaceContainerInstance();
    }

    private function requestWithController(callable $controller): Request
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);

        return $request;
    }

    private function useRoutedControllerFixture(): void
    {
        $this->createContainerWithoutDebugMode();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();
    }

    private function createContainerWithoutDebugMode(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_esales.debug_mode', false);
    }

    private function withoutActiveSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    private function getOfflinePage(): string
    {
        return (string) $this->get(OfflinePageResponseFactoryInterface::class)->create()->getContent();
    }
}
