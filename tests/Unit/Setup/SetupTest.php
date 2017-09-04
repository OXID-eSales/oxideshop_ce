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
namespace OxidEsales\EshopCommunity\Tests\Unit\Setup;

require_once getShopBasePath() . '/Setup/functions.php';

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Edition\EditionPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionRootPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Core\ShopVersion;
use OxidEsales\EshopCommunity\Setup\Core;
use OxidEsales\EshopCommunity\Setup\Setup;

/**
 * Setup tests
 */
class SetupTest extends \OxidTestCase
{
    /**
     * Testing Setup::setTitle() and Setup::getTitle()
     */
    public function testSetTitleAndGetTitle()
    {
        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $oSetup->setTitle("testTitle");
        $this->assertEquals("testTitle", $oSetup->getTitle());
    }

    /**
     * Testing Setup::setMessage() and Setup::getMessage()
     */
    public function testSetMessageAndGetMessage()
    {
        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $oSetup->setMessage("testTMessage");
        $this->assertEquals("testTMessage", $oSetup->getMessage());
    }

    /**
     * Testing Setup::getCurrentStep()
     */
    public function testGetCurrentStep()
    {
        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar")->with($this->equalTo('istep'))->will($this->returnValue(null));

        $core = new Core();
        $oSetup = $core->getInstance('Setup');
        $oSetup = $this->getMock(get_class($oSetup), array("getInstance", "getStep"));
        $oSetup->expects($this->once())->method("getInstance")->with($this->equalTo('Utilities'))->will($this->returnValue($oUtils));
        $oSetup->expects($this->once())->method("getStep")->will($this->returnValue(1));
        $this->assertEquals(1, $oSetup->getCurrentStep());
    }

    /**
     * Testing Setup::setNextStep() and Setup::getNextStep()
     */
    public function testSetNextStepAndGetNextStep()
    {
        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $oSetup->setNextStep("testStep");
        $this->assertEquals("testStep", $oSetup->getNextStep());
    }

    /**
     * Testing Setup::alreadySetUp()
     */
    public function testAlreadySetUp()
    {
        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $this->assertTrue($oSetup->alreadySetUp());
    }

    /**
     * Testing Setup::getShopId()
     */
    public function testGetShopId()
    {
        $sBaseShopId = ShopIdCalculator::BASE_SHOP_ID;

        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $sBaseShopId = '1';
        }

        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $this->assertEquals($sBaseShopId, $oSetup->getShopId());
    }

    /**
     * Testing Setup::getSteps()
     */
    public function testGetSteps()
    {
        $iCount = 11;

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            $iCount = 9;
        }

        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $this->assertEquals($iCount, count($oSetup->getSteps()));
    }

    /**
     * Testing Setup::getStep()
     */
    public function testGetStep()
    {
        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $this->assertEquals(100, $oSetup->getStep("STEP_SYSTEMREQ"));
        $this->assertNull($oSetup->getStep("TESTID"));
    }

    /**
     * Testing Setup::getModuleClass()
     */
    public function testGetModuleClass()
    {
        $core = new Core();
        /** @var Setup $oSetup */
        $oSetup = $core->getInstance('Setup');
        $this->assertEquals('pass', $oSetup->getModuleClass(2));
        $this->assertEquals('pmin', $oSetup->getModuleClass(1));
        $this->assertEquals('null', $oSetup->getModuleClass(-1));
        $this->assertEquals('fail', $oSetup->getModuleClass(0));
    }

    public function testShopVersion()
    {
        $version = ShopVersion::getVersion();
        $this->assertNotEmpty($version);
    }
}
