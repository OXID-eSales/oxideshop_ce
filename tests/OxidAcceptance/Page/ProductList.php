<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;

class ProductList extends Page
{
    use AccountMenu;

    protected $webElementName = 'WebElement\ProductList';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param)
    {
        return $this->webElement->URL.'/index.php?'.http_build_query(['cl' => 'alist', 'cnid' => $param]);
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
     * @param $itemId
     *
     * @return ProductDetails
     */
    public function openDetailsPage($itemId)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->listItemTitle, $itemId));
        return new ProductDetails($I);
    }
}
