<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class UserWishList
{
    // include url of current page
    public $URL = '/en/my-wish-list/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $productTitle = '#noticelistProductList_%s';

    public $productDescription = '//div[@id="noticelistProductList"]/div[%s]/div/form[1]/div[2]/div[2]/div[2]';

    public $productPrice = '#productPrice_noticelistProductList_%s';

    public $basketAmount = '#amountToBasket_noticelistProductList_%s';

    public $toBasketButton = '#toBasket_noticelistProductList_%s';

    public $removeButton = '//button[@triggerform="remove_tonoticelistnoticelistProductList_%s"]';

}
