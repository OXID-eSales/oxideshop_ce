<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use \oxRegistry;
use \oxTestModules;

/**
 * Language component test
 */

class CmpLangTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->getConfig();
        $this->getSession();
        oxTestModules::addFunction('oxutils', 'setseoact', '{oxRegistry::getUtils()->_blSeoIsActive = $aA[0];}');
        oxNew('oxutils')->setseoact(false);
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxRegistry::getUtils()->seoIsActive(true);
        parent::tearDown();
    }

    // if addVoucher fnc was executed
    public function testInitSetLinkRemoveSomeFnc()
    {
        $oLangView = oxNew('oxcmp_lang');

        $oView = oxNew('oxubase');
        $oView->setClassKey('basket');
        $oView->setFncName('addVoucher');

        $oConfig = $this->getConfig();
        $oConfig->setActiveView($oView);

        $oLangView->setParent($oView);
        Registry::set(Config::class, $oConfig);
        $oLangView->init();
        $oLang = $oLangView->render();
        $sExpLink0 = $this->getConfig()->getShopCurrentURL(0) . "cl=basket";
        $sExpLink1 = $this->getConfig()->getShopCurrentURL(0) . "cl=basket&amp;lang=1";

        $this->assertEquals($sExpLink0, $oLang[0]->link);
        $this->assertEquals($sExpLink1, $oLang[1]->link);
    }

    public function testInitSetLink()
    {
        $oLangView = oxNew('oxcmp_lang');

        $oView = oxNew('oxubase');
        $oView->setClassKey('basket');
        $oView->setFncName('changebasket');

        $oConfig = $this->getConfig();
        $oConfig->setActiveView($oView);

        $oLangView->setParent($oView);
        Registry::set(Config::class, $oConfig);
        $oLangView->init();
        $oLang = $oLangView->render();
        $sExpLink0 = $this->getConfig()->getShopCurrentURL(0) . "cl=basket&amp;fnc=changebasket";
        $sExpLink1 = $this->getConfig()->getShopCurrentURL(0) . "cl=basket&amp;fnc=changebasket&amp;lang=1";

        $this->assertEquals($sExpLink0, $oLang[0]->link);
        $this->assertEquals($sExpLink1, $oLang[1]->link);
    }
}
