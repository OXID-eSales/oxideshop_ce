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
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

use function random_int;

#[Group('session')]
final class SessionHandlingCest
{
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

    public function userSessionAfterPasswordChange(AcceptanceTester $I): void
    {
        $I->wantToTest('that user will be logged out if someone changes his password from another active session');
        $userData = Fixtures::get('existingUser');
        $I->amGoingTo('log the existing user in');
        $home = $I
            ->openShop()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeUserLoggedIn();

        $I->amGoingTo('add some product to the shopping cart');
        (new Basket($I))->addProductToBasket('1001', 3);
        $home->seeItemCountBadge('3');

        $I->amGoingTo('mock password change for this user from admin/another browser session');
        $I->updateInDatabase(
            'oxuser',
            ['OXPASSWORD' => 'some-new-password-hash'],
            ['OXUSERNAME' => $userData['userLoginName']]
        );

        $I->amGoingTo('send any page request after password change');
        $home->openAccountPage();
        $I->expect('that user was logged out');
        $home->seeUserLoggedOut();

        $I->expect('that the basket is retained, despite the user being logged out');
        $home->seeItemCountBadge('3');
    }

    public function registerStandardUserInFrontend(AcceptanceTester $I): void
    {
        $I->wantToTest('simple user account opening');
        $username = 'some-user-email@oxid-esales.dev';
        $userRegistration = $I
            ->openShop()
            ->openUserRegistrationPage();

        $homePage = $I->openShop();
        $homePage->openUserRegistrationPage();

        $userRegistration
            ->enterUserLoginData([
                'userLoginNameField' => $username,
                'userPasswordField' => 'user1user1',
            ])
            ->enterUserData([
                'userUstIDField' => '',
                'userMobFonField' => '111-111111-1',
                'userPrivateFonField' => '111111111',
                'userBirthDateDayField' => random_int(1, 28),
                'userBirthDateMonthField' => random_int(1, 12),
                'userBirthDateYearField' => random_int(1960, 2000),
            ])
            ->enterAddressData([
                'userSalutation' => 'Mrs',
                'userFirstName' => 'John',
                'userLastName' => 'Doe',
                'companyName' => 'Unemployed',
                'street' => 'Main Str.',
                'streetNr' => 123,
                'ZIP' => '12341',
                'city' => 'Big City',
                'additionalInfo' => 'Something additional',
                'fonNr' => '111-111-1',
                'faxNr' => '111-111-111-1',
                'countryId' => 'Germany',
                'stateId' => 'Berlin',
            ])
            ->registerUser();

        $I->openShop()
            ->seeUserLoggedIn();

        $I->amGoingTo('mock password change for this user from admin/another browser session');
        $I->updateInDatabase(
            'oxuser',
            ['OXPASSWORD' => 'some-new-password-hash'],
            ['OXUSERNAME' => $username]
        );

        $I->openShop()
            ->seeUserLoggedOut();
    }

    private function createUserSession(AcceptanceTester $I, string $userName, string $password): UserAccount
    {
        return $I
            ->openShop()
            ->loginUser($userName, $password)
            ->openAccountPage();
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
