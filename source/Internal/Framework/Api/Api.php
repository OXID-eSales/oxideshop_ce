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
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Http\AccessToken\HeaderAccessTokenExtractor;
use Symfony\Component\Security\Http\Authenticator\AccessTokenAuthenticator;
use Symfony\Component\Security\Http\EventListener\IsGrantedAttributeListener;

class Api
{
    public function run(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $request = Request::createFromGlobals();

        $matcher = new CompiledUrlMatcher($container->getParameter('oxid.routes'), new RequestContext());
        $parameters = $matcher->matchRequest($request);
        $request->attributes->add($parameters);

        //Get token from header
//        $extractor = new HeaderAccessTokenExtractor();
//        $authenticator = new AccessTokenAuthenticator(
//            // Implement AccessTokenHandlerInterface could be a simple implementation by default, easy replace with OidcTokenHandler or CAS2Handler. We can also ship a component with ready and configurable implementation.
//            $extractor,
//            // Implement UserProviderInterface
//
//        );
//
//        $user = $authenticator->authenticate()->getUser();

        $user = new User();
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken($token);

        $accessDecisionManager = new AccessDecisionManager([
            new AuthenticatedVoter(new AuthenticationTrustResolver()),
            new RoleVoter(),
            new RoleHierarchyVoter(new RoleHierarchy([
                'ROLE_MALL_ADMIN' => ['ROLE_ADMIN'],
                'ROLE_ADMIN' => ['ROLE_USER'],
            ]))
        ]);

        $checker = new AuthorizationChecker($tokenStorage, $accessDecisionManager);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new IsGrantedAttributeListener($checker));

        $kernel = new HttpKernel(
            $dispatcher,
            new ContainerControllerResolver($container),
            new RequestStack(),
            new ArgumentResolver()
        );

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);
    }
}
