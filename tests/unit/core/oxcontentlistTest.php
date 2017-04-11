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

class Unit_Core_oxcontentlistTest extends OxidTestCase
{

    protected $_oContent = null;
    protected $_sShopId = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        // creating demo content
        $this->_oContent = new oxcontent();
        $this->_oContent->oxcontents__oxtitle = new oxField('test_Unit_oxcontentlistTest', oxField::T_RAW);
        $this->_sShopId = $this->getConfig()->getShopId();
        $this->_oContent->oxcontents__oxshopid = new oxField($this->_sShopId, oxField::T_RAW);
        $this->_oContent->oxcontents__oxloadid = new oxField('testid_Unit_oxcontentlistTest', oxField::T_RAW);
        $this->_oContent->oxcontents__oxcontent = new oxField('Unit_oxcontentlistTest', oxField::T_RAW);
        $this->_oContent->oxcontents__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oContent->oxcontents__oxtype = new oxField('1', oxField::T_RAW);
        $this->_oContent->oxcontents__oxsnippet = new oxField('0', oxField::T_RAW);
        $this->_oContent->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->getConfig()->setShopId($this->_sShopId);
        // deleting ..
        $this->_oContent->delete();
        parent::tearDown();
    }

    /**
     * Testing top menu
     */
    public function testLoadMainMenulist()
    {
        $oList = new oxcontentlist();
        $oList->LoadMainMenulist();

        $sOxid = $this->_oContent->getId();

        // testing if there is what to test
        $this->assertTrue(isset($oList->aList[$sOxid]));
        $this->assertTrue(isset($oList->aList[$sOxid]->oxcontents__oxid->value));
        $this->assertTrue(isset($oList->aList[$sOxid]->oxcontents__oxloadid->value));

        // testing real data
        $this->assertEquals($oList->aList[$sOxid]->oxcontents__oxid->value, $sOxid);
        $this->assertEquals($oList->aList[$sOxid]->oxcontents__oxloadid->value, "testid_Unit_oxcontentlistTest");
    }

    /**
     * Testing category menu
     */
    public function testLoadCatMenues()
    {
        $this->_oContent->oxcontents__oxtype = new oxField('2', oxField::T_RAW);
        $this->_oContent->oxcontents__oxcatid = new oxField('testoxcontentlist', oxField::T_RAW);
        $this->_oContent->save();

        $oList = new oxcontentlist();
        $oList->LoadCatMenues();

        $sOxid = $this->_oContent->getId();

        // testing if there is what to test
        $this->assertTrue(isset($oList->aList['testoxcontentlist']));
        $this->assertTrue(isset($oList->aList['testoxcontentlist'][0]));
        $this->assertTrue(isset($oList->aList['testoxcontentlist'][0]->oxcontents__oxid->value));
        $this->assertTrue(isset($oList->aList['testoxcontentlist'][0]->oxcontents__oxloadid->value));

        // testing real data
        $this->assertEquals($oList->aList['testoxcontentlist'][0]->oxcontents__oxid->value, $sOxid);
        $this->assertEquals($oList->aList['testoxcontentlist'][0]->oxcontents__oxloadid->value, "testid_Unit_oxcontentlistTest");
    }


    /**
     * Checks loaded services count.
     */
    public function testLoadServices()
    {
        $oContent = new oxContentList();
        $oContent->loadServices();

        $this->assertEquals(6, count($oContent));
    }
}
