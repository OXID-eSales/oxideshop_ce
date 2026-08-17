<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('basket')]
final class BasketRefreshWithDeletedProductCest
{
    public function testBasketRefreshWithDeletedProduct(AcceptanceTester $I): void
    {
        $I->wantToTest('basket is correctly refreshed when a product is deleted by admin');

        $basket = new Basket($I);
        $I->openShop();

        $productData = Fixtures::get('product-' . '1000');
        $basketPage = $basket->addProductToBasketAndOpenBasket($productData['OXID'], 1);

        $I->deleteFromDatabase('oxarticles', ['OXID' => $productData['OXID']]);

        $basketPage->updateProductAmount(2);

        $I->waitForText(sprintf(
            Translator::translate('ERROR_MESSAGE_ARTICLE_ARTICLE_DOES_NOT_EXIST'),
            $productData['OXTITLE_1']
        ));
    }
}
