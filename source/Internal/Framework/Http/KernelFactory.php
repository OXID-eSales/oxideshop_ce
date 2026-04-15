<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

class KernelFactory
{
    public function create(): HttpKernel
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $context = new RequestContext();
        $matcher = new CompiledUrlMatcher($container->getParameter('oxid.routes'), $context);

        $requestStack = new RequestStack();
        $dispatcher = $container->get(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));

        return new HttpKernel(
            $dispatcher,
            new ContainerControllerResolver($container),
            $requestStack,
            new ArgumentResolver(namedResolvers: $container)
        );
    }
}
