<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ShopHtmlErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponseFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class ShopHtmlErrorRendererTest extends TestCase
{
    private const OFFLINE_PAGE = '<html>offline</html>';

    public static function symfonyPageDataProvider(): array
    {
        return [
            'not found in debug mode' => [true, false, NotFoundHttpException::class],
            'server error in debug mode' => [true, false, RuntimeException::class],
            'not found in admin' => [false, true, NotFoundHttpException::class],
            'server error in admin' => [false, true, RuntimeException::class],
        ];
    }

    #[DataProvider('symfonyPageDataProvider')]
    public function testDelegatesToSymfonyPageInDebugModeOrAdmin(bool $debug, bool $isAdmin, string $errorClass): void
    {
        $exception = $this->createRenderer($debug, $isAdmin)->render(new $errorClass());

        $this->assertSame('symfony-page', $exception->getAsString());
    }

    public function testDelegatesClientErrorsOtherThanNotFoundToTheGenericPage(): void
    {
        $exception = $this->createRenderer()->render(new TooManyRequestsHttpException(45));

        $this->assertSame('symfony-page', $exception->getAsString());
    }

    public function testRendersTheOfflinePageForServerErrors(): void
    {
        $exception = $this->createRenderer()->render(new RuntimeException());

        $this->assertSame(500, $exception->getStatusCode());
        $this->assertSame(self::OFFLINE_PAGE, $exception->getAsString());
        $this->assertSame('text/html', $exception->getHeaders()['Content-Type']);
    }

    public function testKeepsStatusAndHeadersOfServerErrors(): void
    {
        $exception = $this->createRenderer()->render(new ServiceUnavailableHttpException(30));

        $this->assertSame(503, $exception->getStatusCode());
        $this->assertSame(30, $exception->getHeaders()['Retry-After']);
        $this->assertSame(self::OFFLINE_PAGE, $exception->getAsString());
    }

    public function testRendersOfflinePageForOfflineShop(): void
    {
        $exception = $this->createRenderer()->render(new ShopOfflineException());

        $this->assertSame(503, $exception->getStatusCode());
        $this->assertSame(self::OFFLINE_PAGE, $exception->getAsString());
    }

    private function createRenderer(bool $debug = false, bool $isAdmin = false): ShopHtmlErrorRenderer
    {
        $symfonyRenderer = $this->createStub(ErrorRendererInterface::class);
        $symfonyRenderer->method('render')->willReturn(new FlattenException()->setAsString('symfony-page'));
        $context = $this->createStub(ContextInterface::class);
        $context->method('isAdmin')->willReturn($isAdmin);
        $offlinePageResponseFactory = $this->createStub(OfflinePageResponseFactoryInterface::class);
        $offlinePageResponseFactory->method('create')->willReturn(new Response(self::OFFLINE_PAGE, 500));

        return new ShopHtmlErrorRenderer(
            $symfonyRenderer,
            $debug,
            $context,
            $offlinePageResponseFactory,
            new NullLogger()
        );
    }
}
