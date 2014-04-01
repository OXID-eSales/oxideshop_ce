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
class Unit_Core_oxShopRelationsDbGatewayTest extends OxidTestCase
{

    /**
     * Test set/get database class object.
     */
    public function testSetGetDb()
    {
        $oShopRelationsDbGateway = new oxShopRelationsDbGateway();

        // assert default gateway
        $this->isInstanceOf('oxLegacyDb', $oShopRelationsDbGateway->getDbGateway());

        $oCustomDb = new stdClass();

        $oShopRelationsDbGateway->setDbGateway($oCustomDb);
        $this->assertSame($oCustomDb, $oShopRelationsDbGateway->getDbGateway());
    }

    /**
     * Tests add item to shop.
     */
    public function testAddToShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $iShopId   = 45;

        $sSQL = "insert into oxarticles2shop (OXMAPSHOPID, OXMAPOBJECTID) values (?, ?)";

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));
        $oDb->expects($this->once())->method('execute')->with($sSQL, array($iShopId, $iItemId));

        $oShopRelationsDbGateway = new oxShopRelationsDbGateway();
        $oShopRelationsDbGateway->setDbGateway($oDb);

        $oShopRelationsDbGateway->addToShop($iItemId, $sItemType, $iShopId);
    }

    /**
     * Tests remove item from shop.
     */
    public function testRemoveFromShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $iShopId   = 45;

        $sSQL = "delete from oxarticles2shop where OXMAPSHOPID = ? and OXMAPOBJECTID = ?";

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));
        $oDb->expects($this->once())->method('execute')->with($sSQL, array($iShopId, $iItemId));

        $oShopRelationsDbGateway = new oxShopRelationsDbGateway();
        $oShopRelationsDbGateway->setDbGateway($oDb);

        $oShopRelationsDbGateway->removeFromShop($iItemId, $sItemType, $iShopId);
    }

    /**
     * Tests remove item from shop.
     */
    public function testInheritFromShop()
    {
        $iParentShopId = 45;
        $iSubShopId    = 123;
        $sItemType     = 'oxarticles';

        $sSQL = "insert into oxarticles2shop (OXMAPSHOPID, OXMAPOBJECTID) "
                . "select ?, OXMAPOBJECTID from oxarticles2shop where OXMAPSHOPID = ?";

        $aSqlParams = array($iSubShopId, $iParentShopId);

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));
        $oDb->expects($this->once())->method('execute')->with($sSQL, $aSqlParams);

        $oShopRelationsDbGateway = new oxShopRelationsDbGateway();
        $oShopRelationsDbGateway->setDbGateway($oDb);

        $oShopRelationsDbGateway->inheritFromShop($iParentShopId, $iSubShopId, $sItemType);
    }

    /**
     * Tests remove item from shop.
     */
    public function testRemoveInheritedFromShop()
    {
        $iParentShopId = 45;
        $iSubShopId    = 123;
        $sItemType     = 'oxarticles';

        $sSQL = "delete s from oxarticles2shop as s "
                . "left join oxarticles2shop as p on (s.OXMAPOBJECTID = p.OXMAPOBJECTID)"
                . "where s.OXMAPSHOPID = ? and p.OXMAPSHOPID = ?";

        $aSqlParams = array($iSubShopId, $iParentShopId);

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));
        $oDb->expects($this->once())->method('execute')->with($sSQL, $aSqlParams);

        $oShopRelationsDbGateway = new oxShopRelationsDbGateway();
        $oShopRelationsDbGateway->setDbGateway($oDb);

        $oShopRelationsDbGateway->removeInheritedFromShop($iParentShopId, $iSubShopId, $sItemType);
    }
}
