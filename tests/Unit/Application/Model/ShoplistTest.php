<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

/**
 * Testing oxshoplist class
 */
class ShoplistTest extends \OxidTestCase
{
    /**
     * All shop list test
     */
    public function testGetAll()
    {
        /** @var oxShopList|PHPUnit\Framework\MockObject\MockObject $oShopList */
        $oShopList = $this->getMock(\OxidEsales\Eshop\Application\Model\ShopList::class, array('selectString', 'setBaseObject'));
        $oShopList->expects($this->once())->method('selectString');
        $oShopList->getAll();
    }

    /**
     * Tests method getOne for returning shop list
     */
    public function testGetIdTitleList()
    {
        /** @var oxShopList|PHPUnit\Framework\MockObject\MockObject $oShopList */
        $oShopList = $this->getMock(\OxidEsales\Eshop\Application\Model\ShopList::class, array('selectString', 'setBaseObject'));
        $oShopList->expects($this->once())->method('setBaseObject');
        $oShopList->expects($this->once())->method('selectString');
        $oShopList->getIdTitleList();
    }
}
