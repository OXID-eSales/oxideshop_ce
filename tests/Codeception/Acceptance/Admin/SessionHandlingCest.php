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

final class SessionHandlingCest
{
    #[Group('session')]
    public function adminSessionAfterPasswordChange(AcceptanceTester $I): void
    {
        $I->wantToTest('that admin will be logged out if someone changes his password from another active session');
        $userData = Fixtures::get('adminUser');
        $adminLoginPage = $I->openAdmin();
        $I->amGoingTo('log the existing admin in');
        $adminLoginPage->login($userData['userLoginName'], $userData['userPassword']);

        $I->amGoingTo('mock password change for this admin from another browser session');
        $I->updateInDatabase(
            'oxuser',
            ['OXPASSWORD' => 'some-new-password-hash'],
            ['OXUSERNAME' => $userData['userLoginName']]
        );

        $I->amGoingTo('send any page request after password change');
        $I->reloadPage();

        $I->expect('that admin is logged out');
        $adminLoginPage->seeLoginForm();
    }
}
