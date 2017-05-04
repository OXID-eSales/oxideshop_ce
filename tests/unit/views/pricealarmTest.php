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

class Unit_Views_pricealarmTest extends OxidTestCase
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
        modConfig::setRequestParameter('pa', $pa);

        $this->assertEquals('2000', $oPriceAlarm->getProduct()->getId());
    }

    public function testGetBidPrice()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        $pa['price'] = '10';
        modConfig::setRequestParameter('pa', $pa);

        $this->assertEquals('10,00', $oPriceAlarm->getBidPrice());
    }

    public function testGetPriceAlarmStatus()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        $oPriceAlarm->addme();

        $this->assertEquals(2, $oPriceAlarm->getPriceAlarmStatus());
    }

    public function testAddme_incorectCaptcha()
    {
        $oDb = oxDb::getDb();

        $oPriceAlarm = $this->getProxyClass('pricealarm');
        modConfig::setRequestParameter("c_mac", "aa");
        modConfig::setRequestParameter("c_mach", "bb");

        $oPriceAlarm->addme();

        $this->assertEquals(2, $oPriceAlarm->getNonPublicVar("_iPriceAlarmStatus"));

        $sSql = "select count(oxid) from oxpricealarm";
        $this->assertEquals(0, $oDb->getOne($sSql));
    }

    public function testAddme_incorectEmail()
    {
        $oDb = oxDb::getDb();

        $oPriceAlarm = $this->getProxyClass('pricealarm');
        oxTestModules::addFunction('oxCaptcha', 'pass', '{return true;}');

        modConfig::setRequestParameter("pa", array("email" => "ladyGaga"));
        $oPriceAlarm->addme();

        $this->assertEquals(0, $oPriceAlarm->getNonPublicVar("_iPriceAlarmStatus"));

        $sSql = "select count(oxid) from oxpricealarm";
        $this->assertEquals(0, $oDb->getOne($sSql));
    }

    public function testAddme_savesAndSendsPriceAlarm()
    {
        $oPriceAlarm = $this->getProxyClass('pricealarm');
        oxTestModules::addFunction('oxCaptcha', 'pass', '{return true;}');
        oxTestModules::addFunction('oxEmail', 'sendPricealarmNotification', '{return 999;}');

        modSession::getInstance()->setVar('usr', "testUserId");
        $aParams["email"] = "goodemail@ladyGagaFans.lt";
        $aParams["aid"] = "_testArtId";
        $aParams["price"] = "10";

        $aParams["mano"] = "101";

        modConfig::setRequestParameter("pa", $aParams);
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
        oxTestModules::addFunction('oxCaptcha', 'pass', '{return true;}');
        oxTestModules::addFunction('oxEmail', 'sendPricealarmNotification', '{return 999;}');

        modSession::getInstance()->setVar('usr', "testUserId");
        $aParams["email"] = "goodemail@ladyGagaFans.lt";

        oxRegistry::getLang()->setBaseLanguage(1);
        modConfig::setRequestParameter("pa", $aParams);

        $oPriceAlarm->addme();

        $sSql = "select oxlang from oxpricealarm";
        $iLang = $oDb->getOne($sSql);

        $this->assertEquals(1, $iLang);
    }

}
