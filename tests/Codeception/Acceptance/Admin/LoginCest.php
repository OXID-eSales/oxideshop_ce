<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class LoginCest
{
    public function setSessionCookie(AcceptanceTester $I): void
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
