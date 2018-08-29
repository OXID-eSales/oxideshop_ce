<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
