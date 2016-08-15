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

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Database\Doctrine;

/**
 * Tests for our database object.
 */
class DoctrineTest extends UnitTestCase
{

    /**
     * @var Doctrine The doctrine database we want to test in this class.
     */
    protected $database = null;

    public function setUp()
    {
        parent::setUp();

        $this->database = new Doctrine();
    }

    /**
     * Test, that the creation of the database connection works.
     */
    public function testCreation()
    {
        new Doctrine();
    }

    /**
     * Test, that the method 'execute' works for insert and delete.
     */
    public function testExecuteWithInsertAndDelete()
    {
        $this->assureOrderFileIsEmpty();

        $exampleOxId = 'XYZ';

        $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ('$exampleOxId');");

        $this->assertEquals(1, $this->database->affected_rows());
        $this->assureOrderFileHasOnly($exampleOxId);

        $this->database->execute("DELETE FROM oxorderfiles WHERE OXID = '$exampleOxId';");

        $this->assertEquals(1, $this->database->affected_rows());
        $this->assureOrderFileIsEmpty();
    }

    /**
     * Assure, that the table oxorderfiles is empty.
     */
    private function assureOrderFileIsEmpty()
    {
        $orderFileIds = $this->fetchOrderFilesOxIds();

        $this->assertEmpty($orderFileIds);
    }

    /**
     * Assure, that the table oxorderfiles has only the given oxId.
     */
    private function assureOrderFileHasOnly($oxId)
    {
        $orderFileIds = $this->fetchOrderFilesOxIds();

        $this->assertNotEmpty($orderFileIds);
        $this->assertEquals(1, count($orderFileIds));
        $this->assertArrayHasKey('0', $orderFileIds);
        $this->assertArrayHasKey('OXID', $orderFileIds[0]);

        $this->assertEquals($oxId, $orderFileIds[0]['OXID']);
    }

    /**
     * Fetch the oxIds from the table oxorderfiles.
     *
     * @return array The oxIds of the table oxorderfiles.
     */
    private function fetchOrderFilesOxIds()
    {
        return $this->database->select('SELECT OXID FROM oxorderfiles;')->getAll();
    }

}
