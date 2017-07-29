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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Database;

use OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Unit tests for our database abstraction layer object.
 *
 * @group database-adapter
 */
class DatabaseTest extends UnitTestCase
{

    /**
     * @var Database The doctrine database we want to test in this class.
     */
    protected $database = null;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->database = \oxDb::getDb();
    }

    /**
     * Test, that the method 'quote' works with null.
     */
    public function testQuoteWorksWithNull()
    {
        $quoted = $this->database->quote(null);

        $this->assertEquals("''", $quoted);
    }

    /**
     * Test, that the method 'quote' works with an empty string.
     */
    public function testQuoteWorksWithEmptyString()
    {
        $quoted = $this->database->quote('');

        $this->assertEquals("''", $quoted);
    }

    /**
     * Test, that the method 'quote' works with a non empty value.
     */
    public function testQuoteWorksWithNonEmptyValue()
    {
        $quoted = $this->database->quote('NonEmptyValue');

        $this->assertEquals("'NonEmptyValue'", $quoted);
    }

    /**
     * Test, that the method 'quote' works with an already quoted value.
     */
    public function testQuoteWorksWithAlreadyQuotedValue()
    {
        $quoted = $this->database->quote("NonEmptyValue");
        $quoted = $this->database->quote($quoted);

        $this->assertEquals("'\'NonEmptyValue\''", $quoted);
    }

    /**
     * Test, that the method 'quoteArray' works with an empty array.
     */
    public function testQuoteArrayWithEmptyArray()
    {
        $originalArray = array();

        $quotedArray = $this->database->quoteArray($originalArray);

        $this->assertEquals($originalArray, $quotedArray);
    }

    /**
     * Test, that the method 'quoteArray' works with a non empty array.
     */
    public function testQuoteArrayWithFilledArray()
    {
        $originalArray = array('Hello', 'quoteThis');

        $quotedArray = $this->database->quoteArray($originalArray);

        $expectedQuotedArray = array("'Hello'", "'quoteThis'");

        $this->assertEquals($expectedQuotedArray, $quotedArray);
    }

    /**
     * @dataProvider dataProviderTestQuoteIdentifier
     *
     * @param $string
     * @param $expectedResult
     * @param $message
     */
    public function testQuoteIdentifier($string, $expectedResult, $message)
    {
        $actualResult = $this->database->quoteIdentifier($string);

        $this->assertSame($expectedResult, $actualResult, $message);
    }

    /**
     * This test is MySQL database specific, the identifier for other database platforms my be different.
     *
     * @return array
     */
    public function dataProviderTestQuoteIdentifier()
    {
        $identifierQuoteCharacter = '`';
        return [
            [
                'string to be quoted',
                $identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter,
                'A normal string will be quoted with "' . $identifierQuoteCharacter . '""'
            ],
            [
                $identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter,
                $identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter,
                'An already quoted string will be quoted with "' . $identifierQuoteCharacter . '"'
            ],
            [
                $identifierQuoteCharacter . $identifierQuoteCharacter .$identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter . $identifierQuoteCharacter . $identifierQuoteCharacter,
                $identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter,
                'An already quoted string will be quoted with "' . $identifierQuoteCharacter . '"'
            ],
            [
                $identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter . $identifierQuoteCharacter . $identifierQuoteCharacter,
                $identifierQuoteCharacter . 'string to be quoted' . $identifierQuoteCharacter,
                'An already quoted string will be quoted with "' . $identifierQuoteCharacter . '"'
            ],
            [
                $identifierQuoteCharacter . 'string to ' . $identifierQuoteCharacter . ' be quoted' . $identifierQuoteCharacter,
                $identifierQuoteCharacter . 'string to  be quoted' . $identifierQuoteCharacter,
                'An already quoted string will be quoted with "' . $identifierQuoteCharacter . '"'
            ],
            [
                '',
                $identifierQuoteCharacter . '' . $identifierQuoteCharacter,
                'An empty string will be quoted with "' . $identifierQuoteCharacter . '"'
            ],
            [
                null,
                $identifierQuoteCharacter . '' . $identifierQuoteCharacter,
                'An empty string will be quoted as an empty string with "' . $identifierQuoteCharacter . '"'
            ],
        ];
    }
}
