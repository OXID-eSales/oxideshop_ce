<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ShopHtmlErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class ShopHtmlErrorRendererTest extends TestCase
{
    public function testDelegatesNotFoundToTheExceptionPageInDebugMode(): void
    {
        $exception = $this->createRenderer(debug: true)->render(new NotFoundHttpException());

        $this->assertSame('symfony-page', $exception->getAsString());
        $this->assertSame(404, $exception->getStatusCode());
    }

    public function testDelegatesServerErrorsToTheExceptionPageInDebugMode(): void
    {
        $exception = $this->createRenderer(debug: true)->render(new \RuntimeException());

        $this->assertSame('symfony-page', $exception->getAsString());
    }

    public function testDelegatesClientErrorsOtherThanNotFoundToTheGenericPage(): void
    {
        $exception = $this->createRenderer()->render(new TooManyRequestsHttpException(45));

        $this->assertSame('symfony-page', $exception->getAsString());
        $this->assertSame(429, $exception->getStatusCode());
        $this->assertSame(45, $exception->getHeaders()['Retry-After']);
    }

    public function testRendersTheOfflinePageForServerErrors(): void
    {
        $exception = $this->createRenderer()->render(new \RuntimeException());

        $this->assertSame(500, $exception->getStatusCode());
        $this->assertSame((string) new OfflinePageResponse()->getContent(), $exception->getAsString());
        $this->assertSame('text/html', $exception->getHeaders()['Content-Type']);
    }

    public function testKeepsStatusAndHeadersOfServerErrors(): void
    {
        $exception = $this->createRenderer()->render(new ServiceUnavailableHttpException(30));

        $this->assertSame(503, $exception->getStatusCode());
        $this->assertSame(30, $exception->getHeaders()['Retry-After']);
        $this->assertSame((string) new OfflinePageResponse()->getContent(), $exception->getAsString());
    }

    private function createRenderer(bool $debug = false): ShopHtmlErrorRenderer
    {
        $symfonyRenderer = $this->createStub(ErrorRendererInterface::class);
        $symfonyRenderer->method('render')->willReturnCallback(
            static fn(\Throwable $throwable): FlattenException => FlattenException::createFromThrowable($throwable)
                ->setAsString('symfony-page')
        );

        return new ShopHtmlErrorRenderer($symfonyRenderer, $debug);
    }
}
