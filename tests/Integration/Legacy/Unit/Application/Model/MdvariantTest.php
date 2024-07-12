<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

/**
 * Unit_oxmdvariantTest
 *
 */
class MdvariantTest extends \PHPUnit\Framework\TestCase
{

    /**
     * OxMdVariant
     *
     * @var OxMdVariant
     */
    protected $_oSubj;

    /**
     * Setup()
     *
     * @return null;
     */
    protected function setup(): void
    {
        parent::setUp();

        $aNames = ["Red|S|Silk", "Red|M|Silk", "Red|M|Wool", "Red|L|Silk", "Red|L|Wool", "Blue|S|Silk", "Blue|M|Silk", "Blue|M|Wool", "Blue|L|Silk", "Blue|L|Wool", "Green|S|Silk", "Green|S|Wool", "Green|M|Silk", "Green|M|Wool", "Yellow|XL|Leather", "Magenta|S|Wool", "Magenta|S|Silk"];

        $aPrices = [1, 1, 1, 1, 2, 3, 4, 4, 4, 4, 4, 4, 5, 5, 1, 2, 3];
        $aArtIds = ["id01", "id02", "id03", "id04", "id05", "id06", "id07", "id08", "id09", "id10", "id11", "id12", "id13", "id14", "id15", "id16", "id17"];

        $aLinks = ["ld01", "ld02", "ld03", "ld04", "ld05", "ld06", "ld07", "ld08", "ld09", "ld10", "ld11", "ld12", "ld13", "ld14", "ld15", "ld16", "ld17"];

        $this->_oSubj = $this->getProxyClass("oxMdVariant");
        $iC = count($aNames);
        for ($i = 0; $i < $iC; $i++) {
            $this->_oSubj->addNames(
                $aArtIds[$i],
                explode("|", $aNames[$i]),
                $aPrices[$i],
                $aLinks[$i]
            );
        }
    }

    /**
     * test Id setter and getter
     */
    public function testSetGetId()
    {
        $this->_oSubj->setId("testId");
        $this->assertSame("testId", $this->_oSubj->getId());
    }

    /**
     * tests SetGetParentId method
     */
    public function testSetGetParentId()
    {
        $this->_oSubj->setParentId("testParentId");
        $this->assertSame("testParentId", $this->_oSubj->getParentId());
    }

    /**
     * tests addNames method
     */
    public function testAddNames1()
    {
        $this->_oSubj->addNames("testId", ["Yellow", "M", "Wool"], 5, "testLink");
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Yellow")->getMdSubvariantByName("M")->getMdSubvariantByName("Wool")->getDPrice(), 5);
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Yellow")->getMdSubvariantByName("M")->getMdSubvariantByName("Wool")->getArticleId(), "testId");
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Yellow")->getMdSubvariantByName("M")->getMdSubvariantByName("Wool")->getLink(), "testLink");
    }

    /**
     * tests addNames method (secondary test)
     */
    public function testAddNames2()
    {
        $oSubVariant = $this->_oSubj->getMdSubvariantByName("Blue")->getMdSubvariantByName("M");
        $this->_oSubj->addNames("testId", ["Blue", "M", "Test"], 5, "testLink");
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Blue")->getMdSubvariantByName("M")->getMdSubvariantByName("Test")->getParentId(), $oSubVariant->getId());
        $this->assertTrue((bool) $oSubVariant->getId());
    }

    /**
     * tests _setGetMdSubvariants method
     */
    public function testSetGetMdSubvariants()
    {
        $oVariant1 = oxNew('oxMdVariant');
        $oVariant1->setName("testas1");

        $oVariant2 = oxNew('oxMdVariant');
        $oVariant2->setName("testas2");

        $this->_oSubj->setMdSubvariants([$oVariant1, $oVariant2]);
        $this->assertEquals([$oVariant1, $oVariant2], $this->_oSubj->getMdSubvariants());
    }

    /**
     * tests _addMdSubvariant method
     */
    public function testAddMdSubvariant()
    {
        $oVariant1 = oxNew('oxMdVariant');
        $oVariant1->setName("testas1");
        $oVariant1->setId("testId1");

        $this->_oSubj->addMdSubvariant($oVariant1);

        $this->assertEquals($oVariant1, $this->_oSubj->getMdSubvariantByName("testas1"));
    }

    /**
     * tests getFirstMdSubvariant method
     */
    public function testGetFirstMdSubvariant()
    {
        $oVariant1 = oxNew('oxMdVariant');
        $oVariant1->setName("testas1");

        $oVariant2 = oxNew('oxMdVariant');
        $oVariant2->setName("testas2");

        $this->_oSubj->setMdSubvariants([$oVariant1, $oVariant2]);
        $this->assertSame($oVariant1, $this->_oSubj->getFirstMdSubvariant());
    }

