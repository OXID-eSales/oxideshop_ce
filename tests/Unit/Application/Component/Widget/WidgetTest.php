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
namespace Unit\Application\Component\Widget;

/**
 * Tests for oxwCategoryTree class
 */
class WidgetTest extends \OxidTestCase
{

    /**
     * Testing oxWidget::init()
     *
     * @return null
     */
    public function testInitComponentNotSet()
    {
        $oWidget = oxNew('oxwLanguageList');
        $oWidget->init();

        $aComponents = $oWidget->getComponents();
        $this->assertEquals(1, count($aComponents));
        $this->assertEquals('oxidesales\eshopcommunity\application\component\languagecomponent', $aComponents["oxcmp_lang"]->getThisAction());
    }

    /**
     * Testing oxWidget::init()
     *
     * @return null
     */
    public function testInitComponentIsSet()
    {
        $aComponents["oxcmp_lang"] = oxNew('oxcmp_lang');
        $oView = oxNew("details");
        $oView->setComponents($aComponents);
        $this->getConfig()->setActiveView($oView);

        $oWidget = oxNew('oxwLanguageList');
        $oWidget->init();

        $aComponents = $oWidget->getComponents();
        $this->assertEquals(1, count($aComponents));
        $this->assertTrue(isset($aComponents["oxcmp_lang"]));
    }

}
