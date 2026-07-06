<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ShopExceptionResponseListener implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private bool $debugMode
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -5]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if (!$throwable instanceof StandardException) {
            return;
        }

        $event->allowCustomResponseCode();
        $event->setResponse($this->buildResponseFor($throwable));
    }

    private function buildResponseFor(StandardException $exception): Response
    {
        try {
            return $this->respondTo($exception);
        } catch (ResponseReady $responseReady) {
            return $responseReady->getResponse();
        } catch (\Throwable $failure) {
            $this->logger->error($failure->getMessage(), ['exception' => $failure]);

            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function respondTo(StandardException $exception): Response
    {
        if ($exception instanceof RoutingException) {
            unset($_GET['fnc'], $_POST['fnc']);
            error_404_handler($_SERVER['REQUEST_URI'] ?? '');

            return new Response('', Response::HTTP_NOT_FOUND);
        }

        if ($this->debugMode) {
            return $this->renderPageWithError($exception, 'exceptionError', 'displayExceptionError');
        }

        if ($exception instanceof SystemComponentException) {
            Registry::getUtils()->redirect(Registry::getConfig()->getShopHomeUrl() . 'cl=start');

            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->renderPageWithError($exception, null, '');
    }

    private function renderPageWithError(StandardException $exception, ?string $controllerKey, string $function): Response
    {
        Registry::getUtilsView()->addErrorToDisplay($exception);

        return oxNew(ShopControl::class)->buildResponse($controllerKey, $function);
    }
}
