<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Authentication\Fixtures\testModule\src\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\SessionUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SessionAuthTestController
{
    public function __construct(
        private readonly PasswordServiceBridgeInterface $passwordService,
    ) {
    }

    #[Route('/api/test/session-login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $userId = $request->request->get('user_id');
        $isAdmin = $request->request->getBoolean('is_admin');

        $session = Registry::getSession();
        $session->setAdminMode($isAdmin);
        $session->start();

        $userKey = $isAdmin ? 'auth' : 'usr';
        $session->setVariable($userKey, $userId);
        $session->setVariable('login-token', $this->generateLoginToken($userId));

        return new JsonResponse([
            'session_id' => $session->getId(),
            'session_name' => $isAdmin ? 'admin_sid' : 'sid',
            'stoken' => $session->getSessionChallengeToken(),
        ]);
    }

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
    #[SessionUser(roles: ['ROLE_ADMIN'])]
    public function adminOnly(Request $request): Response
    {
        $user = $request->attributes->get('_user');

        return new JsonResponse([
            'authenticated' => true,
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

    private function generateLoginToken(string $userId): string
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load($userId);

        return $this->passwordService->hash((string) $user->getFieldData('oxpassword'));
    }
}
