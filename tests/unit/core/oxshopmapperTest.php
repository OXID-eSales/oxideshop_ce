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
class Unit_Core_oxShopMapperTest extends OxidTestCase
{

    /**
     * Provides shop ID or list of shops.
     *
     * @return array
     */
    public function _dpTestListOfShops()
    {
        return array(
            array(45, 1),
            array(array(), 0),
            array(array(27), 1),
            array(array(3, 46, 5), 3),
        );
    }

    /**
     * Test set/get database gateway.
     */
    public function testSetGetDbGateway()
    {
        $oShopMapper = new oxShopMapper();

        // assert default gateway
        $this->isInstanceOf('oxShopMapperDbGateway', $oShopMapper->getDbGateway());

        $oCustomDbGateway = new stdClass();

        $oShopMapper->setDbGateway($oCustomDbGateway);
        $this->assertSame($oCustomDbGateway, $oShopMapper->getDbGateway());
    }

    /**
     * Tests add object to shop or list of shops.
     *
     * @param int|array $aShops Shop ID or list of shop IDs.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testAddObjectToShops($aShops)
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';

        $oItem = new oxBase();
        $oItem->init($sItemType);
        $oItem->setId($iItemId);

        /** @var oxShopMapper|PHPUnit_Framework_MockObject_MockObject $oShopMapper */
        $oShopMapper = $this->getMock('oxShopMapper', array('addItemToShops'));
        $oShopMapper->expects($this->once())->method('addItemToShops')
            ->with($iItemId, $sItemType, $aShops)->will($this->returnValue(true));

        $this->assertTrue($oShopMapper->addObjectToShops($oItem, $aShops));
    }

    /**
     * Tests remove object from shop or list of shops.
     *
     * @param int|array $aShops Shop ID or list of shop IDs.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testRemoveObjectFromShops($aShops)
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';

        $oItem = new oxBase();
        $oItem->init($sItemType);
        $oItem->setId($iItemId);

        /** @var oxShopMapper|PHPUnit_Framework_MockObject_MockObject $oShopMapper */
        $oShopMapper = $this->getMock('oxShopMapper', array('removeItemFromShops'));
        $oShopMapper->expects($this->once())->method('removeItemFromShops')
            ->with($iItemId, $sItemType, $aShops)->will($this->returnValue(true));

        $this->assertTrue($oShopMapper->removeObjectFromShops($oItem, $aShops));
    }

    /**
     * Tests add item to shop or list of shops.
     *
     * @param int|array $aShops            Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testAddItemToShops($aShops, $iExpectsToProcess)
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('addItemToShop'));
        $oShopMapperDbGateway->expects($this->exactly($iExpectsToProcess))->method('addItemToShop')
            ->will($this->returnValue(true));

        $oShopMapper = new oxShopMapper();
        $oShopMapper->setDbGateway($oShopMapperDbGateway);

        $this->assertTrue($oShopMapper->addItemToShops($iItemId, $sItemType, $aShops));
    }

    /**
     * Tests remove item from shop or list of shops.
     *
     * @param int|array $aShops            Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testRemoveItemFromShops($aShops, $iExpectsToProcess)
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('removeItemFromShop'));
        $oShopMapperDbGateway->expects($this->exactly($iExpectsToProcess))->method('removeItemFromShop')
            ->will($this->returnValue(true));

        $oShopMapper = new oxShopMapper();
        $oShopMapper->setDbGateway($oShopMapperDbGateway);

        $this->assertTrue($oShopMapper->removeItemFromShops($iItemId, $sItemType, $aShops));
    }

    /**
     * Tests inherit items by type to sub shop(-s) from parent shop.
     *
     * @param int|array $aShops            Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testInheritItemsFromShops($aShops, $iExpectsToProcess)
    {
        $iParentShopId = 456;
        $sItemType     = 'oxarticles';

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('inheritItemsFromShop'));
        $oShopMapperDbGateway->expects($this->exactly($iExpectsToProcess))->method('inheritItemsFromShop')
            ->will($this->returnValue(true));

        $oShopMapper = new oxShopMapper();
        $oShopMapper->setDbGateway($oShopMapperDbGateway);

        $this->assertTrue($oShopMapper->inheritItemsFromShops($iParentShopId, $aShops, $sItemType));
    }

    /**
     * Tests remove items by type from sub shop(-s) that were inherited from parent shop.
     *
     * @param int|array $aShops            Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testRemoveInheritedItemsFromShops($aShops, $iExpectsToProcess)
    {
        $iParentShopId = 456;
        $sItemType     = 'oxarticles';

        /** @var oxShopMapperDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopMapperDbGateway */
        $oShopMapperDbGateway = $this->getMock('oxShopMapperDbGateway', array('removeInheritedItemsFromShop'));
        $oShopMapperDbGateway->expects($this->exactly($iExpectsToProcess))->method('removeInheritedItemsFromShop')
            ->will($this->returnValue(true));

        $oShopMapper = new oxShopMapper();
        $oShopMapper->setDbGateway($oShopMapperDbGateway);

        $this->assertTrue($oShopMapper->removeInheritedItemsFromShops($iParentShopId, $aShops, $sItemType));
    }
}
