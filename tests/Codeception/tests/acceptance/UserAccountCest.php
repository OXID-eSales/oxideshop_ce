<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;

class UserAccountCest
{
    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function userLoginInFrontend(AcceptanceTester $I)
    {
        $I->wantToTest('user login (popup in top of the page)');

        $startPage = $I->openShop();

        //login when username/pass are incorrect. error msg should be in place etc.
        $startPage->loginUser('non-existing-user@oxid-esales.dev', '');
        $I->see($I->translate('LOGIN'));
        $I->see($I->translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $startPage::$badLoginError);

        //login with correct user name/pass
        $userData = $this->getExistingUserData();
        $startPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->dontSee($I->translate('LOGIN'));

        $accountPage = $startPage->openAccountPage();
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').' - '.$userData['userLoginName'];
        $I->see($breadCrumb, $accountPage::$breadCrumb);
        $I->see($I->translate('LOGOUT'));
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function userAccountChangePassword(AcceptanceTester $I)
    {
        $I->wantTo('change user password in my account navigation');

        $userData = $this->getExistingUserData();
        $userName = $userData['userLoginName'];
        $userPassword = $userData['userPassword'];

        $startPage = $I->openShop();

        //login with correct user name/pass
        $startPage->loginUser($userName, $userPassword);
        $I->dontSee($I->translate('LOGIN'));

        $accountPage = $startPage->openAccountPage();
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').' - '.$userName;
        $I->see($breadCrumb, $accountPage::$breadCrumb);

        $changePasswordPage = $accountPage->openChangePasswordPage();

        //entered diff new passwords
        $changePasswordPage->enterPasswords($userPassword, 'user1user', 'useruser');
        $I->see($I->translate('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

        //new pass is too short
        $changePasswordPage->changePassword($userPassword, 'user', 'user');
        $I->see($I->translate('ERROR_MESSAGE_PASSWORD_TOO_SHORT'));

        //correct new pass
        $changePasswordPage->changePassword($userPassword, 'user1user', 'user1user');
        $I->see($I->translate('MESSAGE_PASSWORD_CHANGED'));

        $changePasswordPage = $changePasswordPage->logoutUser();

        // try to login with old password
        $changePasswordPage = $changePasswordPage->loginUser($userName, $userPassword);
        $I->see($I->translate('LOGIN'));
        $I->see($I->translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $changePasswordPage::$badLoginError);

        // try to login with new password
        $changePasswordPage->loginUser($userName, 'user1user');
        $I->dontSee($I->translate('LOGIN'));

        //reset new pass to old one
        $changePasswordPage->changePassword('user1user', $userPassword, $userPassword);
        $I->see($I->translate('MESSAGE_PASSWORD_CHANGED'));

    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function userPasswordReminder(AcceptanceTester $I)
    {
        $I->wantToTest('user password reminder in my account navigation');

        $userData = $this->getExistingUserData();

        $startPage = $I->openShop();

        //open password reminder page in account menu popup
        $passwordReminderPage = $startPage->openUserPasswordReminderPage();
        $I->see($I->translate('HAVE_YOU_FORGOTTEN_PASSWORD'));

        //enter not existing email
        $passwordReminderPage = $passwordReminderPage->resetPassword('not_existing_user@oxid-esales.dev');
        $I->see($I->translate('ERROR_MESSAGE_PASSWORD_EMAIL_INVALID'));

        //enter existing email
        $passwordReminderPage = $passwordReminderPage->resetPassword($userData['userLoginName']);
        $I->see($I->translate('PASSWORD_WAS_SEND_TO').' '.$userData['userLoginName']);

        //open password reminder page in main user account page
        $startPage = $passwordReminderPage->openHomePage();
        $userAccountPage = $startPage->openAccountPage();
        $userAccountPage->openUserPasswordReminderPage();
        $I->see($I->translate('HAVE_YOU_FORGOTTEN_PASSWORD'));
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function userChangeEmailInBillingAddress(AcceptanceTester $I)
    {
        $I->wantTo('change user email in my account');

        $userData = $this->getExistingUserData();

        $startPage = $I->openShop();
        $startPage = $startPage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $accountPage = $startPage->openAccountPage();
        $userAddressPage = $accountPage->openUserAddressPage()->openUserBillingAddressForm();
        $I->see('Germany', $userAddressPage::$billCountryId);
        $I->see($I->translate('PLEASE_SELECT_STATE'), $userAddressPage::$billStateId);

        //change user password
        $userAddressPage = $userAddressPage->changeEmail("example01@oxid-esales.dev", $userData['userPassword']);

        //try to login with old and new email address
        $userAddressPage = $userAddressPage->logoutUser();
        $userAddressPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see($I->translate('LOGIN'));
        $I->see($I->translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $userAddressPage::$badLoginError);
        $userAddressPage->loginUser('example01@oxid-esales.dev', $userData['userPassword']);
        $I->dontSee($I->translate('LOGIN'));

        //change password back to original
        $userAddressPage = $userAddressPage->openUserBillingAddressForm();
        $userAddressPage = $userAddressPage->changeEmail("example_test@oxid-esales.dev", $userData['userPassword']);
        $userAddressPage = $userAddressPage->logoutUser();
        $userAddressPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->dontSee($I->translate('LOGIN'));
    }

    /**
     * @group myAccount
     *
     * @param Start $I
     */
    public function newsletterSubscriptionInUserAccount(Start $I)
    {
        $I->wantToTest('newsletter subscription in my account navigation');

        $userData = $this->getExistingUserData();

        $startPage = $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        $accountPage = $startPage->openAccountPage();
        $newsletterSettingsPage = $accountPage->openNewsletterSettingsPage();
        $I->see($I->translate('MESSAGE_NEWSLETTER_SUBSCRIPTION'));
        $newsletterSettingsPage->seeNewsletterUnSubscribed();

        //subscribe for a newsletter
        $newsletterSettingsPage->subscribeNewsletter()->seeNewsletterSubscribed();

        //unsubscribe a newsletter
        $newsletterSettingsPage->unSubscribeNewsletter()->seeNewsletterUnSubscribed();
    }

    /**
     * @group myAccount
     *
     * @param Start $I
     */
    public function userBillingAddress(Start $I)
    {
        $I->wantToTest('user billing address in my account');

        /** Change Germany and Belgium to non EU country to skip online VAT validation. */
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 0], ["OXID" => 'a7c40f632e04633c9.47194042']);
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 0], ["OXID" => 'a7c40f631fc920687.20179984']);

        $existingUserData = $this->getExistingUserData();

        $startPage = $I->loginOnStartPage($existingUserData['userLoginName'], $existingUserData['userPassword']);

        $accountPage = $startPage->openAccountPage();
        $userAddressPage = $accountPage->openUserAddressPage()->openUserBillingAddressForm();
        $I->see('Germany', $userAddressPage::$billCountryId);
        $I->see($I->translate('PLEASE_SELECT_STATE'), $userAddressPage::$billStateId);

        $userLoginData['userLoginNameField'] = $existingUserData['userLoginName'];
        $addressData = $this->getUserAddressData('1', 'Belgium');
        $userData = $this->getUserData('1');
        $userData['userUstIDField'] = 'BE0410521222';
        $userAddressPage = $userAddressPage
            ->enterUserData($userData)
            ->enterAddressData($addressData)
            ->saveAddress();
        $userAddressPage->validateUserBillingAddress(array_merge($addressData, $userData, $userLoginData));

        $userData['userUstIDField'] = '';
        $addressData['UserFirstName'] = $existingUserData['userName'];
        $addressData['UserLastName'] = $existingUserData['userLastName'];
        $userAddressPage = $accountPage->openUserAddressPage()->openUserBillingAddressForm();
        $userAddressPage->enterUserData($userData)
            ->enterAddressData($addressData)
            ->selectBillingCountry('Germany')
            ->saveAddress();
        $I->see('Germany', $userAddressPage::$billingAddress);

        /** Change Germany and Belgium data to original. */
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 1], ["OXID" => 'a7c40f632e04633c9.47194042']);
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 1], ["OXID" => 'a7c40f631fc920687.20179984']);
    }

    /**
     * @group myAccount
     *
     * @param Start $I
     */
    public function userShippingAddress(Start $I)
    {
        $I->wantToTest('user shipping address in my account');

        $userData = $this->getExistingUserData();

        $startPage = $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        $accountPage = $startPage->openAccountPage();
        $userAddressPage = $accountPage->openUserAddressPage()->openUserBillingAddressForm();
        $I->see('Germany', $userAddressPage::$billCountryId);
        $I->see($I->translate('PLEASE_SELECT_STATE'), $userAddressPage::$billStateId);

        //create first new delivery address
        $deliveryAddressData = $this->getUserAddressData('1_2');

        $userAddressPage = $userAddressPage
            ->openShippingAddressForm()
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress();
        $userAddressPage->validateUserDeliveryAddress($deliveryAddressData);

        //create second new delivery address
        $deliveryAddressData = $this->getUserAddressData('1_3');
        $userAddressPage = $accountPage->openUserAddressPage();

        $userAddressPage = $userAddressPage
            ->selectNewShippingAddress()
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress();
        $I->seeElement(sprintf($userAddressPage::$shippingAddress, 3));

        //change existing delivery address
        $deliveryAddressData = $this->getUserAddressData('1_4');

        $userAddressPage = $userAddressPage
            ->selectShippingAddress(1)
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress();
        $userAddressPage->validateUserDeliveryAddress($deliveryAddressData, 1);

        //TODO: delete existing delivery address
    }

    private function getUserLoginData($userId, $userPassword = 'user1user1')
    {
        $userLoginData = [
            "userLoginNameField" => "example".$userId."@oxid-esales.dev",
            "userPasswordField" => $userPassword,
        ];
        return $userLoginData;
    }

    private function getExistingUserData()
    {
        $userLoginData = [
            "userLoginName" => "example_test@oxid-esales.dev",
            "userPassword" => "useruser",
            "userName" => "UserNamešÄßüл",
            "userLastName" => "UserSurnamešÄßüл",
        ];
        return $userLoginData;
    }

    private function getUserData($userId)
    {
        $userData = [
            "userUstIDField" => "",
            "userMobFonField" => "111-111111-$userId",  //still needed?
            "userPrivateFonField" => "11111111$userId",
            "userBirthDateDayField" => rand(10, 28),
            "userBirthDateMonthField" => rand(10, 12),
            "userBirthDateYearField" => rand(1960, 2000),
        ];
        return $userData;
    }

    private function getUserAddressData($userId, $userCountry = 'Germany')
    {
        $addressData = [
            "UserSalutation" => 'Mrs',
            "UserFirstName" => "user$userId name_šÄßüл",
            "UserLastName" => "user$userId last name_šÄßüл",
            "CompanyName" => "user$userId company_šÄßüл",
            "Street" => "user$userId street_šÄßüл",
            "StreetNr" => "$userId-$userId",
            "ZIP" => "1234$userId",
            "City" => "user$userId city_šÄßüл",
            "AdditionalInfo" => "user$userId additional info_šÄßüл",
            "FonNr" => "111-111-$userId",
            "FaxNr" => "111-111-111-$userId",
            "CountryId" => $userCountry,
        ];
        if ( $userCountry == 'Germany' ) {
            $addressData["StateId"] = "BE";
        }
        return $addressData;
    }

}