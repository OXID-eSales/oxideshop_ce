<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

readonly class ShopRunner implements ShopRunnerInterface
{
    public function __construct(
        private HttpKernelInterface&TerminableInterface $kernel,
        private Request $request,
        private array $routes
    ) {
    }

    public function run(callable $fallbackController): void
    {
        $this->request->attributes->add($this->resolveControllerAttributes($fallbackController));

        $response = $this->kernel->handle($this->request);
        $response->send();
        $this->kernel->terminate($this->request, $response);
    }

    private function resolveControllerAttributes(callable $fallbackController): array
    {
        if (str_starts_with($this->request->getPathInfo(), '/api/')) {
            return $this->resolveApiControllerAttributes() + ['_format' => 'json'];
        }

        try {
            return $this->matchRoute();
        } catch (ResourceNotFoundException | MethodNotAllowedException) {
            return ['_controller' => $fallbackController];
        }
    }

    private function resolveApiControllerAttributes(): array
    {
        try {
            return $this->matchRoute();
        } catch (ResourceNotFoundException) {
            return ['_controller' => static fn(): never => throw new NotFoundHttpException()];
        } catch (MethodNotAllowedException $exception) {
            return [
                '_controller' => static fn(): never => throw new MethodNotAllowedHttpException(
                    $exception->getAllowedMethods()
                ),
            ];
        }
    }

    private function matchRoute(): array
    {
        $context = new RequestContext();
        $context->fromRequest($this->request);

        return new CompiledUrlMatcher($this->routes, $context)->match($this->request->getPathInfo());
    }
}
