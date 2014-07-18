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

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxobject2CategoryTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxobject2category');
        parent::tearDown();
    }

    /**
     * Tests setter and getter of product id
     */
    public function testSetGetProductId()
    {
        $oObject2Category = new oxObject2Category();
        $oObject2Category->setProductId('_testProduct');
        $this->assertEquals('_testProduct', $oObject2Category->getProductId());
        $this->assertEquals('_testProduct', $oObject2Category->oxobject2category__oxobjectid->value);
    }

    /**
     * Tests setter and getter of category id
     */
    public function testSetGetCategoryId()
    {
        $oObject2Category = new oxObject2Category();
        $oObject2Category->setCategoryId('_testProduct');
        $this->assertEquals('_testProduct', $oObject2Category->getCategoryId());
        $this->assertEquals('_testProduct', $oObject2Category->oxobject2category__oxcatnid->value);
    }

    /**
     * Tests if category assignment is added for inherited subshops
     */
    public function testAddElement2ShopRelations()
    {
        $oElement2ShopRelations = $this->getMock("oxElement2ShopRelations", array('setShopIds', 'addObjectToShop'), array('oxobject2category'));
        $oElement2ShopRelations->expects($this->once())->method('setShopIds')->with(array(3));
        $oElement2ShopRelations->expects($this->once())->method('addObjectToShop');

        $oObject2Category = $this->getMock("oxObject2Category", array('_getInheritanceGroup', '_getElement2ShopRelations'));
        $oObject2Category->expects($this->once())->method('_getInheritanceGroup')->will($this->returnValue(array(2,3)));
        $oObject2Category->expects($this->once())->method('_getElement2ShopRelations')->will($this->returnValue($oElement2ShopRelations));
        $oObject2Category->setId('_testId');
        $oObject2Category->setCategoryId('_testProduct');
        $oObject2Category->setProductId('_testProduct');
        $oObject2Category->save();
    }

    /**
     * Tests if category assignment has no subshops
     */
    public function testAddElement2ShopRelationsNoSubShops()
    {
        $oElement2ShopRelations = $this->getMock("oxElement2ShopRelations", array('setShopIds', 'addObjectToShop'), array('oxobject2category'));
        $oElement2ShopRelations->expects($this->never())->method('setShopIds');
        $oElement2ShopRelations->expects($this->never())->method('addObjectToShop');

        $oObject2Category = $this->getMock("oxObject2Category", array('_getInheritanceGroup', '_getElement2ShopRelations'));
        $oObject2Category->expects($this->once())->method('_getInheritanceGroup')->will($this->returnValue(array(1)));
        $oObject2Category->expects($this->never())->method('_getElement2ShopRelations')->will($this->returnValue($oElement2ShopRelations));
        $oObject2Category->setId('_testId');
        $oObject2Category->setCategoryId('_testProduct');
        $oObject2Category->setProductId('_testProduct');
        $oObject2Category->save();
    }

}
