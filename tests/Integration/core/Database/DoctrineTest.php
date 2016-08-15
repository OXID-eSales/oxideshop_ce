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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\Eshop\Tests\Integration\Core\Database;

use Doctrine\DBAL\DBALException;
use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Doctrine;

/**
 * Tests for our database object.
 *
 * @group database-adapter
 */
class DoctrineTest extends DatabaseInterfaceImplementationTest
{

    /**
     * @var string The database exception class to be thrown
     */
    const DATABASE_EXCEPTION_CLASS = 'OxidEsales\Eshop\Core\exception\DatabaseException';

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet';

    /**
     * @var string The empty result set class class
     */
    const EMPTY_RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineEmptyResultSet';

    /**
     * @var bool Use the legacy database adapter.
     *
     * @todo get rid of this
     */
    const USE_LEGACY_DATABASE = false;

    /**
     * @var DatabaseInterface|Doctrine The database to test.
     */
    protected $database = null;

    /**
     * Create the database object under test.
     *
     * @return Doctrine The database object under test.
     */
    protected function createDatabase()
    {
        return \oxDb::getDb();
    }

    /**
     * Close the database connection.
     */
    protected function closeConnection()
    {
        $this->database->closeConnection();
    }

    /**
     * @return string The name of the database exception class
     */
    protected function getDatabaseExceptionClassName()
    {
        return self::DATABASE_EXCEPTION_CLASS;
    }

    /**
     * @return string The name of the result set class
     */
    protected function getResultSetClassName()
    {
        return self::RESULT_SET_CLASS;
    }

    /**
     * @return string The name of the empty result set class
     */
    protected function getEmptyResultSetClassName()
    {
        return self::EMPTY_RESULT_SET_CLASS;
    }

    /**
     * Test that the expected exception is thrown for an invalid function parameter.
     * See the data provider for arguments considered invalid.
     *
     * @dataProvider dataProviderTestGetAllThrowsDatabaseExceptionOnInvalidArguments
     *
     * @param mixed $invalidParameter A parameter, which is considered invalid and will trigger an exception
     */
    public function testGetAllThrowsDatabaseExceptionOnInvalidArguments($invalidParameter)
    {
        $expectedExceptionClass = '\InvalidArgumentException';
        $this->setExpectedException($expectedExceptionClass);

        $this->database->getAll(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'",
            $invalidParameter
        );
    }

    /**
     * Test delegation of SELECT queries to Doctrine::select()
     */
    public function testExecuteDelegatesSelectQueriesToSelectMethod()
    {
        $query = 'SELECT * FROM ' . self::TABLE_NAME . ' LIMIT 0,1';

        /** @var \OxidEsales\Eshop\Core\Database\Doctrine|\PHPUnit_Framework_MockObject_MockObject $databaseMock */
        $databaseMock = $this->getMockBuilder('\OxidEsales\Eshop\Core\Database\Doctrine')
            ->setMethods(['select'])
            ->getMock();

        $databaseMock->expects($this->once())->method('select');

        $databaseMock->execute(
            $query,
            array()
        );
    }

    /**
     * Test, that the method 'selectLimit' returns the expected rows from the database for different
     * values of limit and offset.
     *
     * This test assumes that there are at least 3 entries in the table.
     *
     * @dataProvider dataProviderTestSelectLimitForInvalidOffsetAndLimit
     *
     * @param string $assertionMessage A message explaining the assertion
     * @param int    $rowCount         Maximum number of rows to return
     * @param int    $offset           Offset of the first row to return
     * @param array  $expectedResult   The expected result of the method call.
     */
    public function testSelectLimitForInvalidOffsetAndLimit($assertionMessage, $rowCount, $offset, $expectedResult)
    {
        $this->loadFixtureToTestTable();
        $sql = 'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (' .
               '"' . self::FIXTURE_OXID_1 . '",' .
               '"' . self::FIXTURE_OXID_2 . '",' .
               '"' . self::FIXTURE_OXID_3 . '"' .
               ')';
        $resultSet = $this->database->selectLimit($sql, $rowCount, $offset);
        $this->assertError(
            E_USER_DEPRECATED,
            'Parameters rowCount and offset have to be numeric in DatabaseInterface::selectLimit(). ' .
            'Please fix your code as this error may trigger an exception in future versions of OXID eShop.'
        );
        $actualResult = $resultSet->getAll();

        $this->assertSame($expectedResult, $actualResult, $assertionMessage);
    }

    /**
     * Data provider for testing selectLimit() with invalid parameters
     * @return array
     */
    public function dataProviderTestSelectLimitForInvalidOffsetAndLimit()
    {
        return array(
            array(
                'If parameter rowCount is integer 2 and offset is string " UNION SELECT oxusername FROM oxuser" , a warning will be triggered and the first 2 rows will be returned',
                2, // row count
                " UNION SELECT oxusername FROM oxuser", // offset
                [
                    [self::FIXTURE_OXID_1], [self::FIXTURE_OXID_2]  // expected result
                ]
            ),
            array(
                'If parameter rowCount is integer 2 and offset is string "1  UNION SELECT oxusername FROM oxuser -- " , a warning will be triggered and last 2 rows will be returned',
                2, // row count
                "1  UNION SELECT oxusername FROM oxuser", // offset
                [
                    [self::FIXTURE_OXID_2], [self::FIXTURE_OXID_3]  // expected result
                ]
            ),
            array(
                'If parameter rowCount is string " UNION SELECT oxusername FROM oxuser  --" and offset is 0, a warning will be triggered and the first 2 rows will be returned',
                " UNION SELECT oxusername FROM oxuser  --", // row count
                0, // offset
                []  // expected result
            ),
            array(
                'If parameter rowCount is string "1 UNION SELECT oxusername FROM oxuser  --" and offset is 0, a warning will be triggered and the first 2 rows will be returned',
                "1  UNION SELECT oxusername FROM oxuser --", // row count
                0, // offset
                [
                    [self::FIXTURE_OXID_1]  // expected result
                ]
            ),
        );
    }

