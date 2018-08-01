<?php
namespace Page;

use Page\Header\MiniBasket;

class Basket extends Page
{
    use MiniBasket;

    // include url of current page
    public static $URL = '';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $basketSummary = '#basketGrandTotal';

    public static $basketItemAmount = '#basketcontents_table #am_%s';

    public static $basketItemTotalPrice = '//tr[@id="table_cartItem_%s"]/td[@class="totalPrice"]';

    public static $basketItemTitle = '//tr[@id="table_cartItem_%s"]/td[2]/div[2]/a';

    public static $basketItemId = '//tr[@id="table_cartItem_%s"]/td[2]/div[2]/div[1]';

    public static $basketBundledItemAmount = '//tr[@id="table_cartItem_%s"]/td[4]';

    public static $basketUpdateButton = '#basketcontents_table #basketUpdate';

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
    public static function route($params)
    {
        return static::$URL.'/index.php?'.http_build_query($params);
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
        $I->click(self::$basketUpdateButton);
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
            $I->see($I->translate('PRODUCT_NO') . ' ' . $basketProduct['id'], sprintf(self::$basketItemId, $itemPosition));
            $I->see($basketProduct['title'], sprintf(self::$basketItemTitle, $itemPosition));
            $I->see($basketProduct['totalPrice'], sprintf(self::$basketItemTotalPrice, $itemPosition));
            $I->seeInField(sprintf(self::$basketItemAmount, $itemPosition), $basketProduct['amount']);
        }
        $I->see($basketSummaryPrice, self::$basketSummary);
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
        $I->see($I->translate('PRODUCT_NO') . ' ' . $basketProduct['id'], sprintf(self::$basketItemId, $itemPosition));
        $I->see($basketProduct['title'], sprintf(self::$basketItemTitle, $itemPosition));
        $I->see($basketProduct['amount'], sprintf(self::$basketBundledItemAmount, $itemPosition));
        return $this;
    }

    /**
     * @return UserCheckout
     */
    public function goToNextStep()
    {
        $I = $this->user;
        $I->click($I->translate('CONTINUE_TO_NEXT_STEP'));
        $I->waitForElement(self::$breadCrumb);
        return new UserCheckout($I);
    }
}
