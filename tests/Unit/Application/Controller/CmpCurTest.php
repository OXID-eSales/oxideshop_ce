<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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

