<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;

class GiftRegistry extends Page
{
    use AccountMenu;

    protected $webElementName = 'WebElement\GiftRegistry';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($params)
    {
        return $this->webElement->URL.'/index.php?'.http_build_query($params);
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
        $I->see($productData['title'], sprintf($this->webElement->productTitle, $itemPosition));
        $I->see($productData['desc'], sprintf($this->webElement->productDescription, $itemPosition));
        $I->see($productData['price'], sprintf($this->webElement->productPrice, $itemPosition));
        return $this;
    }

}
