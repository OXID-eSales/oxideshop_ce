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

require_once getShopBasePath() . '/Setup/functions.php';
use OxidEsales\Eshop\Setup\Setup;

/**
 * Setup tests
 */
class SetupTest extends OxidTestCase
{
    /**
     * Testing Setup::setTitle() and Setup::getTitle()
     *
     * @return null
     */
    public function testSetTitleAndGetTitle()
    {
        $oSetup = new Setup();
        $oSetup->setTitle("testTitle");
        $this->assertEquals("testTitle", $oSetup->getTitle());
    }

    /**
     * Testing Setup::setMessage() and Setup::getMessage()
     *
     * @return null
     */
    public function testSetMessageAndGetMessage()
    {
        $oSetup = new Setup();
        $oSetup->setMessage("testTMessage");
        $this->assertEquals("testTMessage", $oSetup->getMessage());
    }

    /**
     * Testing Setup::getCurrentStep()
     *
     * @return null
     */
    public function testGetCurrentStep()
    {
        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar")->with($this->equalTo('istep'))->will($this->returnValue(null));

        $oSetup = $this->getMock("OxidEsales\\Eshop\\Setup\\Setup", array("getInstance", "getStep"));
        $oSetup->expects($this->once())->method("getInstance")->with($this->equalTo('Utilities'))->will($this->returnValue($oUtils));
        $oSetup->expects($this->once())->method("getStep")->will($this->returnValue(1));
        $this->assertEquals(1, $oSetup->getCurrentStep());
    }

    /**
     * Testing Setup::setNextStep() and Setup::getNextStep()
     *
     * @return null
     */
    public function testSetNextStepAndGetNextStep()
    {
        $oSetup = new Setup();
        $oSetup->setNextStep("testStep");
        $this->assertEquals("testStep", $oSetup->getNextStep());
    }

    /**
     * Testing Setup::alreadySetUp()
     *
     * @return null
     */
    public function testAlreadySetUp()
    {
        $oSetup = new Setup();
        $this->assertTrue($oSetup->alreadySetUp());
    }

    /**
     * Testing Setup::getShopId()
     *
     * @return null
     */
    public function testGetShopId()
    {
        $sBaseShopId = 'oxbaseshop';

        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $sBaseShopId = '1';
        }

        $oSetup = new Setup();
        $this->assertEquals($sBaseShopId, $oSetup->getShopId());
    }

    /**
     * Testing Setup::getSteps()
     *
     * @return null
     */
    public function testGetSteps()
    {
        $iCount = 11;

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            $iCount = 9;
        }

        $oSetup = new Setup();
        $this->assertEquals($iCount, count($oSetup->getSteps()));
    }

    /**
     * Testing Setup::getStep()
     *
     * @return null
     */
    public function testGetStep()
    {
        $oSetup = new Setup();
        $this->assertEquals(100, $oSetup->getStep("STEP_SYSTEMREQ"));
        $this->assertNull($oSetup->getStep("TESTID"));
    }

    /**
     * Testing Setup::getVersionPrefix()
     *
     * @return null
     */
    public function testGetVersionPrefix()
    {
        $oSetup = new Setup();
        $sVerPrefix = '';

        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $sVerPrefix = '_ee';
        }
        if ($this->getTestConfig()->getShopEdition() === 'PE') {
            $sVerPrefix = '_pe';
        }

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            $sVerPrefix = '_ce';
        }

        $this->assertEquals($sVerPrefix, $oSetup->getVersionPrefix());
    }

    /**
     * Testing Setup::getModuleClass()
     *
     * @return null
     */
    public function testGetModuleClass()
    {
        $oSetup = new Setup();
        $this->assertEquals('pass', $oSetup->getModuleClass(2));
        $this->assertEquals('pmin', $oSetup->getModuleClass(1));
        $this->assertEquals('null', $oSetup->getModuleClass(-1));
        $this->assertEquals('fail', $oSetup->getModuleClass(0));
    }
}
