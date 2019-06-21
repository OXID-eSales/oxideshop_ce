<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\EshopCommunity\Core\Model\ListModel;

use \oxField;

//class should be named Unit_oxSimpleVariantListTest
//but GAAAH IT DOES NOT WORK somehow, just because of the name??????
//T2009-04-20
class SimplevariantlistTest extends \OxidTestCase
{
    public function testSetParent()
    {
        $oSubj = $this->getProxyClass("oxSimpleVariantList");
        $oSubj->setParent("testString");
        $this->assertEquals("testString", $oSubj->getNonPublicVar("_oParent"));
    }

    public function testAssignElement()
    {
        $sParent = "someString";
        $aDbFields = array("field1" => "val1");

        $oListObjectMock = $this->getMock(\OxidEsales\Eshop\Application\Model\SimpleVariant::class, array('setParent'));
        $oListObjectMock->expects($this->once())->method('setParent')->with($sParent);

        $oSubj = $this->getProxyClass("oxSimpleVariantList");
        $oSubj->setNonPublicVar("_oParent", $sParent);
        $oSubj->UNITassignElement($oListObjectMock, $aDbFields);
    }

    //bug #441 test case for lists
    public function testParentPriceIsLoadedForVariant()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $sArtId = '1661';
            $sVariantId = '1661-01';
            $sArtPrice = 13.9;
        } else {
            $sArtId = '2077';
            $sVariantId = '8a142c4100e0b2f57.59530204';
            $sArtPrice = 19;
        }

        $oParent = $this->getProxyClass("oxArticle");
        $oParent->setInList();
        $oParent->load($sArtId);
        $oVariantList = $oParent->getVariants();

        $this->assertTrue($oVariantList instanceof ListModel);
        $this->assertTrue($oVariantList->offsetExists($sVariantId));

        $oVariant = $oVariantList->offsetGet($sVariantId);
        $oVariant->oxarticles__oxprice = new oxField(0);
        $oVariant->setPrice(null);

        $this->assertEquals($sArtPrice, $oVariant->getPrice()->getBruttoPrice());
    }
}
