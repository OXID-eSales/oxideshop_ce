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
namespace OxidEsales\Eshop\Core\Database\Adapter;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Driver\PDOStatement;
use PDO;

class DoctrineResultSet
{

    /**
     * @var bool Did we reach the end of the results?
     */
    public $EOF = true;

    public $fields = array();

    /**
     * @var PDOStatement The doctrine adapted statement.
     */
    protected $adapted = null;

    /**
     * @var bool Was the first element already fetched?
     */
    private $fetchedFirst = false;

    /**
     * DoctrineResultSet constructor.
     *
     * @param Statement $adapted The statement we want to wrap in this class.
     */
    public function __construct(Statement $adapted)
    {
        $this->setAdapted($adapted);

        if (0 < $this->getAdapted()->rowCount()) {
            $this->EOF = false;

            $this->fields = $this->getAdapted()->fetch();

            $this->executeAdapted();
        } else {
            // @todo: double check, if this path or the DoctrineEmptyResultSet could be removed
            $this->EOF = true;
            $this->fields = false;
        }
    }

    /**
     * Fetch the next row from the database. If there is no next row, it gives back false.
     *
     * @return false|array The next row.
     */
    public function fetchRow()
    {
        return $this->getAdapted()->fetch();
    }

    /**
     * Count the result rows of the corresponding statement.
     *
     * @return int How many rows are included in the result?
     */
    public function recordCount()
    {
        return $this->getAdapted()->rowCount();
    }

    /**
     * Fetch all rows from the corresponding statement.
     *
     * @return array The complete result set as an array of associative arrays.
     */
    public function getAll()
    {
        $this->getAdapted()->execute();

        return $this->getAdapted()->fetchAll();
    }

    /**
     * Check, if we already reached the end of the results.
     *
     * @return bool Is the end of the result set reached?
     */
    public function EOF()
    {
        return $this->EOF;
    }

    /**
     * @todo: implement and test
     */
    public function Close()
    {
    }

    /**
     * @todo: implement and test
     */
    public function FetchField($fieldOffset)
    {
    }

    /**
     * @todo: implement and test
     */
    public function FieldCount()
    {
    }

    /**
     * @todo: implement and test
     */
    public function Fields($column)
    {
    }

    /**
     * @todo: implement and test
     */
    public function GetArray($nRows)
    {
    }

    /**
     * @todo: implement and test
     */
    public function GetRows($nRows)
    {
    }

    /**
     * Load the next row from the database.
     *
     * @return bool Is there another row?
     */
    public function MoveNext()
    {
        if ($this->EOF()) {
            return false;
        }

        if (!$this->isFetchedFirst()) {
            $this->fetchRowIntoFields();
        }
        $this->fetchRowIntoFields();

        if (!$this->fields) {
            $this->EOF = true;

            return false;
        }

        return true;
    }

    /**
     * @todo: implement and test
     */
    public function Move($row)
    {
    }

    /**
     * @todo: implement and test
     */
    public function MoveFirst()
    {
    }

    /**
     * @todo: implement and test
     */
    public function MoveLast()
    {
    }

    /**
     * Getter for the adapted statement.
     *
     * @return PDOStatement The adapted statement.
     */
    protected function getAdapted()
    {
        return $this->adapted;
    }

    /**
     * Setter for the adapted statement.
     *
     * @param PDOStatement $adapted The adapted statement.
     */
    protected function setAdapted($adapted)
    {
        $this->adapted = $adapted;
    }

    /**
     * Getter for the fetched first flag.
     *
     * @return boolean Is the first row of the adapted statement already fetched?
     */
    private function isFetchedFirst()
    {
        return $this->fetchedFirst;
    }

    /**
     * Setter for the fetched first flag.
     *
     * @param boolean $fetchedFirst Is the first row of the adapted statement already fetched?
     */
    private function setFetchedFirst($fetchedFirst)
    {
        $this->fetchedFirst = $fetchedFirst;
    }

    /**
     * (Re-)execute the adapted statement.
     */
    private function executeAdapted()
    {
        $this->getAdapted()->execute();
    }

    /**
     * Fetch the next row into the fields attribute.
     */
    private function fetchRowIntoFields()
    {
        $this->fields = $this->getAdapted()->fetch();

        $this->setFetchedFirst(true);
    }
}