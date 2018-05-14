<?php
namespace Page;

use Page\Header\LanguageMenu;

class ProductSearchList extends Page
{
    use LanguageMenu;

    // include url of current page
    public static $URL = '';

    public static $listItemTitle = '#searchList_%s';

    public static $listItemDescription = '//form[@name="tobasketsearchList_%s"]/div[2]/div[2]/div/div[@class="shortdesc"]';

    public static $listItemPrice = '//form[@name="tobasketsearchList_%s"]/div[2]/div[2]/div/div[@class="price"]/div/span[@class="lead text-nowrap"]';

    public static $variantSelection = '#variantselector_searchList_%s button';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.'/index.php?'.http_build_query(['cl' => 'search', 'searchparam' => $param]);
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
     * @param int    $itemId       The position of the item in the list.
     * @param string $variantValue
     * @param string $waitForText
     *
     * @return ProductDetails
     */
    public function selectVariant($itemId, $variantValue, $waitForText = null)
    {
        $I = $this->user;
        $I->click(sprintf(self::$variantSelection, $itemId));
        $I->click($variantValue);
        //wait for JS to finish
        $I->waitForJS("return $.active == 0;",10);
        return new ProductDetails($I);
    }

    /**
     * @param $itemId
     *
     * @return ProductDetails
     */
    public function openProductDetailsPage($itemId)
    {
        $I = $this->user;
        $I->click(sprintf(self::$listItemTitle, $itemId));
        return new ProductDetails($I);
    }
}
