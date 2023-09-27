<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Context;
use OxidEsales\Codeception\Page\Account\UserAccount;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

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
        $accountUser = $this->createUserSession($I, $userData['userLoginName'], $userData['userPassword']);
        $accountUser->seeUserAccount($userData);
        $sessionId = $I->grabCookie('sid');

        $I->amGoingTo('clear the cookie data and see that the session is not accessible');
        $this->forgetUserSession($I);
        $I->openShop()->openUserAccountPage()->seePageOpened();

        $I->amGoingTo('add force_sid to URL and see that the session is accessible');
        $this->restoreUserSession($I, $sessionId);
        $I->openShop()->openAccountPage()->seeUserAccount($userData);
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
        $accountUser = $this->createUserSession($I, $userData['userLoginName'], $userData['userPassword']);
        $accountUser->seeUserAccount($userData);
        $sessionId = $I->grabCookie('sid');

        $I->amGoingTo('clear the cookie data and see that the session is not accessible');
        $this->forgetUserSession($I);
        $I->openShop()->openUserAccountPage()->seePageOpened();

        $I->amGoingTo('add force_sid to URL and see that the session is not accessible');
        $this->restoreUserSession($I, $sessionId);
        $I->openShop()->openUserAccountPage()->seePageOpened();
    }

    private function createUserSession(AcceptanceTester $I, string $userName, string $password): UserAccount
    {
        $homePage = $I->openShop();
        $accountMenu = $homePage->loginUser($userName, $password);
        return $accountMenu->openAccountPage();
    }

    private function forgetUserSession(AcceptanceTester $I): void
    {
        $I->resetCookie('sid');
        Context::resetActiveUser();
    }

    private function restoreUserSession(AcceptanceTester $I, string $sessionId): void
    {
        $I->amOnUrl("{$I->getCurrentURL()}?force_sid=$sessionId");
    }

    private function getExistingUserData(): array
    {
        return Fixtures::get('existingUser');
    }
}
