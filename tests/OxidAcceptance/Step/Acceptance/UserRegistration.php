<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Step\Acceptance;

class UserRegistration extends \AcceptanceTester
{
    public function registerUser($userLoginDataToFill, $userDataToFill, $addressDataToFill)
    {
        $I = $this;
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ":" . $I->translate("PAGE_TITLE_REGISTER");
        $registrationPage = new \OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\UserRegistration($I);
        $registrationPage->enterUserLoginData($userLoginDataToFill)
            ->enterUserData($userDataToFill)
            ->enterAddressData($addressDataToFill)
            ->registerUser();

        $I->see($breadCrumbName, $registrationPage::$breadCrumb);
        $I->see($I->translate('MESSAGE_WELCOME_REGISTERED_USER'));
    }
}