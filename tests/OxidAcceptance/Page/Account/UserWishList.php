<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\MiniBasket;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\ProductDetails;

class UserWishList extends Page
{
    use MiniBasket;

    protected $webElementName = 'WebElement\UserWishList';

    /**
     * @param array $productData
     * @param int   $itemPosition
     *
     * @return $this
     */
    public function seeProductData($productData, $itemPosition = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf($this->webElement->productTitle, $itemPosition));
        $I->see($productData['desc'], sprintf($this->webElement->productDescription, $itemPosition));
        $I->see($productData['price'], sprintf($this->webElement->productPrice, $itemPosition));
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
        $I->click(sprintf($this->webElement->productTitle, $itemPosition));
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
        $I->fillField(sprintf($this->webElement->basketAmount, $itemPosition), $amount);
        $I->click(sprintf($this->webElement->toBasketButton, $itemPosition));
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
        $I->click(sprintf($this->webElement->removeButton, $itemPosition));
        return $this;
    }

}
