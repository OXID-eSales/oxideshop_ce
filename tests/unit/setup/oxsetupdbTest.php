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
 * oxSetupDb tests
 */
class Unit_Setup_oxSetupDbTest extends OxidTestCase
{

    /**
     * Testing oxSetupDb::execSql()
     *
     * @return null
     */
    public function testExecSqlBadConnection()
    {
        // bad connection
        $oDb = $this->getMock("OxSetupDb", array("getConnection"));
        $oDb->expects($this->any())->method("getConnection")->will($this->returnValue(null));

        try {
            $oDb->execSql("select 1 + 1");
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("Due to undefined connection exception should be thrown");
    }

    /**
     * Testing oxSetupDb::execSql()
     *
     * @return null
     */
    public function testExecSql()
    {
        $myConfig = oxRegistry::getConfig();
        $reportingLevel = error_reporting((E_ALL ^ E_NOTICE ^ E_DEPRECATED) | E_STRICT);
        $rConnection = mysql_connect($myConfig->getConfigParam('dbHost'), $myConfig->getConfigParam('dbUser'), $myConfig->getConfigParam('dbPwd'));
        error_reporting($reportingLevel);

        // bad connection
        $oDb = $this->getMock("OxSetupDb", array("getConnection"));
        $oDb->expects($this->once())->method("getConnection")->will($this->returnValue($rConnection));
        $rRes = $oDb->execSql("select 1 + 1");
        $this->assertTrue((bool) $rRes);
        $aRes = mysql_fetch_row($rRes);
        $this->assertTrue(is_array($aRes));
        $this->assertTrue(isset($aRes[0]));
        $this->assertEquals(2, $aRes[0]);
    }

    /**
     * Testing oxSetupDb::queryFile()
     *
     * @return null
     */
    public function testQueryFileUnexistingFile()
    {
        $oSetup = $this->getMock("oxSetup", array("getStep", "setNextStep"));
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));
        $oSetup->expects($this->once())->method("setNextStep");

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->once())->method("getText");

        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("getInstance"));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));

        try {
            $oDb->queryFile(time());
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("Importing unexisting file should throw an exception");
    }

    /**
     * Testing oxSetupDb::queryFile()
     *
     * @return null
     */
    public function testQueryFile()
    {
        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("getDatabaseVersion", "parseQuery", "execSql"));
        $oDb->expects($this->at($iAt++))->method("getDatabaseVersion")->will($this->returnValue("5.1"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("SET @@session.sql_mode = ''"));
        $oDb->expects($this->at($iAt++))->method("parseQuery")->will($this->returnValue(array(1, 2, 3)));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo(1));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo(2));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo(3));
        $oDb->queryFile(getShopBasePath() . '/config.inc.php');
    }

    /**
     * Testing oxSetupDb::getDatabaseVersion()
     *
     * @return null
     */
    public function testGetDatabaseVersion()
    {
        $aVersionInfo = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("SHOW VARIABLES LIKE 'version'");
        $sVersion = $aVersionInfo[0]["Value"];

        $myConfig = oxRegistry::getConfig();
        $reportingLevel = error_reporting((E_ALL ^ E_NOTICE ^ E_DEPRECATED) | E_STRICT);
        $rConnection = mysql_connect($myConfig->getConfigParam('dbHost'), $myConfig->getConfigParam('dbUser'), $myConfig->getConfigParam('dbPwd'));
        error_reporting($reportingLevel);

        $oDb = $this->getMock("OxSetupDb", array("getConnection"));
        $oDb->expects($this->once())->method("getConnection")->will($this->returnValue($rConnection));
        $this->assertEquals($sVersion, $oDb->getDatabaseVersion());
    }

    /**
     * Testing oxSetupDb::getConnection()
     *
     * @return null
     */
    public function testGetConnection()
    {
        $oDb = $this->getMock("OxSetupDb", array("openDatabase"));
        $oDb->expects($this->once())->method("openDatabase")->will($this->returnValue("testConnection"));
        $this->assertEquals("testConnection", $oDb->getConnection());
    }

    /**
     * Testing oxSetupDb::openDatabase()
     *
     * @return null
     */
    public function testOpenDatabaseConnectionImpossible()
    {
        $aParams['dbHost'] = oxRegistry::getConfig()->getConfigParam('dbHost');
        $aParams['dbUser'] = $aParams['dbPwd'] = time();

        try {
            $oDb = new oxSetupDb();
            $oDb->openDatabase($aParams);
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("Connection should not be established due to wrong access info");
    }

    /**
     * Testing oxSetupDb::openDatabase()
     *
     * @return null
     */
    public function testOpenDatabaseImpossibleToSelectGivenDatabase()
    {
        $myConfig = oxRegistry::getConfig();
        $aParams['dbHost'] = $myConfig->getConfigParam('dbHost');
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = time();

        try {
            $oDb = new oxSetupDb();
            $oDb->openDatabase($aParams);
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("Table selection should fail");
    }

    /**
     * Testing oxSetupDb::openDatabase()
     *
     * @return null
     */
    public function testOpenDatabaseWrongDbVersion()
    {
        $myConfig = oxRegistry::getConfig();
        $aParams['dbHost'] = $myConfig->getConfigParam('dbHost');
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = time();

        try {
            $oDb = $this->getMock("oxSetupDb", array("getDatabaseVersion"));
            $oDb->expects($this->once())->method("getDatabaseVersion")->will($this->returnValue(4));
            $oDb->openDatabase($aParams);
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("Table selection should fail");
    }

    /**
     * Testing oxSetupDb::openDatabase()
     *
     * @return null
     */
    public function testOpenDatabase()
    {
        $myConfig = oxRegistry::getConfig();
        $aParams['dbHost'] = $myConfig->getConfigParam('dbHost');
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = $myConfig->getConfigParam('dbName');

        $oDb = new oxSetupDb();
        $this->assertTrue((bool) $oDb->openDatabase($aParams));
    }

    /**
     * Testing oxSetupDb::createDb()
     *
     * @return null
     */
    public function testCreateDb()
    {
        $oSetup = $this->getMock("oxSetup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oLang = $this->getMock("oxSetupLang", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_COULD_NOT_CREATE_DB"));

        $oDb = $this->getMock("OxSetupDb", array("execSql", "getInstance"));
        $oDb->expects($this->at(0))->method("execSql")->will($this->returnValue(false));
        $oDb->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oDb->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oLang));
        try {
            $oDb->createDb("");
        } catch (Exception $oExcp) {
            return;
        }
        $this->fail("Database creation failure should throw an exception");
    }

    /**
     * Testing oxSetupDb::saveShopSettings()
     *
     * @return null
     */
    public function testSaveShopSettings()
    {
        $oUtils = $this->getMock("oxSetupUtils", array("generateUid"));
        $oUtils->expects($this->any())->method("generateUid")->will($this->returnValue("testid"));

        $iAt = 0;
        $oSession = $this->getMock("oxSetupSession", array("setSessionParam", "getSessionParam"), array(), '', null);

        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("location_lang"))->will($this->returnValue(null));
        $oSession->expects($this->at($iAt++))->method("setSessionParam")->with($this->equalTo("use_dynamic_pages"), $this->equalTo("false"));

        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("use_dynamic_pages"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("location_lang"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("check_for_updates"));
        $oSession->expects($this->at($iAt++))->method("getSessionParam")->with($this->equalTo("country_lang"));

        $oSetup = $this->getMock("oxSetup", array("getShopId"));
        $oSetup->expects($this->any())->method("getShopId");

        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("execSql", "getInstance"));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oSession));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("update oxcountry set oxactive = '0'"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("update oxcountry set oxactive = '1' where oxid = ''"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("UPDATE oxuser SET oxcountryid = '' where oxid='oxdefaultadmin'"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("delete from oxconfig where oxvarname = 'blLoadDynContents'"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("delete from oxconfig where oxvarname = 'sShopCountry'"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("delete from oxconfig where oxvarname = 'blCheckForUpdates'"));
        $oDb->expects($this->at($iAt++))->method("execSql");
        $oDb->expects($this->at($iAt++))->method("execSql");
        $oDb->expects($this->at($iAt++))->method("execSql");
        $oDb->expects($this->at($iAt++))->method("execSql")->will($this->returnValue(false));
        $oDb->saveShopSettings(array());
    }


    /**
     * Testing oxSetupDb::setMySqlCollation()
     *
     * @return null
     */
    public function testSetMySqlCollationUtfMode()
    {
        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("execSql"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("ALTER SCHEMA CHARACTER SET utf8 COLLATE utf8_general_ci"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("set names 'utf8'"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("set character_set_database=utf8"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("SET CHARACTER SET latin1"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("SET CHARACTER_SET_CONNECTION = utf8"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("SET character_set_results = utf8"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("SET character_set_server = utf8"));
        $oDb->setMySqlCollation(1);
    }

    /**
     * Testing oxSetupDb::setMySqlCollation()
     *
     * @return null
     */
    public function testSetMySqlCollation()
    {
        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("execSql"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("ALTER SCHEMA CHARACTER SET latin1 COLLATE latin1_general_ci"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("SET CHARACTER SET latin1"));
        $oDb->setMySqlCollation(0);
    }

    /**
     * Testing oxSetupDb::writeUtfMode()
     *
     * @return null
     */
    public function testWriteUtfMode()
    {
        $oSetup = $this->getMock("oxSetup", array("getShopId"));
        $oSetup->expects($this->once())->method("getShopId")->will($this->returnValue('testShopId'));

        $oConfk = new Conf();
        $sQ = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values('iSetUtfMode', 'testShopId', 'iSetUtfMode', 'str', ENCODE( '1', '" . $oConfk->sConfigKey . "') )";

        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("getInstance", "execSql"));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oSetup));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo($sQ));
        $oDb->writeUtfMode(1);
    }

    /**
     * Testing oxSetupDb::writeAdminLoginData()
     *
     * @return null
     */
    public function testWriteAdminLoginData()
    {
        $sLoginName = 'testLoginName';
        $sPassword = 'testPassword';
        $sPassSalt = 'testSalt';

        $oUtils = $this->getMock("oxSetupUtils", array("generateUID"));
        $oUtils->expects($this->once())->method("generateUID")->will($this->returnValue($sPassSalt));

        $iAt = 0;
        $oDb = $this->getMock("OxSetupDb", array("getInstance", "execSql"));
        $oDb->expects($this->at($iAt++))->method("getInstance")->with($this->equalTo("OxSetupUtils"))->will($this->returnValue($oUtils));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("update oxuser set oxusername='{$sLoginName}', oxpassword='" . hash('sha512', $sPassword . $sPassSalt) . "', oxpasssalt='{$sPassSalt}' where oxid='oxdefaultadmin'"));
        $oDb->expects($this->at($iAt++))->method("execSql")->with($this->equalTo("update oxnewssubscribed set oxemail='{$sLoginName}' where oxuserid='oxdefaultadmin'"));
        $oDb->writeAdminLoginData($sLoginName, $sPassword);
    }

    /**
     * Testing oxSetupDb::convertConfigTableToUtf()
     *
     * @return null
     */
    public function testConvertConfigTableToUtf()
    {
        $oConfk = new Conf();
        $myConfig = oxRegistry::getConfig();
        $reportingLevel = error_reporting((E_ALL ^ E_NOTICE ^ E_DEPRECATED) | E_STRICT);
        $rConnection = mysql_connect($myConfig->getConfigParam('dbHost'), $myConfig->getConfigParam('dbUser'), $myConfig->getConfigParam('dbPwd'));
        mysql_select_db($myConfig->getConfigParam('dbName'));
        $rResult = mysql_query("SELECT oxvarname, oxvartype, DECODE( oxvarvalue, '" . $oConfk->sConfigKey . "') AS oxvarvalue FROM oxconfig WHERE oxvartype IN ('str', 'arr', 'aarr')");
        error_reporting($reportingLevel);
        $iConfRecordsCount = oxDb::getDb()->getOne("SELECT count(*) FROM oxconfig WHERE oxvartype IN ('str', 'arr', 'aarr')");

        $oUtils = $this->getMock("oxSetupUtils", array("convertToUtf8"));
        $oUtils->expects($this->exactly((int) $iConfRecordsCount))->method("convertToUtf8")->will($this->returnValue('testValue'));

        $oDb = $this->getMock("OxSetupDb", array("getInstance", "execSql", "getConnection"));
        $oDb->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oUtils));
        $oDb->expects($this->exactly($iConfRecordsCount + 1))->method("execSql")->will($this->returnValue($rResult));
        $oDb->expects($this->exactly(1))->method("getConnection")->will($this->returnValue($rConnection));
        $oDb->convertConfigTableToUtf();
    }
}
