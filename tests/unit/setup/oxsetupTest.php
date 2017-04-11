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

require_once getShopBasePath() . '/setup/oxsetup.php';

/**
 * oxSetup tests
 */
class Unit_Setup_oxSetupTest extends OxidTestCase
{

    /**
     * Testing oxSetup::setTitle() and oxSetup::getTitle()
     *
     * @return null
     */
    public function testSetTitleAndGetTitle()
    {
        $oSetup = new OxSetup();
        $oSetup->setTitle("testTitle");
        $this->assertEquals("testTitle", $oSetup->getTitle());
    }

    /**
     * Testing if ViewConfig class gives us the version information
     */
    public function testVersionConfig()
    {
        $versionConfig = new VersionConfig();
        $this->assertNotEmpty($versionConfig->version);
    }

    /**
     * Testing if ViewConfig class gives us the version information
     */
    public function testVersionConfigParams()
    {
        // creating test file
        $versionFile = oxRegistry::getConfig()->getConfigParam('sCompileDir') . '/' . uniqid("version_");
        $contents = array('<?php', '$this->version = "specialVersion";');
        file_put_contents($versionFile, implode("\n", $contents));

        $versionConfig = new VersionConfig($versionFile);
        $this->assertEquals($versionConfig->version, "specialVersion");
    }

    /**
     * Testing oxSetup::setMessage() and oxSetup::getMessage()
     *
     * @return null
     */
    public function testSetMessageAndGetMessage()
    {
        $oSetup = new OxSetup();
        $oSetup->setMessage("testTMessage");
        $this->assertEquals("testTMessage", $oSetup->getMessage());
    }

    /**
     * Testing oxSetup::getCurrentStep()
     *
     * @return null
     */
    public function testGetCurrentStep()
    {
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar")->with($this->equalTo('istep'))->will($this->returnValue(null));

        $oSetup = $this->getMock("oxSetup", array("getInstance", "getStep"));
        $oSetup->expects($this->once())->method("getInstance")->with($this->equalTo('oxSetupUtils'))->will($this->returnValue($oUtils));
        $oSetup->expects($this->once())->method("getStep")->will($this->returnValue(1));
        $this->assertEquals(1, $oSetup->getCurrentStep());
    }

    /**
     * Testing oxSetup::setNextStep() and oxSetup::getNextStep()
     *
     * @return null
     */
    public function testSetNextStepAndGetNextStep()
    {
        $oSetup = new OxSetup();
        $oSetup->setNextStep("testStep");
        $this->assertEquals("testStep", $oSetup->getNextStep());
    }


    /**
     * Testing oxSetup::alreadySetUp()
     *
     * @return null
     */
    public function testAlreadySetUp()
    {
        $oSetup = new oxSetup();
        $this->assertTrue($oSetup->alreadySetUp());
    }

    /**
     * Testing oxSetup::getShopId()
     *
     * @return null
     */
    public function testGetShopId()
    {
        $sBaseShopId = 'oxbaseshop';


        $oSetup = new oxSetup();
        $this->assertEquals($sBaseShopId, $oSetup->getShopId());
    }

    /**
     * Testing oxSetup::getSteps()
     *
     * @return null
     */
    public function testGetSteps()
    {
        $iCount = 11;

        $iCount = 9;

        $oSetup = new oxSetup();
        $this->assertEquals($iCount, count($oSetup->getSteps()));
    }

    /**
     * Testing oxSetup::getStep()
     *
     * @return null
     */
    public function testGetStep()
    {
        $oSetup = new oxSetup();
        $this->assertEquals(100, $oSetup->getStep("STEP_SYSTEMREQ"));
        $this->assertNull($oSetup->getStep("TESTID"));
    }

    /**
     * Testing oxSetup::getVersionPrefix()
     *
     * @return null
     */
    public function testGetVersionPrefix()
    {
        $oSetup = new oxSetup();
        $sVerPrefix = '';


        $this->assertEquals($sVerPrefix, $oSetup->getVersionPrefix());
    }

    /**
     * Testing oxSetup::getModuleClass()
     *
     * @return null
     */
    public function testGetModuleClass()
    {
        $oSetup = new oxSetup();
        $this->assertEquals('pass', $oSetup->getModuleClass(2));
        $this->assertEquals('pmin', $oSetup->getModuleClass(1));
        $this->assertEquals('null', $oSetup->getModuleClass(-1));
        $this->assertEquals('fail', $oSetup->getModuleClass(0));
    }
}
