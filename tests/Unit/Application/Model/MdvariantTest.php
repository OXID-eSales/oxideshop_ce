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
class MdvariantTest extends \OxidTestCase
{

    /**
     * OxMdVariant
     *
     * @var OxMdVariant
     */
    protected $_oSubj = null;

    /**
     * Setup()
     *
     * @return null;
     */
    public function setup()
    {
        parent::setUp();

        $aNames = array("Red|S|Silk",
                        "Red|M|Silk",
                        "Red|M|Wool",
                        "Red|L|Silk",
                        "Red|L|Wool",
                        "Blue|S|Silk",
                        "Blue|M|Silk",
                        "Blue|M|Wool",
                        "Blue|L|Silk",
                        "Blue|L|Wool",
                        "Green|S|Silk",
                        "Green|S|Wool",
                        "Green|M|Silk",
                        "Green|M|Wool",
                        "Yellow|XL|Leather",
                        "Magenta|S|Wool",
                        "Magenta|S|Silk"
        );

        $aPrices = array(1, 1, 1, 1, 2, 3, 4, 4, 4, 4, 4, 4, 5, 5, 1, 2, 3);
        $aArtIds = array("id01",
                         "id02",
                         "id03",
                         "id04",
                         "id05",
                         "id06",
                         "id07",
                         "id08",
                         "id09",
                         "id10",
                         "id11",
                         "id12",
                         "id13",
                         "id14",
                         "id15",
                         "id16",
                         "id17",
        );

        $aLinks = array("ld01",
                        "ld02",
                        "ld03",
                        "ld04",
                        "ld05",
                        "ld06",
                        "ld07",
                        "ld08",
                        "ld09",
                        "ld10",
                        "ld11",
                        "ld12",
                        "ld13",
                        "ld14",
                        "ld15",
                        "ld16",
                        "ld17",
        );

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
     *
     * @return null
     */
    public function testSetGetId()
    {
        $this->_oSubj->setId("testId");
        $this->assertEquals("testId", $this->_oSubj->getId());
    }

    /**
     * tests SetGetParentId method
     *
     * @return null
     */
    public function testSetGetParentId()
    {
        $this->_oSubj->setParentId("testParentId");
        $this->assertEquals("testParentId", $this->_oSubj->getParentId());
    }

    /**
     * tests addNames method
     *
     * @return null
     */
    public function testAddNames1()
    {
        $this->_oSubj->addNames("testId", array("Yellow", "M", "Wool"), 5, "testLink");
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Yellow")->getMdSubvariantByName("M")->getMdSubvariantByName("Wool")->getDPrice(), 5);
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Yellow")->getMdSubvariantByName("M")->getMdSubvariantByName("Wool")->getArticleId(), "testId");
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Yellow")->getMdSubvariantByName("M")->getMdSubvariantByName("Wool")->getLink(), "testLink");
    }

    /**
     * tests addNames method (secondary test)
     *
     * @return null
     */
    public function testAddNames2()
    {
        $oSubVariant = $this->_oSubj->getMdSubvariantByName("Blue")->getMdSubvariantByName("M");
        $oNewSubVariant = $this->_oSubj->addNames("testId", array("Blue", "M", "Test"), 5, "testLink");
        $this->assertEquals($this->_oSubj->getMdSubvariantByName("Blue")->getMdSubvariantByName("M")->getMdSubvariantByName("Test")->getParentId(), $oSubVariant->getId());
        $this->assertTrue((bool) $oSubVariant->getId());
    }

    /**
     * tests _setGetMdSubvariants method
     *
     * @return null
     */
    public function testSetGetMdSubvariants()
    {
        $oVariant1 = oxNew('oxMdVariant');
        $oVariant1->setName("testas1");

        $oVariant2 = oxNew('oxMdVariant');
        $oVariant2->setName("testas2");

        $this->_oSubj->setMdSubvariants(array($oVariant1, $oVariant2));
        $this->assertEquals(array($oVariant1, $oVariant2), $this->_oSubj->getMdSubvariants());
    }

    /**
     * tests _addMdSubvariant method
     *
     * @return null
     */
    public function testAddMdSubvariant()
    {
        $oVariant1 = oxNew('oxMdVariant');
        $oVariant1->setName("testas1");
        $oVariant1->setId("testId1");

        $this->_oSubj->UNITaddMdSubvariant($oVariant1);

        $this->assertEquals($oVariant1, $this->_oSubj->getMdSubvariantByName("testas1"));
    }

    /**
     * tests getFirstMdSubvariant method
     *
     * @return null
     */
    public function testGetFirstMdSubvariant()
    {
        $oVariant1 = oxNew('oxMdVariant');
        $oVariant1->setName("testas1");

        $oVariant2 = oxNew('oxMdVariant');
        $oVariant2->setName("testas2");

        $this->_oSubj->setMdSubvariants(array($oVariant1, $oVariant2));
        $this->assertSame($oVariant1, $this->_oSubj->getFirstMdSubvariant());
    }

    /**
     * tests getMdSubvariantByName method
     *
     * @return null
     */
    public function testGetMdSubvariantByName()
    {
        $this->assertSame($this->_oSubj->getFirstMdSubvariant(), $this->_oSubj->getMdSubvariantByName("Red"));
    }

    /**
     * tests getMdSubvariantByName method (By creating non existing subvariant)
     *
     * @return null
     */
    public function testGetMdSubvariantByNameCreatesNew()
    {
        $oGreen = $this->_oSubj->getMdSubvariantByName("Green");
        $oVariant = $oGreen->getMdSubvariantByName("XXL");
        $this->assertNotNull($oVariant);
        $this->assertEquals(32, strlen($oVariant->getId()));
        $this->assertEquals($oGreen->getId(), $oGreen->getMdSubvariantByName("XXL")->getParentId());
    }

    /**
     * tests getArticleId method
     *
     * @return null
     */
    public function testGetArticleId()
    {
        $this->assertEquals("id01", $this->_oSubj->getArticleId());
        $this->assertEquals("id11", $this->_oSubj->getMdSubvariantByName("Green")->getArticleId());
        $this->assertEquals("id11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getArticleId());
        $this->assertEquals("id11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getArticleId());
    }

    /**
     * tests hasArticleId method
     *
     * @return null
     */
    public function testHasArticleId()
    {
        $this->assertTrue($this->_oSubj->hasArticleId("id10"));
        $this->assertTrue($this->_oSubj->hasArticleId("id11"));
    }

    /**
     * tests hasArticleId method (Negative test)
     *
     * @return null
     */
    public function testHasArticleIdNot()
    {
        $this->assertFalse($this->_oSubj->hasArticleId("id25"));
    }

    /**
     * tests getLink method
     *
     * @return null
     */
    public function testGetLink()
    {
        $this->assertEquals("ld01", $this->_oSubj->getLink());
        $this->assertEquals("ld11", $this->_oSubj->getMdSubvariantByName("Green")->getLink());
        $this->assertEquals("ld11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getLink());
        $this->assertEquals("ld11", $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getLink());
    }

    /**
     * tests name setter and getter method
     *
     * @return null
     */
    public function testSetGetName()
    {
        $this->_oSubj->setName("testName");
        $this->assertEquals("testName", $this->_oSubj->getName());
    }

    /**
     * tests getDPrice method
     *
     * @return null
     */
    public function testGetDPrice()
    {
        $this->assertNull($this->_oSubj->getDPrice());
        $this->assertNull($this->_oSubj->getMdSubvariantByName("Green")->getDPrice());
        $this->assertNull($this->_oSubj->getMdSubvariantByName("Red")->getDPrice());
        $this->assertNull($this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getDPrice());
        $this->assertEquals(4, $this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getDPrice());
        $this->assertEquals(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->getDPrice());
    }

    /**
     * tests _isFixedPrice method
     *
     * @return null
     */
    public function testIsFixedPrice()
    {
        $this->assertFalse($this->_oSubj->UNITisFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Green")->UNITisFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->UNITisFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Green")->getMdSubvariantByName("S")->getMdSubvariantByName("Silk")->UNITisFixedPrice());
        $this->assertFalse($this->_oSubj->getMdSubvariantByName("Red")->UNITisFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("M")->UNITisFixedPrice());
        $this->assertFalse($this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->UNITisFixedPrice());
        $this->assertTrue($this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->UNITisFixedPrice());
    }

    /**
     * tests getMinDPrice method
     *
     * @return null
     */
    public function testGetMinDPrice()
    {
        $this->assertEquals(1, $this->_oSubj->getMinDPrice());
        $this->assertEquals(1, $this->_oSubj->getMdSubvariantByName("Red")->getMinDPrice());
        $this->assertEquals(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMinDPrice());
        $this->assertEquals(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->getMinDPrice());
        $this->assertEquals(2, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Wool")->getMinDPrice());
    }

    /**
     * tests getMaxDepth method
     *
     * @return null
     */
    public function testGetMaxDepth()
    {
        $this->assertEquals(3, $this->_oSubj->getMaxDepth());
        $this->assertEquals(2, $this->_oSubj->getMdSubvariantByName("Red")->getMaxDepth());
        $this->assertEquals(1, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMaxDepth());
        $this->assertEquals(0, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->getMaxDepth());
        $this->assertEquals(0, $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Wool")->getMaxDepth());
    }

    /**
     * tests getFPrice method
     *
     * @return null
     */
    public function testGetFPrice()
    {
        $this->assertEquals('ab 1,00 €', $this->_oSubj->getFPrice());
        $this->assertEquals('ab 1,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getFPrice());
        $this->assertEquals('ab 1,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getFPrice());
        $this->assertEquals('1,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Silk")->getFPrice());
        $this->assertEquals('2,00 €', $this->_oSubj->getMdSubvariantByName("Red")->getMdSubvariantByName("L")->getMdSubvariantByName("Wool")->getFPrice());
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
        $aNames = array();
        $dPrice = 10.10;
        $sUrl = '';
        $oSubj->addNames($sArtId, $aNames, $dPrice, $sUrl);
        $iPrice = $oSubj->getFPrice();
        $this->assertTrue(empty($iPrice));
    }
}
