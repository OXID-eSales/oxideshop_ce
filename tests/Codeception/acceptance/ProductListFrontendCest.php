<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\Codeception\Module\Translation\Translator;

final class ProductListFrontendCest
{
    /**
     * Product list. check category filter reset button functionality
     * @group product_list
     * @group frontend
     */
    public function testCategoryFilterReset(AcceptanceTester $I)
    {
        $I->wantToTest('category filter reset button functionality');

        $homePage = $I->openShop();
        $I->waitForPageLoad();
        $homePage->openCategoryPage('Kiteboarding');
        $I->waitForPageLoad();
        $homePage->openCategoryPage('Kites');
        $I->waitForPageLoad();
        $I->seeElement("//form[@id='filterList']");

        $I->click("//*[@id='filterList']/div[@class='btn-group'][1]/button");
        $I->waitForText("Freeride");
        $I->click("Freeride");
        $I->waitForPageLoad();
        $I->seeElement("//*[@id='resetFilter']/button");
        $I->click("//*[@id='resetFilter']/button");
        $I->waitForPageLoad();

        $I->click("//*[@id='filterList']/div[@class='btn-group'][2]/button");
        $I->waitForText("kite");
        $I->click("kite");
        $I->waitForPageLoad();
        $I->seeElement("//*[@id='resetFilter']/button");

        $I->click("//*[@id='resetFilter']/button");
        $I->waitForPageLoad();
        $I->dontSeeElement("//*[@id='resetFilter']/button");
    }
}
