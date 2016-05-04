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

namespace OxidEsales\Eshop\Tests\integration\core\Database\Adapter;

/**
 * Class LegacyResultSetTest
 *
 * @package OxidEsales\Eshop\Tests\integration\core\Database\Adapter
 *
 * @group database-adapter
 */
class LegacyResultSetTest extends ResultSetTest
{

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'object_ResultSet';

    /**
     * @var string The empty result set class class
     */
    const EMPTY_RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet';

    /**
     * Create the database object under test.
     *
     * @return \oxLegacyDb The database object under test.
     */
    protected function createDatabase()
    {
        return \oxDb::getDb();
    }

    /**
     * Create the database object under test - the static pendant to use in the setUpBeforeClass and tearDownAfterClass.
     *
     * @return \oxLegacyDb The database object under test.
     */
    protected static function createDatabaseStatic()
    {
        return \oxDb::getDb();
    }

    /**
     * The following test are exceptions of the abstract ResultSetTest, which are designed especially for using
     * ADODB lite with the 'mysqli' driver.
     *
     * ADODB lite returns a sightly different result set for the driver 'mysqli', which is currently the standard driver
     * in v6.0-beta and which is used in the CI.
     * Yet the Doctrine implementation is based on the result set, which ADODB lite would return when using the 'mysql' driver.
     * This behaviour is also closer to pdp_mysql, which will be the preferred driver, when using Doctrine in v6.0.
     */

    /**
     * Test, that the method 'moveNext' works for a non empty result set.
     */
    public function testMoveNextWithNonEmptyResultSetReachingEnd()
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $resultSet->moveNext();
        $resultSet->moveNext();
        $methodResult = $resultSet->moveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertNull($resultSet->fields);
        $this->assertFalse($methodResult);

        $methodResult = $resultSet->moveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertNull($resultSet->fields);
        $this->assertFalse($methodResult);
    }
    /**
     * Test, that the method 'moveNext' works for an empty result set.
     */
    public function testMoveNextWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $methodResult = $resultSet->moveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertNull($resultSet->fields);
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'fetchField' works as expected.
     */
    public function testFetchField()
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->database->select('SELECT * FROM ' . self::TABLE_NAME);

        $columnInformationOne = $resultSet->fetchField(0);

        $this->assertSame('stdClass', get_class($columnInformationOne));

        /**
         * We are skipping the doctrine unsupported features here.
         */
        $fields = array(
            'name'        => 'oxid',
            'table'       => self::TABLE_NAME,
            'max_length'  => 6, // There is a difference when using ADODB lite with 'mysqli' or 'mysql' driver
            // 'not_null'    => 0,
            // 'primary_key' => 0,
            'type'        => 254, // There is a difference when using ADODB lite with 'mysqli' or 'mysql' driver
            // 'unsigned'     => 0,
            // 'zerofill'     => 0
            // 'def'          => '',
            // 'multiple_key' => 0,
            // 'unique_key'   => 0,
            // 'numeric'      => 0,
            // 'blob'         => 0,
        );

        foreach ($fields as $attributeName => $attributeValue) {
            $this->assertObjectHasAttributeWithValue($columnInformationOne, $attributeName, $attributeValue);
        }
    }


    /**
     * @return array The parameters we want to use for the testFields method.
     */
    public function dataProviderTestFields()
    {
        return array(
            array('SELECT OXID FROM ' . self::TABLE_NAME, 0, false, null),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 'OXID', false, null),
            array('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 0, true, array(self::FIXTURE_OXID_1)),
            array('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 'OXID', true, null),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 0, true, array(self::FIXTURE_OXID_1, self::FIXTURE_OXUSERID_1)),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 1, true, self::FIXTURE_OXUSERID_1),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 'OXID', true, self::FIXTURE_OXID_1, true),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 0, true, array('OXID' => self::FIXTURE_OXID_1, 'OXUSERID' => self::FIXTURE_OXUSERID_1), true),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 'NOTNULL', true, null, true),
        );
    }


    /**
     * Test, that the method 'move' works with an empty result set.
     */
    public function testMoveWithEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $methodResult = $resultSet->move(7);

        $this->assertFalse($methodResult);
        $this->assertTrue($resultSet->EOF);
        $this->assertNull($resultSet->fields);
    }

    /**
     * Test, that the method 'moveFirst' works as expected for an empty result set.
     */
    public function testMoveFirstEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $methodResult = $resultSet->moveFirst();

        $this->assertTrue($methodResult);
        $this->assertNull($resultSet->fields);
        $this->assertTrue($resultSet->EOF);
    }

    /**
     * Test, that the method 'moveLast' works as expected for an empty result set.
     */
    public function testMoveLastEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $methodResult = $resultSet->moveLast();

        $this->assertFalse($methodResult);
        $this->assertTrue($resultSet->EOF);
        $this->assertNull($resultSet->fields);
    }
}
