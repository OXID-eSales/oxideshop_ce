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
 * oxSetupController tests
 */
class Unit_Setup_oxSetupControllerTest extends OxidTestCase
{

    /**
     * Testing oxSetupController::getView()
     *
     * @return null
     */
    public function testGetView()
    {
        $oController = new oxSetupController();
        $this->assertTrue($oController->getView() instanceof oxSetupView);
    }

    // ---- controllers ----
    /**
     * Testing oxSetupController::systemReq()
     *
     * @return null
     */
    public function testSystemReq()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_0_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('blContinue'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('aGroupModuleInfo'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('aLanguages'));
        $oView->expects($this->at(4))->method("setViewParam")->with($this->equalTo('sSetupLang'), $this->equalTo("testLangId"));

        $oSetup = $this->getMock("oxSetup", array("getModuleClass"));
        $oSetup->expects($this->any())->method("getModuleClass")->will($this->returnValue("testModuleClass"));

        $oLang = $this->getMock("oxSetupLang", array("getModuleName"));
        $oLang->expects($this->any())->method("getModuleName")->will($this->returnValue("testModuleName"));

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->with($this->equalTo('setup_lang'))->will($this->returnValue("testLangId"));

        $oUtils = $this->getMock("oxSetupUtils", array("getDefaultPathParams", "extractRewriteBase", "updateHtaccessFile"));
        $oUtils->expects($this->once())->method("getDefaultPathParams")->will($this->returnValue(array("sBaseUrlPath" => "sBaseUrlPath", "sShopURL" => "sShopURL")));
        $oUtils->expects($this->once())->method("extractRewriteBase")->with($this->equalTo('sShopURL'))->will($this->returnValue("sBaseUrlPath"));
        $oUtils->expects($this->once())->method("updateHtaccessFile");

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $this->assertEquals("systemreq.php", $oController->systemReq());
    }

