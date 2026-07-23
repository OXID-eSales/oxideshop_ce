<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\NotFoundListener;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class NotFoundListenerTest extends IntegrationTestCase
{
    public function testDeliversRenderedPageWithNotFoundStatus(): void
    {
        $event = $this->createExceptionEvent();

        new NotFoundListener($this->rendererReturning('<html>404</html>'))->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertSame('<html>404</html>', $event->getResponse()->getContent());
        $this->assertTrue($event->isPropagationStopped());
        $this->assertTrue($event->isAllowingCustomResponseCode());
    }

    public function testRendersTheNotFoundTemplateWithTheRequestedUri(): void
    {
        $rendered = [];
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willReturnCallback(
            function (string $template, array $viewData) use (&$rendered): string {
                $rendered = [
                    'template' => $template,
                    'sUrl' => $viewData['sUrl'],
                    'view' => $viewData['oView']->getClassKey(),
                    'hasViewConfig' => isset($viewData['oViewConf']),
                ];

                return 'page';
            }
        );

        new NotFoundListener($renderer)->onKernelException($this->createExceptionEvent('/retired-url/?a=1'));

        $this->assertSame(
            [
                'template' => 'message/err_404',
                'sUrl' => '/retired-url/?a=1',
                'view' => 'oxubase',
                'hasViewConfig' => true,
            ],
            $rendered
        );
    }

    public function testDeliversRedirectWhenTheViewRequestsOne(): void
    {
        $event = $this->createExceptionEvent();
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')
            ->willThrowException(new RedirectException('https://shop.example/target', 301));

        new NotFoundListener($renderer)->onKernelException($event);

        $this->assertSame(301, $event->getResponse()->getStatusCode());
        $this->assertSame('https://shop.example/target', $event->getResponse()->headers->get('Location'));
    }

    public function testDoesNotConvertOtherRenderFailuresIntoNotFound(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willThrowException(new \RuntimeException());

        $this->expectException(\RuntimeException::class);

        new NotFoundListener($renderer)->onKernelException($this->createExceptionEvent());
    }

    public function testRendersTheThemedNotFoundPage(): void
    {
        $renderer = $this->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
        $this->skipWithoutTemplateEngine($renderer);
        $event = $this->createExceptionEvent('/"><script>alert(1)</script>');

        new NotFoundListener($renderer)->onKernelException($event);

        $content = $event->getResponse()->getContent();
        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertStringContainsString('err-404', $content);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $content);
    }

    private function skipWithoutTemplateEngine(TemplateRendererInterface $renderer): void
    {
        if ($renderer->renderTemplate('message/err_404', []) === 'message/err_404.html.twig') {
            $this->markTestSkipped('No template engine is registered for this shop configuration.');
        }
    }

    private function rendererReturning(string $output): TemplateRendererInterface
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willReturn($output);

        return $renderer;
    }

    private function createExceptionEvent(string $uri = '/'): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create($uri),
            HttpKernelInterface::MAIN_REQUEST,
            new RoutingException()
        );
    }
}
