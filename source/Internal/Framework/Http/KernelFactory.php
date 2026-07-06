<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

readonly class KernelFactory
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private RequestStack $requestStack,
        private ContainerInterface $container
    ) {
    }

    public function createShopKernel(): HttpKernelInterface&TerminableInterface
    {
        return new HttpKernel(
            $this->dispatcher,
            new ContainerControllerResolver($this->container),
            $this->requestStack,
            new ArgumentResolver(),
            handleAllThrowables: true
        );
    }
}
