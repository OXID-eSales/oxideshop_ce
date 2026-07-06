<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

readonly class ShopRequestRunner
{
    public function __construct(
        private KernelFactory $kernelFactory,
        private Request $request,
        private array $routes
    ) {
    }

    public function run(callable $fallbackController): void
    {
        $this->request->attributes->add($this->resolveControllerAttributes($fallbackController));

        $kernel = $this->kernelFactory->createShopKernel();
        $response = $kernel->handle($this->request);
        $response->prepare($this->request);
        $response->send();
        $kernel->terminate($this->request, $response);
    }

    private function resolveControllerAttributes(callable $fallbackController): array
    {
        $context = new RequestContext();
        $context->fromRequest($this->request);

        try {
            return new CompiledUrlMatcher($this->routes, $context)
                ->match(rawurldecode($this->request->getPathInfo()));
        } catch (ResourceNotFoundException | MethodNotAllowedException) {
            return ['_controller' => $fallbackController];
        }
    }
}
