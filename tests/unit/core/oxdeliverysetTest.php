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

class Unit_Core_oxDeliverySetTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oDelSet = oxNew('oxdeliveryset');
        $oDelSet->setId('_testDeliverySetId');
        $oDelSet->oxdeliveryset__oxtitle = new oxField('_testDeliverySetTitle');
        $oDelSet->save();

        // generating relations
        $oDel = oxNew('oxbase');
        $oDel->init('oxobject2payment');
        $oDel->setId('_testO2PayId');
        $oDel->oxobject2payment__oxobjectid = new oxField($oDelSet->getId(), oxField::T_RAW);
        $oDel->save();

        $oDel = oxNew('oxbase');
        $oDel->Init('oxobject2delivery');
        $oDel->setId('_testO2DelId');
        $oDel->oxobject2delivery__oxdeliveryid = new oxField($oDelSet->getId(), oxField::T_RAW);
        $oDel->save();

        $oDel = oxNew('oxbase');
        $oDel->Init('oxdel2delset');
        $oDel->setId('_testO2DelSetId');
        $oDel->oxdel2delset__oxdelsetid = new oxField($oDelSet->getId(), oxField::T_RAW);
        $oDel->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxdeliveryset');
        $this->cleanUpTable('oxobject2payment');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxdel2delset');
        parent::tearDown();
    }

    /**
     * Testing if constructor initiates object properly
     */
    public function testOxDeliverySet()
    {
        $oDelSet = oxNew('oxDeliverySet');

        $this->assertEquals('oxdeliveryset', $oDelSet->getClassName());
        $this->assertEquals('oxdeliveryset', $oDelSet->getCoreTableName());
    }

    /**
     * Testing if deletion does nothing when no oxId is specified
     */
    public function testDeleteNoObject()
    {
        $oDelSet = oxNew('oxDeliverySet');
        $this->assertFalse($oDelSet->delete());
    }

    /**
     * Testing if deletion erases all related record information
     */
    public function testDelete()
    {
        $oDB = oxDb::getDb();

        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->load('_testDeliverySetId');

        // checking before deletion
        $this->assertEquals(1, $oDB->getOne('select count(*) from oxobject2payment where oxobjectid = "' . $oDelSet->getId() . '" '));
        $this->assertEquals(1, $oDB->getOne('select count(*) from oxobject2delivery where oxdeliveryid = "' . $oDelSet->getId() . '" '));
        $this->assertEquals(1, $oDB->getOne('select count(*) from oxdel2delset where oxdelsetid = "' . $oDelSet->getId() . '" '));
        $this->assertEquals(1, $oDB->getOne('select count(*) from oxdeliveryset where oxid = "' . $oDelSet->getId() . '" '));

        $oDelSet->delete();

        // checking if deletion cleared up everything
        $this->assertFalse((bool) $oDB->getOne('select count(*) from oxobject2payment where oxobjectid = "' . $oDelSet->getId() . '" '));
        $this->assertFalse((bool) $oDB->getOne('select count(*) from oxobject2delivery where oxdeliveryid = "' . $oDelSet->getId() . '" '));
        $this->assertFalse((bool) $oDB->getOne('select count(*) from oxdel2delset where oxdelsetid = "' . $oDelSet->getId() . '" '));
        $this->assertFalse((bool) $oDB->getOne('select count(*) from oxdeliveryset where oxid = "' . $oDelSet->getId() . '" '));
    }


    public function testGetIdByName()
    {
        $oD = new oxDeliverySet();
        $this->assertEquals('_testDeliverySetId', $oD->getIdByName('_testDeliverySetTitle'));
    }
}
