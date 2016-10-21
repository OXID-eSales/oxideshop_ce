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
namespace Unit\Setup;

require_once getShopBasePath() . '/Setup/functions.php';
use Exception;
use OxidEsales\EshopCommunity\Setup\Controller;
use OxidEsales\EshopCommunity\Setup\Core;
use OxidEsales\EshopCommunity\Setup\Database;
use OxidEsales\EshopCommunity\Setup\View;
use OxidEsales\EshopCommunity\Setup\Session as SetupSession;

/**
 * controller tests
 */
class ControllerTest extends \OxidTestCase
{
    /**
     * Testing controller::getView()
     */
    public function testGetView()
    {

        /** @var Controller $oController */
        $oController = $this->getController();
        $this->assertTrue($oController->getView() instanceof View);
    }

    /**
     * Testing controller::systemReq()
     *
     * @return null
     */
    public function testSystemReq()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_0_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('blContinue'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('aGroupModuleInfo'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('aLanguages'));
        $oView->expects($this->at(4))->method("setViewParam")->with($this->equalTo('sLanguage'), $this->equalTo("testLangId"));

        $oSetup = $this->getMock("Setup", array("getModuleClass"));
        $oSetup->expects($this->any())->method("getModuleClass")->will($this->returnValue("testModuleClass"));

        $oLang = $this->getMock("Language", array("getModuleName"));
        $oLang->expects($this->any())->method("getModuleName")->will($this->returnValue("testModuleName"));

        $oSession = $this->getMock("Setup", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->with($this->equalTo('setup_lang'))->will($this->returnValue("testLangId"));

        $oUtils = $this->getMock("Utilities", array("getDefaultPathParams", "extractRewriteBase", "updateHtaccessFile"));
        $oUtils->expects($this->once())->method("getDefaultPathParams")->will($this->returnValue(array("sBaseUrlPath" => "sBaseUrlPath", "sShopURL" => "sShopURL")));
        $oUtils->expects($this->once())->method("extractRewriteBase")->with($this->equalTo('sShopURL'))->will($this->returnValue("sBaseUrlPath"));
        $oUtils->expects($this->once())->method("updateHtaccessFile");


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $this->assertEquals("systemreq.php", $oController->systemReq());
    }

    /**
     * Testing controller::welcome()
     *
     * @return null
     */
    public function testWelcome()
    {
        $oSession = $this->getMock('SetupSession', array("getSessionParam", '_startSession'), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->will($this->returnValue("en"));
        $oSession->expects($this->at(1))->method("getSessionParam")->will($this->returnValue("de"));
        $oSession->expects($this->any())->method("_startSession");

        $oUtils = $this->getMock('OxidEsales\EshopCommunity\Setup\Utilities', array("setCookie"));
        $oUtils->expects($this->once())->method("setCookie")->with($this->equalTo("oxidadminlanguage"));

        $oView = $this->getMock('OxidEsales\EshopCommunity\Setup\View', array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_1_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aCountries'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('aLocations'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('aLanguages'));
        $oView->expects($this->at(4))->method("setViewParam")->with($this->equalTo('sShopLang'));
        $oView->expects($this->at(5))->method("setViewParam")->with($this->equalTo('sLanguage'));
        $oView->expects($this->at(6))->method("setViewParam")->with($this->equalTo('sLocationLang'));
        $oView->expects($this->at(7))->method("setViewParam")->with($this->equalTo('sCountryLang'));

        $oLang = $this->getMock('OxidEsales\EshopCommunity\Setup\Language', array("getLanguage"));
        $oLang->expects($this->once())->method("getLanguage")->will($this->returnValue("oxidadminlanguage"));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(2))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(3))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $this->assertEquals("welcome.php", $oController->welcome());
    }

    /**
     * Testing controller::license()
     *
     * @return null
     */
    public function testLicense()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_2_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aLicenseText'));

        $oLang = $this->getMock("Language", array("getLanguage"));
        $oLang->expects($this->once())->method("getLanguage")->will($this->returnValue("de"));

        $oUtils = $this->getMock("Utilities", array("getFileContents"));
        $oLang->expects($this->once())->method("getLanguage")->will($this->returnValue("contents"));

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $this->assertEquals("license.php", $oController->license());
    }

    /**
     * Testing controller::dbInfo()
     *
     * @return null
     */
    public function testDbInfoCanceledSetup()
    {
        $oView = $this->getMock("viewStub", array("setMessage"));
        $oView->expects($this->once())->method("setMessage");

        $oSetup = $this->getMock("Setup", array("getStep", "setNextStep"));
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_WELCOME"));
        $oSetup->expects($this->once())->method("setNextStep");

        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->will($this->returnValue(false));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_SETUP_CANCELLED"))->will($this->returnValue(false));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(false));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(3))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $this->assertEquals("licenseerror.php", $oController->dbInfo());
    }

