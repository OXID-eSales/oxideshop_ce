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

class Unit_Core_oxobject2CategoryTest extends OxidTestCase
{

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

}
