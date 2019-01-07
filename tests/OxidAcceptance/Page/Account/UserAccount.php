<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;

class UserAccount extends Page
{
    use AccountMenu, AccountNavigation;

    protected $webElementName = 'WebElement\UserAccount';

    /**
     * @return UserLogin
     */
    public function logoutUser()
    {
        $I = $this->user;
        $this->openAccountMenu();
        $I->click($I->translate('LOGOUT'));
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('LOGIN');
        $I->see($breadCrumb, UserLogin::$breadCrumb);
        return new UserLogin($I);
    }

    /**
     * Opens my-password page
     *
     * @return UserChangePassword
     */
    public function openChangePasswordPage()
    {
        $I = $this->user;
        $I->click($this->webElement->dashboardChangePasswordPanelHeader);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('CHANGE_PASSWORD');
        $I->see($breadCrumb, UserChangePassword::$breadCrumb);
        return new UserChangePassword($I);
    }
}
