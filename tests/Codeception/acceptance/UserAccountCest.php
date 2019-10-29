<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Step\Start;
use OxidEsales\Codeception\Module\Translation\Translator;

class UserAccountCest
{
    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function loginUserInFrontend(AcceptanceTester $I)
    {
        $I->wantToTest('user login (popup in top of the page)');

        $startPage = $I->openShop();

        //login when username/pass are incorrect. error msg should be in place etc.
        $startPage->loginUser('non-existing-user@oxid-esales.dev', '');
        $I->see(Translator::translate('LOGIN'));
        $I->see(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $startPage->badLoginError);

        //login with correct user name/pass
        $userData = $this->getExistingUserData();
        $startPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->dontSee(Translator::translate('LOGIN'));

        $accountPage = $startPage->openAccountPage();
        $breadCrumb = Translator::translate('MY_ACCOUNT').' - '.$userData['userLoginName'];
        $accountPage->seeOnBreadCrumb($breadCrumb);
        $I->see(Translator::translate('LOGOUT'));
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function changeUserAccountPassword(AcceptanceTester $I)
    {
        $I->wantTo('change user password in my account navigation');

        $userData = $this->getExistingUserData();
        $userName = $userData['userLoginName'];
        $userPassword = $userData['userPassword'];

        $startPage = $I->openShop()
            ->loginUser($userName, $userPassword);
        $I->dontSee(Translator::translate('LOGIN'));

        $accountPage = $startPage->openAccountPage();
        $breadCrumb = Translator::translate('MY_ACCOUNT').' - '.$userName;
        $accountPage->seeOnBreadCrumb($breadCrumb);

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

        $changePasswordPage->logoutUser();

        // try to login with old password
        $changePasswordPage->loginUser($userName, $userPassword);
        $I->see(Translator::translate('LOGIN'));
        $I->see(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $changePasswordPage->badLoginError);

        // try to login with new password
        $changePasswordPage->loginUser($userName, 'user1user');
        $I->dontSee(Translator::translate('LOGIN'));

        //reset new pass to old one
        $changePasswordPage->changePassword('user1user', $userPassword, $userPassword);
        $I->see(Translator::translate('MESSAGE_PASSWORD_CHANGED'));
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function sendUserPasswordReminder(AcceptanceTester $I)
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
        $I->see(Translator::translate('PASSWORD_WAS_SEND_TO').' '.$userData['userLoginName']);

        //open password reminder page in main user account page
        $passwordReminderPage->openAccountPage()
            ->openUserPasswordReminderPage();
        $I->see(Translator::translate('HAVE_YOU_FORGOTTEN_PASSWORD'));
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function changeUserEmailInBillingAddress(AcceptanceTester $I)
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
        $userAddressPage = $userAddressPage->changeEmail("example01@oxid-esales.dev", $userData['userPassword']);

        $I->dontSee(Translator::translate('COMPLETE_MARKED_FIELDS'));
        $userAddressPage = $userAddressPage->logoutUser();

        //try to login with old and new email address
        $userAddressPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('LOGIN'));
        $I->see(Translator::translate('ERROR_MESSAGE_USER_NOVALIDLOGIN'), $userAddressPage->badLoginError);
        //login with new email address
        $userAddressPage->loginUser('example01@oxid-esales.dev', $userData['userPassword']);
        $I->dontSee(Translator::translate('LOGIN'));

        //change password back to original
        $userAddressPage->openUserBillingAddressForm()
            ->changeEmail("example_test@oxid-esales.dev", $userData['userPassword'])
            ->logoutUser();
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester $I
     */
    public function subscribeNewsletterInUserAccount(AcceptanceTester $I)
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
     *
     * @param AcceptanceTester $I
     */
    public function changeUserBillingAddress(AcceptanceTester $I)
    {
        $start = new Start($I);
        $I->wantToTest('user billing address in my account');

        /** Change Germany and Belgium to non EU country to skip online VAT validation. */
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 0], ["OXID" => 'a7c40f632e04633c9.47194042']);
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 0], ["OXID" => 'a7c40f631fc920687.20179984']);

        $existingUserData = $this->getExistingUserData();

        $userAddressPage = $start->loginOnStartPage($existingUserData['userLoginName'], $existingUserData['userPassword'])
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
     *
     * @param AcceptanceTester $I
     */
    public function modifyUserShippingAddress(AcceptanceTester $I)
    {
        $start = new Start($I);
        $I->wantToTest('user shipping address in my account');

        $userData = $this->getExistingUserData();

        $userAddressPage = $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage()
            ->openUserAddressPage()
            ->openUserBillingAddressForm();
        $I->see('Germany', $userAddressPage->billCountryId);
        $I->see(Translator::translate('PLEASE_SELECT_STATE'), $userAddressPage->billStateId);

        //create first new delivery address
        $deliveryAddressData = $this->getUserAddressData('1_2');

        $userAddressPage = $userAddressPage
            ->openShippingAddressForm()
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress()
            ->validateUserDeliveryAddress($deliveryAddressData);

        //create second new delivery address
        $deliveryAddressData = $this->getUserAddressData('1_3');
        $userAddressPage = $userAddressPage
            ->selectNewShippingAddress()
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress();
        $I->seeElement(sprintf($userAddressPage->shippingAddress, 3));

        //change existing delivery address
        $deliveryAddressData = $this->getUserAddressData('1_4');

        $userAddressPage->selectShippingAddress(1)
            ->enterShippingAddressData($deliveryAddressData)
            ->saveAddress()
            ->validateUserDeliveryAddress($deliveryAddressData, 1);
    }

    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
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
            "userSalutation" => 'Mrs',
            "userFirstName" => "user$userId name_šÄßüл",
            "userLastName" => "user$userId last name_šÄßüл",
            "companyName" => "user$userId company_šÄßüл",
            "street" => "user$userId street_šÄßüл",
            "streetNr" => "$userId-$userId",
            "ZIP" => "1234$userId",
            "city" => "user$userId city_šÄßüл",
            "additionalInfo" => "user$userId additional info_šÄßüл",
            "fonNr" => "111-111-$userId",
            "faxNr" => "111-111-111-$userId",
            "countryId" => $userCountry,
        ];
        if ( $userCountry == 'Germany' ) {
            $addressData["stateId"] = "Berlin";
        }
        return $addressData;
    }

    public function _failed(AcceptanceTester $I)
    {
        $this->cleanUpUserData($I);
    }

    protected function cleanUpUserData(AcceptanceTester $I)
    {
        /** Change Germany and Belgium data to original. */
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 1], ["OXID" => 'a7c40f632e04633c9.47194042']);
        $I->updateInDatabase('oxcountry', ["oxvatstatus" => 1], ["OXID" => 'a7c40f631fc920687.20179984']);
    }
}