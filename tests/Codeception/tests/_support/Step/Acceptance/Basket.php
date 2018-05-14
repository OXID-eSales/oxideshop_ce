<?php
namespace Step\Acceptance;

use Page\Header\MiniBasket;
use Page\UserCheckout;
use Page\Basket as BasketPage;

class Basket extends \AcceptanceTester
{
    use MiniBasket;

    public function openBasket()
    {
        $I = $this;
        $this->openMiniBasket();
        $I->click($I->translate('DISPLAY_BASKET'));
        return new BasketPage($I);
    }

    /**
     * @param $productId
     * @param $amount
     * @param $controller
     *
     * @return mixed
     */
    public function addProductToBasket($productId, $amount, $controller)
    {
        $I = $this;
        //add Product to basket
        $params['cl'] = $controller;
        $params['fnc'] = 'tobasket';
        $params['aid'] = $productId;
        $params['am'] = $amount;
        $params['anid'] = $productId;
        $I->amOnPage(BasketPage::route($params));
        if ($controller === 'user') {
            $breadCrumbName = $I->translate("YOU_ARE_HERE") . ':' . $I->translate("ADDRESS");
            $I->see($breadCrumbName, UserCheckout::$breadCrumb);
            return new UserCheckout($I);
        } else {
            $breadCrumbName = $I->translate("YOU_ARE_HERE") . ':' . $I->translate("CART");
            $I->see($breadCrumbName, BasketPage::$breadCrumb);
            return new BasketPage($I);
        }
    }
}