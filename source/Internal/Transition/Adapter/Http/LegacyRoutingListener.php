<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Core\SeoDecoder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class LegacyRoutingListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 24]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isHandledByTheLegacyController($request)) {
            return;
        }

        if (str_ends_with($request->getScriptName(), 'oxseo.php')) {
            $this->decodeSeoUrl($request);
        }

        if ($request->getPathInfo() !== '/' && !$this->hasControllerKey($request)) {
            throw new NotFoundHttpException($request->getPathInfo());
        }
    }

    private function isHandledByTheLegacyController(Request $request): bool
    {
        $controller = $request->attributes->get('_fallback_controller');

        return $controller !== null && $request->attributes->get('_controller') === $controller;
    }

    private function decodeSeoUrl(Request $request): void
    {
        oxNew(SeoDecoder::class)->processSeoCall(
            $request->getRequestUri(),
            str_replace('oxseo.php', '', $request->getScriptName())
        );
        $request->query->add($_GET);
    }

    private function hasControllerKey(Request $request): bool
    {
        $post = $request->getRealMethod() === 'POST' ? $request->request->all() : [];
        $controllerKey = $post['cl'] ?? $request->query->all()['cl'] ?? '';

        return (bool) (is_string($controllerKey) ? str_replace("\0", '', $controllerKey) : $controllerKey);
    }
}
