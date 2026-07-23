<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ShopHtmlErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ShopHtmlErrorRendererTest extends IntegrationTestCase
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

    public function testRendersTheNotFoundPageWithNotFoundStatus(): void
    {
        $this->useTemplateRenderer($this->rendererReturning('<html>404</html>'));

        $exception = $this->createRenderer()->render(new NotFoundHttpException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        $this->assertSame('<html>404</html>', $exception->getAsString());
        $this->assertSame('text/html', $exception->getHeaders()['Content-Type']);
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

        $this->createRenderer()->render(new NotFoundHttpException());

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

    public function testRedirectsWhenTheNotFoundPageRequestsIt(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')
            ->willThrowException(new RedirectException('https://shop.example/target', 301));
        $this->useTemplateRenderer($renderer);

        $exception = $this->createRenderer()->render(new NotFoundHttpException());

        $this->assertSame(301, $exception->getStatusCode());
        $this->assertSame('https://shop.example/target', $exception->getHeaders()['Location']);
        $this->assertSame('', $exception->getAsString());
    }

    public function testFallsBackToTheExceptionPageWhenTheTemplateFails(): void
    {
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

        $exception = $this->createRenderer()->render(new NotFoundHttpException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        $this->assertSame('fallback', $exception->getAsString());
    }

    public function testRendersTheThemedNotFoundPage(): void
    {
        $this->skipWithoutTemplateEngine($this->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer());
        ContainerFacade::get(Request::class)->server->set('REQUEST_URI', '/"><script>alert(1)</script>');

        $exception = $this->createRenderer()->render(new NotFoundHttpException());

        $this->assertSame(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        $this->assertStringContainsString('err-404', $exception->getAsString());
        $this->assertStringNotContainsString('<script>alert(1)</script>', $exception->getAsString());
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

    private function createRenderer(): ShopHtmlErrorRenderer
    {
        return new ShopHtmlErrorRenderer(new HtmlErrorRenderer(), false);
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
}
