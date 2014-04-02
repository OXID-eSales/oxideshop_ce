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
class Unit_Core_oxShopRelationsTest extends OxidTestCase
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
     * Tests construct method with shop IDs parameter provided.
     *
     * @param int|array $aShopIds          Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testConstructWithParamShopIdsProvided($aShopIds, $iExpectsToProcess)
    {
        $oShopRelations = new oxShopRelations($aShopIds);

        $this->assertTrue(is_array($oShopRelations->getShopIds()));
        $this->assertEquals($iExpectsToProcess, count($oShopRelations->getShopIds()));
    }

    /**
     * Tests set/get database gateway.
     */
    public function testSetGetDbGateway()
    {
        $oShopRelations = new oxShopRelations(null);

        // assert default gateway
        $this->isInstanceOf('oxShopRelationsDbGateway', $oShopRelations->getDbGateway());

        $oCustomDbGateway = new stdClass();

        $oShopRelations->setDbGateway($oCustomDbGateway);
        $this->assertSame($oCustomDbGateway, $oShopRelations->getDbGateway());
    }

    /**
     * Tests set/get shop ID or list of shop IDs
     *
     * @param int|array $aShopIds          Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testSetGetShopIds($aShopIds, $iExpectsToProcess)
    {
        $oShopRelations = new oxShopRelations(null);

        $oShopRelations->setShopIds($aShopIds);

        $this->assertTrue(is_array($oShopRelations->getShopIds()));
        $this->assertEquals($iExpectsToProcess, count($oShopRelations->getShopIds()));
    }

    /**
     * Tests add item to shop or list of shops.
     *
     * @param int|array $aShopIds          Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testAddToShop($aShopIds, $iExpectsToProcess)
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('addToShop'));
        $oShopRelationsDbGateway->expects($this->exactly($iExpectsToProcess))->method('addToShop')
            ->will($this->returnValue(true));

        $oShopRelations = new oxShopRelations($aShopIds);
        $oShopRelations->setDbGateway($oShopRelationsDbGateway);

        $oShopRelations->addToShop($iItemId, $sItemType);
    }

    /**
     * Tests remove item from shop or list of shops.
     *
     * @param int|array $aShopIds          Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testRemoveFromShop($aShopIds, $iExpectsToProcess)
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('removeFromShop'));
        $oShopRelationsDbGateway->expects($this->exactly($iExpectsToProcess))->method('removeFromShop')
            ->will($this->returnValue(true));

        $oShopRelations = new oxShopRelations($aShopIds);
        $oShopRelations->setDbGateway($oShopRelationsDbGateway);

        $oShopRelations->removeFromShop($iItemId, $sItemType);
    }

    /**
     * Tests inherit items by type to sub shop(-s) from parent shop.
     *
     * @param int|array $aShopIds          Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testInheritFromShop($aShopIds, $iExpectsToProcess)
    {
        $iParentShopId = 456;
        $sItemType     = 'oxarticles';

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('inheritFromShop'));
        $oShopRelationsDbGateway->expects($this->exactly($iExpectsToProcess))->method('inheritFromShop')
            ->will($this->returnValue(true));

        $oShopRelations = new oxShopRelations($aShopIds);
        $oShopRelations->setDbGateway($oShopRelationsDbGateway);

        $oShopRelations->inheritFromShop($iParentShopId, $sItemType);
    }

    /**
     * Tests remove items by type from sub shop(-s) that were inherited from parent shop.
     *
     * @param int|array $aShopIds          Shop ID or list of shop IDs.
     * @param int       $iExpectsToProcess Number of shops expected to be processed.
     *
     * @dataProvider _dpTestListOfShops
     */
    public function testRemoveInheritedFromShop($aShopIds, $iExpectsToProcess)
    {
        $iParentShopId = 456;
        $sItemType     = 'oxarticles';

        /** @var oxShopRelationsDbGateway|PHPUnit_Framework_MockObject_MockObject $oShopRelationsDbGateway */
        $oShopRelationsDbGateway = $this->getMock('oxShopRelationsDbGateway', array('removeInheritedFromShop'));
        $oShopRelationsDbGateway->expects($this->exactly($iExpectsToProcess))->method('removeInheritedFromShop')
            ->will($this->returnValue(true));

        $oShopRelations = new oxShopRelations($aShopIds);
        $oShopRelations->setDbGateway($oShopRelationsDbGateway);

        $oShopRelations->removeInheritedFromShop($iParentShopId, $sItemType);
    }
}
