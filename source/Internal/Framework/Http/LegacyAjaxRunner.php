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

/**
 * @deprecated will be removed once admin ajax requests are served by Symfony routes
 */
readonly class LegacyAjaxRunner implements LegacyAjaxRunnerInterface
{
    public function __construct(
        private HttpKernelInterface&TerminableInterface $kernel,
        private Request $request
    ) {
    }

    public function runController(callable $controller): void
    {
        $this->request->attributes->set('_controller', $controller);

        $response = $this->kernel->handle($this->request);
        $response->send();
        $this->kernel->terminate($this->request, $response);
    }
}
