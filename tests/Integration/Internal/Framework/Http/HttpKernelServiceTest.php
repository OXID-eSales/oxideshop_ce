<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
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

        $this->assertSame('https://shop.example/target', $response->headers->get('Location'));
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testAnswersRoutingExceptionWithJsonProblemWithoutBuildingTheTemplateRenderer(): void
    {
        $response = $this->handleJsonRequestThrowing(new RoutingException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame($this->problem(404, 'Not Found'), json_decode($response->getContent(), true));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertFalse($this->container->initialized(TemplateRendererInterface::class));
    }

    public function testAnswersWrongMethodWithJsonProblemAndAllowedMethods(): void
    {
        $response = $this->handleJsonRequestThrowing(new MethodNotAllowedHttpException(['GET']));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
        $this->assertSame($this->problem(405, 'Method Not Allowed'), json_decode($response->getContent(), true));
    }

    public function testAnswersServerErrorsOfHtmlClientsWithTheOfflinePage(): void
    {
        $request = $this->requestWithController(static function (): Response {
            throw new \RuntimeException('boom');
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame((string) new OfflinePageResponse()->getContent(), $response->getContent());
        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
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

    public function testLogsExceptionsByTheirMappedLevel(): void
    {
        $logger = $this->useLoggerSpy();

        foreach ($this->loggedThrowables() as $throwable) {
            $this->handleJsonRequestThrowing($throwable);
        }

        $this->assertSame(
            [
                [LogLevel::INFO, NotFoundHttpException::class, RoutingException::class],
                [LogLevel::INFO, NotFoundHttpException::class, null],
                [LogLevel::INFO, ShopOfflineException::class, null],
                [LogLevel::INFO, TooManyRequestsHttpException::class, null],
                [LogLevel::ERROR, MethodNotAllowedHttpException::class, null],
                [LogLevel::CRITICAL, \RuntimeException::class, null],
            ],
            $logger->records
        );
    }

    public function testDoesNotLogRedirects(): void
    {
        $logger = $this->useLoggerSpy();

        $this->handleJsonRequestThrowing(new RedirectException('https://shop.example/target'));

        $this->assertSame([], $logger->records);
    }

    public function testAddsExceptionDetailsToJsonProblemsInDebugMode(): void
    {
        $this->setParameter('oxid_esales.debug_mode', true);

        $response = $this->handleJsonRequestThrowing(new \RuntimeException('boom'));

        $problem = json_decode($response->getContent(), true);
        $this->assertSame('boom', $problem['detail']);
        $this->assertSame(\RuntimeException::class, $problem['class']);
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

    /**
     * @return \Throwable[]
     */
    private function loggedThrowables(): array
    {
        return [
            new RoutingException(),
            new NotFoundHttpException(),
            new ShopOfflineException(),
            new TooManyRequestsHttpException(45),
            new MethodNotAllowedHttpException(['GET']),
            new \RuntimeException(),
        ];
    }

    private function handleJsonRequestThrowing(\Throwable $throwable): Response
    {
        $request = Request::create('/nothing', 'GET', server: ['HTTP_ACCEPT' => 'application/json']);
        $request->attributes->set('_controller', static function () use ($throwable): Response {
            throw $throwable;
        });

        return $this->get(HttpKernelInterface::class)->handle($request);
    }

    private function useLoggerSpy(): AbstractLogger
    {
        $logger = new class extends AbstractLogger {
            public array $records = [];

            public function log($level, $message, array $context = []): void
            {
                $previous = $context['exception']->getPrevious();
                $this->records[] = [$level, $context['exception']::class, $previous ? $previous::class : null];
            }
        };
        $this->createContainer();
        $this->replaceService(LoggerInterface::class, $logger);
        $this->replaceContainerInstance();

        return $logger;
    }

    private function problem(int $status, string $detail): array
    {
        return [
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => 'An error occurred',
            'status' => $status,
            'detail' => $detail,
        ];
    }

    private function requestWithController(callable $controller): Request
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);

        return $request;
    }
}
