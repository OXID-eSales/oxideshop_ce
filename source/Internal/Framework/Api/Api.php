<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Api;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Api
{
    public function run(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $request = Request::createFromGlobals();

        $matcher = new CompiledUrlMatcher($container->getParameter('oxid.routes'), new RequestContext());
        $parameters = $matcher->matchRequest($request);
        $request->attributes->add($parameters);

        $kernel = new HttpKernel(
            new EventDispatcher(),
            new ContainerControllerResolver($container),
            new RequestStack(),
            new ArgumentResolver()
        );

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);
    }
}
