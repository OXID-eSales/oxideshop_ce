<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Page\PrivateSales\Login;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Step\Start;

class PrivateSalesCest
{
    /**
     * @group privateSales
     *
     * @after disablePrivateSales
     * @fails disablePrivateSales
     *
     * @param AcceptanceTester $I
     */
    public function sendInvitationEmail(AcceptanceTester $I)
    {
        $I->wantToTest('invitations functionality of the private sales.');

        $I->updateConfigInDatabase('iUseGDVersion', '2', 'str');

        //enabling functionality
        $I->updateConfigInDatabase('blInvitationsEnabled', true, 'bool');
        $I->updateConfigInDatabase('dPointsForInvitation', '5', 'str');
        $I->updateConfigInDatabase('dPointsForRegistration', '5', 'str');

        $userData = $this->getExistingUserData();
        $senderData = [
            'sender_name' => 'example_test',
            'sender_email' => $userData['userLoginName'],
            'sender_message' => 'Invitation to shop',
        ];

        $startStep = new Start($I);
        $homePage = $startStep->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);
        $invitationPage = $homePage->openPrivateSalesInvitationPage();
        $invitationPage = $invitationPage->sendInvitationEmail(['example@oxid-esales.dev'], $senderData);

        $I->waitForText(Translator::translate('INVITE_YOUR_FRIENDS'));
        $I->see(Translator::translate('MESSAGE_INVITE_YOUR_FRIENDS_INVITATION_SENT'));

        $invitationPage = $invitationPage->logoutUser();
        $I->dontSee(Translator::translate('INVITE_YOUR_FRIENDS'), $invitationPage->headerTitle);
        $breadCrumb = Translator::translate('INVITE_YOUR_FRIENDS');
        $invitationPage->seeOnBreadCrumb($breadCrumb);
    }

    /**
     * @group privateSales
     *
     * @after disablePrivateSales
     * @fails disablePrivateSales
     *
     * @param AcceptanceTester $I
     */
    public function registerAndLogin(AcceptanceTester $I)
    {
        $I->wantToTest('registration and login functionality of the private sales.');

        $I->updateConfigInDatabase('blPsLoginEnabled', true, 'bool');
        $I->updateConfigInDatabase('blConfirmAGB', true, 'bool');

        $userData = $this->getExistingUserData();

        $privateSalesLoginPage = new Login($I);
        $I->amOnPage($privateSalesLoginPage->URL);

        $I->dontSee(Translator::translate('HOME'));
        $I->dontSee(Translator::translate('START_BARGAIN_HEADER'));

        //forgot password functionality
        $passwordReminderPage = $privateSalesLoginPage->openUserPasswordReminderPage();
        $I->see(Translator::translate('FORGOT_PASSWORD'));
        $passwordReminderPage = $passwordReminderPage->resetPassword($userData['userLoginName']);
        $I->see(Translator::translate('PASSWORD_WAS_SEND_TO').' '.$userData['userLoginName']);

        $privateSalesLoginPage = $passwordReminderPage->goBackToShop();

        $I->dontSee(Translator::translate('HOME'));

        //login to shop
        $breadCrumb = Translator::translate('MY_ACCOUNT');
        $accountPage = $privateSalesLoginPage
            ->login($userData['userLoginName'], $userData['userPassword'])
            ->confirmAGB()
            ->seeOnBreadCrumb($breadCrumb);
        $I->see(Translator::translate('HOME'));
        $accountPage->logoutUser();

        //register new user
        $userLoginDataToFill = $this->getUserLoginData();
        $addressDataToFill = $this->getUserAddressData();

        $I->amOnPage($privateSalesLoginPage->URL);
        $registrationPage = $privateSalesLoginPage->openRegistrationPage();

        $registrationPage = $registrationPage->enterUserLoginData($userLoginDataToFill)
            ->enterAddressData($addressDataToFill)
            ->registerUser();
        $I->see(Translator::translate('READ_AND_CONFIRM_TERMS'));

        $registrationPage = $registrationPage->confirmAGB()
            ->confirmNewsletterSubscription()
            ->registerUser();
        $I->see(Translator::translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

        $registrationPage->enterUserLoginData($userLoginDataToFill)
            ->registerUser();
        $I->see(Translator::translate('MESSAGE_CONFIRMING_REGISTRATION'));
    }

    /**
     * @return mixed
     */
    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
    }

    protected function disablePrivateSales(AcceptanceTester $I)
    {
        $I->updateConfigInDatabase('blInvitationsEnabled', false, 'bool');
        $I->updateConfigInDatabase('dPointsForInvitation', '0', 'str');
        $I->updateConfigInDatabase('dPointsForRegistration', '0', 'str');
        $I->updateConfigInDatabase('blPsLoginEnabled', false, 'bool');
        $I->updateConfigInDatabase('blConfirmAGB', false, 'bool');
    }

    private function getUserLoginData()
    {
        return [
            "userLoginNameField" => "example01@oxid-esales.dev",
            "userPasswordField" => "111111",
        ];
    }

    private function getUserAddressData()
    {
        return [
            "userSalutation" => 'Mrs',
            "userFirstName" => "userName",
            "userLastName" => "userLastName",
            "street" => "street",
            "streetNr" => "10",
            "ZIP" => "3000",
            "city" => "city",
            "countryId" => 'Germany',
        ];
    }
}
