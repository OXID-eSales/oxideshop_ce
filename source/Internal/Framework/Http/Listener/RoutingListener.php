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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

readonly class RoutingListener implements EventSubscriberInterface
{
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
        } catch (ResourceNotFoundException | MethodNotAllowedException $exception) {
            $request->attributes->set(
                '_controller',
                $this->getFallbackController($request) ?? throw $this->toHttpException($exception)
            );
        }
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    private function matchRoute(Request $request): array
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        return new CompiledUrlMatcher($this->routes, $context)->match($request->getPathInfo());
    }

    private function getFallbackController(Request $request): ?callable
    {
        return $this->isApiRequest($request) ? null : $request->attributes->get('_fallback_controller');
    }

    private function toHttpException(ResourceNotFoundException | MethodNotAllowedException $exception): HttpException
    {
        if ($exception instanceof MethodNotAllowedException) {
            return new MethodNotAllowedHttpException($exception->getAllowedMethods(), previous: $exception);
        }

        return new NotFoundHttpException(previous: $exception);
    }
}
