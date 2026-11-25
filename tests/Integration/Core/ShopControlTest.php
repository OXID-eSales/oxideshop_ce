<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use Exception;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\ViewRenderedEvent;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Core\Fixtures\FailingTemplateRenderer;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use TypeError;

final class ShopControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        unset($_GET['renderPartial']);
        Registry::set(Config::class, null);

        parent::tearDown();
    }

    public function testHandleRequestPropagatesShopExceptions(): void
    {
        $config = $this->createMock(Config::class);
        $config->expects($this->once())
            ->method('init')
            ->willThrowException(new SystemComponentException());
        Registry::set(Config::class, $config);

        $this->expectException(SystemComponentException::class);

        new ShopControl()->handleRequest();
    }

    public function testBuildResponseRejectsActionLeadingToUnknownController(): void
    {
        $this->expectException(RoutingException::class);

        new ShopControl()->buildResponse('start', 'getTitle');
    }

    public function testBuildResponseRejectsControllerWithoutView(): void
    {
        $this->expectException(RoutingException::class);

        new ShopControl()->buildResponse('article_pictures_ajax', '');
    }

    public function testBuildResponseRejectsViewWithoutTemplate(): void
    {
        $this->expectException(RoutingException::class);

        new ShopControl()->buildResponse('oxubase', '');
    }

    public function testBuildResponseDeliversTheResponseReturnedByRender(): void
    {
        $this->useResponseControllerFixture();

        $response = new ShopControl()->buildResponse('shop_control_test', '');

        $this->assertSame('rendered', $response->getContent());
    }

    public function testBuildResponseNotifiesAboutRenderedViewReturningResponse(): void
    {
        $this->useResponseControllerFixture();
        $renderedViews = 0;
        $this->get(EventDispatcherInterface::class)->addListener(
            ViewRenderedEvent::class,
            static function () use (&$renderedViews): void {
                $renderedViews++;
            }
        );

        new ShopControl()->buildResponse('shop_control_test', '');

        $this->assertSame(1, $renderedViews);
    }

    public function testBuildResponseDeliversActionResponseWithoutRendering(): void
    {
        $this->useResponseControllerFixture();

        $response = new ShopControl()->buildResponse('shop_control_test', 'download');

        $this->assertSame('downloaded', $response->getContent());
    }

    public function testBuildResponseWithRenderPartialReturnsContentAndErrorsAsJson(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willReturn('partial content');
        $this->useTemplateRenderer($renderer);
        Registry::getUtilsView()->addErrorToDisplay('Partial error');
        $_GET['renderPartial'] = '1';

        $response = new ShopControl()->buildResponse('start', '');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(
            ['errors' => ['default' => ['Partial error']], 'content' => 'partial content'],
            json_decode((string) $response->getContent(), true)
        );
    }

    public static function templateErrorDataProvider(): array
    {
        return [
            'shop exception' => [StandardException::class],
            'error' => [TypeError::class],
        ];
    }

    #[DataProvider('templateErrorDataProvider')]
    public function testBuildResponseShowsExceptionPageForTemplateErrors(string $errorClass): void
    {
        $this->useTemplateRenderer(new FailingTemplateRenderer(new $errorClass()));

        $response = new ShopControl()->buildResponse('start', '');

        $this->assertSame('exception page', $response->getContent());
    }

    public function testBuildResponseRethrowsRedirectExceptionWrappedByTemplateEngine(): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willThrowException(
            new Exception('', 0, new RedirectException('https://shop.example/target'))
        );
        $this->useTemplateRenderer($renderer);

        $this->expectException(RedirectException::class);

        new ShopControl()->buildResponse('start', '');
    }

    private function useResponseControllerFixture(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();
        $this->replaceContainerInstance();
    }

    private function useTemplateRenderer(TemplateRendererInterface $renderer): void
    {
        $bridge = $this->createStub(TemplateRendererBridgeInterface::class);
        $bridge->method('getTemplateRenderer')->willReturn($renderer);
        $this->createContainer();
        $this->replaceService(TemplateRendererBridgeInterface::class, $bridge);
        $this->replaceContainerInstance();
    }
}
