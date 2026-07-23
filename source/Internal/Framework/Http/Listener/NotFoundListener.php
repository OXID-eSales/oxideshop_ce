<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class NotFoundListener implements EventSubscriberInterface
{
    public function __construct(private TemplateRendererInterface $renderer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', 0]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof RoutingException || !$event->isMainRequest()) {
            return;
        }

        $event->allowCustomResponseCode();
        $event->setResponse($this->createResponse($event->getRequest()));
        $event->stopPropagation();
    }

    private function createResponse(Request $request): Response
    {
        try {
            return new Response($this->renderPage($request), Response::HTTP_NOT_FOUND);
        } catch (RedirectException $redirect) {
            return new RedirectResponse($redirect->getUrl(), $redirect->getStatusCode());
        }
    }

    private function renderPage(Request $request): string
    {
        $view = oxNew(FrontendController::class);
        $view->setClassKey(
            Registry::getControllerClassNameResolver()->getIdByClassName(FrontendController::class)
        );
        Registry::getConfig()->setActiveView($view);
        $view->init();
        $view->render();

        $viewData = $view->getViewData();
        $viewData['sUrl'] = $request->getRequestUri();

        return $this->renderer->renderTemplate('message/err_404', $viewData);
    }
}
