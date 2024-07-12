<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class ShopViewValidatorTest extends \OxidTestCase
{

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetMultiLangTables()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setMultiLangTables(["table1", "table2"]);

        $aList = $oValidator->getMultiLangTables();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("table1", $aList[0]);
        $this->assertEquals("table2", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetMultiShopTables()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setMultiShopTables(["table3", "table4"]);

        $aList = $oValidator->getMultiShopTables();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("table3", $aList[0]);
        $this->assertEquals("table4", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetLanguages()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setLanguages(["de", "xx"]);

        $aList = $oValidator->getLanguages();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("de", $aList[0]);
        $this->assertEquals("xx", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetAllShopLanguages()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setAllShopLanguages(["de", "xx"]);

        $aList = $oValidator->getAllShopLanguages();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("de", $aList[0]);
        $this->assertEquals("xx", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetShopId()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setShopId(100);

        $this->assertEquals(100, $oValidator->getShopId());
    }

    /**
     * Tests getting list of invalid views
     */
    public function testGetInvalidViews()
    {
        $aAllShopViews = $this->getShopViews();

        $aAllViews = $aAllShopViews['baseshop'];
        $aAllShopLanguageIds = $aLanguageIds = [0 => 'de', 1 => 'en'];

        $oValidator = $this->getMock(\OxidEsales\Eshop\Application\Model\ShopViewValidator::class, ['getAllViews']);
        $oValidator->expects($this->once())->method('getAllViews')->will($this->returnValue($aAllViews));

        $oValidator->setShopId(1);
        $oValidator->setLanguages($aLanguageIds);
        $oValidator->setAllShopLanguages($aAllShopLanguageIds);
        $oValidator->setMultiLangTables(['oxartextends', 'oxarticles']);
        $oValidator->setMultiShopTables(['oxarticles']);

        $aResult = $oValidator->getInvalidViews();

        $this->assertEquals(3, count($aResult));
        $this->assertContains('oxv_oxartextends_lt', $aResult);
        $this->assertContains('oxv_oxarticles_lt', $aResult);

        $this->assertContains('oxv_oxarticles_ru', $aResult);
    }

    /**
     * @return array
     */
    private function getShopViews()
    {
        return ['baseshop'  => ['oxv_oxartextends', 'oxv_oxartextends_en', 'oxv_oxartextends_de', 'oxv_oxartextends_lt', 'oxv_oxarticles', 'oxv_oxarticles_en', 'oxv_oxarticles_de', 'oxv_oxarticles_lt', 'oxv_oxarticles_ru'], 'multishop' => ['oxv_oxarticles_1', 'oxv_oxarticles_1_en', 'oxv_oxarticles_1_de', 'oxv_oxarticles_1_lt', 'oxv_oxarticles_1_ru', 'oxv_oxarticles_10', 'oxv_oxarticles_10_en', 'oxv_oxarticles_10_de', 'oxv_oxarticles_10_lt', 'oxv_oxarticles_10_ru', 'oxv_oxarticles_19', 'oxv_oxarticles_19_en', 'oxv_oxarticles_19_de', 'oxv_oxarticles_19_lt', 'oxv_oxarticles_19_ru']];
    }
}
