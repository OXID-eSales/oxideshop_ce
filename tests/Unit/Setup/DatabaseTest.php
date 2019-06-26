<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Setup;

use Exception;
use oxDb;
use OxidEsales\EshopCommunity\Setup\Database;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use StdClass;

require_once getShopBasePath() . '/Setup/functions.php';

/**
 * Class TestSetupDatabase
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Setup
 */
class TestSetupDatabase extends \OxidEsales\EshopCommunity\Setup\Database
{
    protected $sessionMock = null;
    protected $languageMock = null;

    public function __construct($sessionMock, $languageMock)
    {
        $this->sessionMock = $sessionMock;
        $this->languageMock = $languageMock;
    }

    /**
     * Returns requested instance object.
     * Test helper to mock session instance.
     *
     * @param string $instanceName instance name
     *
     * @return Core
     */
    public function getInstance($instanceName)
    {
        if ('session' === strtolower($instanceName)) {
            return $this->sessionMock;
        }
        if ('language' === strtolower($instanceName)) {
            return $this->languageMock;
        }

        return parent::getInstance($instanceName);
    }
}

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
        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getConnection"));
        $database->expects($this->any())->method("getConnection")->will($this->throwException(new Exception('Test')));

        $this->expectException('Exception');
        $this->expectExceptionMessage('Test');
        $database->execSql("select 1 + 1");
    }

    /**
     * Testing SetupDb::execSql()
     */
    public function testExecSql()
    {
        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
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

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance"));

        $at = 0;
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($setup));
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($language));

        $this->expectException('Exception');
        $database->queryFile(time());
    }

    /**
     * Testing SetupDb::queryFile()
     */
    public function testQueryFile()
    {
        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
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

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getConnection"));
        $database->expects($this->once())->method("getConnection")->will($this->returnValue($this->createConnection()));
        $this->assertEquals($version, $database->getDatabaseVersion());
    }

    /**
     * Testing SetupDb::getConnection()
     */
    public function testGetConnection()
    {
        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
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

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance"));
        $database->expects($this->any())->method("getInstance")->will($this->returnValue($sessionMock));

        $this->expectException('Exception');

        $database->openDatabase($parameters);
    }

    /**
     * Testing SetupDb::openDatabase()
     */
    public function testOpenDatabaseImpossibleToSelectGivenDatabase()
    {
        $config = $this->getConfig();
        $parameters['dbHost'] = $config->getConfigParam('dbHost');
        $parameters['dbPort'] = $config->getConfigParam('dbPort') ? $config->getConfigParam('dbPort') : 3306;
        $parameters['dbUser'] = $config->getConfigParam('dbUser');
        $parameters['dbPwd'] = $config->getConfigParam('dbPwd');
        $parameters['dbName'] = "wrong_database_name";

        $this->expectException('Exception');

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
        $aParams['dbPort'] = $myConfig->getConfigParam('dbPort') ? $myConfig->getConfigParam('dbPort') : 3306;
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = time();

        $this->expectException('Exception');

        $sessionMock = $this->getMockBuilder('OxidEsales\\EshopCommunity\\Setup\\Session')->disableOriginalConstructor()->getMock();
        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getDatabaseVersion", 'getInstance', 'userDecidedIgnoreDBWarning'));
        $database->expects($this->any())->method("getDatabaseVersion")->will($this->returnValue('5.6.53-0ubuntu0.14.04.1'));
        $database->expects($this->any())->method("getInstance")->will($this->returnValue($sessionMock));
        $database->expects($this->any())->method('userDecidedIgnoreDBWarning')->will($this->returnValue(false));
        $database->openDatabase($aParams);
    }

    /**
     * Testing SetupDb::openDatabase()
     */
    public function testOpenDatabaseWrongDbVersionUserDecidedNotIgnore()
    {
        $myConfig = $this->getConfig();
        $aParams['dbHost'] = $myConfig->getConfigParam('dbHost');
        $aParams['dbPort'] = $myConfig->getConfigParam('dbPort') ? $myConfig->getConfigParam('dbPort') : 3306;
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = time();

        $message = 'WARNING: A bug in MySQL 5.6 may lead to problems in OXID eShop Enterprise Edition. Hence, we do not recommend MySQL 5.6.';
        $this->expectException('Exception');
        $this->expectExceptionMessage($message);

        $sessionMock = $this->getMockBuilder('OxidEsales\\EshopCommunity\\Setup\\Language')->disableOriginalConstructor()->getMock();
        $languageMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Language::class, array('getInstance', 'getLanguage'), array(), '', false);
        $languageMock->expects($this->any())->method('getLanguage')->will($this->returnValue('en'));

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Setup\TestSetupDatabase::class, array('getDatabaseVersion', 'userDecidedIgnoreDBWarning'), array($sessionMock, $languageMock));
        $database->expects($this->any())->method('getDatabaseVersion')->will($this->returnValue('5.6.53-0ubuntu0.14.04.1'));
        $database->expects($this->any())->method('userDecidedIgnoreDBWarning')->will($this->returnValue(false));
        $database->openDatabase($aParams);
    }

    /**
     * Testing SetupDb::openDatabase().
     * User decided to ignore warning about not recommended MySQL version.
     * We get an error because the database was not yet created which is ok in this case.
     */
    public function testOpenDatabaseWrongDbVersionUserDecidedIgnore()
    {
        $myConfig = $this->getConfig();
        $aParams['dbHost'] = $myConfig->getConfigParam('dbHost');
        $aParams['dbPort'] = $myConfig->getConfigParam('dbPort') ? $myConfig->getConfigParam('dbPort') : 3306;
        $aParams['dbUser'] = $myConfig->getConfigParam('dbUser');
        $aParams['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $aParams['dbName'] = time();

        $this->expectException('Exception');
        $this->expectExceptionMessage('ERROR: Database not available and also cannot be created!');

        $sessionMock = $this->getMockBuilder('OxidEsales\\EshopCommunity\\Setup\\Language')->disableOriginalConstructor()->getMock();
        $languageMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Language::class, array('getInstance', 'getLanguage'), array(), '', false);
        $languageMock->expects($this->any())->method('getLanguage')->will($this->returnValue('en'));

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Setup\TestSetupDatabase::class, array('getDatabaseVersion', 'userDecidedIgnoreDBWarning'), array($sessionMock, $languageMock));
        $database->expects($this->any())->method('getDatabaseVersion')->will($this->returnValue('5.6.53-0ubuntu0.14.04.1'));
        $database->expects($this->any())->method('userDecidedIgnoreDBWarning')->will($this->returnValue(true));
        $database->openDatabase($aParams);
    }

    /**
     * Testing SetupDb::openDatabase()
     */
    public function testOpenDatabase()
    {
        $config = $this->getConfig();
        $parameters['dbHost'] = $config->getConfigParam('dbHost');
        $parameters['dbPort'] = $config->getConfigParam('dbPort') ? $config->getConfigParam('dbPort') : 3306;
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

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("execSql", "getInstance"));
        $database->expects($this->at(0))->method("execSql")->will($this->throwException(new Exception()));
        $database->expects($this->at(1))->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oSetup));
        $database->expects($this->at(2))->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oLang));

        $this->expectException('Exception');

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
            array('check_for_updates', null),
            array('country_lang', null),
        );
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $map[] = array('send_technical_information_to_oxid', true);
        } else {
            $map[] = array('send_technical_information_to_oxid', false);
        }
        $session->expects($this->any())->method("getSessionParam")->will($this->returnValueMap($map));


        $setup = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Setup', array("getShopId"));
        $setup->expects($this->any())->method("getShopId");

        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
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
        /** @var Database|PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Database', array("getInstance", "execSql"));
        $database->expects($this->at($at++))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oUtils));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo("update oxuser set oxusername='{$loginName}', oxpassword='" . hash('sha512', $password . $passwordSalt) . "', oxpasssalt='{$passwordSalt}' where OXUSERNAME='admin'"));
        $database->expects($this->at($at++))->method("execSql")->with($this->equalTo("update oxnewssubscribed set oxemail='{$loginName}' where OXEMAIL='admin'"));
        $database->writeAdminLoginData($loginName, $password);
    }

    /**
     * @return PDO
     */
    protected function createConnection()
    {
        $config = $this->getConfig();
        $dsn = sprintf('mysql:dbname=%s;host=%s;port=%s', $config->getConfigParam('dbName'), $config->getConfigParam('dbHost'), $config->getConfigParam('dbPort'));
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
        $dsn = sprintf('mysql:host=%s;port=%s', $config->getConfigParam('dbHost'), $config->getConfigParam('dbPort'));
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
