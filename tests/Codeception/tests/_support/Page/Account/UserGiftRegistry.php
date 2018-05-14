<?php
namespace Page\Account;

use Page\GiftRegistry;
use Page\Header\AccountMenu;
use Page\Header\MiniBasket;
use Page\Page;
use Page\ProductDetails;

class UserGiftRegistry extends Page
{
    use MiniBasket, AccountMenu;

    // include url of current page
    public static $URL = '/en/my-gift-registry/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $publicSelection = '#wishlist_blpublic';

    public static $saveButton = '';

    public static $giftRegistrySearch = '#input_account_wishlist';

    public static $searchButton = '';

    public static $foundListLink = '//ul[@class="wishlistResults"]/li/a';

    public static $recipientName = 'editval[rec_name]';

    public static $recipientEmail = 'editval[rec_email]';

    public static $emailMessage = 'editval[send_message]';

    public static $sendEmailButton = '';

    public static $removeFromGitRegistry = '//button[@triggerform="remove_towishlistwishlistProductList_%s"]';

    public static $productTitle = '#wishlistProductList_%s';

    public static $productDescription = '//div[@id="wishlistProductList"]/div[%s]/div/form[1]/div[2]/div[2]/div[2]';

    public static $productPrice = '#productPrice_wishlistProductList_%s';

    public static $basketAmount = '#amountToBasket_wishlistProductList_%s';

    public static $toBasketButton = '#toBasket_wishlistProductList_%s';

    /**
     * @param string $userName
     *
     * @return $this
     */
    public function searchForGiftRegistry($userName)
    {
        $I = $this->user;
        $I->fillField(self::$giftRegistrySearch, $userName);
        $I->click($I->translate('SEARCH'));
        return $this;
    }

    /**
     * Opens gift-registry page
     *
     * @return GiftRegistry
     */
    public function openFoundGiftRegistryList()
    {
        $I = $this->user;
        $I->click(self::$foundListLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('PUBLIC_GIFT_REGISTRIES');
        $I->see($breadCrumb, GiftRegistry::$breadCrumb);
        return new GiftRegistry($I);
    }

    /**
     * @param string $email
     * @param string $recipient
     * @param string $message
     *
     * @return $this
     */
    public function sendGiftRegistryEmail($email, $recipient, $message)
    {
        $I = $this->user;
        $this->openGiftRegistryEmailForm();
        $I->fillField(self::$recipientName, $recipient);
        $I->fillField(self::$recipientEmail, $email);
        $I->fillField(self::$emailMessage, $message);
        $I->click($I->translate('SUBMIT'));
        return $this;
    }

    /**
     * @return $this
     */
    public function openGiftRegistryEmailForm()
    {
        $I = $this->user;
        $I->click($I->translate('MESSAGE_SEND_GIFT_REGISTRY'));
        $I->waitForText($I->translate('SEND_GIFT_REGISTRY'));
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('MY_GIFT_REGISTRY');
        $I->see($breadCrumb, self::$breadCrumb);
        return $this;
    }

    /**
     * @param int $itemPosition
     *
     * @return $this
     */
    public function removeFromGiftRegistry($itemPosition)
    {
        $I = $this->user;
        $I->click(sprintf(self::$removeFromGitRegistry, $itemPosition));
        return $this;
    }

    /**
     * @return $this
     */
    public function makeListSearchable()
    {
        $I = $this->user;
        $I->selectOption(self::$publicSelection, 1);
        $I->click($I->translate('SAVE'));
        return $this;
    }

    /**
     * @return $this
     */
    public function makeListNotSearchable()
    {
        $I = $this->user;
        $I->selectOption(self::$publicSelection, 0);
        $I->click($I->translate('SAVE'));
        return $this;
    }

    /**
     * @param array $productData
     * @param int   $itemPosition
     *
     * @return $this
     */
    public function seeProductData($productData, $itemPosition = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf(self::$productTitle, $itemPosition));
        $I->see($productData['desc'], sprintf(self::$productDescription, $itemPosition));
        $I->see($productData['price'], sprintf(self::$productPrice, $itemPosition));
        return $this;
    }

    /**
     * @param integer $itemPosition
     *
     * @return ProductDetails
     */
    public function openProductDetailsPage($itemPosition)
    {
        $I = $this->user;
        $I->click(sprintf(self::$productTitle, $itemPosition));
        return new ProductDetails($I);
    }

    /**
     * @param integer $itemPosition
     * @param integer $amount
     *
     * @return $this
     */
    public function addProductToBasket($itemPosition, $amount)
    {
        $I = $this->user;
        $I->fillField(sprintf(self::$basketAmount, $itemPosition), $amount);
        $I->click(sprintf(self::$toBasketButton, $itemPosition));
        return $this;
    }

}
