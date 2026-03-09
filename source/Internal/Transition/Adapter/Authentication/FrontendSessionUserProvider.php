<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Authentication;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\SessionUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class FrontendSessionUserProvider implements SessionUserProviderInterface
{
    public function loadSessionUser(Request $request): UserInterface
    {
        $session = Registry::getSession();
        $session->setAdminMode(false);
        $session->start();

        if (!$session->checkSessionChallenge()) {
            throw new AuthenticationException('CSRF token mismatch');
        }

        $user = oxNew(User::class);

        if (!$user->loadActiveUser()) {
            throw new AuthenticationException('No active session user');
        }

        return new InMemoryUser((string) $user->getFieldData('oxusername'), null, ['ROLE_USER']);
    }
}
