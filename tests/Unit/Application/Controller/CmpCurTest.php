<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

class CmpCurTest extends \OxidTestCase
{

    /**
     * Checking if basket currency object is updated initialising currency
     * (M:825, M:890)
     */
    public function testInitUpdatesBasketCurrency()
    {
        $oParentView = oxNew('oxUBase');
        $oCurView = oxNew('oxcmp_cur');
        $oCurView->setParent($oParentView);

        $oCur = $oCurView->getSession()->getBasket()->getBasketCurrency();
        $this->assertEquals(2, $oCur->decimal);

        // changing decimal percision from 2 => 1
        $this->getConfig()->setConfigParam("aCurrencies", array("EUR@ 1.00@ ,@ .@ ¤@ 1"));
        $oCurView->init();

        $oCur = $oCurView->getSession()->getBasket()->getBasketCurrency();
        $this->assertEquals(1, $oCur->decimal);
    }
}