    /**
     * Test, that startTransaction() throws the expected Exception on failure.
     */
    public function testStartTransactionThrowsExpectedExceptionOnFailure()
    {
        $this->setExpectedException(self::DATABASE_EXCEPTION_CLASS);

        $connectionMock =  $this->getMockBuilder('\Doctrine')
            ->setMethods(['beginTransaction'])
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('beginTransaction')
            ->willThrowException(new DBALException());

        /** @var \OxidEsales\Eshop\Core\Database\Doctrine|\PHPUnit_Framework_MockObject_MockObject $databaseMock */
        $databaseMock = $this->getMockBuilder('\OxidEsales\Eshop\Core\Database\Doctrine')
            ->setMethods(['getConnection'])
            ->getMock();
        $databaseMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $databaseMock->startTransaction();
    }

    /**
     * Test, that commitTransaction() throws the expected Exception on failure.
     */
    public function testCommitTransactionThrowsExpectedExceptionOnFailure()
    {
        $this->setExpectedException(self::DATABASE_EXCEPTION_CLASS);

        $connectionMock =  $this->getMockBuilder('\Doctrine')
            ->setMethods(['commit'])
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('commit')
            ->willThrowException(new DBALException());

        /** @var \OxidEsales\Eshop\Core\Database\Doctrine|\PHPUnit_Framework_MockObject_MockObject $databaseMock */
        $databaseMock = $this->getMockBuilder('\OxidEsales\Eshop\Core\Database\Doctrine')
            ->setMethods(['getConnection'])
            ->getMock();
        $databaseMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $databaseMock->commitTransaction();
    }

    /**
     * Test, that rollbackTransaction() throws the expected Exception on failure.
     */
    public function testRollbackTransactionThrowsExpectedExceptionOnFailure()
    {
        $this->setExpectedException(self::DATABASE_EXCEPTION_CLASS);

        $connectionMock =  $this->getMockBuilder('\Doctrine')
            ->setMethods(['rollBack'])
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('rollBack')
            ->willThrowException(new DBALException());

        /** @var \OxidEsales\Eshop\Core\Database\Doctrine|\PHPUnit_Framework_MockObject_MockObject $databaseMock */
        $databaseMock = $this->getMockBuilder('\OxidEsales\Eshop\Core\Database\Doctrine')
            ->setMethods(['getConnection'])
            ->getMock();
        $databaseMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $databaseMock->rollbackTransaction();
    }

    /**
     * Test, that setTransactionIsolationLevel() throws the expected Exception on failure.
     */
    public function testSetTransactionIsolationLevelThrowsExpectedExceptionOnFailure()
    {
        $this->setExpectedException(self::DATABASE_EXCEPTION_CLASS);

        /** @var \OxidEsales\Eshop\Core\Database\Doctrine|\PHPUnit_Framework_MockObject_MockObject $databaseMock */
        $databaseMock = $this->getMockBuilder('\OxidEsales\Eshop\Core\Database\Doctrine')
            ->setMethods(['execute'])
            ->getMock();
        $databaseMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new DBALException());

        $databaseMock->setTransactionIsolationLevel('READ COMMITTED');
    }

    /**
     * Test, that setTransactionIsolationLevel() throws the expected Exception on failure.
     */
    public function testSetTransactionIsolationLevelThrowsExpectedExceptionOnInvalidParameter()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->database->setTransactionIsolationLevel('INVALID TRANSACTION ISOLATION LEVEL');
    }

    /**
     * Test, that the methods exception->getCode and exception->getMessage work like errorNo and errorMsg.
     */
    public function testExceptionGetCodeAndExceptionGetMessageReturnSameResultsAsErrorNoAndErrorMsg()
    {
        $expectedCode = self::EXPECTED_MYSQL_SYNTAX_ERROR_CODE;
        $expectedMessage = self::EXPECTED_MYSQL_SYNTAX_ERROR_MESSAGE;

        try {
            $this->database->execute("INVALID SQL QUERY");
            $actualCode = 0;
            $actualMessage = '';
        } catch (\Exception $exception) {
            $actualCode = $exception->getCode();
            $actualMessage = $exception->getMessage();
        }

        $this->assertSame($expectedCode, $actualCode, 'A mysql syntax error should produce an exception with the expected code');
        $this->assertSame($expectedMessage, $actualMessage, 'A mysql syntax error should produce an exception with the expected message');
    }

    
    /**
     * Assert a given error level and a given error message
     *
     * @param integer $errorLevel   Error number as defined in http://php.net/manual/en/errorfunc.constants.php
     * @param string  $errorMessage Error message
     *
     * @return boolean Returns true on assertion success
     */
    protected function assertError($errorLevel, $errorMessage)
    {
        foreach ($this->errors as $error) {
            if ($error["errorMessage"] === $errorMessage
                && $error["errorLevel"] === $errorLevel
            ) {
                return true;
            }
        }
        $this->fail(
            "No error with level " . $errorLevel . " and message '" . $errorMessage . "' was triggered"
        );
    }
}
