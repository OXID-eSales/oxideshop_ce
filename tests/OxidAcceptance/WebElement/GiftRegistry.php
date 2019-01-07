<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class GiftRegistry
{
    // include url of current page
    public $URL = '/en/gift-registry/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $productTitle = '#wishlistProductList_%s';

    public $productDescription = '//div[@id="wishlistProductList"]/div[%s]/div/form[1]/div[2]/div[2]/div[2]';

    public $productPrice = '#productPrice_wishlistProductList_%s';

}
