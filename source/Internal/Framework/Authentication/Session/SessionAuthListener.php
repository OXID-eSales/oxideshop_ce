<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session;

use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

class SessionAuthListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly AuthenticatorInterface $authenticator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $attribute = $this->resolveAttribute($event);

        if ($attribute === null) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->authenticator->supports($request)) {
            $event->setController(fn() => new JsonResponse(['error' => 'Authentication required'], 401));
            return;
        }

        try {
            $passport = $this->authenticator->authenticate($request);
        } catch (AuthenticationException) {
            $event->setController(fn() => new JsonResponse(['error' => 'Authentication required'], 401));
            return;
        }

        $user = $passport->getUser();

        if ($attribute->roles && !$this->hasRequiredRoles($attribute->roles, $user->getRoles())) {
            $event->setController(fn() => new JsonResponse(['error' => 'Access denied'], 403));
            return;
        }

        $request->attributes->set('_user', $user);
    }

    private function resolveAttribute(ControllerEvent $event): ?SessionUser
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            [$object, $method] = $controller;

            $attrs = (new ReflectionMethod($object, $method))->getAttributes(SessionUser::class);
            if ($attrs) {
                return $attrs[0]->newInstance();
            }

            $attrs = (new \ReflectionClass($object))->getAttributes(SessionUser::class);
            if ($attrs) {
                return $attrs[0]->newInstance();
            }
        } elseif (is_object($controller)) {
            $attrs = (new \ReflectionClass($controller))->getAttributes(SessionUser::class);
            if ($attrs) {
                return $attrs[0]->newInstance();
            }
        }

        return null;
    }

    /** @param string[] $required @param string[] $actual */
    private function hasRequiredRoles(array $required, array $actual): bool
    {
        return array_diff($required, $actual) === [];
    }
}
