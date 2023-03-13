<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Step\Start;
use OxidEsales\Codeception\Module\Translation\Translator;

final class UserAccountCest
{
    /**
     * @group myAccount
     */
    public function loginUserInFrontend(AcceptanceTester $I): void
    {
        $I->wantToTest('user login (popup in top of the page)');

        $startPage = $I->openShop();

        //login when username/pass are incorrect. error msg should be in place etc.
        $startPage->loginUser('non-existing-user@oxid-esales.dev', '')
            ->seeUserLoggedOut();
        $I->see(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $startPage->badLoginError);

        //login with correct user name/pass
        $userData = $this->getExistingUserData();
        $startPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeUserLoggedIn();

        $startPage->openAccountPage()->seePageOpened()->seeUserAccount($userData);
    }

    /**
     * @group myAccount
     */
    public function changeUserAccountPassword(AcceptanceTester $I): void
    {
        $I->wantTo('change user password in my account navigation');

        $userData = $this->getExistingUserData();
        $userName = $userData['userLoginName'];
        $userPassword = $userData['userPassword'];

        $startPage = $I->openShop()
            ->loginUser($userName, $userPassword);
        $I->dontSee(Translator::translate('LOGIN'));

        $accountPage = $startPage->openAccountPage()->seePageOpened()->seeUserAccount($userData);

        $changePasswordPage = $accountPage->openChangePasswordPage();

        //entered not matching new passwords
        $changePasswordPage->fillPasswordFields($userPassword, 'user1user', 'useruser');
        $I->see(Translator::translate('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

        //new pass is too short
        $changePasswordPage->changePassword($userPassword, 'user', 'user');
        $I->see(Translator::translate('ERROR_MESSAGE_PASSWORD_TOO_SHORT'));

        //correct new pass
        $changePasswordPage->changePassword($userPassword, 'user1user', 'user1user');
        $I->see(Translator::translate('MESSAGE_PASSWORD_CHANGED'));

        $loginPage = $changePasswordPage->openAccountPage()->logoutUserInAccountPage();

        // try to login with old password
        $loginPage = $loginPage->loginWithError($userName, $userPassword);
        $I->see(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'));

        // try to login with new password
        $changePasswordPage = $loginPage->login($userName, 'user1user')->openChangePasswordPage();
        $I->dontSee(Translator::translate('LOGIN'));

        //reset new pass to old one
        $changePasswordPage->changePassword('user1user', $userPassword, $userPassword);
        $I->see(Translator::translate('MESSAGE_PASSWORD_CHANGED'));
    }

    /**
     * @group myAccount
     */
    public function sendUserPasswordReminder(AcceptanceTester $I): void
    {
        $I->wantToTest('user password reminder in my account navigation');

        $userData = $this->getExistingUserData();

        $startPage = $I->openShop();

        //open password reminder page in account menu popup
        $passwordReminderPage = $startPage->openUserPasswordReminderPage();
        $I->see(Translator::translate('HAVE_YOU_FORGOTTEN_PASSWORD'));

        //enter not existing email
        $passwordReminderPage = $passwordReminderPage->resetPassword('not_existing_user@oxid-esales.dev');
        $I->see(Translator::translate('ERROR_MESSAGE_PASSWORD_EMAIL_INVALID'));

        //enter existing email
        $passwordReminderPage = $passwordReminderPage->resetPassword($userData['userLoginName']);
        $I->see(Translator::translate('PASSWORD_WAS_SEND_TO') . ' ' . $userData['userLoginName']);

        //open password reminder page in main user account page
        $passwordReminderPage->openUserPasswordReminderPage();
        $I->see(Translator::translate('HAVE_YOU_FORGOTTEN_PASSWORD'));
    }

    /**
     * @group myAccount
     */
    public function changeUserEmailInBillingAddress(AcceptanceTester $I): void
    {
        $I->wantTo('change user email in my account');

        $userData = $this->getExistingUserData();

        $userAddressPage = $I->openShop()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openUserAddressPage()
            ->openUserBillingAddressForm();
        $I->see('Germany', $userAddressPage->billCountryId);
        $I->see(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

        //change user password
        $userAddressPage = $userAddressPage->changeEmail('example02@oxid-esales.dev', $userData['userPassword']);

        $I->dontSee(Translator::translate('COMPLETE_MARKED_FIELDS'));
        $userAddressPage = $userAddressPage->logoutUser();

        //try to login with old and new email address
        $userAddressPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('LOGIN'));
        $I->see(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $userAddressPage->badLoginError);
        //login with new email address
        $userAddressPage->loginUser('example02@oxid-esales.dev', $userData['userPassword']);
        $I->dontSee(Translator::translate('LOGIN'));

        //change password back to original
        $userAddressPage->openUserBillingAddressForm()
            ->changeEmail('example_test@oxid-esales.dev', $userData['userPassword'])
            ->logoutUser();
    }

    /**
     * @group myAccount
     */
    public function subscribeNewsletterInUserAccount(AcceptanceTester $I): void
    {
        $start = new Start($I);
        $I->wantToTest('newsletter subscription in my account navigation');

        $userData = $this->getExistingUserData();

        $newsletterSettingsPage = $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openNewsletterSettingsPage();
        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION'));
        $newsletterSettingsPage->seeNewsletterUnSubscribed();

        //subscribe for a newsletter
        $newsletterSettingsPage->subscribeNewsletter()
            ->seeNewsletterSubscribed();

        //unsubscribe a newsletter
        $newsletterSettingsPage->unSubscribeNewsletter()
            ->seeNewsletterUnSubscribed();
    }

    /**
     * @group myAccount
     *
     * @after cleanUpUserData
     */
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
        $I->see('Germany', $userAddressPage->billCountryId);
        $I->see(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

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
        $I->see('Germany', $userAddressPage->billingAddress);
    }

    /**
     * @group myAccount
     */
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
        $I->see('Germany', $userAddressPage->billCountryId);
        $I->see(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

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

    /**
     * @group myAccount
     */
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
        $I->see('Germany', $userAddressPage->billCountryId);
        $I->see(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

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

    public function _after(AcceptanceTester $I)
    {
        $this->cleanUpUserData($I);
    }

    private function cleanUpUserData(AcceptanceTester $I): void
    {
        /** Change Germany and Belgium data to original. */
        $I->updateInDatabase('oxcountry', ['oxvatstatus' => 1], ['OXID' => 'a7c40f632e04633c9.47194042']);
        $I->updateInDatabase('oxcountry', ['oxvatstatus' => 1], ['OXID' => 'a7c40f631fc920687.20179984']);
        $userData = $this->getExistingUserData();
        $I->deleteFromDatabase('oxaddress', ['OXUSERID' => $userData['userId']]);
    }

    private function getExistingUserData()
    {
        return Fixtures::get('existingUser');
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

    private function getUserAddressData(string $userId, $userCountry = 'Germany'): array
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
