<?php
namespace Page\Header;

use Page\PaymentCheckout;
use Page\UserCheckout;

trait MiniBasket
{
    public static $miniBasketMenuElement = '//div[@class="btn-group minibasket-menu"]/button';

    public static $miniBasketTitle = '//div[@class="minibasket-menu-box"]/p';

    public static $miniBasketItemTitle = '//div[@id="basketFlyout"]/table/tbody/tr[%s]/td[2]/a';

    public static $miniBasketItemAmount = '//div[@id="basketFlyout"]/table/tbody/tr[%s]/td[1]/span';

    public static $miniBasketItemPrice = '//div[@id="basketFlyout"]/table/tbody/tr[%s]/td[3]';

    public static $miniBasketSummaryPrice = '//td[@class="total_price text-right"]';

    /**
     * Assert basket product
     *
     * $basketProducts[] = ['title' => productTitle,
     *                   'price' => productPrice,
     *                   'amount' => productAmount,]
     *
     * @param array  $basketProducts
     * @param string $basketSummaryPrice
     * @param string $totalAmount
     *
     * @return $this
     */
    public function seeMiniBasketContains(array $basketProducts, $basketSummaryPrice, $totalAmount)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $this->openMiniBasket();
        $I->see( $totalAmount . ' ' . $I->translate('ITEMS_IN_BASKET'));
        foreach ($basketProducts as $key => $basketProduct) {
            $itemPosition = $key + 1;
            $I->see($basketProduct['title'], $I->clearString(sprintf(self::$miniBasketItemTitle, $itemPosition)));
            $I->see($basketProduct['amount'], sprintf(self::$miniBasketItemAmount, $itemPosition));
            $I->see($basketProduct['price'], sprintf(self::$miniBasketItemPrice, $itemPosition));
        }
        $I->see($basketSummaryPrice, self::$miniBasketSummaryPrice);
        return $this;
    }

    /**
     * @return $this
     */
    public function openMiniBasket()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click(self::$miniBasketMenuElement);
        return $this;
    }

    /**
     * @return UserCheckout
     */
    public function openCheckoutForNotLoggedInUser()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click($I->translate('CHECKOUT'));
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ':' . $I->translate("ADDRESS");
        $I->see($breadCrumbName, UserCheckout::$breadCrumb);
        return new UserCheckout($I);
    }

    /**
     * @return PaymentCheckout
     */
    public function openCheckoutForLoggedInUser()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click($I->translate('CHECKOUT'));
        $breadCrumbName = $I->translate("YOU_ARE_HERE") . ':' . $I->translate("PAY");
        $I->see($breadCrumbName, PaymentCheckout::$breadCrumb);
        return new PaymentCheckout($I);
    }
}
