<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\NotFoundListener;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class NotFoundListenerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $context = $this->get(BasicContextInterface::class);
        $this->get(ThemeConfigurationInstallerInterface::class)
            ->install(Path::join($context->getVendorPath(), 'oxid-esales', 'apex-theme'));
        $this->get(ThemeActivationServiceInterface::class)->activate('apex', $context->getDefaultShopId());
    }

    public function testDeliversRenderedPageWithNotFoundStatus(): void
    {
        $event = $this->createExceptionEvent();
        $this->useTemplateRenderer($this->rendererReturning('<html>404</html>'));

        new NotFoundListener()->onKernelException($event);

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
        $this->useTemplateRenderer($renderer);
        ContainerFacade::get(Request::class)->server->set('REQUEST_URI', '/retired-url/?a=1');

        new NotFoundListener()->onKernelException($this->createExceptionEvent());

        $this->assertSame(
            [
                'template' => 'message/err_404',
                'sUrl' => '/retired-url/?a=1',
                'view' => 'notfound',
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
        $this->useTemplateRenderer($renderer);

        new NotFoundListener()->onKernelException($event);

        $this->assertSame(301, $event->getResponse()->getStatusCode());
        $this->assertSame('https://shop.example/target', $event->getResponse()->headers->get('Location'));
    }

    public function testFallsBackToTheExceptionPageWhenTheTemplateFails(): void
    {
        $event = $this->createExceptionEvent();
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willReturnCallback(
            static function (string $template): string {
                if ($template === 'message/err_404') {
                    throw new \RuntimeException();
                }

                return 'fallback';
            }
        );
        $this->useTemplateRenderer($renderer);

        new NotFoundListener()->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertSame('fallback', $event->getResponse()->getContent());
    }

    public function testRendersTheThemedNotFoundPage(): void
    {
        $this->skipWithoutTemplateEngine($this->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer());
        ContainerFacade::get(Request::class)->server->set('REQUEST_URI', '/"><script>alert(1)</script>');
        $event = $this->createExceptionEvent();

        new NotFoundListener()->onKernelException($event);

        $content = $event->getResponse()->getContent();
        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertStringContainsString('err-404', $content);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $content);
    }

    public function testKernelRendersTheThemedPageForHtmlClients(): void
    {
        $this->skipWithoutTemplateEngine($this->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer());
        $request = Request::create('/retired-url/');
        $request->attributes->set('_controller', static function (): Response {
            throw new RoutingException();
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsString('err-404', $response->getContent());
    }

    private function skipWithoutTemplateEngine(TemplateRendererInterface $renderer): void
    {
        if ($renderer->renderTemplate('message/err_404', []) === 'message/err_404.html.twig') {
            $this->markTestSkipped('No template engine is registered for this shop configuration.');
        }
    }

    private function useTemplateRenderer(TemplateRendererInterface $renderer): void
    {
        $bridge = $this->createStub(TemplateRendererBridgeInterface::class);
        $bridge->method('getTemplateRenderer')->willReturn($renderer);
        $this->createContainer();
        $this->replaceService(TemplateRendererBridgeInterface::class, $bridge);
        $this->replaceContainerInstance();
    }

    private function rendererReturning(string $output): TemplateRendererInterface
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willReturn($output);

        return $renderer;
    }

    private function createExceptionEvent(): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/'),
            HttpKernelInterface::MAIN_REQUEST,
            new RoutingException()
        );
    }
}
