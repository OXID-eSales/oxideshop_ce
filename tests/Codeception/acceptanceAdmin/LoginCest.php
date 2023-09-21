<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use Codeception\Util\Fixtures;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class LoginCest
{
    public function setSessionCookie(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('correct session ID name is set in cookies');

        $userData = $this->getAdminUserData();

        $loginPage = $I->openAdmin();
        $loginPage->login($userData['userLoginName'], $userData['userPassword']);

        $I->seeCookie('admin_sid');
        $I->dontSeeCookie('sid');
    }

    private function getAdminUserData(): array
    {
        return Fixtures::get('adminUser');
    }
}
