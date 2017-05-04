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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxcounterTest extends OxidTestCase
{

    protected function tearDown()
    {
        oxDb::getDb("delete from oxcounters");

        return parent::tearDown();
    }

    /**
     * oxCounter:::getNext() test case
     *
     * @return null
     */
    public function testGetNext()
    {
        $oCounter = new oxCounter();

        $iNext1 = $oCounter->getNext("test1");
        $this->assertEquals(++$iNext1, $oCounter->getNext("test1"));
        $this->assertEquals(++$iNext1, $oCounter->getNext("test1"));
        $this->assertEquals(++$iNext1, $oCounter->getNext("test1"));

        $iNext2 = $oCounter->getNext("test2");
        $this->assertNotEquals($iNext2, $iNext1);
        $this->assertEquals(++$iNext2, $oCounter->getNext("test2"));
        $this->assertEquals(++$iNext2, $oCounter->getNext("test2"));
        $this->assertEquals(++$iNext2, $oCounter->getNext("test2"));
    }

    /**
     * oxCounter:::update() test case
     *
     * @return null
     */
    public function testUpdate()
    {

        $oCounter = new oxCounter();

        $this->assertEquals(1, $oCounter->getNext("test4"));
        $oCounter->update("test3", 3);
        $this->assertEquals(4, $oCounter->getNext("test3"));
        $oCounter->update("test3", 2);
        $this->assertEquals(5, $oCounter->getNext("test3"));
        $this->assertEquals(2, $oCounter->getNext("test4"));

    }
}
