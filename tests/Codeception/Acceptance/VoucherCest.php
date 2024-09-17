<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('voucher')]
final class VoucherCest
{
    public function seeVoucherWarning(AcceptanceTester $I): void
    {
        $I->wantToTest('if voucher warning exists');

        $I->updateInDatabase(
            'oxdelivery',
            [
                'OXADDSUM' => 5
            ]
        );

        $I->amGoingTo('add product to basket');

        $shop = $I->loginShopWithExistingUser();
        $shop
            ->searchFor('1000')
            ->openFirstProductInSearchResults()
            ->addProductToBasket();

        $I->amGoingTo('add see voucher warning');

        $basket = $shop->openBasket();
        $basket->openCouponDropDown();

        $I->see(Translator::translate('APPLY_COUPON_WARNING'));
    }
}
