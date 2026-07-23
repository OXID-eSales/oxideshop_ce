<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ShopControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        Registry::set(Config::class, null);

        parent::tearDown();
    }

    public function testBuildResponsePropagatesShopExceptions(): void
    {
        $config = $this->createMock(Config::class);
        $config->expects($this->once())
            ->method('init')
            ->willThrowException(new SystemComponentException());
        Registry::set(Config::class, $config);

        $this->expectException(SystemComponentException::class);

        new ShopControl()->buildResponse();
    }

    public function testRenderRethrowsShopExceptionWrappedByTemplateEngine(): void
    {
        $this->useTemplateRendererThrowing(new \Exception('', 0, new StandardException()));

        $this->expectException(StandardException::class);

        $this->renderView();
    }

    public function testRenderRethrowsRedirectExceptionWrappedByTemplateEngine(): void
    {
        $signal = new RedirectException('https://shop.example/target');
        $this->useTemplateRendererThrowing(new \Exception('', 0, $signal));

        try {
            $this->renderView();
            $this->fail('RedirectException was not rethrown');
        } catch (RedirectException $rethrown) {
            $this->assertSame($signal, $rethrown);
        }
    }

    private function renderView(): void
    {
        $view = $this->createStub(FrontendController::class);
        $view->method('render')->willReturn('page');
        $view->method('getViewData')->willReturn([]);
        $view->method('getClassKey')->willReturn('start');
        $view->method('getViewId')->willReturn('viewId');

        $shopControl = new class extends ShopControl {
            public function renderView($view): string
            {
                return $this->render($view);
            }
        };
        $shopControl->renderView($view);
    }

    private function useTemplateRendererThrowing(\Throwable $throwable): void
    {
        $renderer = $this->createStub(TemplateRendererInterface::class);
        $renderer->method('renderTemplate')->willThrowException($throwable);
        $bridge = $this->createStub(TemplateRendererBridgeInterface::class);
        $bridge->method('getTemplateRenderer')->willReturn($renderer);
        $this->createContainer();
        $this->replaceService(TemplateRendererBridgeInterface::class, $bridge);
        $this->replaceContainerInstance();
    }
}
