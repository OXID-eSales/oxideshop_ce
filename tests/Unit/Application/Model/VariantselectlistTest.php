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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Model;

use \oxVariantSelectList;

class VariantselectlistTest extends \OxidTestCase
{

    /**
     * Testing constructor and setters
     *
     * @return null
     */
    public function testConstructorAndSetters()
    {
        $oSelectionList = new oxVariantSelectList("test", 0);

        // initial state
        $this->assertNull($oSelectionList->getActiveSelection());
        $this->assertEquals("test", $oSelectionList->getLabel());

        // adding variants
        $oSelectionList->addVariant("test1", "test1", true, false);

        // checking various getters
        $this->assertEquals(1, count($oSelectionList->getSelections()));

        // adding variants
        $oSelectionList->addVariant("test1", "test1", false, true);
        $oSelectionList->addVariant("test2", "test2", false, true);
        $oSelectionList->addVariant("test2", "test2", true, false);

        // checking for active selection
        $oActiveSelection = $oSelectionList->getActiveSelection();
        $this->assertNotNull($oActiveSelection);
        $this->assertEquals("test2", $oActiveSelection->getName());
        $this->assertEquals("test2", $oActiveSelection->getValue());

        // checking various getters
        $this->assertEquals(2, count($oSelectionList->getSelections()));
    }

    /**
     * @see https://bugs.oxid-esales.com/view.php?id=6053
     */
    public function testAddVariantAllowsZeroAsValue()
    {
        $oSelectionList = new oxVariantSelectList("test", 0);

        $oSelectionList->addVariant("0", "test1", false, true);
        $oSelectionList->addVariant(" 0 ", "test2", false, true);
        $oSelectionList->addVariant("01", "test3", false, true);
        $oSelectionList->addVariant(" ", "test4", false, true);
        $oSelectionList->addVariant("", "test5", false, true);

        $aSelections = $oSelectionList->getSelections();

        $this->assertInstanceOf('\OxidEsales\EshopCommunity\Application\Model\Selection', $aSelections["test1"]);
        $this->assertInstanceOf('\OxidEsales\EshopCommunity\Application\Model\Selection', $aSelections["test2"]);
        $this->assertInstanceOf('\OxidEsales\EshopCommunity\Application\Model\Selection', $aSelections["test3"]);

        $this->assertEquals("0", $aSelections["test1"]->getName());
        $this->assertEquals("0", $aSelections["test2"]->getName());
        $this->assertEquals("01", $aSelections["test3"]->getName());

        $this->assertNotInstanceOf('oxSelection', $aSelections["test4"]);
        $this->assertNotInstanceOf('oxSelection', $aSelections["test5"]);
    }
}
