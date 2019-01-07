<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\MiniBasket;

class Basket extends Page
{
    use MiniBasket;

    protected $webElementName = 'WebElement\Basket';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

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
     * Update product amount in the basket
     *
     * @param int   $itemPosition
     * @param float $amount
     *
     * @return $this
     */
    public function updateProductAmount($amount, $itemPosition = 1)
    {
        $I = $this->user;
        $I->fillField('#basketcontents_table #am_1', $amount);
        $I->click($this->webElement->basketUpdateButton);
        return $this;
    }

    /**
     * Assert basket product
     *
     * $basketProducts[] = ['id' => productId,
     *                   'title' => productTitle,
     *                   'amount' => productAmount,
     *                   'totalPrice' => productTotalPrice]
     *
     * @param array $basketProducts
     * @param string $basketSummaryPrice
     *
     * @return $this
     */
    public function seeBasketContains($basketProducts, $basketSummaryPrice)
    {
        $I = $this->user;
        foreach ($basketProducts as $key => $basketProduct) {
            $itemPosition = $key + 1;
            $I->see($I->translate('PRODUCT_NO') . ' ' . $basketProduct['id'], sprintf($this->webElement->basketItemId, $itemPosition));
            $I->see($basketProduct['title'], sprintf($this->webElement->basketItemTitle, $itemPosition));
            $I->see($basketProduct['totalPrice'], sprintf($this->webElement->basketItemTotalPrice, $itemPosition));
            $I->seeInField(sprintf($this->webElement->basketItemAmount, $itemPosition), $basketProduct['amount']);
        }
        $I->see($basketSummaryPrice, $this->webElement->basketSummary);
        return $this;
    }

    /**
     * Assert basket product
     *
     * $basketProduct = ['id' => productId,
     *                   'title' => productTitle,
     *                   'amount' => productAmount]
     *
     * @param array $basketProduct
     * @param int   $itemPosition
     *
     * @return $this
     */
    public function seeBasketContainsBundledProduct($basketProduct, $itemPosition)
    {
        $I = $this->user;
        $I->see($I->translate('PRODUCT_NO') . ' ' . $basketProduct['id'], sprintf($this->webElement->basketItemId, $itemPosition));
        $I->see($basketProduct['title'], sprintf($this->webElement->basketItemTitle, $itemPosition));
        $I->see($basketProduct['amount'], sprintf($this->webElement->basketBundledItemAmount, $itemPosition));
        return $this;
    }

    /**
     * @return UserCheckout
     */
    public function goToNextStep()
    {
        $I = $this->user;
        $I->click($I->translate('CONTINUE_TO_NEXT_STEP'));
        $I->waitForElement($this->webElement->breadCrumb);
        return new UserCheckout($I);
    }
}
