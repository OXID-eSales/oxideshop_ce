<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Authentication\Fixtures\testModule\src\Controller;

use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\AdminSessionUser;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\SessionUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SessionAuthTestController
{
    #[Route('/api/test/session-auth', methods: ['GET'])]
    #[SessionUser]
    public function checkAuth(Request $request): Response
    {
        $user = $request->attributes->get('_user');

        return new JsonResponse([
            'authenticated' => true,
            'username' => $user->getUserIdentifier(),
        ]);
    }

    #[Route('/api/test/session-admin', methods: ['GET'])]
    #[AdminSessionUser]
    public function adminOnly(Request $request): Response
    {
        $user = $request->attributes->get('_user');

        return new JsonResponse([
            'authenticated' => true,
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/test/session-malladmin', methods: ['GET'])]
    #[AdminSessionUser(roles: ['ROLE_ADMIN_MALL'])]
    public function mallAdminOnly(Request $request): Response
    {
        $user = $request->attributes->get('_user');

        return new JsonResponse([
            'authenticated' => true,
            'username' => $user->getUserIdentifier(),
        ]);
    }
}
