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

use Conf;
use Exception;
use oxDb;
use OxidEsales\EshopCommunity\Setup\Database;
use PDO;
use PHPUnit_Framework_MockObject_MockObject;
use StdClass;

require_once getShopBasePath() . '/Setup/functions.php';

/**
 * SetupDb tests
 */
class DatabaseTest extends \OxidTestCase
{
    /** @var array Queries will be logged here. */
    private $loggedQueries;

    /**
     * Resets logged queries.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loggedQueries = new StdClass();
    }

    /**
     * Testing SetupDb::execSql()
     */
    public function testExecSqlBadConnection()
    {
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getConnection"));
        $database->expects($this->any())->method("getConnection")->will($this->throwException(new Exception('Test')));

        $this->setExpectedException('Exception', 'Test');
        $database->execSql("select 1 + 1");
    }

    /**
     * Testing SetupDb::execSql()
     */
    public function testExecSql()
    {
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getConnection"));
        $database->expects($this->once())->method("getConnection")->will($this->returnValue($this->createConnection()));

        $result = $database->execSql("select 1 + 1")->fetch();
        $this->assertSame('2', $result[0]);
    }

    /**
     * Testing SetupDb::queryFile()
     */
    public function testQueryFileNotExistingFile()
    {
        $setup = $this->getMock("Setup", array("getStep", "setNextStep"));
        $setup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));
        $setup->expects($this->once())->method("setNextStep");

        $language = $this->getMock("Language", array("getText"));
        $language->expects($this->once())->method("getText");

        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance"));

        $at = 0;
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($setup));
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($language));

        $this->setExpectedException('Exception');
        $database->queryFile(time());
    }

    /**
     * Testing SetupDb::queryFile()
     */
    public function testQueryFile()
    {
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getDatabaseVersion", "parseQuery", "execSql"));

        $at = 0;
        $database->expects($this->at($at++))->method("getDatabaseVersion")->will($this->returnValue("5.1"));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo("SET @@session.sql_mode = ''"));
        $database->expects($this->at($at++))->method("parseQuery")->will($this->returnValue(array(1, 2, 3)));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo(1));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo(2));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo(3));

        $database->queryFile(getShopBasePath() . '/config.inc.php');
    }

    /**
     * Testing SetupDb::getDatabaseVersion()
     */
    public function testGetDatabaseVersion()
    {
        $versionInfo = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("SHOW VARIABLES LIKE 'version'");
        $version = $versionInfo[0]["Value"];

        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getConnection"));
        $database->expects($this->once())->method("getConnection")->will($this->returnValue($this->createConnection()));
        $this->assertEquals($version, $database->getDatabaseVersion());
    }

    /**
     * Testing SetupDb::getConnection()
     */
    public function testGetConnection()
    {
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("openDatabase"));
        $database->expects($this->once())->method("openDatabase")->will($this->returnValue("testConnection"));

        $this->assertEquals("testConnection", $database->getConnection());
    }

    /**
     * Testing SetupDb::openDatabase().
     * Connection should not be established due to wrong access info.
     */
    public function testOpenDatabaseConnectionImpossible()
    {
        $parameters['dbHost'] = $this->getConfig()->getConfigParam('dbHost');
        $parameters['dbUser'] = $parameters['dbPwd'] = "wrong_password";

        $sessionMock = $this->getMockBuilder('OxidEsales\\EshopCommunity\\Setup\\Session')->disableOriginalConstructor()->getMock();

        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance"));
        $database->expects($this->any())->method("getInstance")->will($this->returnValue($sessionMock));

        $this->setExpectedException('Exception');

        $database->openDatabase($parameters);
    }

    /**
     * Testing SetupDb::openDatabase()
     */
    public function testOpenDatabaseImpossibleToSelectGivenDatabase()
    {
        $config = $this->getConfig();
        $parameters['dbHost'] = $config->getConfigParam('dbHost');
        $parameters['dbUser'] = $config->getConfigParam('dbUser');
        $parameters['dbPwd'] = $config->getConfigParam('dbPwd');
        $parameters['dbName'] = "wrong_database_name";

        $this->setExpectedException('Exception');

        $sessionMock = $this->getMockBuilder('OxidEsales\\EshopCommunity\\Setup\\Session')->disableOriginalConstructor()->getMock();
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance"));
        $database->expects($this->any())->method("getInstance")->will($this->returnValue($sessionMock));

        $database->openDatabase($parameters);
    }

    /**
     * Testing SetupDb::openDatabase()
     */
    public function testOpenDatabaseWrongDbVersion()
    {
        $myConfig = $this->getConfig();
        $aParams['dbHost'] = $myConfig->getConfigParam('dbHost');
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = time();

        $this->setExpectedException('Exception');

        $sessionMock = $this->getMockBuilder('OxidEsales\\EshopCommunity\\Setup\\Session')->disableOriginalConstructor()->getMock();
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getDatabaseVersion", 'getInstance'));
        $database->expects($this->once())->method("getDatabaseVersion")->will($this->returnValue(4));
        $database->expects($this->any())->method("getInstance")->will($this->returnValue($sessionMock));
        $database->openDatabase($aParams);
    }

    /**
     * Testing SetupDb::openDatabase()
     */
    public function testOpenDatabase()
    {
        $config = $this->getConfig();
        $parameters['dbHost'] = $config->getConfigParam('dbHost');
        $parameters['dbUser'] = $config->getConfigParam('dbUser');
        $parameters['dbPwd'] = $config->getConfigParam('dbPwd');
        $parameters['dbName'] = $config->getConfigParam('dbName');

        $database = new Database();
        $this->assertTrue((bool) $database->openDatabase($parameters));
    }

    /**
     * Testing SetupDb::createDb()
     */
    public function testCreateDb()
    {
        $oSetup = $this->getMock("Setup", array("setNextStep", "getStep"));
        $oSetup->expects($this->once())->method("setNextStep");
        $oSetup->expects($this->once())->method("getStep")->with($this->equalTo("STEP_DB_INFO"));

        $oLang = $this->getMock("Language", array("getText"));
        $oLang->expects($this->once())->method("getText")->with($this->equalTo("ERROR_COULD_NOT_CREATE_DB"));

        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("execSql", "getInstance"));
        $database->expects($this->at(0))->method("execSql")->will($this->throwException(new Exception()));
        $database->expects($this->at(1))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $database->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));

        $this->setExpectedException('Exception');

        $database->createDb("");
    }

    /**
     * Testing SetupDb::saveShopSettings()
     */
    public function testSaveShopSettings()
    {
        $utils = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Utilities', array("generateUid"));
        $utils->expects($this->any())->method("generateUid")->will($this->returnValue("testid"));

        $session = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Session', array("setSessionParam", "getSessionParam"), array(), '', null);

        $map = array(
            array('location_lang', null),
            array('check_for_updates', null),
            array('country_lang', null),
        );
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $map[] = array('use_dynamic_pages', true);
        } else {
            $map[] = array('use_dynamic_pages', false);
        }
        $session->expects($this->any())->method("getSessionParam")->will($this->returnValueMap($map));


        $setup = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Setup', array("getShopId"));
        $setup->expects($this->any())->method("getShopId");

        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("execSql", "getInstance", "getConnection"));
        $map = array(
            array('Utilities', $utils),
            array('Session', $session),
            array('Setup', $setup)
        );
        $database->expects($this->any())->method("getInstance")->will($this->returnValueMap($map));
        $database->expects($this->any())->method("getConnection")->will($this->returnValue($this->createConnection()));

        $database->saveShopSettings(array());
    }

    /**
     * Testing SetupDb::setMySqlCollation()
     */
    public function testSetMySqlCollationUtfMode()
    {
        $connection = $this->createConnectionMock();
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array('getConnection'));
        $database->expects($this->any())->method('getConnection')->will($this->returnValue($connection));
        $database->setMySqlCollation(1);

        $expectedQueries = array(
            "ALTER SCHEMA CHARACTER SET utf8 COLLATE utf8_general_ci",
            "set names 'utf8'",
            "set character_set_database=utf8",
            "SET CHARACTER SET latin1",
            "SET CHARACTER_SET_CONNECTION = utf8",
            "SET character_set_results = utf8",
            "SET character_set_server = utf8"
        );
        $this->assertEquals($expectedQueries, $this->getLoggedQueries());
    }

    /**
     * Testing SetupDb::setMySqlCollation()
     */
    public function testSetMySqlCollation()
    {
        $connection = $this->createConnectionMock();
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getConnection"));
        $database->expects($this->any())->method("getConnection")->will($this->returnValue($connection));
        $database->setMySqlCollation(0);

        $expectedQueries = array(
            "ALTER SCHEMA CHARACTER SET latin1 COLLATE latin1_general_ci",
            "SET CHARACTER SET latin1",
        );
        $this->assertEquals($expectedQueries, $this->getLoggedQueries());
    }

    /**
     * Testing SetupDb::writeUtfMode()
     */
    public function testWriteUtfMode()
    {
        $setup = $this->getMock("Setup", array("getShopId"));
        $setup->expects($this->once())->method("getShopId")->will($this->returnValue('testShopId'));

        $configKey = new Conf();
        $query = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values('iSetUtfMode', 'testShopId', 'iSetUtfMode', 'str', ENCODE( '1', '" . $configKey->sConfigKey . "') )";

        $at = 0;
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance", "execSql"));
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($setup));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo($query));
        $database->writeUtfMode(1);
    }

    /**
     * Testing SetupDb::writeAdminLoginData()
     */
    public function testWriteAdminLoginData()
    {
        $loginName = 'testLoginName';
        $password = 'testPassword';
        $passwordSalt = 'testSalt';

        $oUtils = $this->getMock("Utilities", array("generateUID"));
        $oUtils->expects($this->once())->method("generateUID")->will($this->returnValue($passwordSalt));

        $at = 0;
        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance", "execSql"));
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo("update oxuser set oxusername='{$loginName}', oxpassword='" . hash('sha512', $password . $passwordSalt) . "', oxpasssalt='{$passwordSalt}' where OXUSERNAME='admin'"));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo("update oxnewssubscribed set oxemail='{$loginName}' where OXEMAIL='admin'"));
        $database->writeAdminLoginData($loginName, $password);
    }

    /**
     * Testing SetupDb::convertConfigTableToUtf()
     */
    public function testConvertConfigTableToUtf()
    {
        $connection = $this->createConnection();
        $configRecordsCount = oxDb::getDb()->getOne("SELECT count(*) FROM oxconfig WHERE oxvartype IN ('str', 'arr', 'aarr')");

        $utils = $this->getMock("Utilities", array("convertToUtf8"));
        $utils->expects($this->exactly((int) $configRecordsCount))->method("convertToUtf8")->will($this->returnValue('testValue'));

        /** @var Database|PHPUnit_Framework_MockObject_MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance", "execSql", "getConnection"));
        $database->expects($this->once())->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($utils));
        $database->expects($this->exactly(1))->method("getConnection")->will($this->returnValue($connection));

        $database->convertConfigTableToUtf();
    }

    /**
     * @return PDO
     */
    protected function createConnection()
    {
        $config = $this->getConfig();
        $dsn = sprintf('mysql:dbname=%s;host=%s', $config->getConfigParam('dbName'), $config->getConfigParam('dbHost'));
        $pdo = new PDO(
            $dsn,
            $config->getConfigParam('dbUser'),
            $config->getConfigParam('dbPwd'),
            array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
        );

        return $pdo;
    }

    /**
     * Logs exec queries instead of executing them.
     * Prepared statements will be executed as usual and will not be logged.
     *
     * @return PDO
     */
    protected function createConnectionMock()
    {
        $config = $this->getConfig();
        $dsn = sprintf('mysql:host=%s', $config->getConfigParam('dbHost'));
        $pdoMock = $this->getMock('PDO', array('exec'), array(
            $dsn,
            $config->getConfigParam('dbUser'),
            $config->getConfigParam('dbPwd')));

        $loggedQueries = $this->loggedQueries;
        $pdoMock->expects($this->any())
            ->method('exec')
            ->will($this->returnCallback(function ($query) use ($loggedQueries) {
                $loggedQueries->queries[] = $query;
            }));

        return $pdoMock;
    }

    /**
     * Returns logged queries when mocked connection is used.
     *
     * @return array
     */
    protected function getLoggedQueries()
    {
        return $this->loggedQueries->queries;
    }
}
