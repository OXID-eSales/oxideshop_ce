<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class UserAddress
{
    // include url of current page
    public $URL = '/en/my-address/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $openBillingAddressFormButton = '#userChangeAddress';

    public $userEmail = 'invadr[oxuser__oxusername]';

    public $userPassword = '//input[@name="user_password"]';

    public $saveUserAddressButton = '#accUserSaveTop';

    public $billingAddress = '#addressText';

    public $shippingAddress = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[1]';

    public $openShipAddressPanel = '#showShipAddress';

    public $shipAddressPanel = '#shippingAddress';

    public $shipAddressForm = '#shippingAddressForm';

    public $openShipAddressForm = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[1]/button[1]';

    public $deleteShipAddress = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[1]/button[2]';

    public $selectShipAddress = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[2]/label';

    public $newShipAddressForm = '//div[@class="panel panel-default dd-add-delivery-address"]';

}
