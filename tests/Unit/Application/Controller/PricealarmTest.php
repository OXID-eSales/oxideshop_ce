<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxDb;
use \oxRegistry;
use \oxTestModules;

class PricealarmTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable("oxpricealarm", "oxartid");

        parent::tearDown();
    }

    public function testGetProduct()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        $pa['aid'] = '2000';
        $this->setRequestParameter('pa', $pa);

        $this->assertEquals('2000', $oPriceAlarm->getProduct()->getId());
    }

    public function testGetBidPrice()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        $pa['price'] = '10';
        $this->setRequestParameter('pa', $pa);

        $this->assertEquals('10,00', $oPriceAlarm->getBidPrice());
    }

    public function testAddme_incorectEmail()
    {
        $oDb = oxDb::getDb();

        $oPriceAlarm = $this->getProxyClass('pricealarm');

        $this->setRequestParameter("pa", array("email" => "ladyGaga"));
        $oPriceAlarm->addme();

        $this->assertEquals(0, $oPriceAlarm->getNonPublicVar("_iPriceAlarmStatus"));

        $sSql = "select count(oxid) from oxpricealarm";
        $this->assertEquals(0, $oDb->getOne($sSql));
    }

    public function testAddme_savesAndSendsPriceAlarm()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        oxTestModules::addFunction('oxEmail', 'sendPricealarmNotification', '{return 999;}');

        $this->getSession()->setVariable('usr', "testUserId");
        $aParams["email"] = "goodemail@ladyGagaFans.lt";
        $aParams["aid"] = "_testArtId";
        $aParams["price"] = "10";

        $aParams["mano"] = "101";

        $this->setRequestParameter("pa", $aParams);
        $oPriceAlarm->addme();

        $this->assertEquals(999, $oPriceAlarm->getNonPublicVar("_iPriceAlarmStatus"));

        $sSql = "select * from oxpricealarm";

        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $aAlarm = $oDb->getRow($sSql);

        $this->assertEquals($aParams["email"], $aAlarm["OXEMAIL"]);
        $this->assertEquals($aParams["aid"], $aAlarm["OXARTID"]);
        $this->assertEquals($aParams["price"], $aAlarm["OXPRICE"]);
        $this->assertEquals("testUserId", $aAlarm["OXUSERID"]);
        $this->assertEquals("EUR", $aAlarm["OXCURRENCY"]);
        $this->assertEquals(0, $aAlarm["OXLANG"]);
    }

    public function testAddme_savesCurrentActiveLang()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $oPriceAlarm = $this->getProxyClass('pricealarm');
        oxTestModules::addFunction('oxEmail', 'sendPricealarmNotification', '{return 999;}');

        $this->getSession()->setVariable('usr', "testUserId");
        $aParams["email"] = "goodemail@ladyGagaFans.lt";

        oxRegistry::getLang()->setBaseLanguage(1);
        $this->setRequestParameter("pa", $aParams);

        $oPriceAlarm->addme();

        $sSql = "select oxlang from oxpricealarm";
        $iLang = $oDb->getOne($sSql);

        $this->assertEquals(1, $iLang);
    }
}
