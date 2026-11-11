<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserComponentBlockedUserTest extends IntegrationTestCase
{
    private const USER_ID = 'blocked-user';
    private const USER_NAME = 'blocked-user@example.com';
    private const PASSWORD = 'blocked-user-password';

    public function tearDown(): void
    {
        unset($_SESSION['usr'], $_SESSION['login-token'], $_POST['lgn_usr'], $_POST['lgn_pwd']);
        Registry::getSession()->setUser(null);

        parent::tearDown();
    }

    public function testSignedInBlockedUserIsSignedOutAndSentToTheBlockedPage(): void
    {
        $this->createBlockedUser();
        oxNew(User::class)->login(self::USER_NAME, self::PASSWORD);

        $redirect = $this->catchRedirect($this->createComponent()->init(...));
        $blockedPageRedirect = $this->catchRedirect($this->createComponent()->init(...));

        $this->assertSame(
            [['content', 'user_blocked'], null, null],
            [$this->getRedirectTarget($redirect), Registry::getSession()->getVariable('usr'), $blockedPageRedirect]
        );
    }

    public function testBlockedUserLoggingInIsSignedOutAndSentToTheBlockedPage(): void
    {
        $this->createBlockedUser();
        $_POST['lgn_usr'] = self::USER_NAME;
        $_POST['lgn_pwd'] = self::PASSWORD;

        $redirect = $this->catchRedirect($this->createComponent()->login(...));

        $this->assertSame(
            [['content', 'user_blocked'], null],
            [$this->getRedirectTarget($redirect), Registry::getSession()->getVariable('usr')]
        );
    }

    private function createBlockedUser(): void
    {
        $user = oxNew(User::class);
        $user->setId(self::USER_ID);
        $user->oxuser__oxusername = new Field(self::USER_NAME);
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->setPassword(self::PASSWORD);
        $user->save();
        $user->addToGroup('oxidblocked');
    }

    private function createComponent(): UserComponent
    {
        $component = oxNew(UserComponent::class);
        $component->setParent(oxNew(FrontendController::class));

        return $component;
    }

    private function catchRedirect(callable $action): ?RedirectException
    {
        try {
            $action();
        } catch (RedirectException $redirect) {
            return $redirect;
        }

        return null;
    }

    /**
     * @return array<int, string|null>
     */
    private function getRedirectTarget(?RedirectException $redirect): array
    {
        parse_str((string) parse_url((string) $redirect?->getUrl(), PHP_URL_QUERY), $query);

        return [$query['cl'] ?? null, $query['tpl'] ?? null];
    }
}
