<?php
namespace Page\Account;

use Page\Header\Navigation;
use Page\Page;

class UserPasswordReminder extends Page
{
    use Navigation;

    // include url of current page
    public static $URL = '/en/forgot-password/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $forgotPasswordUserEmail = '#forgotPasswordUserLoginName';

    public static $resetPasswordButton = '';

    /**
     * @param $userEmail
     *
     * @return $this
     */
    public function resetPassword($userEmail)
    {
        $I = $this->user;
        $I->fillField(self::$forgotPasswordUserEmail, $userEmail);
        $I->click($I->translate('REQUEST_PASSWORD'));
        return $this;
    }

}
