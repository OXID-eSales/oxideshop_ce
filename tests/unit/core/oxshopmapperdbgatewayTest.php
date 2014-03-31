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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(".") . '/unit/OxidTestCase.php';
require_once realpath(".") . '/unit/test_config.inc.php';

/**
 * Testing oxArticle class.
 */
class Unit_Core_oxShopMapperDbGatewayTest extends OxidTestCase
{

    /**
     * Test set/get database class object.
     */
    public function testSetGetDb()
    {
        $oShopMapperDbGateway = new oxShopMapperDbGateway();

        // assert default gateway
        $this->isInstanceOf('oxLegacyDb', $oShopMapperDbGateway->getDbGateway());

        $oCustomDb = new stdClass();

        $oShopMapperDbGateway->setDbGateway($oCustomDb);
        $this->assertSame($oCustomDb, $oShopMapperDbGateway->getDbGateway());
    }

    /**
     * Tests add item to shop.
     */
    public function testAddItemToShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $iShopId   = 45;

        $sSQL = "insert into oxarticles2shop (OXMAPSHOPID, OXMAPOBJECTID) values (?, ?)";

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('execute'));
        $oShopMapperDbGateway->expects($this->once())->method('execute')->with($sSQL, array($iShopId, $iItemId));

        $oShopMapperDbGateway->addItemToShop($iItemId, $sItemType, $iShopId);
    }

    /**
     * Tests remove item from shop.
     */
    public function testRemoveItemFromShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $iShopId   = 45;

        $sSQL = "delete from oxarticles2shop where OXMAPSHOPID = ? and OXMAPOBJECTID = ?";

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('execute'));
        $oShopMapperDbGateway->expects($this->once())->method('execute')->with($sSQL, array($iShopId, $iItemId));

        $oShopMapperDbGateway->removeItemFromShop($iItemId, $sItemType, $iShopId);
    }

    /**
     * Tests execute database query.
     */
    public function testExecuteNoParams()
    {
        $sSQL = 'test execute sql query';

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));
        $oDb->expects($this->once())->method('execute')->with($sSQL);

        $oShopMapperDbGateway = new oxShopMapperDbGateway();
        $oShopMapperDbGateway->setDbGateway($oDb);

        $oShopMapperDbGateway->execute($sSQL);
    }

    /**
     * Tests execute database query with parameters.
     */
    public function testExecuteWithParams()
    {
        $sSQL    = 'test execute sql query';
        $aParams = array('test', 'sql', 'parameters');

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));
        $oDb->expects($this->once())->method('execute')->with($sSQL, $aParams);

        $oShopMapperDbGateway = new oxShopMapperDbGateway();
        $oShopMapperDbGateway->setDbGateway($oDb);

        $oShopMapperDbGateway->execute($sSQL, $aParams);
    }

    /**
     * Tests remove item from shop.
     */
    public function testInheritItemsFromShop()
    {
        $iParentShopId = 45;
        $iSubShopId    = 123;
        $sItemType     = 'oxarticles';

        $sSQL = "insert into oxarticles2shop (OXMAPSHOPID, OXMAPOBJECTID) "
                . "select ?, OXMAPOBJECTID from oxarticles2shop where OXMAPSHOPID = ?";

        $aSqlParams = array($iSubShopId, $iParentShopId);

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('execute'));
        $oShopMapperDbGateway->expects($this->once())->method('execute')->with($sSQL, $aSqlParams);

        $oShopMapperDbGateway->inheritItemsFromShop($iParentShopId, $iSubShopId, $sItemType);
    }

    /**
     * Tests remove item from shop.
     */
    public function testRemoveInheritedItemsFromShop()
    {
        $iParentShopId = 45;
        $iSubShopId    = 123;
        $sItemType     = 'oxarticles';

        $sSQL = "delete s from oxarticles2shop as s "
                . "left join oxarticles2shop as p on (s.OXMAPOBJECTID = p.OXMAPOBJECTID)"
                . "where s.OXMAPSHOPID = ? and p.OXMAPSHOPID = ?";

        $aSqlParams = array($iSubShopId, $iParentShopId);

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('execute'));
        $oShopMapperDbGateway->expects($this->once())->method('execute')->with($sSQL, $aSqlParams);

        $oShopMapperDbGateway->removeInheritedItemsFromShop($iParentShopId, $iSubShopId, $sItemType);
    }
}
