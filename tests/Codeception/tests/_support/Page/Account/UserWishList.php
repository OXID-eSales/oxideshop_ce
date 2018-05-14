<?php
namespace Page\Account;

use Page\Header\MiniBasket;
use Page\Page;
use Page\ProductDetails;

class UserWishList extends Page
{
    use MiniBasket;

    // include url of current page
    public static $URL = '/en/my-wish-list/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $productTitle = '#noticelistProductList_%s';

    public static $productDescription = '//div[@id="noticelistProductList"]/div[%s]/div/form[1]/div[2]/div[2]/div[2]';

    public static $productPrice = '#productPrice_noticelistProductList_%s';

    public static $basketAmount = '#amountToBasket_noticelistProductList_%s';

    public static $toBasketButton = '#toBasket_noticelistProductList_%s';

    public static $removeButton = '//button[@triggerform="remove_tonoticelistnoticelistProductList_%s"]';

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

    /**
     * @param integer $itemPosition
     *
     * @return $this
     */
    public function removeProductFromList($itemPosition)
    {
        $I = $this->user;
        $I->click(sprintf(self::$removeButton, $itemPosition));
        return $this;
    }

}
