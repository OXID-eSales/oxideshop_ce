<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

readonly class RoutingListener implements EventSubscriberInterface
{
    /**
     * @param array<int, mixed> $routes
     */
    public function __construct(private array $routes)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 32]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->attributes->has('_controller')) {
            return;
        }

        if ($this->isApiRequest($request)) {
            $request->attributes->set('_format', 'json');
        }

        try {
            $request->attributes->add($this->matchRoute($request));
        } catch (ResourceNotFoundException $exception) {
            if (!$this->hasFallbackController($request)) {
                throw new NotFoundHttpException(previous: $exception);
            }
            $request->attributes->set('_controller', $request->attributes->get('_fallback_controller'));
        } catch (MethodNotAllowedException $exception) {
            throw new MethodNotAllowedHttpException($exception->getAllowedMethods(), previous: $exception);
        }
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    private function matchRoute(Request $request): array
    {
        if ($request->getPathInfo() === '/') {
            throw new ResourceNotFoundException();
        }

        $context = new RequestContext();
        $context->fromRequest($request);

        return new CompiledUrlMatcher($this->routes, $context)->match($request->getPathInfo());
    }

    private function hasFallbackController(Request $request): bool
    {
        return !$this->isApiRequest($request) && $request->attributes->get('_fallback_controller') !== null;
    }
}
