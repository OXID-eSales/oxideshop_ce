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
namespace Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Discount;

use \oxTestModules;

/**
 * Tests for Discount_Articles class
 */
class DiscountArticlesTest extends \OxidTestCase
{

    /**
     * Discount_Articles::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdiscount", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Discount_Articles');
        $this->assertEquals('discount_articles.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue(isset($aViewData['readonly']));
        $this->assertTrue($aViewData['edit'] instanceof Discount);
    }
}
