<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxDb;
use \oxRegistry;
use \oxTestModules;

class PricealarmTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable("oxpricealarm", "oxartid");

        parent::tearDown();
    }

    public function testGetProduct()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        $pa['aid'] = '2000';
        $this->setRequestParameter('pa', $pa);

        $this->assertSame('2000', $oPriceAlarm->getProduct()->getId());
    }

    public function testGetBidPrice()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        $pa['price'] = '10';
        $this->setRequestParameter('pa', $pa);

        $this->assertSame('10,00', $oPriceAlarm->getBidPrice());
    }

    public function testAddme_incorectEmail()
    {
        $oDb = oxDb::getDb();

        $oPriceAlarm = $this->getProxyClass('pricealarm');

        $this->setRequestParameter("pa", ["email" => "ladyGaga"]);
        $oPriceAlarm->addme();

        $this->assertSame(0, $oPriceAlarm->getNonPublicVar("_iPriceAlarmStatus"));

        $sSql = "select count(oxid) from oxpricealarm";
        $this->assertSame(0, $oDb->getOne($sSql));
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

        $this->assertSame(999, $oPriceAlarm->getNonPublicVar("_iPriceAlarmStatus"));

        $sSql = "select * from oxpricealarm";

        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $aAlarm = $oDb->getRow($sSql);

        $this->assertSame($aParams["email"], $aAlarm["OXEMAIL"]);
        $this->assertSame($aParams["aid"], $aAlarm["OXARTID"]);
        $this->assertSame($aParams["price"], $aAlarm["OXPRICE"]);
        $this->assertSame("testUserId", $aAlarm["OXUSERID"]);
        $this->assertSame("EUR", $aAlarm["OXCURRENCY"]);
        $this->assertSame(0, $aAlarm["OXLANG"]);
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

        $this->assertSame(1, $iLang);
    }
}
