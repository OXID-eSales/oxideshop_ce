<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReadyEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http\ShopExceptionResponseListener;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[RunTestsInSeparateProcesses]
final class ShopExceptionResponseListenerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->activateThemeIfNoneActive();
        $this->seedKeyedActiveView();

        $_GET['searchparam'] = '';
        $_GET['page'] = '';
        $_GET['tpl'] = '';
    }

    private function seedKeyedActiveView(): void
    {
        $view = oxNew(FrontendController::class);
        $view->setClassKey('oxubase');
        Registry::getConfig()->setActiveView($view);
    }

    private function activateThemeIfNoneActive(): void
    {
        $shopAdapter = $this->get(ShopAdapterInterface::class);
        if ($shopAdapter->getActiveThemeId() === '') {
            $shopAdapter->activateTheme(getenv('THEME_ID') ?: 'apex');
        }
    }

    public function testRedirectsToShopHomeOnSystemComponentException(): void
    {
        $event = $this->createExceptionEvent(new SystemComponentException());

        $this->createListener(loggedErrors: 0, debugMode: false)->onKernelException($event);

        $this->assertTrue($event->isAllowingCustomResponseCode());
        $this->assertSame(Response::HTTP_FOUND, $event->getResponse()->getStatusCode());
        $this->assertStringContainsString('cl=start', (string) $event->getResponse()->headers->get('Location'));
    }

    public function testAnswersWithInternalErrorOnSystemComponentExceptionDuringCyclicRedirect(): void
    {
        $_GET['redirected'] = '1';
        $event = $this->createExceptionEvent(new SystemComponentException());

        $this->createListener(loggedErrors: 0, debugMode: false)->onKernelException($event);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode());
        $this->assertSame('', $event->getResponse()->getContent());
    }

    public function testRendersExceptionErrorPageOnSystemComponentExceptionInDebugMode(): void
    {
        $exception = new SystemComponentException();
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())
            ->method('addErrorToDisplay')
            ->with($exception);
        Registry::set(UtilsView::class, $utilsView);
        $event = $this->createExceptionEvent($exception);

        $this->createListener(loggedErrors: 0, debugMode: true)->onKernelException($event);

        $this->assertSame(Response::HTTP_OK, $event->getResponse()->getStatusCode());
        $this->assertNotSame('', $event->getResponse()->getContent());
    }

    public function testAnswersWithPageNotFoundOnRoutingException(): void
    {
        $_GET['fnc'] = 'nonPublicMethod';
        $_POST['fnc'] = 'nonPublicMethod';
        $event = $this->createExceptionEvent(new RoutingException());

        $this->createListener(loggedErrors: 0, debugMode: false)->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertNotSame('', $event->getResponse()->getContent());
        $this->assertArrayNotHasKey('fnc', $_GET);
        $this->assertArrayNotHasKey('fnc', $_POST);
    }

    public function testAnswersWithEmptyPageNotFoundWhenErrorPageRenderingIsSkipped(): void
    {
        $utils = $this->createStub(Utils::class);
        Registry::set(Utils::class, $utils);
        $event = $this->createExceptionEvent(new RoutingException());

        $this->createListener(loggedErrors: 0, debugMode: false)->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertSame('', $event->getResponse()->getContent());
    }

    public function testAnswersWithSignaledResponseWhenExceptionErrorPageSignalsOne(): void
    {
        $signaledResponse = new Response('signaled');
        $utilsView = $this->createStub(UtilsView::class);
        $utilsView->method('addErrorToDisplay')
            ->willThrowException(new ResponseReady($signaledResponse));
        Registry::set(UtilsView::class, $utilsView);
        $event = $this->createExceptionEvent(new StandardException());

        $this->createListener(loggedErrors: 0, debugMode: true)->onKernelException($event);

        $this->assertSame($signaledResponse, $event->getResponse());
    }

    public function testRendersRequestedPageWithErrorOnStandardException(): void
    {
        $event = $this->createExceptionEvent(new StandardException());

        $this->createListener(loggedErrors: 0, debugMode: false)->onKernelException($event);

        $this->assertSame(Response::HTTP_OK, $event->getResponse()->getStatusCode());
        $this->assertNotSame('', $event->getResponse()->getContent());
    }

    public function testRendersExceptionErrorPageOnStandardExceptionInDebugMode(): void
    {
        $event = $this->createExceptionEvent(new StandardException());

        $this->createListener(loggedErrors: 0, debugMode: true)->onKernelException($event);

        $this->assertSame(Response::HTTP_OK, $event->getResponse()->getStatusCode());
        $this->assertNotSame('', $event->getResponse()->getContent());
    }

    public function testIgnoresOtherThrowables(): void
    {
        $event = $this->createExceptionEvent(new RuntimeException());

        $this->createListener(loggedErrors: 0, debugMode: false)->onKernelException($event);

        $this->assertNull($event->getResponse());
        $this->assertFalse($event->isAllowingCustomResponseCode());
    }

    public function testAnswersWithInternalErrorWhenExceptionErrorPageFails(): void
    {
        $utilsView = $this->createStub(UtilsView::class);
        $utilsView->method('addErrorToDisplay')
            ->willThrowException(new RuntimeException());
        Registry::set(UtilsView::class, $utilsView);
        $event = $this->createExceptionEvent(new StandardException());

        $this->createListener(loggedErrors: 1, debugMode: true)->onKernelException($event);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode());
        $this->assertSame('', $event->getResponse()->getContent());
    }

    public function testDispatchedResponseReadyEventStopsRequest(): void
    {
        $response = new Response('stopped');

        try {
            $this->get(EventDispatcherInterface::class)->dispatch(new ResponseReadyEvent($response));
            $this->fail('Request was not stopped');
        } catch (ResponseReady $signal) {
            $this->assertSame($response, $signal->getResponse());
        }
    }

    public function testKernelConvertsShopExceptionsToResponses(): void
    {
        $this->setParameter('oxid_esales.debug_mode', false);
        $request = new Request();
        $request->attributes->set('_controller', static function (): Response {
            throw new SystemComponentException();
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringContainsString('cl=start', (string) $response->headers->get('Location'));
    }

    private function createListener(int $loggedErrors, bool $debugMode): ShopExceptionResponseListener
    {
        return new ShopExceptionResponseListener($this->createLogger($loggedErrors), $debugMode);
    }

    private function createLogger(int $expectedErrors): LoggerInterface&MockObject
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly($expectedErrors))
            ->method('error');

        return $logger;
    }

    private function createExceptionEvent(\Throwable $throwable): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $throwable
        );
    }
}
