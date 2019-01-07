<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;

class UserChangePassword extends Page
{
    use AccountMenu;

    protected $webElementName = 'WebElement\UserChangePassword';

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
        $I->fillField($this->webElement->userOldPassword, $oldPassword);
        $I->fillField($this->webElement->userNewPassword, $newPassword);
        $I->fillField($this->webElement->userConfirmNewPassword, $confirmPassword);
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
        $I->click($this->webElement->userChangePasswordButton);
        return $this;
    }

}
