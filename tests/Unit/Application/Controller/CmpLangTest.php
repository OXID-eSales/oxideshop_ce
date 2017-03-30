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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxRegistry;
use \oxTestModules;

/**
 * Language component test
 */

class CmpLangTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getConfig();
        $this->getSession();
        oxTestModules::addFunction('oxutils', 'setseoact', '{oxRegistry::getUtils()->_blSeoIsActive = $aA[0];}');
        oxNew('oxutils')->setseoact(false);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRegistry::getUtils()->seoIsActive(true);
        parent::tearDown();

    }

    // if addVoucher fnc was executed
    public function testInitSetLinkRemoveSomeFnc()
    {
        $oLangView = oxNew('oxcmp_lang');

        $oView = oxNew('oxubase');
        $oView->setClassName('basket');
        $oView->setFncName('addVoucher');
        $oConfig = $this->getConfig();
        $oConfig->setActiveView($oView);
        $oLangView->setParent($oView);
        $oLangView->setConfig($oConfig);
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
        $oView->setClassName('basket');
        $oView->setFncName('changebasket');
        $oConfig = $this->getConfig();
        $oConfig->setActiveView($oView);
        $oLangView->setParent($oView);
        $oLangView->setConfig($oConfig);
        $oLangView->init();
        $oLang = $oLangView->render();
        $sExpLink0 = $this->getConfig()->getShopCurrentURL(0) . "cl=basket&amp;fnc=changebasket";
        $sExpLink1 = $this->getConfig()->getShopCurrentURL(0) . "cl=basket&amp;fnc=changebasket&amp;lang=1";

        $this->assertEquals($sExpLink0, $oLang[0]->link);
        $this->assertEquals($sExpLink1, $oLang[1]->link);
    }
}

