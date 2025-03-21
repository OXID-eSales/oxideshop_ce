<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Step\Start;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('myAccount')]
final class UserAccountCest
{
    public function loginUserInFrontend(AcceptanceTester $I): void
    {
        $I->wantToTest('user login (popup in top of the page)');

        $startPage = $I->openShop();

        //login when username/pass are incorrect. error msg should be in place etc.
        $startPage->loginUser('non-existing-user@oxid-esales.dev', '')
            ->seeUserLoggedOut();
        $I->seeText(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $startPage->badLoginError);

        //login with correct user name/pass
        $userData = $this->getExistingUserData();
        $startPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeUserLoggedIn();

        $startPage
            ->openAccountPage()
            ->seePageOpened()
            ->seeUserAccount($userData);
    }

    public function changeUserAccountPassword(AcceptanceTester $I): void
    {
        $I->wantTo('change user password in my account navigation');

        $userData = $this->getExistingUserData();
        $userName = $userData['userLoginName'];
        $oldPass = $userData['userPassword'];
        $newPass = 'someNewPassword123';
        $invalidPass = 'pass';

        $startPage = $I->openShop()
            ->loginUser($userName, $oldPass);
        $I->dontSee(Translator::translate('LOGIN'));

        $changePasswordPage = $startPage
            ->openAccountPage()
            ->seePageOpened()
            ->seeUserAccount($userData)
            ->openChangePasswordPage();

        //entered not matching new passwords
        $changePasswordPage->fillPasswordFields($oldPass, $newPass, $oldPass);
        $I->seeText(Translator::translate('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

        //new pass is too short
        $changePasswordPage->changePassword($oldPass, $invalidPass, $invalidPass);
        $I->seeText(Translator::translate('ERROR_MESSAGE_PASSWORD_TOO_SHORT'));

        //correct new pass
        $changePasswordPage->changePassword($oldPass, $newPass, $newPass);
        $I->seeText(Translator::translate('MESSAGE_PASSWORD_CHANGED'));

        $I->reloadPage();
        $startPage->seeUserLoggedOut();

        // try to login with old password
        $I->openShop()
            ->loginUser($userName, $oldPass);
        $I->seeText(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'));

        // try to login with new password
        $changePasswordPage = $I->openShop()
            ->loginUser($userName, $newPass)
            ->openUserAccountPage()
            ->seeUserLoggedIn()
            ->openChangePasswordPage();
        $I->dontSee(Translator::translate('LOGIN'));

        //reset new pass to old one
        $changePasswordPage->changePassword($newPass, $oldPass, $oldPass);
        $I->seeText(Translator::translate('MESSAGE_PASSWORD_CHANGED'));
    }

    public function sendUserPasswordReminder(AcceptanceTester $I): void
    {
        $I->wantToTest('user password reminder in my account navigation');

        $userData = $this->getExistingUserData();
        $startPage = $I->openShop();

        $passwordReminderPage = $startPage->openUserPasswordReminderPage();
        $I->seeText(Translator::translate('HAVE_YOU_FORGOTTEN_PASSWORD'));

        $I->amGoingTo('reset password with invalid email format');
        $passwordReminderPage->resetPassword('wrongEmail');
        $I->seeText(Translator::translate('DD_FORM_VALIDATION_VALIDEMAIL'));

        $I->amGoingTo('reset password with existing user email');
        $passwordReminderPage->resetPassword($userData['userLoginName']);
        $I->seeText(Translator::translate('PASSWORD_WAS_SEND_TO') . ' ' . $userData['userLoginName']);

        $I->amGoingTo('reset password with non-existing user email');
        $nonExistingEmail = 'not_existing_user@oxid-esales.dev';
        $startPage->openUserPasswordReminderPage()
            ->resetPassword($nonExistingEmail);
        $I->seeText(Translator::translate('PASSWORD_WAS_SEND_TO') . ' ' . $nonExistingEmail);
    }

    public function changeUserEmailInBillingAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('changing user email address in my account billing information');

        $userData = $this->getExistingUserData();
        $guestUserData = $this->getExistingGuestUserData();
        $adminUserData = $this->getAdminUserData();
        $newEmail = 'example02@oxid-esales.dev';

        $I->amGoingTo('login to shop and navigate to billing address form');
        $userAddressPage = $I->openShop()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openUserAddressPage()
            ->openUserBillingAddressForm();

        $I->expect('to see default country and state selections');
        $I->seeText('Germany', $userAddressPage->billCountryId);
        $I->seeText(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

        $I->amGoingTo('try to change email to an existing guest user email');
        $userAddressPage->changeEmail($guestUserData['userLoginName'], $userData['userPassword']);
        $I->expect('to see an error message about existing user');
        $I->seeText(Translator::translate('ERROR_MESSAGE_USER_USEREXISTS'));

        $I->amGoingTo('try to change email to an existing admin email');
        $userAddressPage->openUserBillingAddressForm()
            ->changeEmail($adminUserData['userLoginName'], $userData['userPassword']);
        $I->expect('to see an error message about existing user');
        $I->seeText(Translator::translate('ERROR_MESSAGE_USER_USEREXISTS'));

        $I->amGoingTo('change user email to a new valid email address');
        $userAddressPage = $userAddressPage->openUserBillingAddressForm()
            ->changeEmail($newEmail, $userData['userPassword']);
        $I->expect('not to see any error messages');
        $I->dontSee(Translator::translate('ERROR_MESSAGE_USER_USEREXISTS'));
        $I->dontSee(Translator::translate('COMPLETE_MARKED_FIELDS'));

        $I->amGoingTo('logout and try to login with the old email address');
        $userAddressPage = $userAddressPage->logoutUser();
        $userAddressPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->expect('to see login form and error message');
        $I->seeText(Translator::translate('LOGIN'));
        $I->seeText(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $userAddressPage->badLoginError);

        $I->amGoingTo('login with the new email address');
        $userAddressPage->loginUser($newEmail, $userData['userPassword']);
        $I->expect('to be logged in successfully');
        $I->dontSee(Translator::translate('LOGIN'));

        $I->amGoingTo('change the email back to the original one');
        $userAddressPage->openUserBillingAddressForm()
            ->changeEmail('example_test@oxid-esales.dev', $userData['userPassword'])
            ->logoutUser();
        $I->expect('to be logged out');
        $I->seeText(Translator::translate('LOGIN'));
    }

    public function subscribeNewsletterInUserAccount(AcceptanceTester $I): void
    {
        $start = new Start($I);
        $I->wantToTest('newsletter subscription in my account navigation');

        $userData = $this->getExistingUserData();

        $newsletterSettingsPage = $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openNewsletterSettingsPage();
        $I->seeText(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION'));
        $newsletterSettingsPage->seeNewsletterUnSubscribed();

        //subscribe for a newsletter
        $newsletterSettingsPage->subscribeNewsletter()
            ->seeNewsletterSubscribed();

        //unsubscribe a newsletter
        $newsletterSettingsPage->unSubscribeNewsletter()
            ->seeNewsletterUnSubscribed();
    }

    public function changeUserBillingAddress(AcceptanceTester $I): void
    {
        $start = new Start($I);
        $I->wantToTest('user billing address in my account');

        $I->updateConfigInDatabase('blShowBirthdayFields', true, 'bool');
        $I->updateConfigInDatabase('blVatIdCheckDisabled', true, 'bool');
        /** Change Germany and Belgium to non EU country to skip online VAT validation. */
        $I->updateInDatabase('oxcountry', ['oxvatstatus' => 0], ['OXID' => 'a7c40f632e04633c9.47194042']);
        $I->updateInDatabase('oxcountry', ['oxvatstatus' => 0], ['OXID' => 'a7c40f631fc920687.20179984']);

        $existingUserData = $this->getExistingUserData();

        $userAddressPage = $start
            ->loginOnStartPage($existingUserData['userLoginName'], $existingUserData['userPassword'])
            ->openAccountPage()
            ->openUserAddressPage()
            ->openUserBillingAddressForm();
        $I->seeText('Germany', $userAddressPage->billCountryId);
        $I->seeText(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

        $userLoginData['userLoginNameField'] = $existingUserData['userLoginName'];
        $addressData = $this->getUserAddressData('1', 'Belgium');
        $userData = $this->getUserData('1');
        $userData['userUstIDField'] = 'BE0410521222';
        $userAddressPage = $userAddressPage
            ->enterUserData($userData)
            ->enterAddressData($addressData)
            ->saveAddress()
            ->validateUserBillingAddress(array_merge($addressData, $userData, $userLoginData));

        $userData['userUstIDField'] = '';
        $addressData['UserFirstName'] = $existingUserData['userName'];
        $addressData['UserLastName'] = $existingUserData['userLastName'];
        $userAddressPage = $userAddressPage->openUserBillingAddressForm()
            ->enterUserData($userData)
            ->enterAddressData($addressData)
            ->selectBillingCountry('Germany')
            ->saveAddress();
        $I->seeText('Germany', $userAddressPage->billingAddress);
    }

    #[Group('user_account_address', 'exclude_from_compilation')]
    public function modifyUserShippingAddress(AcceptanceTester $I): void
    {
        $start = new Start($I);
        $I->wantToTest('user shipping address in my account');

        $userData = $this->getExistingUserData();

        $userAddressPage = $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openUserAddressPage()
            ->seeNumberOfShippingAddresses(0)
            ->openUserBillingAddressForm();
        $I->seeText('Germany', $userAddressPage->billCountryId);
        $I->seeText(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

        $deliveryAddressData = $this->getUserAddressData('1_2');

        $userAddressPage = $userAddressPage
            ->openShippingAddressForm()
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress()
            ->validateUserDeliveryAddress($deliveryAddressData);

        $deliveryAddressData = $this->getUserAddressData('1_4');

        $userAddressPage->selectShippingAddress(1)
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress()
            ->validateUserDeliveryAddress($deliveryAddressData);
    }

    #[Group('user_account_address', 'exclude_from_compilation')]
    public function createAndDeleteUserShippingAddress(AcceptanceTester $I): void
    {
        $start = new Start($I);
        $I->wantToTest('user shipping address create and delete');

        $userData = $this->getExistingUserData();

        $userAddressPage = $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openUserAddressPage()
            ->seeNumberOfShippingAddresses(0)
            ->openUserBillingAddressForm();
        $I->seeText('Germany', $userAddressPage->billCountryId);
        $I->seeText(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

        $deliveryAddressData = $this->getUserAddressData('1_2');

        $userAddressPage = $userAddressPage
            ->openShippingAddressForm()
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress()
            ->validateUserDeliveryAddress($deliveryAddressData);

        $userAddressPage->seeNumberOfShippingAddresses(1)
            ->selectShippingAddress(1)
            ->deleteShippingAddress(1)
            ->seeNumberOfShippingAddresses(0);
    }

    private function getExistingUserData()
    {
        return Fixtures::get('existingUser');
    }

    private function getExistingGuestUserData(): array
    {
        return Fixtures::get('existingGuestUser');
    }

    private function getAdminUserData(): array
    {
        return Fixtures::get('adminUser');
    }

    private function getUserData(string $userId): array
    {
        return [
            'userUstIDField' => '',
            'userMobFonField' => '111-111111-' . $userId,  //still needed?
            'userPrivateFonField' => '11111111' . $userId,
            'userBirthDateDayField' => random_int(10, 28),
            'userBirthDateMonthField' => random_int(8, 10),
            'userBirthDateYearField' => random_int(1960, 2000),
        ];
    }

    private function getUserAddressData(string $userId, string $userCountry = 'Germany'): array
    {
        $addressData = [
            'userSalutation' => 'Mrs',
            'userFirstName' => 'user' . $userId . ' name_šÄßüл',
            'userLastName' => 'user' . $userId . ' last name_šÄßüл',
            'companyName' => 'user' . $userId . ' company_šÄßüл',
            'street' => 'user' . $userId . ' street_šÄßüл',
            'streetNr' => $userId . '-' . $userId,
            'ZIP' => '1234' . $userId,
            'city' => 'user' . $userId . ' city_šÄßüл',
            'additionalInfo' => 'user' . $userId . ' additional info_šÄßüл',
            'fonNr' => '111-111-' . $userId,
            'faxNr' => '111-111-111-' . $userId,
            'countryId' => $userCountry,
        ];
        if ($userCountry === 'Germany') {
            $addressData['stateId'] = 'Berlin';
        }
        return $addressData;
    }
}
