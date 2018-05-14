<?php
namespace Step\Acceptance;

use Page\UserRegistration as UserRegistrationPage;

class UserRegistration extends \AcceptanceTester
{
    public function registerUser($userLoginDataToFill, $userDataToFill, $addressDataToFill)
    {
        $I = $this;
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ":" . $I->translate("PAGE_TITLE_REGISTER");
        $registrationPage = new \Page\UserRegistration($I);
        $registrationPage->enterUserLoginData($userLoginDataToFill)
            ->enterUserData($userDataToFill)
            ->enterAddressData($addressDataToFill)
            ->registerUser();

        $I->see($breadCrumbName, $registrationPage::$breadCrumb);
        $I->see($I->translate('MESSAGE_WELCOME_REGISTERED_USER'));
    }
}