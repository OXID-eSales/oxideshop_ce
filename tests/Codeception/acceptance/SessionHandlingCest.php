<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;

final class SessionHandlingCest
{
    /**
     * @group session
     */
    public function checkForceSidWithDefaultConfig(AcceptanceTester $I): void
    {
        $I->wantToTest('that force_sid allows to access the current users session');

        $userData = $this->getExistingUserData();

        $I->amGoingTo('login to existing user account and grab active session ID');
        $this->createUserSession($I, $userData['userLoginName'], $userData['userPassword']);
        $I->see($userData['userLoginName'], '#accountMain');
        $sessionId = $I->grabCookie('sid');

        $I->amGoingTo('clear the cookie data and see that the session is not accessible');
        $this->forgetUserSession($I);
        $I->dontSee($userData['userLoginName'], '#accountMain');

        $I->amGoingTo('add force_sid to URL and see that the session is accessible');
        $this->restoreUserSession($I, $sessionId);
        $I->see($userData['userLoginName'], '#accountMain');
    }

    /**
     * @group session
     */
    public function checkForceSidWithDisabledForceSid(AcceptanceTester $I): void
    {
        $I->wantToTest('that force_sid is not working after configuration update');

        $userData = $this->getExistingUserData();

        $I->amGoingTo('disable force_sid via configuration');
        $I->updateConfigInDatabase('disallowForceSessionIdInRequest', true);

        $I->amGoingTo('login to existing user account and grab active session ID');
        $this->createUserSession($I, $userData['userLoginName'], $userData['userPassword']);
        $I->see($userData['userLoginName'], '#accountMain');
        $sessionId = $I->grabCookie('sid');

        $I->amGoingTo('clear the cookie data and see that the session is not accessible');
        $this->forgetUserSession($I);
        $I->dontSee($userData['userLoginName'], '#accountMain');

        $I->amGoingTo('add force_sid to URL and see that the session is not accessible');
        $this->restoreUserSession($I, $sessionId);
        $I->dontSee($userData['userLoginName'], '#accountMain');
    }

    private function createUserSession(AcceptanceTester $I, string $userName, string $password): void
    {
        $homePage = $I->openShop();
        $accountMenu = $homePage->loginUser($userName, $password);
        $accountMenu->openAccountPage();
    }

    private function forgetUserSession(AcceptanceTester $I): void
    {
        $I->resetCookie('sid');
        $homePage = $I->openShop();
        $homePage->openAccountPage();
    }

    private function restoreUserSession(AcceptanceTester $I, string $sessionId): void
    {
        $I->amOnUrl("{$I->getCurrentURL()}?force_sid=$sessionId");
        $homePage = $I->openShop();
        $homePage->openAccountPage();
    }

    private function getExistingUserData(): array
    {
        return Fixtures::get('existingUser');
    }
}