    /**
     * Testing oxSetupController::welcome()
     *
     * @return null
     */
    public function testWelcome()
    {
        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->will($this->returnValue("en"));
        $oSession->expects($this->at(1))->method("getSessionParam")->will($this->returnValue("de"));

        $oUtils = $this->getMock("oxSetupUtils", array("setCookie"));
        $oUtils->expects($this->once())->method("setCookie")->with($this->equalTo("oxidadminlanguage"));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_1_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aCountries'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('aLocations'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('aLanguages'));
        $oView->expects($this->at(4))->method("setViewParam")->with($this->equalTo('sShopLang'));
        $oView->expects($this->at(5))->method("setViewParam")->with($this->equalTo('sSetupLang'));
        $oView->expects($this->at(6))->method("setViewParam")->with($this->equalTo('sLocationLang'));
        $oView->expects($this->at(7))->method("setViewParam")->with($this->equalTo('sCountryLang'));

        $oLang = $this->getMock("oxSetupLang", array("getSetupLang"));
        $oLang->expects($this->once())->method("getSetupLang")->will($this->returnValue("oxidadminlanguage"));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(2))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(3))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $this->assertEquals("welcome.php", $oController->welcome());
    }

    /**
     * Testing oxSetupController::license()
     *
     * @return null
     */
    public function testLicense()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_2_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aLicenseText'));

        $oLang = $this->getMock("oxSetupLang", array("getSetupLang"));
        $oLang->expects($this->once())->method("getSetupLang")->will($this->returnValue("de"));

        $oUtils = $this->getMock("oxSetupUtils", array("getFileContents"));
        $oLang->expects($this->once())->method("getSetupLang")->will($this->returnValue("contents"));

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $this->assertEquals("license.php", $oController->license());
    }

    /**
     * Testing oxSetupController::dbInfo()
     *
     * @return null
     */
    public function testDbInfoCanceledSetup()
    {
        $oView = $this->getMock("oxSetupView", array("setMessage"));
        $oView->expects($this->once())->method("setMessage");

        $oSetup = $this->getMock("oxSetup", array("getStep", "setNextStep"));
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_WELCOME"));
        $oSetup->expects($this->once())->method("setNextStep");

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->will($this->returnValue(false));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_SETUP_CANCELLED"))->will($this->returnValue(false));

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(false));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(3))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $this->assertEquals("licenseerror.php", $oController->dbInfo());
    }

    /**
     * Testing oxSetupController::dbInfo()
     *
     * @return null
     */
    public function testDbInfo()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_3_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aDB'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('blMbStringOn'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('blUnicodeSupport'));

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->will($this->returnValue(true));
        $oSession->expects($this->at(1))->method("getSessionParam")->will($this->returnValue(null));

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(false));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("dbinfo.php", $oController->dbInfo());
    }

    /**
     * Testing oxSetupController::dirsInfo()
     *
     * @return null
     */
    public function testDirsInfo()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo('STEP_4_TITLE'));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo('aSetupConfig'));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo('aAdminData'));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo('aPath'));

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aSetupConfig"));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("aAdminData"));

        $oUtils = $this->getMock("oxSetupUtils", array("getDefaultPathParams"));
        $oUtils->expects($this->once())->method("getDefaultPathParams");

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(1))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("dirsinfo.php", $oController->dirsInfo());
    }

    /**
     * Testing oxSetupController::dbConnect()
     *
     * @return null
     */
    public function testDbConnectMissingParameters()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_FILL_ALL_FIELDS"));

        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(false));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dbConnect());
    }

    /**
     * Testing oxSetupController::dbConnect()
     *
     * @return null
     */
    public function testDbConnectUnableToConnect()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_DB_CONNECT"));

        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(array("dbHost" => "testHost", "dbName" => "testName")));

        $oDb = $this->getMock("oxSetupDb", array("openDatabase"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->throwException(new Exception("", oxSetupDb::ERROR_DB_CONNECT)));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbConnect());
    }

    /**
     * Testing oxSetupController::dbConnect()
     *
     * @return null
     */
    public function testDbConnectUnableToCreateDb()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(array("dbHost" => "testHost", "dbName" => "testName")));

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "createDb"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->throwException(new Exception("")));
        $oDb->expects($this->once())->method("createDb")->will($this->throwException(new Exception("")));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbConnect());
    }

    /**
     * Testing oxSetupController::dbConnect()
     *
     * @return null
     */
    public function testDbConnect()
    {
        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo("STEP_3_1_TITLE"));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo("blCreated"));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo("aDB"));

        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("setSessionParam")->with($this->equalTo("aDB"));

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_CREATE"));

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->any())->method("getRequestVar")->will($this->returnValue(array("dbHost" => "testHost", "dbName" => "testName")));

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "createDb"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->throwException(new Exception("")));
        $oDb->expects($this->once())->method("createDb");

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("dbconnect.php", $oController->dbConnect());
    }

    /**
     * Testing oxSetupController::dbCreate()
     *
     * @return null
     */
    public function testDbCreateDbExists()
    {
        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_CREATE"));

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam", "getSid"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->with($this->equalTo("aDB"));
        $oSession->expects($this->once())->method("getSid");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "execSql", "testCreateView"));
        $oDb->expects($this->once())->method("openDatabase");
        $oDb->expects($this->once())->method("execSql");
        $oDb->expects($this->once())->method("testCreateView");

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing oxSetupController::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedDbCreation()
    {
        $oSetup = $this->getMock("oxSetup", array("getVersionPrefix"));
        $oSetup->expects($this->any())->method("getVersionPrefix");

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->with($this->equalTo("aDB"));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "testCreateView"));
        $oDb->expects($this->once())->method("openDatabase");
        $oDb->expects($this->once())->method("setMySqlCollation");
        $oDb->expects($this->once())->method("execSql")->will($this->throwException(new Exception));
        $oDb->expects($this->once())->method("queryFile")->will($this->throwException(new Exception));
        $oDb->expects($this->once())->method("testCreateView");

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing oxSetupController::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedDataInsert()
    {
        $oSetup = $this->getMock("oxSetup", array("getVersionPrefix"));
        $oSetup->expects($this->any())->method("getVersionPrefix");

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->once())->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array("dbiDemoData" => '1')));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "testCreateView"));
        $oDb->expects($this->at(0))->method("openDatabase");
        $oDb->expects($this->at(1))->method("testCreateView");
        $oDb->expects($this->at(2))->method("execSql")->will($this->throwException(new Exception));
        $oDb->expects($this->at(3))->method("setMySqlCollation");
        $oDb->expects($this->at(4))->method("queryFile");
        $oDb->expects($this->at(5))->method("queryFile")->will($this->throwException(new Exception));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing oxSetupController::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedEnDataInsert()
    {
        $oSetup = $this->getMock("oxSetup", array("getVersionPrefix"));
        $oSetup->expects($this->any())->method("getVersionPrefix");

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array("dbiDemoData" => '1')));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("location_lang"))->will($this->returnValue("en"));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "testCreateView"));
        $oDb->expects($this->at(0))->method("openDatabase");
        $oDb->expects($this->at(1))->method("testCreateView");
        $oDb->expects($this->at(2))->method("execSql")->will($this->throwException(new Exception));
        $oDb->expects($this->at(3))->method("setMySqlCollation");
        $oDb->expects($this->at(4))->method("queryFile");
        $oDb->expects($this->at(5))->method("queryFile");
        $oDb->expects($this->at(6))->method("queryFile")->will($this->throwException(new Exception));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing oxSetupController::dbCreate()
     *
     * @return null
     */
    public function testDbCreateFailedViewTest()
    {
        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep");

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array("dbiDemoData" => 1)));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "testCreateView"));
        $oDb->expects($this->at(0))->method("openDatabase");
        $oDb->expects($this->at(1))->method("testCreateView")->will($this->throwException(new Exception));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }

    /**
     * Testing oxSetupController::dbCreate()
     *
     * @return null
     */
    public function testDbCreate()
    {
        $oSetup = $this->getMock("oxSetup", array("getVersionPrefix", "setNextStep", "getStep"));
        $oSetup->expects($this->any())->method("getVersionPrefix");
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo('STEP_DIRS_INFO'));

        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array("dbiDemoData" => 1, "iUtfMode" => 1)));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("location_lang"))->will($this->returnValue("en"));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->once())->method("setTitle")->with($this->equalTo("STEP_3_2_TITLE"));
        $oView->expects($this->once())->method("setMessage");

        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar"));
        $oUtils->expects($this->once())->method("getRequestVar");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "execSql", "setMySqlCollation", "queryFile", "saveShopSettings", "convertConfigTableToUtf", "testCreateView"));
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

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at(3))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at(4))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at(5))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dbCreate());
    }


    /**
     * Testing oxSetupController::finish()
     *
     * @return null
     */
    public function testFinish()
    {
        $oSession = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->will($this->returnValue(array("sShopDir" => getShopBasePath())));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo("aSetupConfig"));

        $oView = $this->getMock("oxSetupView", array("setTitle", "setViewParam"));
        $oView->expects($this->at(0))->method("setTitle")->with($this->equalTo("STEP_6_TITLE"));
        $oView->expects($this->at(1))->method("setViewParam")->with($this->equalTo("aPath"));
        $oView->expects($this->at(2))->method("setViewParam")->with($this->equalTo("aSetupConfig"));
        $oView->expects($this->at(3))->method("setViewParam")->with($this->equalTo("blWritableConfig"));

        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at(0))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at(1))->method("getView")->will($this->returnValue($oView));
        $this->assertEquals("finish.php", $oController->finish());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteMissingPathParameters()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "extractRewriteBase"));
        $oUtils->expects($this->exactly(3))->method("getRequestVar");
        $oUtils->expects($this->exactly(3))->method("preparePath");
        $oUtils->expects($this->exactly(1))->method("extractRewriteBase");

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWritePasswordTooShort()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText")->with($this->equalTo("ERROR_PASSWORD_TOO_SHORT"));

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "checkPaths", "extractRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPass", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteEmailDoesNotMatchExpectedPattern()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText")->with($this->equalTo("ERROR_USER_NAME_DOES_NOT_MATCH_PATTERN"));

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "extractRewriteBase", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(false));

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWritePasswordsDoNotMatch()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText")->with($this->equalTo("ERROR_PASSWORDS_DO_NOT_MATCH"));

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "checkPaths", "extractRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPasswor", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteChecksPathsFails()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "checkPaths", "extractRewriteBase", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(true));
        $oUtils->expects($this->at($iAt++))->method("checkPaths")->will($this->throwException(new Exception));

        $oDb = $this->getMock("oxSetupDb", array("writeAdminLoginData"));
        $oDb->expects($this->once())->method("writeAdminLoginData");

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup")); //->will( $this->returnValue( $oSetup ) );
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang")); //->will( $this->returnValue( $oLang ) );
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWriteConfigUpdateFails()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DIRS_INFO"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aAdminData"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array()));

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "checkPaths", "updateConfigFile", "extractRewriteBase", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(true));
        $oUtils->expects($this->at($iAt++))->method("checkPaths");
        $oUtils->expects($this->at($iAt++))->method("updateConfigFile")->will($this->throwException(new Exception));

        $oDb = $this->getMock("oxSetupDb", array("writeAdminLoginData"));
        $oDb->expects($this->once())->method("writeAdminLoginData");

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang")); //->will( $this->returnValue( $oLang ) );
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }

    /**
     * Testing oxSetupController::dirsWrite()
     *
     * @return null
     */
    public function testDirsWrite()
    {
        $iAt = 0;
        $oView = $this->getMock("oxSetupView", array("setTitle", "setMessage", "setViewParam"));
        $oView->expects($this->at($iAt++))->method("setTitle")->with($this->equalTo("STEP_4_1_TITLE"));
        $oView->expects($this->at($iAt++))->method("setMessage");
        $oView->expects($this->at($iAt++))->method("setViewParam")->with($this->equalTo("aPath"));
        $oView->expects($this->at($iAt++))->method("setViewParam")->with($this->equalTo("aSetupConfig"));

        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");


        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_FINISH"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aPath"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aSetupConfig"));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("aAdminData"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("aDB"))->will($this->returnValue(array()));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->atLeastOnce())->method("getText");

        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getRequestVar", "preparePath", "checkPaths", "updateConfigFile", "extractRewriteBase", "updateHtaccessFile", "isValidEmail"));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aPath"), $this->equalTo("post"))->will($this->returnValue(array("sShopURL" => "sShopURL", "sShopDir" => "sShopDir", "sCompileDir" => "sCompileDir")));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aSetupConfig"), $this->equalTo("post"))->will($this->returnValue(array("blDelSetupDir" => 1)));
        $oUtils->expects($this->at($iAt++))->method("getRequestVar")->with($this->equalTo("aAdminData"), $this->equalTo("post"))->will($this->returnValue(array("sLoginName" => "sLoginName", "sPassword" => "sPassword", "sPasswordConfirm" => "sPassword")));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopURL"))->will($this->returnValue("sShopURL"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sShopDir"))->will($this->returnValue("sShopDir"));
        $oUtils->expects($this->at($iAt++))->method("preparePath")->with($this->equalTo("sCompileDir"))->will($this->returnValue("sCompileDir"));
        $oUtils->expects($this->at($iAt++))->method("extractRewriteBase")->with($this->equalTo("sShopURL"))->will($this->returnValue("sRewriteBase"));
        $oUtils->expects($this->at($iAt++))->method("isValidEmail")->will($this->returnValue(true));
        $oUtils->expects($this->at($iAt++))->method("checkPaths");
        $oUtils->expects($this->at($iAt++))->method("updateConfigFile");
        $oUtils->expects($this->at($iAt++))->method("updateHtaccessFile");

        $oDb = $this->getMock("oxSetupDb", array("writeAdminLoginData"));
        $oDb->expects($this->once())->method("writeAdminLoginData");

        $iAt = 0;
        $oController = $this->getMock("oxSetupController", array("getView", "getInstance"));
        $oController->expects($this->at($iAt++))->method("getView")->will($this->returnValue($oView));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oController->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $this->assertEquals("default.php", $oController->dirsWrite());
    }
}
