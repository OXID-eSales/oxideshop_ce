<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class LoginCest
{
    /** @param AcceptanceAdminTester $I */
    public function setSessionCookie(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('correct session ID name is set in cookies');

        $I->loginAdmin();

        $I->seeCookie('admin_sid');
        $I->dontSeeCookie('sid');
    }
}
