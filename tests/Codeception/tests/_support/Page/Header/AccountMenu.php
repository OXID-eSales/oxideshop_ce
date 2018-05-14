<?php
namespace Page\Header;

use Page\Account\ProductCompare;
use Page\Account\UserAccount;
use Page\Account\UserGiftRegistry;
use Page\Account\UserLogin;
use Page\Account\UserPasswordReminder;
use Page\Account\UserWishList;
use Page\UserRegistration;

trait AccountMenu
{
    public static $accountMenuButton = "//div[@class='menu-dropdowns pull-right']/div[3]/button";

    public static $openAccountMenuButton = "//div[@class='menu-dropdowns pull-right']/div[3]/ul";

    public static $userRegistrationLink = '#registerLink';

    public static $userLoginName = '#loginEmail';

    public static $userLoginPassword = '#loginPasword';

    public static $userForgotPasswordButton = '//a[@class="forgotPasswordOpener btn btn-default"]';

    public static $userLoginButton = '//div[@id="loginBox"]/button';

    public static $userLogoutButton = '';

    public static $badLoginError = '#errorBadLogin';

    public static $userAccountLink = '//ul[@id="services"]/li[1]/a';

    public static $userAccountCompareListLink = '//ul[@id="services"]/li[2]/a';

    public static $userAccountWishListLink = '//ul[@id="services"]/li[3]/a';

    public static $userAccountGiftRegistryLink = '//ul[@id="services"]/li[4]/a';

    public static $userAccountCompareListText = '//ul[@id="services"]/li[2]';

    public static $userAccountWishListText = '//ul[@id="services"]/li[3]';

    public static $userAccountGiftRegistryText = '//ul[@id="services"]/li[4]';

    /**
     * Opens open-account page.
     *
     * @return UserRegistration
     */
    public function openUserRegistrationPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userRegistrationLink);
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ":" . $I->translate("PAGE_TITLE_REGISTER");
        $I->see($breadCrumbName, UserRegistration::$breadCrumb);
        return new UserRegistration($I);
    }

    /**
     * Opens forgot-password page.
     *
     * @return UserPasswordReminder
     */
    public function openUserPasswordReminderPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userForgotPasswordButton);
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ":" . $I->translate("FORGOT_PASSWORD");
        $I->see($breadCrumbName, UserPasswordReminder::$breadCrumb);
        return new UserPasswordReminder($I);
    }

    /**
     * @param string $userName
     * @param string $userPassword
     *
     * @return $this
     */
    public function loginUser($userName, $userPassword)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        // logging in
        $this->openAccountMenu();
        $I->fillField(self::$userLoginName, $userName);
        $I->fillField(self::$userLoginPassword, $userPassword);
        $I->click(self::$userLoginButton);
        return $this;
    }

    /**
     * @return $this
     */
    public function logoutUser()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click($I->translate('LOGOUT'));
        return $this;
    }

    /**
     * Opens my-account page.
     *
     * @return UserAccount
     */
    public function openAccountPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userAccountLink);
        return new UserAccount($I);
    }

    /**
     * Opens my-gift-registry page.
     *
     * @return UserGiftRegistry
     */
    public function openUserGiftRegistryPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userAccountGiftRegistryLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('MY_GIFT_REGISTRY');
        $I->see($breadCrumb, UserGiftRegistry::$breadCrumb);
        $I->see($I->translate('PAGE_TITLE_ACCOUNT_WISHLIST'), UserGiftRegistry::$headerTitle);
        return new UserGiftRegistry($I);
    }

    /**
     * Opens my-wish-list page.
     *
     * @return UserWishList
     */
    public function openUserWishListPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userAccountWishListLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('MY_WISH_LIST');
        $I->see($breadCrumb, UserWishList::$breadCrumb);
        $I->see($I->translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), UserWishList::$headerTitle);
        return new UserWishList($I);
    }

    /**
     * Opens my-product-comparison page.
     *
     * @return ProductCompare
     */
    public function openProductComparePage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userAccountCompareListLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('PRODUCT_COMPARISON');
        $I->see($breadCrumb, ProductCompare::$breadCrumb);
        $I->see($I->translate('COMPARE'), ProductCompare::$headerTitle);
        return new ProductCompare($I);
    }

    /**
     * @return UserLogin
     */
    public function openUserLoginPage()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openAccountMenu();
        $I->click(self::$userAccountLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('LOGIN');
        $I->see($breadCrumb, UserLogin::$breadCrumb);
        return new UserLogin($I);
    }

    /**
     * @return $this
     */
    public function openAccountMenu()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click(self::$accountMenuButton);
        $I->waitForElement(self::$openAccountMenuButton);
        return $this;
    }

    /**
     * @return $this
     */
    public function closeAccountMenu()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click(self::$accountMenuButton);
        $I->waitForElement(self::$openAccountMenuButton);
        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function checkCompareListItemCount($count)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $cnt = ($count) ? ' '.$count : '';
        $I->see($I->translate('MY_PRODUCT_COMPARISON').$cnt, self::$userAccountCompareListText);
        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function checkWishListItemCount($count)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $cnt = ($count) ? ' '.$count : '';
        $I->see($I->translate('MY_WISH_LIST').$cnt, self::$userAccountWishListText);
        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function checkGiftRegistryItemCount($count)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $cnt = ($count) ? ' '.$count : '';
        $I->see($I->translate('MY_GIFT_REGISTRY').$cnt, self::$userAccountGiftRegistryText);
        return $this;
    }
}
