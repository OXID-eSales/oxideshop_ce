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

readonly class ShopRunner implements ShopRunnerInterface
{
    public function __construct(private HttpKernelInterface&TerminableInterface $kernel)
    {
    }

    public function run(Request $request, callable $fallbackController): void
    {
        $request->attributes->set('_fallback_controller', $fallbackController);

        $this->handle($request);
    }

    public function runRoutes(Request $request): void
    {
        $this->handle($request);
    }

    public function runController(Request $request, callable $controller): void
    {
        $request->attributes->set('_controller', $controller);

        $this->handle($request);
    }

    private function handle(Request $request): void
    {
        $response = $this->kernel->handle($request);
        $response->send();
        $this->kernel->terminate($request, $response);
    }
}
