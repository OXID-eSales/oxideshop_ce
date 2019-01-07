<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class UserAccount
{
    // include url of current page
    public $URL = '/en/my-account/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $dashboardChangePasswordPanelHeader = '#linkAccountPassword';

    public $dashboardCompareListPanelHeader = '//div[@class="accountDashboardView"]/div/div[2]/div[3]/div[1]';

    public $dashboardCompareListPanelContent = '//div[@class="accountDashboardView"]/div/div[2]/div[3]/div[2]';

    public $dashboardWishListPanelHeader = '//div[@class="accountDashboardView"]/div/div[2]/div[1]/div[1]';

    public $dashboardWishListPanelContent = '//div[@class="accountDashboardView"]/div/div[2]/div[1]/div[2]';

    public $dashboardGiftRegistryPanelHeader = '//div[@class="accountDashboardView"]/div/div[2]/div[2]/div[1]';

    public $dashboardGiftRegistryPanelContent = '//div[@class="accountDashboardView"]/div/div[2]/div[2]/div[2]';

}