    /**
     * Testing controller::dbInfo()
     *
     * @return null
     */
    public function testDbInfo()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_3_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aDB'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('blMbStringOn'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('blUnicodeSupport'));

        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->will($this->returnValue(true));
        $oSession->expects($this->at(1))->method("getSessionParam")->will($this->returnValue(null));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(false));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("dbinfo.php", $oController->dbInfo());
    }

    /**
     * Testing controller::dirsInfo()
     *
     * @return null
     */
    public function testDirsInfo()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_4_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aSetupConfig'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('aAdminData'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('aPath'));

        $oSession = $this->getMock('SetupSession', array("getSessionParam", "leaveSetupDirectory"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aSetupConfig"));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("aAdminData"));

        $oSessionToCheckIfUserDecideToOverwriteDB = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSessionToCheckIfUserDecideToOverwriteDB->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("blOverwrite"));

        $oUtils = $this->getMock("Utilities", array("getDefaultPathParams", "getRequestVar"));
        $oUtils->expects($this->once())->method("getDefaultPathParams");
        $oUtils->expects($this->once())->method("getRequestVar");

        $oSetup = $this->getMock("Setup", array("leaveSetupDirectory", "deleteSetupDirectory"));
        $oSession->expects($this->any())->method("leaveSetupDirectory");
        $oSession->expects($this->any())->method("deleteSetupDirectory");

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSessionToCheckIfUserDecideToOverwriteDB));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $this->assertEquals("dirsinfo.php", $oController->dirsInfo());
    }

    /**
     * Testing controller::dbConnect()
     *
     * @return null
     */
    public function testDbConnectMissingParameters()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_FILL_ALL_FIELDS"));

        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(false));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dbConnect());
    }

    /**
     * Testing controller::dbConnect()
     *
     * @return null
     */
    public function testDbConnectUnableToConnect()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_DB_CONNECT"));

        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(array("dbHost" => "testHost", "dbName" => "testName")));

        $oDb = $this->getMock('OxidEsales\EshopCommunity\Setup\Database', array("openDatabase"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->throwException(new Exception("", Database::ERROR_DB_CONNECT)));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbConnect());
    }

    /**
     * Testing controller::dbConnect()
     *
     * @return null
     */
    public function testDbConnectUnableToCreateDb()
    {
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(array("dbHost" => "testHost", "dbName" => "testName")));

        $oDb = $this->getMock("databaseStub", array("openDatabase", "createDb"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->throwException(new Exception("")));
        $oDb->expects($this->once())->method("createDb")->will($this->throwException(new Exception("")));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbConnect());
    }

    /**
     * Testing controller::dbConnect()
     *
     * @return null
     */
    public function testDbConnect()
    {
        $oLanguage = $this->getMock("Language", array("getText"));

        $oView = $this->getMock("viewStub", array("setTitle", "setViewParam", "setMessage"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo("blCreated"));

        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep", "setMessage"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(array("dbHost" => "testHost", "dbName" => "testName")));

        $oDb = $this->getMock("databaseStub", array("openDatabase", "createDb"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->throwException(new Exception("")));
        $oDb->expects($this->once())->method("createDb");

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLanguage));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $oController->expects($this->at(6))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("dbconnect.php", $oController->dbConnect());
    }

    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreateDbExists()
    {
        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_CREATE"));

        $oSession = $this->getMock('SetupSession', array("getSessionParam", "getSid"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->with($this->equalTo("aDB"));
        $oSession->expects($this->once())->method("getSid");

        $oSessionToCheckIfUserDecideToOverwriteDB = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSessionToCheckIfUserDecideToOverwriteDB->expects($this->once())->method("getSessionParam")->with($this->equalTo("blOverwrite"));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_4_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oDb = $this->getMock("databaseStub", array("openDatabase", "execSql", "testCreateView"));
        $oDb->expects($this->once())->method("openDatabase");
        $oDb->expects($this->once())->method("execSql");
        $oDb->expects($this->once())->method("testCreateView");


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(6))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSessionToCheckIfUserDecideToOverwriteDB));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreateDbExistsUserDecidedOverwrite()
    {
        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));

        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_SERIAL"));
        }

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_FINISH"));
        }

        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->atLeastOnce())->method("getSessionParam");

        $oSessionToCheckIfUserDecideToOverwriteDB = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSessionToCheckIfUserDecideToOverwriteDB->expects($this->once())->method("getSessionParam")->with($this->equalTo("blOverwrite"))->will($this->returnValue(true));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle");
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oDb = $this->getMock("databaseStub", array("openDatabase", "execSql", "testCreateView", "setMySqlCollation", "queryFile", "saveShopSettings", "convertConfigTableToUtf", "writeAdminLoginData"));
        $oDb->expects($this->once())->method("openDatabase");
        $oDb->expects($this->never())->method("execSql");
        $oDb->expects($this->once())->method("testCreateView");
        $oDb->expects($this->atLeastOnce())->method("setMySqlCollation");
        $oDb->expects($this->atLeastOnce())->method("queryFile");
        $oDb->expects($this->atLeastOnce())->method("saveShopSettings");
        $oDb->expects($this->atLeastOnce())->method("convertConfigTableToUtf");
        $oDb->expects($this->atLeastOnce())->method("writeAdminLoginData");


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(6))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSessionToCheckIfUserDecideToOverwriteDB));
        $this->assertEquals("default.php", $oController->dbCreate());
    }


    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedDbCreation()
    {
        $oSetup = $this->getMock("Setup");

        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aDB"));

        $oSessionToCheckIfUserDecideToOverwriteDB = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSessionToCheckIfUserDecideToOverwriteDB->expects($this->once())->method("getSessionParam")->with($this->equalTo("blOverwrite"));

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_4_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oDb = $this->getMock("databaseStub", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "testCreateView"));
        $oDb->expects($this->once())->method("openDatabase");
        $oDb->expects($this->once())->method("setMySqlCollation");
        $oDb->expects($this->once())->method("execSql")->will($this->throwException(new Exception));
        $oDb->expects($this->once())->method("queryFile")->will($this->throwException(new Exception));
        $oDb->expects($this->once())->method("testCreateView");


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(6))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSessionToCheckIfUserDecideToOverwriteDB));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedDataInsert()
    {
        $oSetup = $this->getMock("Setup");

        $sessionValues = [
            "aDb" => [
                "dbiDemoData" => 1
            ],
            "blOverwrite" => 1
        ];
        $oSession = $this->getMock('SetupSession', array("getSessionParam", "getSid"), array(), '', null);
        $oSession->method("getSessionParam")->will($this->returnValueMap($sessionValues));

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_4_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar")->willReturn(true);

        $oLang = $this->getMock("Language", array("getText"));

        $oDb = $this->getMock("databaseStub", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "testCreateView"));
        $oDb->method("queryFile")->will($this->throwException(new Exception));
        $oDb->method("execSql")->will($this->throwException(new Exception));

        $map = [
            ["Setup", $oSetup],
            ["Session", $oSession],
            ["Language", $oLang],
            ["Utilities", $oUtils],
            ["Database", $oDb]
        ];

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->method("getInstance")->will($this->returnValueMap($map));
        $oController->method("getView")->will($this->returnValue($oView));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedEnDataInsert()
    {
        $oSetup = $this->getMock("Setup");

        $sessionValues = [
            "aDb" => [
                "dbiDemoData" => 1
            ],
            "location_lang" => "en",
            "blOverwrite" => 1
        ];
        $oSession = $this->getMock('SetupSession', array("getSessionParam", "getSid"), array(), '', null);
        $oSession->method("getSessionParam")->will($this->returnValueMap($sessionValues));

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_4_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar")->willReturn(true);

        $oLang = $this->getMock("Language", array("getText"));

        $callback = function($filename) {
            if (preg_match("@en.sql$@i", $filename)) {
                throw new Exception();
            } else {
                return true;
            }
        };

        $oDb = $this->getMock("databaseStub", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "testCreateView"));
        $oDb->method("queryFile")->will($this->returnCallback($callback));

        $map = [
            ["Setup", $oSetup],
            ["Session", $oSession],
            ["Language", $oLang],
            ["Utilities", $oUtils],
            ["Database", $oDb]
        ];

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->method("getInstance")->will($this->returnValueMap($map));
        $oController->method("getView")->will($this->returnValue($oView));
        $this->assertEquals("default.php", $oController->dbCreate());

    }

    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedViewTest()
    {
        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep");

        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array("dbiDemoData" => 1)));

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_4_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->never())->method("getRequestVar");

        $oDb = $this->getMock("databaseStub", array("openDatabase", "testCreateView"));
        $oDb->expects($this->at(0))->method("openDatabase");
        $oDb->expects($this->at(1))->method("testCreateView")->will($this->throwException(new Exception));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing controller::dbCreate()
     *
     * @return null
     */
    public function testDbCreate()
    {
        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");

        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_SERIAL"));
        }

        if ($this->getTestConfig()->getShopEdition() === 'CE') {
            $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_FINISH"));
        }

        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array("dbiDemoData" => 1, "iUtfMode" => 1)));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("location_lang"))->will($this->returnValue("en"));

        $oSessionToCheckIfUserDecideToOverwriteDB = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSessionToCheckIfUserDecideToOverwriteDB->expects($this->atLeastOnce())->method("getSessionParam")->with($this->equalTo("blOverwrite"));

        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_4_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oDb = $this->getMock("databaseStub", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "saveShopSettings", "convertConfigTableToUtf", "testCreateView", "writeAdminLoginData"));
        $oDb->expects($this->at(0))->method("openDatabase");
        $oDb->expects($this->at(1))->method("testCreateView");
        $oDb->expects($this->at(2))->method("execSql")->will($this->throwException(new Exception));
        $oDb->expects($this->at(3))->method("setMySqlCollation");
        $oDb->expects($this->at(4))->method("queryFile");
        $oDb->expects($this->at(5))->method("queryFile");
        $oDb->expects($this->at(6))->method("saveShopSettings");
        $oDb->expects($this->at(7))->method("queryFile");
        $oDb->expects($this->at(8))->method("setMySqlCollation");
        $oDb->expects($this->at(9))->method("convertConfigTableToUtf");
        $oDb->expects($this->at(10))->method("writeAdminLoginData");

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("Database"))->will($this->returnValue($oDb));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(6))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSessionToCheckIfUserDecideToOverwriteDB));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing controller::finish()
     *
     * @return null
     */
    public function testFinish()
    {
        $oSession = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->will($this->returnValue(array("sShopDir" => getShopBasePath())));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("aSetupConfig"));

        $oView = $this->getMock("viewStub", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo("STEP_6_TITLE"));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo("aPath"));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo("aSetupConfig"));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo("blWritableConfig"));


        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at(1))->method("getView")->will($this->returnValue($oView));
        $this->assertEquals("finish.php", $oController->finish());
    }

    /**
     * Testing controller::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteMissingPathParameters()
    {
        $iAt = 0;
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $iAt = 0;
        $oUtils = $this->getMock("Utilities", array("getRequestVar", "preparePath", "extractRewriteBase"));
        $oUtils->expects($this->exactly(3))->method("getRequestVar");
        $oUtils->expects($this->exactly(3))->method("preparePath");
        $oUtils->expects($this->exactly(1))->method("extractRewriteBase");

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing controller::dirsWrite()
     *
     * @return null
     */
    public function testDirsWritePasswordTooShort()
    {
        $iAt = 0;
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText")->with($this->equalTo("ERROR_PASSWORD_TOO_SHORT"));

        $iAt = 0;
        $oUtils = $this->getMock("Utilities", array("getRequestVar", "preparePath", "extractRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPass", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing controller::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteEmailDoesNotMatchExpectedPattern()
    {
        $iAt = 0;
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock("SetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText")->with($this->equalTo("ERROR_USER_NAME_DOES_NOT_MATCH_PATTERN"));

        $iAt = 0;
        $oUtils = $this->getMock("Utilities", array("getRequestVar", "preparePath", "extractRewriteBase", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(false));

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing controller::dirsWrite()
     *
     * @return null
     */
    public function testDirsWritePasswordsDoNotMatch()
    {
        $iAt = 0;
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock('SetupSession', array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText")->with($this->equalTo("ERROR_PASSWORDS_DO_NOT_MATCH"));

        $iAt = 0;
        $oUtils = $this->getMock("Utilities", array("getRequestVar", "preparePath", "extractRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPasswor", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing controller::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteConfigUpdateFails()
    {
        $iAt = 0;
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock('SetupSession', array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aAdminData"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array()));

        $iAt = 0;
        $oUtils = $this->getMock("Utilities", array("getRequestVar", "preparePath", "updateConfigFile", "extractRewriteBase", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(true));
        $oUtils->expects($this->at($iAt++))->method("updateConfigFile")->will($this->throwException(new Exception));

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language")); //->will( $this->returnValue( $oLang ) );
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing controller::dirsWrite()
     *
     * @return null
     */
    public function testDirsWrite()
    {
        $iAt = 0;
        $oView = $this->getMock("viewStub", array("setTitle", "setMessage", "setViewParam"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");
        $oView->expects($this->at($iAt++))->method("setViewParam")->with($this->equalTo("aPath"));
        $oView->expects($this->at($iAt++))->method("setViewParam")->with($this->equalTo("aSetupConfig"));

        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");

        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_CREATE"));

        $iAt = 0;
        $oSession = $this->getMock('SetupSession', array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aAdminData"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array()));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $iAt = 0;
        $oUtils = $this->getMock("Utilities", array("getRequestVar", "preparePath", "updateConfigFile", "extractRewriteBase", "updateHtaccessFile", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(true));
        $oUtils->expects($this->at($iAt++))->method("updateConfigFile");
        $oUtils->expects($this->at($iAt++))->method("updateHtaccessFile");

        $iAt = 0;

        $oController = $this->getMock(get_class($this->getController()), array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * @return Controller
     */
    protected function getController()
    {
        $core = new Core();
        return $core->getInstance('Controller');
    }
}
