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

//class should be named Unit_oxSimpleVariantListTest
//but GAAAH IT DOES NOT WORK somehow, just because of the name??????
//T2009-04-20
class Unit_Core_oxsimplevariantlistTest extends OxidTestCase
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

        $oListObjectMock = $this->getMock('oxSimpleVariant', array('setParent'));
        $oListObjectMock->expects($this->once())->method('setParent')->with($sParent);

        $oSubj = $this->getProxyClass("oxSimpleVariantList");
        $oSubj->setNonPublicVar("_oParent", $sParent);
        $oSubj->UNITassignElement($oListObjectMock, $aDbFields);
    }

    //bug #441 test case for lists
    public function testParentPriceIsLoadedForVariant()
    {
        $sArtId = '2077';
        $sVariantId = '8a142c4100e0b2f57.59530204';
        //adjust to demodata
        $sArtPrice = 19;

        $oParent = $this->getProxyClass("oxArticle");
        $oParent->setInList();
        $oParent->load($sArtId);
        $oVariantList = $oParent->getVariants();

        $this->assertTrue($oVariantList instanceof oxlist);
        $this->assertTrue($oVariantList->offsetExists($sVariantId));

        $oVariant = $oVariantList->offsetGet($sVariantId);
        $oVariant->oxarticles__oxprice = new oxField(0);
        $oVariant->setPrice(null);

        $this->assertEquals($sArtPrice, $oVariant->getPrice()->getBruttoPrice());
    }
}
