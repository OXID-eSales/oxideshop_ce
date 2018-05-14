<?php
namespace Page\Account;

use Page\Page;

class UserLogin extends Page
{
    // include url of current page
    public static $URL = '/en/my-account/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $userAccountLoginName = '#loginUser';

    public static $userAccountLoginPassword = '#loginPwd';

    public static $userAccountLoginButton = '#loginButton';

    public static $userForgotPasswordLink = '#forgotPasswordLink';

    /**
     * @param $userName
     * @param $userPassword
     *
     * @return UserAccount
     */
    public function login($userName, $userPassword)
    {
        $I = $this->user;
        $I->fillField(self::$userAccountLoginName, $userName);
        $I->fillField(self::$userAccountLoginPassword, $userPassword);
        $I->click(self::$userAccountLoginButton);
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
        $I->click(self::$userForgotPasswordLink);
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ":" . $I->translate("FORGOT_PASSWORD");
        $I->see($breadCrumbName, UserPasswordReminder::$breadCrumb);
        return new UserPasswordReminder($I);
    }

}
