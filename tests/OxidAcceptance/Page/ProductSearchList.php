<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\LanguageMenu;

class ProductSearchList extends Page
{
    use LanguageMenu;

    protected $webElementName = 'WebElement\ProductSearchList';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param)
    {
        return $this->webElement->URL.'/index.php?'.http_build_query(['cl' => 'search', 'searchparam' => $param]);
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
        $I->see($productData['title'], sprintf($this->webElement->listItemTitle, $itemId));
        $I->see($productData['desc'], sprintf($this->webElement->listItemDescription, $itemId));
        $I->see($productData['price'], sprintf($this->webElement->listItemPrice, $itemId));
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
        $I->click(sprintf($this->webElement->variantSelection, $itemId));
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
        $I->click(sprintf($this->webElement->listItemTitle, $itemId));
        return new ProductDetails($I);
    }
}
