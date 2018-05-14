<?php
namespace Page;

use Page\Header\AccountMenu;

class GiftRegistry extends Page
{
    use AccountMenu;

    // include url of current page
    public static $URL = '/en/gift-registry/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $productTitle = '#wishlistProductList_%s';

    public static $productDescription = '//div[@id="wishlistProductList"]/div[%s]/div/form[1]/div[2]/div[2]/div[2]';

    public static $productPrice = '#productPrice_wishlistProductList_%s';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($params)
    {
        return static::$URL.'/index.php?'.http_build_query($params);
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

}
