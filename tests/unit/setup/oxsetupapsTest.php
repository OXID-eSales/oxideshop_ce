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
 * oxSetupAps tests
 */
class Unit_Setup_oxSetupApsTest extends OxidTestCase
{

    /**
     * Testing oxSetupAps::execute()
     *
     * @return null
     */
    public function testExecute()
    {
        $oSetupAps = $this->getMock("oxSetupAps", array("install", "remove", "configure", "upgrade"));
        $oSetupAps->expects($this->once())->method("install");
        $oSetupAps->expects($this->once())->method("remove");
        $oSetupAps->expects($this->once())->method("configure");
        $oSetupAps->expects($this->once())->method("upgrade");

        $oSetupAps->execute("install");
        $oSetupAps->execute("remove");
        $oSetupAps->execute("configure");
        $oSetupAps->execute("upgrade");

        try {
            $oSetupAps->execute("unknown");
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("While executing unknown command exception must be thrown");
    }

    /**
     * Testing oxSetupAps::install()
     *
     * @return null
     */
    public function testInstall()
    {
        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getEnvVar", "checkPaths", "updateConfigFile", "updateHtaccessFile"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_PORT"))->will($this->returnValue("testPort"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_HOST"))->will($this->returnValue("testHost"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_LOGIN"))->will($this->returnValue("testLogin"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_PASSWORD"))->will($this->returnValue("testPass"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_NAME"))->will($this->returnValue("testName"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_install_demodata"))->will($this->returnValue("testInstallDemo"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_utf8_mode"))->will($this->returnValue(1));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("BASE_URL_HOST"))->will($this->returnValue("testHost"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("BASE_URL_SCHEME"))->will($this->returnValue("testSchemet"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("BASE_URL_PATH"))->will($this->returnValue("testPath"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_check_for_updates"))->will($this->returnValue("testCheckforUpdates"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_location_lang"))->will($this->returnValue("testLocationLang"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_location_lang"))->will($this->returnValue("testLocationLang"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_country_lang"))->will($this->returnValue("testCountryLang"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_use_dynamic_pages"))->will($this->returnValue("testUseDynPages"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_admin_user_name"))->will($this->returnValue("testAdminUserName"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_admin_user_password"))->will($this->returnValue("testAdminUserPass"));
        $oUtils->expects($this->at($iAt++))->method("checkPaths");
        $oUtils->expects($this->at($iAt++))->method("updateConfigFile");
        $oUtils->expects($this->at($iAt++))->method("updateHtaccessFile");

        $iAt = 0;
        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "setMySqlCollation", "queryFile", "saveShopSettings", "convertConfigTableToUtf", "writeAdminLoginData"));
        $oDb->expects($this->at($iAt++))->method("openDatabase");
        $oDb->expects($this->at($iAt++))->method("setMySqlCollation");
        $oDb->expects($this->at($iAt++))->method("queryFile");
        $oDb->expects($this->at($iAt++))->method("queryFile");
        $oDb->expects($this->at($iAt++))->method("saveShopSettings");
        $oDb->expects($this->at($iAt++))->method("queryFile");
        $oDb->expects($this->at($iAt++))->method("setMySqlCollation");
        $oDb->expects($this->at($iAt++))->method("convertConfigTableToUtf");
        $oDb->expects($this->at($iAt++))->method("writeAdminLoginData");


        $iAt = 0;

        $oSetupAps = $this->getMock("oxSetupAps", array("getInstance"));
        $oSetupAps->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oSetupAps->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));

        $oSetupAps->install();
    }

    /**
     * Testing oxSetupAps::remove()
     *
     * @return null
     */
    public function testRemove()
    {
        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("removeDir"));
        $oUtils->expects($this->at($iAt++))->method("removeDir");

        $iAt = 0;
        $oSetupAps = $this->getMock("oxSetupAps", array("getInstance"));
        $oSetupAps->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oSetupAps->remove();
    }

    /**
     * Testing oxSetupAps::configure()
     *
     * @return null
     */
    public function testConfigure()
    {
        $iAt = 0;
        $oUtils = $this->getMock("oxSetupUtils", array("getEnvVar", "generateUid"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_PORT"))->will($this->returnValue("testPort"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_HOST"))->will($this->returnValue("testHost"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_LOGIN"))->will($this->returnValue("testLogin"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_PASSWORD"))->will($this->returnValue("testPass"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("DB_main_NAME"))->will($this->returnValue("testName"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_check_for_updates"))->will($this->returnValue("testCheckForUpdates"));
        $oUtils->expects($this->at($iAt++))->method("getEnvVar")->with($this->equalTo("SETTINGS_use_dynamic_pages"))->will($this->returnValue("testUseDynPages"));
        $oUtils->expects($this->at($iAt++))->method("generateUid");
        $oUtils->expects($this->at($iAt++))->method("generateUid");

        $oDb = $this->getMock("oxSetupDb", array("openDatabase", "execSql"));
        $oDb->expects($this->once())->method("openDatabase");
        $oDb->expects($this->exactly(4))->method("execSql");

        $oSetup = $this->getMock("oxSetup", array("getShopId"));
        $oSetup->expects($this->once())->method("getShopId");

        $iAt = 0;
        $oSetupAps = $this->getMock("oxSetupAps", array("getInstance"));
        $oSetupAps->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oSetupAps->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupDb"))->will($this->returnValue($oDb));
        $oSetupAps->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oSetupAps->configure();
    }

    /**
     * Testing oxSetupAps::upgrade()
     *
     * @return null
     */
    public function testUpgrade()
    {
        // currently it does nothing
        $oSetupAps = new oxSetupAps();
        $this->assertNull($oSetupAps->upgrade());
    }
}
