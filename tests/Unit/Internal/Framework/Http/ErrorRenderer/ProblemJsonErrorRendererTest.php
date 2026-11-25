<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ProblemJsonErrorRenderer;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

final class ProblemJsonErrorRendererTest extends TestCase
{
    public function testRendersProblemWithStatusForJsonRoutes(): void
    {
        $exception = $this->render(new NotFoundHttpException(), $this->jsonRouteRequest());

        $this->assertSame(404, json_decode($exception->getAsString(), true)['status']);
    }

    public function testHidesTheMessageOfHttpExceptionsBehindStatusText(): void
    {
        $exception = $this->render(new NotFoundHttpException('internal detail'), $this->jsonRouteRequest());

        $this->assertSame('Not Found', json_decode($exception->getAsString(), true)['detail']);
    }

    public function testAnswersWithJsonContentType(): void
    {
        $exception = $this->render(new NotFoundHttpException(), $this->jsonRouteRequest());

        $this->assertSame('application/json', $exception->getHeaders()['Content-Type']);
    }

    public function testRendersProblemForClientsAcceptingJson(): void
    {
        $request = Request::create('/page', server: ['HTTP_ACCEPT' => 'application/json']);

        $exception = $this->render(new NotFoundHttpException(), $request);

        $this->assertSame('Not Found', json_decode($exception->getAsString(), true)['detail']);
    }

    public function testCarriesTheHeadersOfHttpExceptions(): void
    {
        $exception = $this->render(new TooManyRequestsHttpException(45), $this->jsonRouteRequest());

        $this->assertSame(429, $exception->getStatusCode());
        $this->assertSame(45, $exception->getHeaders()['Retry-After']);
    }

    public function testHidesTheMessageOfOtherThrowables(): void
    {
        $exception = $this->render(new RuntimeException('database password leaked'), $this->jsonRouteRequest());

        $problem = json_decode($exception->getAsString(), true);
        $this->assertSame(500, $problem['status']);
        $this->assertSame('Internal Server Error', $problem['detail']);
        $this->assertArrayNotHasKey('trace', $problem);
    }

    public function testAddsExceptionDetailsInDebugMode(): void
    {
        $exception = $this->render(new RuntimeException('boom'), $this->jsonRouteRequest(), debug: true);

        $problem = json_decode($exception->getAsString(), true);
        $this->assertSame('boom', $problem['detail']);
        $this->assertSame(RuntimeException::class, $problem['class']);
        $this->assertNotEmpty($problem['trace']);
    }

    public function testSubstitutesInvalidCharactersInDebugMode(): void
    {
        $exception = $this->render(new RuntimeException("\xB1boom"), $this->jsonRouteRequest(), debug: true);

        $this->assertSame("\u{FFFD}boom", json_decode($exception->getAsString(), true)['detail']);
    }

    public function testRendersOtherFormatsWithTheHtmlRenderer(): void
    {
        $exception = $this->render(new NotFoundHttpException(), Request::create('/page'));

        $this->assertSame('html-page', $exception->getAsString());
    }

    public function testRendersWithTheHtmlRendererWithoutRequest(): void
    {
        $renderer = new ProblemJsonErrorRenderer(new RequestStack(), $this->htmlRenderer(), debug: false);

        $exception = $renderer->render(new NotFoundHttpException());

        $this->assertSame('html-page', $exception->getAsString());
    }

    private function render(Throwable $throwable, Request $request, bool $debug = false): FlattenException
    {
        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new ProblemJsonErrorRenderer($requestStack, $this->htmlRenderer(), $debug)->render($throwable);
    }

    private function htmlRenderer(): ErrorRendererInterface
    {
        $renderer = $this->createStub(ErrorRendererInterface::class);
        $renderer->method('render')->willReturn(new FlattenException()->setAsString('html-page'));

        return $renderer;
    }

    private function jsonRouteRequest(): Request
    {
        $request = Request::create('/api/products');
        $request->attributes->set('_format', 'json');

        return $request;
    }
}
