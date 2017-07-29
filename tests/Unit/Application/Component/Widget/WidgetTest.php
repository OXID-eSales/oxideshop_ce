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
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCategoryTree class
 */
class WidgetTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * @covers \OxidEsales\Eshop\Application\Component\Widget\WidgetController::init()
     */
    public function testInitComponentNotSet()
    {
        $languageList = oxNew(\OxidEsales\Eshop\Application\Component\Widget\LanguageList::class);
        $languageList->init();

        $components = $languageList->getComponents();
        $this->assertEquals(1, count($components));
        $this->assertEquals('oxidesales\eshop\application\component\languagecomponent', $components["oxcmp_lang"]->getThisAction());
    }

    /**
     * @covers \OxidEsales\Eshop\Application\Component\Widget\WidgetController::init()
     */
    public function testInitComponentIsSet()
    {
        $components["oxcmp_lang"] = oxNew(\OxidEsales\Eshop\Application\Component\LanguageComponent::class);
        $view = oxNew(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class);
        $view->setComponents($components);
        $this->getConfig()->setActiveView($view);

        $languageListWidget = oxNew(\OxidEsales\Eshop\Application\Component\Widget\LanguageList::class);
        $languageListWidget->init();

        $components = $languageListWidget->getComponents();
        $this->assertEquals(1, count($components));
        $this->assertTrue(isset($components["oxcmp_lang"]));
    }

}
