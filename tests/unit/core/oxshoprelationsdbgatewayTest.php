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

        $aSqlParams = array($iShopId, $iItemId);

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_addSql', 'flush'));
        $oShopRelationsDbGateway->expects($this->once())->method('_addSql')->with($sSQL, $aSqlParams);
        $oShopRelationsDbGateway->expects($this->once())->method('flush');

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

        $aSqlParams = array($iShopId, $iItemId);

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_addSql', 'flush'));
        $oShopRelationsDbGateway->expects($this->once())->method('_addSql')->with($sSQL, $aSqlParams);
        $oShopRelationsDbGateway->expects($this->once())->method('flush');

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

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_addSql', 'flush'));
        $oShopRelationsDbGateway->expects($this->once())->method('_addSql')->with($sSQL, $aSqlParams);
        $oShopRelationsDbGateway->expects($this->once())->method('flush');

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

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_addSql', 'flush'));
        $oShopRelationsDbGateway->expects($this->once())->method('_addSql')->with($sSQL, $aSqlParams);
        $oShopRelationsDbGateway->expects($this->once())->method('flush');

        $oShopRelationsDbGateway->removeInheritedFromShop($iParentShopId, $iSubShopId, $sItemType);
    }

    /**
     * Tests execute SQL queries from the list with empty list.
     */
    public function testFlushSqlListEmpty()
    {
        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_getSqlList', 'getDbGateway'));

        $oShopRelationsDbGateway->expects($this->once())->method('_getSqlList')->will($this->returnValue(array()));
        $oShopRelationsDbGateway->expects($this->never())->method('getDbGateway');

        $oShopRelationsDbGateway->flush();
    }

    /**
     * Tests execute SQL queries from the list with one query in the list.
     */
    public function testFlushSqlList1Query()
    {
        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_getSqlList', 'getDbGateway'));

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));

        $oShopRelationsDbGateway->expects($this->once())
            ->method('_getSqlList')
            ->will(
                $this->returnValue(
                    array(
                         array('sql' => 'test SQL query 1', 'params' => array('test', 'SQL', 'params', '1')),
                    )
                )
            );

        $oShopRelationsDbGateway->expects($this->exactly(1))->method('getDbGateway')->will($this->returnValue($oDb));

        $oDb->expects($this->at(0))->method('execute')->with('test SQL query 1', array('test', 'SQL', 'params', '1'));

        $oShopRelationsDbGateway->flush();
    }

    /**
     * Tests execute SQL queries from the list with two queries in the list.
     */
    public function testFlushSqlList2Queries()
    {
        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('_getSqlList', 'getDbGateway'));

        /** @var oxLegacyDb|PHPUnit_Framework_MockObject_MockObject $oDb */
        $oDb = $this->getMock('oxLegacyDb', array('execute'));

        $oShopRelationsDbGateway->expects($this->once())
            ->method('_getSqlList')
            ->will(
                $this->returnValue(
                    array(
                         array('sql' => 'test SQL query 1', 'params' => array('test', 'SQL', 'params', '1')),
                         array('sql' => 'test SQL query 2', 'params' => array('test', 'SQL', 'params', '2')),
                    )
                )
            );

        $oShopRelationsDbGateway->expects($this->exactly(2))->method('getDbGateway')->will($this->returnValue($oDb));

        $oDb->expects($this->at(0))->method('execute')->with('test SQL query 1', array('test', 'SQL', 'params', '1'));
        $oDb->expects($this->at(1))->method('execute')->with('test SQL query 2', array('test', 'SQL', 'params', '2'));

        $oShopRelationsDbGateway->flush();
    }
}
