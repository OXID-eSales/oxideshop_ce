<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Context;
use OxidEsales\Codeception\Page\Account\UserAccount;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('session')]
final class SessionHandlingCest
{
    public function checkForceSidWithDefaultConfig(AcceptanceTester $I): void
    {
        $I->wantToTest('that force_sid allows to access the current users session');
        $userData = Fixtures::get('existingUser');

        $I->amGoingTo('login to existing user account and grab active session ID');
        $this->createUserSession(
            $I,
            $userData['userLoginName'],
            $userData['userPassword']
        )
            ->seePageOpened()
            ->seeUserAccount($userData);
        $sessionId = $I->grabCookie('sid');

        $I->amGoingTo('clear the cookie data and see that the session is not accessible');
        $this->resetSessionCookie($I);
        $I->openShop()
            ->openUserAccountPage()
            ->seePageOpened()
            ->seeLoginForm();

        $I->amGoingTo('add force_sid to URL and see that the session is accessible');
        $this->sendRequestToSwitchSessionId($I, $sessionId);
        $I->openShop()
            ->openAccountPage()
            ->seePageOpened()
            ->seeUserAccount($userData);
    }

    public function checkForceSidWithDisabledForceSid(AcceptanceTester $I): void
    {
        $I->wantToTest('that force_sid is not working after configuration update');
        $userData = Fixtures::get('existingUser');

        $I->amGoingTo('disable force_sid via configuration');
        $I->updateProjectConfigurations(['oxid_disallow_force_session_id' => true], []);

        $I->amGoingTo('login to existing user account and grab active session ID');
        $this->createUserSession(
            $I,
            $userData['userLoginName'],
            $userData['userPassword']
        )
            ->seeUserAccount($userData);
        $sessionId = $I->grabCookie('sid');

        $I->amGoingTo('clear the cookie data and see that the session is not accessible');
        $this->resetSessionCookie($I);
        $I->openShop()
            ->openUserAccountPage()
            ->seePageOpened()
            ->seeLoginForm();

        $I->amGoingTo('add force_sid to URL and see that the session is not accessible');
        $this->sendRequestToSwitchSessionId($I, $sessionId);
        $I->expect('to see user login form instead of user account page');
        $I->openShop()
            ->openUserAccountPage()
            ->seePageOpened()
            ->seeLoginForm();

        $I->amGoingTo('do cleanup');
        $I->restoreProjectConfigurations();
    }

    private function createUserSession(AcceptanceTester $I, string $userName, string $password): UserAccount
    {
        return $I->openShop()
            ->loginUser($userName, $password)
            ->openAccountPage();
    }

    private function resetSessionCookie(AcceptanceTester $I): void
    {
        $I->resetCookie('sid');
        Context::resetActiveUser();
    }

    private function sendRequestToSwitchSessionId(AcceptanceTester $I, string $sessionId): void
    {
        $I->amOnUrl("{$I->getCurrentURL()}?force_sid=$sessionId");
    }
}
