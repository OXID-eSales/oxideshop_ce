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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Models;

/**
 * Testing oxShopList class
 */
class ShopListTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        for ($i = 2; $i < 5; $i++) {
            if ($this->getTestConfig()->getShopEdition() == 'EE') {
                $query = "INSERT INTO `oxshops` (OXID, OXACTIVE, OXNAME, OXPARENTID) VALUES ($i, 1, 'Test Shop $i', 1)";
            } else {
                $query = "INSERT INTO `oxshops` (OXID, OXACTIVE, OXNAME) VALUES ($i, 1, 'Test Shop $i')";
            }
            $this->addToDatabase($query, 'oxshops');
        }

        $this->addTableForCleanup('oxshops');
    }

    /**
     * All shop list test
     */
    public function testGetAll()
    {
        $oShopList = oxNew('oxShopList');
        $oShopList->getAll();
        $this->assertEquals(4, $oShopList->count());
    }

    /**
     * Tests method getOne for returning shop list
     */
    public function testGetIdTitleList()
    {
        $oShopList = oxNew('oxShopList');
        $oShopList->getIdTitleList();
        $this->assertEquals(4, $oShopList->count());
    }
}
