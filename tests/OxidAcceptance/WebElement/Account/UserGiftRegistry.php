<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class UserGiftRegistry
{
    // include url of current page
    public $URL = '/en/my-gift-registry/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $publicSelection = '#wishlist_blpublic';

    public $saveButton = '';

    public $giftRegistrySearch = '#input_account_wishlist';

    public $searchButton = '';

    public $foundListLink = '//ul[@class="wishlistResults"]/li/a';

    public $recipientName = 'editval[rec_name]';

    public $recipientEmail = 'editval[rec_email]';

    public $emailMessage = 'editval[send_message]';

    public $sendEmailButton = '';

    public $removeFromGitRegistry = '//button[@triggerform="remove_towishlistwishlistProductList_%s"]';

    public $productTitle = '#wishlistProductList_%s';

    public $productDescription = '//div[@id="wishlistProductList"]/div[%s]/div/form[1]/div[2]/div[2]/div[2]';

    public $productPrice = '#productPrice_wishlistProductList_%s';

    public $basketAmount = '#amountToBasket_wishlistProductList_%s';

    public $toBasketButton = '#toBasket_wishlistProductList_%s';

}
