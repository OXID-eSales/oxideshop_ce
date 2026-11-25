<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ShopHtmlErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponseFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShopHtmlErrorRendererTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testRendersTheNotFoundPageWithNotFoundStatus(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willReturn('<html>404</html>');
        $this->useTemplateRenderer($renderer);
        $this->pushErrorSubRequest(Request::create('/missing/'));

        $exception = $this->createRenderer(new NullLogger())->render(new NotFoundHttpException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        $this->assertSame('<html>404</html>', $exception->getAsString());
    }

    public function testRedirectsWhenTheNotFoundPageRequestsIt(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')
            ->willThrowException(new RedirectException('https://shop.example/target', 301));
        $this->useTemplateRenderer($renderer);
        $this->pushErrorSubRequest(Request::create('/missing/'));

        $exception = $this->createRenderer(new NullLogger())->render(new NotFoundHttpException());

        $this->assertSame(301, $exception->getStatusCode());
        $this->assertSame('https://shop.example/target', $exception->getHeaders()['Location']);
    }

    public function testFallsBackToSymfonyNotFoundPageWhenShopCannotRenderIt(): void
    {
        $this->useTemplateRenderer($this->failingRenderer());
        $this->pushErrorSubRequest(Request::create('/missing/'));

        $exception = $this->createRenderer(new NullLogger())->render(new NotFoundHttpException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        $this->assertStringContainsString('404 Not Found', $exception->getAsString());
    }

    public function testLogsWhenShopCannotRenderTheNotFoundPage(): void
    {
        $this->useTemplateRenderer($this->failingRenderer());
        $this->pushErrorSubRequest(Request::create('/missing/'));
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $this->createRenderer($logger)->render(new NotFoundHttpException());
    }

    public function testRendersTheOfflinePageWhenTheShopGoesOfflineWhileRenderingTheNotFoundPage(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willThrowException(new ShopOfflineException());
        $this->useTemplateRenderer($renderer);
        $this->pushErrorSubRequest(Request::create('/missing/'));

        $exception = $this->createRenderer(new NullLogger())->render(new NotFoundHttpException());

        $this->assertSame(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getStatusCode());
        $this->assertSame($this->getOfflinePage(), $exception->getAsString());
    }

    private function createRenderer(LoggerInterface $logger): ShopHtmlErrorRenderer
    {
        return new ShopHtmlErrorRenderer(
            new HtmlErrorRenderer(),
            false,
            $this->get(ContextInterface::class),
            $this->get(OfflinePageResponseFactoryInterface::class),
            $logger
        );
    }

    private function useTemplateRenderer(TemplateRendererInterface $renderer): void
    {
        $bridge = $this->createStub(TemplateRendererBridgeInterface::class);
        $bridge->method('getTemplateRenderer')->willReturn($renderer);
        $this->createContainer();
        $this->replaceService(TemplateRendererBridgeInterface::class, $bridge);
        $this->replaceContainerInstance();
    }

    private function pushErrorSubRequest(Request $errorRequest): void
    {
        $requestStack = $this->get(RequestStack::class);
        $requestStack->push(Request::create('/'));
        $requestStack->push($errorRequest);
    }

    private function failingRenderer(): TemplateRendererInterface
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willThrowException(new RuntimeException());

        return $renderer;
    }

    private function getOfflinePage(): string
    {
        return (string) $this->get(OfflinePageResponseFactoryInterface::class)->create()->getContent();
    }
}
