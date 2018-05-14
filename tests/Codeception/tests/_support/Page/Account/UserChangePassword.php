<?php
namespace Page\Account;

use Page\Header\AccountMenu;
use Page\Page;

class UserChangePassword extends Page
{
    use AccountMenu;

    // include url of current page
    public static $URL = '/en/my-password/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $userOldPassword = '#passwordOld';

    public static $userNewPassword = '#passwordNew';

    public static $userConfirmNewPassword = '#passwordNewConfirm';

    public static $userChangePasswordButton = '#savePass';

    public static $errorMessage = '//div[@class="alert alert-danger"]';

    /**
     * Fill the password fields.
     *
     * @param $oldPassword
     * @param $newPassword
     * @param $confirmPassword
     *
     * @return $this
     */
    public function enterPasswords($oldPassword, $newPassword, $confirmPassword)
    {
        $I = $this->user;
        $I->fillField(self::$userOldPassword, $oldPassword);
        $I->fillField(self::$userNewPassword, $newPassword);
        $I->fillField(self::$userConfirmNewPassword, $confirmPassword);
        return $this;
    }

    /**
     * Fill and submit the password fields.
     *
     * @param $oldPassword
     * @param $newPassword
     * @param $confirmPassword
     *
     * @return $this
     */
    public function changePassword($oldPassword, $newPassword, $confirmPassword)
    {
        $I = $this->user;
        $this->enterPasswords($oldPassword, $newPassword, $confirmPassword);
        $I->click(self::$userChangePasswordButton);
        return $this;
    }

}
