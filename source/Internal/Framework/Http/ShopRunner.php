<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\HttpFoundation\Request;
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
        $context = new RequestContext();
        $context->fromRequest($this->request);

        try {
            return new CompiledUrlMatcher($this->routes, $context)
                ->match($this->request->getPathInfo());
        } catch (ResourceNotFoundException | MethodNotAllowedException) {
            return ['_controller' => $fallbackController];
        }
    }
}
