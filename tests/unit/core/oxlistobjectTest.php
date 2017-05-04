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

/**
 * Testing oxshoplist class
 */
class Unit_Core_oxlistobjectTest extends OxidTestCase
{


    /**
     * Tests getId method
     */
    public function testgetId()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign(array('oxid' => 10));
        $this->assertEquals(10, $oListObject->getId());
    }

    /**
     * Tests getId method
     */
    public function testgetIdWhenNotSet()
    {
        $oListObject = new oxListObject('table');
        $this->assertEquals(null, $oListObject->getId());
    }

    /**
     * Checks that assign method assigns values properly
     */
    public function testAssign()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign(array('oxid' => 10));
        $this->assertEquals(new oxField(10), $oListObject->table__oxid);
    }

    /**
     * Checks that assign method assigns values properly
     */
    public function testAssignTwo()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign(array('oxid' => 10));
        $oListObject->assign(array('oxname' => 'title'));
        $this->assertEquals(10, $oListObject->table__oxid->value);
        $this->assertEquals('title', $oListObject->table__oxname->value);
    }

    /**
     * Checking if assigning with incorrect data works.
     */
    public function testAssignIncorrect()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign('oxid');
        $this->assertEquals(0, count(get_object_vars($oListObject)));
    }

}
