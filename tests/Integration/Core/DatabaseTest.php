<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use oxDb;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseConnectionException;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseNotConfiguredException;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class DatabaseTest
 *
 * @group database-adapter
 * @covers OxidEsales\EshopCommunity\Core\DatabaseProvider
 */
class DatabaseTest extends UnitTestCase
{
    /** @var mixed Backing up for earlier value of database link object */
    private $dbObjectBackup = null;

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dbObjectBackup = $this->getProtectedClassProperty(oxDb::getInstance(), 'db');
    }

    /**
     * Executed after test is down.
     */
    protected function tearDown()
    {
        oxDb::getDb()->closeConnection();

        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', $this->dbObjectBackup);

        oxDb::getDb()->closeConnection();

        parent::tearDown();
    }

    public function testEnsureConnectionIsEstablishedExceptionPath()
    {
        $exceptionMessage = "Exception: the database connection couldn't be established!";

        $dbMock = $this->createDatabaseMock($exceptionMessage);
        $dbMock->expects($this->once())
            ->method('isConnectionEstablished')
            ->willReturn(false);

        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', $dbMock);

        $this->expectException('Exception');
        $this->expectExceptionMessage($exceptionMessage);

        oxDb::getDb()->connect();
    }

    public function testEnsureConnectionIsEstablishedNonExceptionPath()
    {
        $dbMock = $this->createDatabaseMock();
        $dbMock->expects($this->once())
            ->method('isConnectionEstablished')
            ->willReturn(true);

        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', $dbMock);

        oxDb::getDb()->connect();
    }

    public function testGetDbThrowsDatabaseConnectionException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);
        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', null);

        $exceptionThrown = false;
        try {
            oxDb::getDb();
        } catch (DatabaseConnectionException $exception) {
            $exceptionThrown = true;
        } finally {
            /** Restore original configFile object */
            Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFileBackup);
        }

        if (!$exceptionThrown) {
            $this->fail('A DatabaseConnectionException should have been thrown, as the ConfigFile object does contain proper credentials.');
        }
    }

    public function testGetDbThrowsDatabaseNotConfiguredException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        $configFile->setVar('dbHost', '<');
        Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);
        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', null);

        try {
            oxDb::getDb();
            $this->fail('A DatabaseNotConfiguredException should have been thrown, as the ConfigFile object does does not pass validation.');
        } catch (DatabaseNotConfiguredException $exception) {
            /** Restore original configFile object */
            Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFileBackup);
        }
    }

    /**
     * Test, that the cache will not
     */
    public function testUnflushedCacheDoesntWorks()
    {
        $database = oxDb::getInstance();
        $connection = oxDb::getDb();

        $connection->execute('CREATE TABLE IF NOT EXISTS TEST(OXID char(32));');

        $tableDescription = $database->getTableDescription('TEST');

        $connection->execute('ALTER TABLE TEST ADD OXTITLE CHAR(32); ');

        $tableDescriptionTwo = $database->getTableDescription('TEST');

        // clean up
        $connection->execute('DROP TABLE IF EXISTS TEST;');

        $this->assertSame(1, count($tableDescription));
        $this->assertSame(1, count($tableDescriptionTwo));
    }

    /**
     * Test, that the flushing really refreshes the table description cache.
     */
    public function testFlushedCacheHasActualInformation()
    {
        $database = oxDb::getInstance();
        $connection = oxDb::getDb();

        $connection->execute('CREATE TABLE IF NOT EXISTS TEST(OXID char(32));');

        $tableDescription = $database->getTableDescription('TEST');

        $connection->execute('ALTER TABLE TEST ADD OXTITLE CHAR(32); ');

        $database->flushTableDescriptionCache();

        $tableDescriptionTwo = $database->getTableDescription('TEST');

        // clean up
        $connection->execute('DROP TABLE IF EXISTS TEST;');

        $this->assertSame(1, count($tableDescription));
        $this->assertSame(2, count($tableDescriptionTwo));
    }

    /**
     * Test default connection encoding is utf8
     */
    public function testDefaultDatabaseConnectionEncoding()
    {
        $connection = DatabaseProvider::getDb();

        $result = $connection->getRow("SHOW VARIABLES LIKE  'character_set_connection';");
        $this->assertSame("utf8", $result[1]);
    }

    /**
     * Test default connection encoding is utf8
     */
    public function testSpecificDatabaseConnectionEncoding()
    {
        $configFile = Registry::get(ConfigFile::class);
        $configFile->setVar('dbCharset', 'utf8mb4');
        Registry::set(ConfigFile::class, $configFile);

        $this->setProtectedClassProperty(DatabaseProvider::getInstance(), 'db', null);
        $connection = DatabaseProvider::getDb();

        $result = $connection->getRow("SHOW VARIABLES LIKE  'character_set_connection';");
        $this->assertSame("utf8mb4", $result[1]);
    }

    /**
     * Helper methods
     */

    /**
     * @return ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new ConfigFile($this->createFile('config.inc.php', '<?php '));
    }

    /**
     * Create mock of the connection. Only the connect method is mocked.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createConnectionMock()
    {
        $connectionMock = $this->getMock('Connection', array('connect'));
        $connectionMock->expects($this->once())->method('connect');

        return $connectionMock;
    }

    /**
     * Create a mock of the database. If there is an exception message, we expect, that it will be created by the
     * connection error message creation method.
     *
     * @param string $exceptionMessage The optional method of the maybe thrown exception.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createDatabaseMock($exceptionMessage = '')
    {
        $connectionMock = $this->createConnectionMock();

        $dbMock = $this->getMock(
            'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database',
            array('isConnectionEstablished', 'getConnectionFromDriverManager', 'createConnectionErrorMessage', 'setFetchMode', 'closeConnection')
        );
        $dbMock->expects($this->once())
            ->method('getConnectionFromDriverManager')
            ->willReturn($connectionMock);

        if ('' !== $exceptionMessage) {
            $dbMock->expects($this->once())->method('createConnectionErrorMessage')->willReturn($exceptionMessage);
        } else {
            $dbMock->expects($this->never())->method('createConnectionErrorMessage');
        }

        return $dbMock;
    }
}
