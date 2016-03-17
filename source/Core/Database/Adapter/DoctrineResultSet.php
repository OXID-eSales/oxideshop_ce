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

use Doctrine\DBAL\Statement;

class DoctrineResultSet
{

    /**
     * @var Statement The doctrine adapted statement.
     */
    protected $adapted = null;

    /**
     * @var bool Did we reach the end of the results?
     */
    public $EOF = true;

    public $fields = array();

    /**
     * DoctrineResultSet constructor.
     *
     * @param $adapted
     */
    public function __construct($adapted)
    {
        $this->setAdapted($adapted);
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
        return $this->getAdapted()->fetchAll();
    }

    /**
     * @todo: implement and test
     *
     * @return mixed
     */
    public function moveNext()
    {
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
    public function EOF()
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
     * @return Statement The adapted statement.
     */
    protected function getAdapted()
    {
        return $this->adapted;
    }

    /**
     * Setter for the adapted statement.
     *
     * @param Statement $adapted The adapted statement.
     */
    protected function setAdapted($adapted)
    {
        $this->adapted = $adapted;
    }
}