<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\MiniBasket;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\ProductDetails;

class ProductCompare extends Page
{
    use MiniBasket;

    protected $webElementName = 'WebElement\MiniBasket';

    /**
     * @param array $productData
     * @param int   $position    The Item position
     *
     * @return $this
     */
    public function seeProductData($productData, $position = 1)
    {
        $I = $this->user;
        $I->see($I->translate('PRODUCT_NO').': '.$productData['id'], sprintf($this->webElement->productNumber, $position));
        $I->see($productData['title'], sprintf($this->webElement->productTitle, $position));
        // TODO: uncomment
        //$I->see($productData['price'], sprintf($this->webElement->productPrice, $id));
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
        $I->see($attributeName, sprintf($this->webElement->attributeName, ($attributeId+1)));
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
        $I->see($attributeValue, sprintf($this->webElement->attributeValue, ($attributeId+1), $position));
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
        $I->click(sprintf($this->webElement->productTitle, $id));
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
        $I->click(sprintf($this->webElement->rightArrow, $productId));
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
        $I->click(sprintf($this->webElement->leftArrow, $productId));
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
        $I->click(sprintf($this->webElement->removeButton, $productId));
        return $this;
    }

}
