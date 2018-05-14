<?php
namespace Page\Account;

use Page\Header\MiniBasket;
use Page\Page;
use Page\ProductDetails;

class ProductCompare extends Page
{
    use MiniBasket;

    // include url of current page
    public static $URL = '/en/my-product-comparison/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $productTitle = '//tr[@class="products"]/td[%s]/div[2]/strong/a';

    public static $productNumber = '//tr[@class="products"]/td[%s]/div[2]/span';

    public static $productPrice = '//tr[@class="products"]/td[%s]/div[2]/form[1]/div[2]/div[1]/span[1]';

    public static $attributeName = '//div[@id="compareLandscape"]/table/tbody/tr[%s]/th';

    public static $attributeValue = '//div[@id="compareLandscape"]/table/tbody/tr[%s]/td[%s]';

    public static $rightArrow = '#compareRight_%s';

    public static $leftArrow = '#compareLeft_%s';

    public static $removeButton = '#remove_cmp_%s';

    /**
     * @param array $productData
     * @param int   $position    The Item position
     *
     * @return $this
     */
    public function seeProductData($productData, $position = 1)
    {
        $I = $this->user;
        $I->see($I->translate('PRODUCT_NO').': '.$productData['id'], sprintf(self::$productNumber, $position));
        $I->see($productData['title'], sprintf(self::$productTitle, $position));
        // TODO: uncomment
        //$I->see($productData['price'], sprintf(self::$productPrice, $id));
        return $this;
    }

    /**
     * @param string $attributeName
     * @param int    $attributeId
     *
     * @return $this
     */
    public function seeProductAttributeName($attributeName, $attributeId)
    {
        $I = $this->user;
        $I->see($attributeName, sprintf(self::$attributeName, ($attributeId+1)));
        return $this;
    }

    /**
     * @param string $attributeValue
     * @param int    $attributeId
     * @param int    $position       The Item position
     *
     * @return $this
     */
    public function seeProductAttributeValue($attributeValue, $attributeId, $position)
    {
        $I = $this->user;
        $I->see($attributeValue, sprintf(self::$attributeValue, ($attributeId+1), $position));
        return $this;
    }

    /**
     * Opens details page
     *
     * @param integer $id
     *
     * @return ProductDetails
     */
    public function openProductDetailsPage($id)
    {
        $I = $this->user;
        $I->click(sprintf(self::$productTitle, $id));
        return new ProductDetails($I);
    }

    /**
     * @param string $productId
     *
     * @return $this
     */
    public function moveItemToRight($productId)
    {
        $I = $this->user;
        $I->click(sprintf(self::$rightArrow, $productId));
        return $this;
    }

    /**
     * @param string $productId
     *
     * @return $this
     */
    public function moveItemToLeft($productId)
    {
        $I = $this->user;
        $I->click(sprintf(self::$leftArrow, $productId));
        return $this;
    }

    /**
     * @param string $productId
     *
     * @return $this
     */
    public function removeProductFromList($productId)
    {
        $I = $this->user;
        $I->click(sprintf(self::$removeButton, $productId));
        return $this;
    }

}
