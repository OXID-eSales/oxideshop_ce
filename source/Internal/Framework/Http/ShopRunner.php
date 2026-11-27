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
    public function __construct(
        private HttpKernelInterface&TerminableInterface $kernel,
        private Request $request
    ) {
    }

    public function run(callable $fallbackController): void
    {
        $this->request->attributes->set('_fallback_controller', $fallbackController);

        $response = $this->kernel->handle($this->request);
        $response->send();
        $this->kernel->terminate($this->request, $response);
    }
}
