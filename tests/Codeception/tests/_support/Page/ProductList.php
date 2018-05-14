<?php
namespace Page;

use Page\Header\AccountMenu;

class ProductList extends Page
{
    use AccountMenu;

    // include url of current page
    public static $URL = '';

    public static $listItemTitle = '#productList_%s';

    public static $listItemDescription = '//form[@name="tobasketproductList_%s"]/div[2]/div[2]/div/div[@class="shortdesc"]';

    public static $listItemPrice = '//form[@name="tobasketproductList_%s"]/div[2]/div[2]/div/div[@class="price"]/div/span[@class="lead text-nowrap"]';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.'/index.php?'.http_build_query(['cl' => 'alist', 'cnid' => $param]);
    }

    /**
     * @param array $productData
     * @param int   $itemId      The position of the item in the list.
     *
     * @return $this
     */
    public function seeProductData($productData, $itemId = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf(self::$listItemTitle, $itemId));
        $I->see($productData['desc'], sprintf(self::$listItemDescription, $itemId));
        $I->see($productData['price'], sprintf(self::$listItemPrice, $itemId));
        return $this;
    }

    /**
     * @param $itemId
     *
     * @return ProductDetails
     */
    public function openDetailsPage($itemId)
    {
        $I = $this->user;
        $I->click(sprintf(self::$listItemTitle, $itemId));
        return new ProductDetails($I);
    }
}
