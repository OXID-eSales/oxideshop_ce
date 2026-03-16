<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Authentication;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\CsrfTokenMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\InsufficientAdminRightsException;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\NoActiveSessionUserException;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\SessionUserProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class AdminSessionUserProvider implements SessionUserProviderInterface
{
    public function __construct(
        private ContextInterface $context,
    ) {
    }

    public function loadSessionUser(Request $request): UserInterface
    {
        $session = Registry::getSession();
        $session->setAdminMode(true);
        $session->start();

        if (!$session->checkSessionChallenge()) {
            throw new CsrfTokenMismatchException();
        }

        if (!Registry::getUtils()->checkAccessRights()) {
            throw new InsufficientAdminRightsException();
        }

        $user = oxNew(User::class);

        if (!$user->loadActiveUser(true)) {
            throw new NoActiveSessionUserException();
        }

        return new InMemoryUser(
            (string) $user->getFieldData('oxusername'),
            null,
            $this->resolveRoles((string) $user->getFieldData('oxrights'))
        );
    }

    /** @return string[] */
    private function resolveRoles(string $oxidRights): array
    {
        if ($oxidRights === 'malladmin') {
            return ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ADMIN_MALL'];
        }

        if ($oxidRights === (string) $this->context->getCurrentShopId()) {
            return ['ROLE_USER', 'ROLE_ADMIN'];
        }

        throw new InsufficientAdminRightsException();
    }
}
