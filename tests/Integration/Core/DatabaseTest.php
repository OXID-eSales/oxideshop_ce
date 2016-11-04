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

namespace Integration\Core;

use oxDb;
use OxidEsales\EshopCommunity\Core\ConfigFile;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseConnectionException;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseNotConfiguredException;

/**
 * Class DatabaseTest
 *
 * @group database-adapter
 * @covers OxidEsales\EshopCommunity\Core\Database
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

        $this->setExpectedException('Exception', $exceptionMessage);

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
        Registry::set('oxConfigFile', $configFile);
        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', null);

        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\DatabaseConnectionException');

        try {
            oxDb::getDb();
        } catch (DatabaseConnectionException $exception) {
            /** Restore original configFile object */
            Registry::set('oxConfigFile', $configFileBackup);
            throw $exception;
        }
    }

    public function testGetDbThrowsDatabaseNotConfiguredException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        $configFile->setVar('dbHost', '<');
        Registry::set('oxConfigFile', $configFile);
        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', null);

        $this->setExpectedException('OxidEsales\EshopCommunity\Core\Exception\DatabaseNotConfiguredException');

        try {
            oxDb::getDb();
        } catch (DatabaseNotConfiguredException $exception) {
            /** Restore original configFile object */
            Registry::set('oxConfigFile', $configFileBackup);
            throw $exception;
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
     * @return \PHPUnit_Framework_MockObject_MockObject
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
     * @return \PHPUnit_Framework_MockObject_MockObject
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
