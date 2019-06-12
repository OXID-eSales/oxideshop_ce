<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;

class ContentlistTest extends \OxidTestCase
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
        $this->_oContent = oxNew('oxcontent');
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
        $oList = oxNew('oxContentList');
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

        $oList = oxNew('oxContentList');
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
        $oContent = oxNew('oxContentList');
        $oContent->loadServices();

        $this->assertEquals(6, count($oContent));
    }
}
