<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;

class UserLogin extends Page
{
    protected $webElementName = 'WebElement\UserLogin';

    /**
     * @param $userName
     * @param $userPassword
     *
     * @return UserAccount
     */
    public function login($userName, $userPassword)
    {
        $I = $this->user;
        $I->fillField($this->webElement->userAccountLoginName, $userName);
        $I->fillField($this->webElement->userAccountLoginPassword, $userPassword);
        $I->click($this->webElement->userAccountLoginButton);
        $I->dontSee($I->translate('LOGIN'));
        return new UserAccount($I);
    }

    /**
     * Opens forgot-password page
     *
     * @return UserPasswordReminder
     */
    public function openUserPasswordReminderPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click($this->webElement->userForgotPasswordLink);
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ":" . $I->translate("FORGOT_PASSWORD");
        $I->see($breadCrumbName, UserPasswordReminder::$breadCrumb);
        return new UserPasswordReminder($I);
    }

}
