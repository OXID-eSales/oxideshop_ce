<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Application\Controller\NotFoundController;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class NotFoundControllerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testRenderPassesRequestedUriToNotFoundTemplate(): void
    {
        $requestStack = new RequestStack([Request::create('/'), Request::create('/retired-url/?a=1')]);
        $controller = new NotFoundController($requestStack);

        $template = $controller->render();

        $this->assertSame('message/err_404', $template);
        $this->assertSame('/retired-url/?a=1', $controller->getViewData()['sUrl']);
    }

    public function testInitRejectsDirectRequest(): void
    {
        $controller = new NotFoundController(new RequestStack([Request::create('/index.php?cl=notfound')]));

        $this->expectException(RoutingException::class);

        $controller->init();
    }

    public function testKernelRespondsNotFoundForDirectRequest(): void
    {
        $this->activateApexTheme();
        $this->setParameter('oxid_esales.debug_mode', false);
        $request = Request::create('/index.php?cl=notfound');
        $request->attributes->set(
            '_controller',
            static fn(): Response => oxNew(ShopControl::class)->buildResponse('notfound', '')
        );

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsString('err-404', $response->getContent());
    }

    public function testKernelRespondsNotFoundWithoutRedirectingToSeoUrlOfRequestedPage(): void
    {
        $this->activateApexTheme();
        $this->setParameter('oxid_esales.debug_mode', false);
        $_SERVER['REQUEST_URI'] = '/api.php?cl=start';
        $request = Request::create('/api.php?cl=start');
        $request->attributes->set(
            '_controller',
            static fn(): Response => throw new RoutingException('Route not found')
        );

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    private function activateApexTheme(): void
    {
        $context = $this->get(BasicContextInterface::class);
        $this->get(ThemeConfigurationInstallerInterface::class)
            ->install(Path::join($context->getVendorPath(), 'oxid-esales', 'apex-theme'));
        $this->get(ThemeActivationServiceInterface::class)->activate('apex', $context->getDefaultShopId());
    }
}
