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
        unset($_SESSION['usr'], $_SESSION['login-token']);
        Registry::getSession()->setUser(null);

        parent::tearDown();
    }

    public function testInitRedirectsBlockedUserToTheBlockedPage(): void
    {
        $this->loginBlockedUser();
        $component = oxNew(UserComponent::class);
        $component->setParent(oxNew(FrontendController::class));

        try {
            $component->init();
            $this->fail('Redirect to the blocked page was expected');
        } catch (RedirectException $redirect) {
            parse_str((string) parse_url($redirect->getUrl(), PHP_URL_QUERY), $query);
            $this->assertSame('content', $query['cl']);
            $this->assertSame('user_blocked', $query['tpl']);
        }
    }

    private function loginBlockedUser(): void
    {
        $user = oxNew(User::class);
        $user->setId(self::USER_ID);
        $user->oxuser__oxusername = new Field(self::USER_NAME);
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->setPassword(self::PASSWORD);
        $user->save();
        $user->addToGroup('oxidblocked');

        oxNew(User::class)->login(self::USER_NAME, self::PASSWORD);
    }
}
