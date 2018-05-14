<?php
namespace Page\Account;

use Page\Header\AccountMenu;
use Page\Page;

class UserAccount extends Page
{
    use AccountMenu, AccountNavigation;

    // include url of current page
    public static $URL = '/en/my-account/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $dashboardChangePasswordPanelHeader = '#linkAccountPassword';

    public static $dashboardCompareListPanelHeader = '//div[@class="accountDashboardView"]/div/div[2]/div[3]/div[1]';

    public static $dashboardCompareListPanelContent = '//div[@class="accountDashboardView"]/div/div[2]/div[3]/div[2]';

    public static $dashboardWishListPanelHeader = '//div[@class="accountDashboardView"]/div/div[2]/div[1]/div[1]';

    public static $dashboardWishListPanelContent = '//div[@class="accountDashboardView"]/div/div[2]/div[1]/div[2]';

    public static $dashboardGiftRegistryPanelHeader = '//div[@class="accountDashboardView"]/div/div[2]/div[2]/div[1]';

    public static $dashboardGiftRegistryPanelContent = '//div[@class="accountDashboardView"]/div/div[2]/div[2]/div[2]';

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
        $I->click(self::$dashboardChangePasswordPanelHeader);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('CHANGE_PASSWORD');
        $I->see($breadCrumb, UserChangePassword::$breadCrumb);
        return new UserChangePassword($I);
    }
}