    /**
     * tests getMdSubvariantByName method
     */
    public function testGetMdSubvariantByName()
    {
        $this->assertSame($this->_oSubj->getFirstMdSubvariant(), $this->_oSubj->getMdSubvariantByName("Red"));
    }

    /**
     * tests getMdSubvariantByName method (By creating non existing subvariant)
     */
    public function testGetMdSubvariantByNameCreatesNew()
    {
        $oGreen = $this->_oSubj->getMdSubvariantByName("Green");
        $oVariant = $oGreen->getMdSubvariantByName("XXL");
        $this->assertNotNull($oVariant);
        $this->assertSame(32, strlen((string) $oVariant->getId()));
        $this->assertEquals($oGreen->getId(), $oGreen->getMdSubvariantByName("XXL")->getParentId());
    }

    /**
     * tests getArticleId method
     */
    public function testGetArticleId()
    {
        $this->assertSame("id01", $this->_oSubj->getArticleId());
        $this->assertSame("id11", $this->_oSubj->getMdSubvariantByName("Green")->getArticleId());
        $this->assertSame("id11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getArticleId());
        $this->assertSame("id11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getArticleId());
    }

    /**
     * tests hasArticleId method
     */
    public function testHasArticleId()
    {
        $this->assertTrue($this->_oSubj->hasArticleId("id10"));
        $this->assertTrue($this->_oSubj->hasArticleId("id11"));
    }

    /**
     * tests hasArticleId method (Negative test)
     */
    public function testHasArticleIdNot()
    {
        $this->assertFalse($this->_oSubj->hasArticleId("id25"));
    }

    /**
     * tests getLink method
     */
    public function testGetLink()
    {
        $this->assertSame("ld01", $this->_oSubj->getLink());
        $this->assertSame("ld11", $this->_oSubj->getMdSubvariantByName("Green")->getLink());
        $this->assertSame("ld11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getLink());
        $this->assertSame("ld11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getLink());
    }

    /**
     * tests name setter and getter method
     */
    public function testSetGetName()
    {
        $this->_oSubj->setName("testName");
        $this->assertSame("testName", $this->_oSubj->getName());
    }

    /**
     * tests getDPrice method
     */
    public function testGetDPrice()
    {
        $this->assertNull($this->_oSubj->getDPrice());
        $this->assertNull($this->_oSubj->getMdSubvariantByName("Green")->getDPrice());
        $this->assertNull($this->_oSubj->getMdSubvariantByName("Red")->getDPrice());
        $this->assertNull($this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getDPrice());
        $this->assertSame(4, $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getDPrice());
        $this->assertSame(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getDPrice());
    }

    /**
     * tests _isFixedPrice method
     */
    public function testIsFixedPrice()
    {
        $this->assertFalse($this->_oSubj->isFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Green")->isFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->isFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->isFixedPrice());
        $this->assertFalse($this->_oSubj->getMdSubvariantByName("Red")->isFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("M")->isFixedPrice());
        $this->assertFalse($this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->isFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->isFixedPrice());
    }

    /**
     * tests getMinDPrice method
     */
    public function testGetMinDPrice()
    {
        $this->assertSame(1, $this->_oSubj->getMinDPrice());
        $this->assertSame(1, $this->_oSubj->getMdSubvariantByName("Red")->getMinDPrice());
        $this->assertSame(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMinDPrice());
        $this->assertSame(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->getMinDPrice());
        $this->assertSame(2, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Wool")->getMinDPrice());
    }

    /**
     * tests getMaxDepth method
     */
    public function testGetMaxDepth()
    {
        $this->assertSame(3, $this->_oSubj->getMaxDepth());
        $this->assertSame(2, $this->_oSubj->getMdSubvariantByName("Red")->getMaxDepth());
        $this->assertSame(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMaxDepth());
        $this->assertSame(0, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->getMaxDepth());
        $this->assertSame(0, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Wool")->getMaxDepth());
    }

    /**
     * tests getFPrice method
     */
    public function testGetFPrice()
    {
        $this->assertSame('ab 1,00 €', $this->_oSubj->getFPrice());
        $this->assertSame('ab 1,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getFPrice());
        $this->assertSame('ab 1,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getFPrice());
        $this->assertSame('1,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->getFPrice());
        $this->assertSame('2,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Wool")->getFPrice());
    }

    /**
     * 0002030: Option "Calculate Product Price" does not work with variants.
     * Check if no price returned when unset Calculate Product Price.
     */
    public function testGetPriceNoPriceCalculate()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadPrice', false);

        $oSubj = oxNew('oxMdVariant');
        $sArtId = '';
        $aNames = [];
        $dPrice = 10.10;
        $sUrl = '';
        $oSubj->addNames($sArtId, $aNames, $dPrice, $sUrl);
        $iPrice = $oSubj->getFPrice();
        $this->assertEmpty($iPrice);
    }
}
