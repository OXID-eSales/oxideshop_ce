<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use Psr\Log\NullLogger;
use Psr\Log\Test\TestLogger;
use ReflectionClass;

/**
 * Class DbTest
 *
 * @group   database-adapter
 * @covers  \OxidEsales\Eshop\Core\DatabaseProvider
 * @package Unit\Core
 */
class DatabaseTest extends UnitTestCase
{
    /**
     * Clean-up oxarticles table + parent::tearDown()
     */
    protected function tearDown(): void
    {
        $configFile = new \OxidEsales\Eshop\Core\ConfigFile(OX_BASE_PATH . 'config.inc.php');
        Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);

        $this->cleanUpTable('oxarticles');

        Registry::set('logger', getLogger());

        parent::tearDown();
    }


    /**
     * Call a given protected method on an given instance of a class and return the result.
     *
     * @param object $classInstance Instance of the class on which the method will be called
     * @param string $methodName    Name of the method to be called
     * @param array  $params        Parameters of the method to be called
     *
     * @return mixed
     */
    protected function callProtectedClassMethod($classInstance, $methodName, array $params = [])
    {
        $className = get_class($classInstance);

        $reflectionClass = new ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($classInstance, $params);
    }

    public function testSetConfig()
    {
        $debug = 7;

        $configFile = $this->getBlankConfigFile();
        $configFile->iDebug = $debug;

        $database = oxDb::getInstance();
        $database->setConfigFile($configFile);

        $actualResult = $this->callProtectedClassMethod($database, 'getConfigParam', array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');

        $debug = 8;
        $configFile->iDebug = $debug;
        $database->setConfigFile($configFile);
        $actualResult = $this->callProtectedClassMethod($database, 'getConfigParam', array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');
    }

    public function testGetTableDescription()
    {
        /** Reset the table description cache */
        $database = oxDb::getInstance();
        $database->flushTableDescriptionCache();

        $resultSet = oxDb::getDb()->select("SHOW TABLES");
        $count = 3;
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF && $count--) {
                $tableName = $resultSet->fields[0];

                $metaColumns = oxDb::getDb()->metaColumns($tableName);
                $metaColumnOne = oxDb::getInstance()->getTableDescription($tableName);
                $metaColumnOneCached = oxDb::getInstance()->getTableDescription($tableName);

                $this->assertEquals($metaColumns, $metaColumnOne, "not cached return is bad [shouldn't be] of $tableName.");
                $this->assertEquals($metaColumns, $metaColumnOneCached, "cached [simple] return is bad of $tableName.");

                $resultSet->fetchRow();
            }
        } else {
            $this->fail("No tables found with 'SHOW TABLES'!");
        }
    }

    public function testGetInstanceReturnsInstanceOfDatabase()
    {
        $database = oxDb::getInstance();

        $this->assertInstanceOf(DatabaseProvider::class, $database);
    }

    public function testGetDbReturnsAnInstanceOfDatabaseInterface()
    {
        $database = oxDb::getDb();

        $this->assertInstanceOf(DatabaseInterface::class, $database);
    }

    public function testGetDbReturnsAnInstanceOfDoctrine()
    {
        $database = oxDb::getDb();

        $this->assertInstanceOf(Database::class, $database);
    }

    public function provideQueriesToBeChecked()
    {
        return [
            [
                "SELECT * FROM oxid.oxcontents where OXCONTENT LIKE '%&nbsp;%';",
            ],
            [
                "SELECT * FROM oxid.oxcontents where OXCONTENT LIKE '\';';",
            ],
            [
                "SELECT * FROM oxid.oxcontents where OXCONTENT LIKE ';\'';",
            ],
            [
                'SELECT * FROM oxid.oxcontents where OXCONTENT LIKE "%&nbsp;%";',
            ],
            [
                'SELECT * FROM oxid.oxcontents where OXCONTENT LIKE "\";";',
            ],
            [
                'SELECT * FROM `oxid`.`oxshops;`;',
            ],
            [
                "SELECT 
                oxv_oxarticles_1_de.oxid, oxv_oxarticles_1_de.oxtimestamp
            FROM
                oxv_oxarticles_1_de
            WHERE
                (oxv_oxarticles_1_de.oxactive = 1
                    AND oxv_oxarticles_1_de.oxhidden = 0
                    AND (oxv_oxarticles_1_de.oxstockflag != 2
                    OR (oxv_oxarticles_1_de.oxstock + oxv_oxarticles_1_de.oxvarstock) > 0)
                    AND IF(oxv_oxarticles_1_de.oxvarcount = 0,
                    1,
                    (SELECT 
                            1
                        FROM
                            oxv_oxarticles_1_de AS art
                        WHERE
                            art.oxparentid = oxv_oxarticles_1_de.oxid
                                AND art.oxactive = 1
                                AND (art.oxstockflag != 2 OR art.oxstock > 0)
                        LIMIT 1)))
                    AND oxv_oxarticles_1_de.oxparentid = ''
                    AND oxv_oxarticles_1_de.oxissearch = 1
                    AND ((oxv_oxarticles_1_de.oxtitle LIKE '%ledergürtel%'
                    OR oxv_oxarticles_1_de.oxtitle LIKE '%lederg&uuml;rtel%'
                    OR oxv_oxarticles_1_de.oxshortdesc LIKE '%ledergürtel%'
                    OR oxv_oxarticles_1_de.oxshortdesc LIKE '%lederg&uuml;rtel%'
                    OR oxv_oxarticles_1_de.oxsearchkeys LIKE '%ledergürtel%'
                    OR oxv_oxarticles_1_de.oxsearchkeys LIKE '%lederg&uuml;rtel%'
                    OR oxv_oxarticles_1_de.oxartnum LIKE '%ledergürtel%'
                    OR oxv_oxarticles_1_de.oxartnum LIKE '%lederg&uuml;rtel%'))"
            ],
            [
                "SELECT 
                    `oxv_oxarticles_de`.`oxid`, oxv_oxarticles_de.oxtimestamp
                FROM
                    oxv_oxarticles_de
                WHERE
                    (oxv_oxarticles_de.oxactive = 1
                        AND oxv_oxarticles_de.oxhidden = 0
                        AND (oxv_oxarticles_de.oxstockflag != 2
                        OR (oxv_oxarticles_de.oxstock + oxv_oxarticles_de.oxvarstock) > 0)
                        AND IF(oxv_oxarticles_de.oxvarcount = 0,
                        1,
                        (SELECT 
                                1
                            FROM
                                oxv_oxarticles_de AS art
                            WHERE
                                art.oxparentid = oxv_oxarticles_de.oxid
                                    AND art.oxactive = 1
                                    AND (art.oxstockflag != 2 OR art.oxstock > 0)
                            LIMIT 1)))
                        AND oxv_oxarticles_de.oxparentid = ''
                        AND oxv_oxarticles_de.oxissearch = 1
                        AND ((oxv_oxarticles_de.oxtitle LIKE '%test\"%'
                        OR oxv_oxarticles_de.oxshortdesc LIKE '%test\"%'
                        OR oxv_oxarticles_de.oxsearchkeys LIKE '%test\"%'
                        OR oxv_oxarticles_de.oxartnum LIKE '%test\"%')
                        OR (oxv_oxarticles_de.oxtitle LIKE '%test2%'
                        OR oxv_oxarticles_de.oxshortdesc LIKE '%test2%'
                        OR oxv_oxarticles_de.oxsearchkeys LIKE '%test2%'
                        OR oxv_oxarticles_de.oxartnum LIKE '%test2%')
                        OR (oxv_oxarticles_de.oxtitle LIKE '%&#x84;%'
                        OR oxv_oxarticles_de.oxshortdesc LIKE '%&#x84;%'
                        OR oxv_oxarticles_de.oxsearchkeys LIKE '%&#x84;%'
                        OR oxv_oxarticles_de.oxartnum LIKE '%&#x84;%'))
                LIMIT 10 OFFSET 0"
            ]
        ];
    }

    /**
     * @dataProvider provideQueriesToBeChecked
     */
    public function testCheckForMultipleQueriesWontSplit($queryToSplit)
    {
        $database = oxDb::getDb();

        $logger = new TestLogger();
        Registry::set('logger', $logger);

        $this->assertEquals(
            $queryToSplit,
            $this->callProtectedClassMethod(
                $database,
                'checkForMultipleQueries',
                [$queryToSplit, []]
            )
        );

        $this->assertFalse($logger->hasErrorRecords());
    }

    public function testCheckForMultipleQueriesRealSplit()
    {
        $queryWhichNeedsSplit = "SELECT 1 as 'id'; UPDATE oxuser SET oxusername = 'myUser' WHERE oxusername = 'myUser';";
        $database = oxDb::getDb();

        $logger = new TestLogger();
        Registry::set('logger', $logger);

        $this->assertEquals(
            "SELECT 1 as 'id';",
            $this->callProtectedClassMethod(
                $database,
                'checkForMultipleQueries',
                [$queryWhichNeedsSplit, []]
            )
        );

        $this->assertTrue($logger->hasErrorRecords());
    }

    /**
     * Helper methods
     */

    /**
     * @return \OxidEsales\Eshop\Core\ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new \OxidEsales\Eshop\Core\ConfigFile($this->createFile('config.inc.php', '<?php '));
    }
}
