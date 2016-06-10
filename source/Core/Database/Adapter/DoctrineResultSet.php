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

/**
 * The doctrine statement wrapper, to support the old adodblite interface.
 *
 * @package OxidEsales\Eshop\Core\Database\Adapter
 */
class DoctrineResultSet
{

    /**
     * @var Statement The doctrine adapted statement.
     */
    protected $statement;

    /**
     * @var array Holds the retrieved fields of the resultSet row on the current cursor position.
     */
    public $fields;

    /**
     * @var int The current cursor position.
     */
    private $currentRow = 0;

    /**
     * @var bool Did we reach the end of the results?
     */
    public $EOF;

    /**
     * DoctrineResultSet constructor.
     *
     * @param Statement $statement The statement we want to wrap in this class.
     */
    public function __construct(Statement $statement)
    {
        $this->fields = array();
        $this->setStatement($statement);
        $this->EOF = false;
        $this->currentRow = 0;

        if( $this->recordCount() == 0) {
            $this->setToEmptyState();
        }

        $this->fetchRow();
    }

    /**
     * Returns fields array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Close the pointer to the database connection.
     */
    public function close()
    {
        $this->getStatement()->closeCursor();
        $this->fields = array();
       // $this->statement = false;
    }

    /**
     * Fetches the next row from a result set and fills the fields array.
     *
     * @return mixed The return value of this function on success depends on the fetch type.
     *               In all cases, FALSE is returned on failure.
     */
    public function fetchRow()
    {
        $this->fields = $this->getStatement()->fetch();
        return $this->fields;
    }

    /**
     * Get a specific column value.
     *
     * @param string|int $columnKey The key of the wished column.
     *
     * @return null|boolean|string|array The column value (string or array). If the result set is empty or the last row is reached, we give back false. If the column name is not present, we give back null.
     */
    public function fields($columnKey)
    {
        if(empty($columnKey)) {
            return $this->getFields();
        } else {
            return $this->fields[$columnKey];
        }
    }

    /**
     * Returns the number of rows affected by the last execution of this statement.
     *
     * @return int The number of affected rows
     */
    public function recordCount()
    {
        return $this->getStatement()->rowCount();
    }

    /**
     * Returns the number of columns in the result set.
     *
     * @return int The number of columns.
     */
    public function fieldCount()
    {
        return $this->getStatement()->columnCount();
    }

    /**
     * Load the next row from the database.
     *
     * @return bool Is there another row?
     */
    public function moveNext()
    {
        if (@$this->fields = $this->getStatement()->fetch()) {
            $this->currentRow += 1;
            return true;
        }
        if (!$this->EOF) {
            $this->currentRow += 1;
            $this->EOF = true;
        }
        return false;
    }

    /**
     * Check, if we already reached the end of the results.
     *
     * @return bool Is the end of the result set reached?
     */
    public function EOF()
    {
        if( $this->currentRow < $this->recordCount()) {
            return false;
        } else {
            $this->EOF = true;
            return true;
        }
    }

    /**
     * Get the given number of rows, from the current row pointer on, as an array.
     *
     * @param int $numberOfRows The number of rows to fetch.
     *
     * @return array The rows of the corresponding statement, starting at the current row pointer.
     */
    public function getArray($numberOfRows)
    {
        $results = array();
        $cnt = 0;
        while (!$this->EOF && $numberOfRows != $cnt) {
            $results[] = $this->fields;
            $this->moveNext();
            $cnt++;
        }
        return $results;
    }

    /**
     * Get the given number of rows from the current row pointer on.
     *
     * @param int $numberOfRows The number of rows to fetch.
     *
     * @return array The rows of the corresponding statement, starting at the current row pointer.
     */
    public function getRows($numberOfRows)
    {
        $arr = $this->getArray($numberOfRows);
        return $arr;
    }

    /**
     * Fetch all rows from the corresponding statement.
     *
     * @param int $nRows The number of rows to fetch.
     *
     * @return array The complete result set as an array of associative arrays.
     */
    public function getAll($nRows = -1)
    {
        $arr = $this->getArray($nRows);
        return $arr;
    }

    /**
     * Get information about the column, specified by the given index.
     *
     * @param int $columnIndex The index of the column of this result set.
     *
     * @return \stdClass An object, filled with the column information.
     */
    public function fetchField($columnIndex)
    {
        /** @todo The method getColumnMeta is specific of the PDO driver. Change to unspecific version, if not exits be creative ;-) */
        $metaInformation = $this->getStatement()->getColumnMeta($columnIndex);

        $result = new \stdClass();
        $result->name = $metaInformation['name'];
        $result->table = $metaInformation['table'];
        $result->max_length = $metaInformation['len'];
        $result->not_null = (int) in_array('not_null', $metaInformation['flags']);
        $result->primary_key = (int) in_array('primary_key', $metaInformation['flags']);
        $result->type = strtolower($metaInformation['native_type']);

        return $result;
    }

    /**
     * Getter for the adapted statement.
     *
     * @return Statement The adapted statement.
     */
    protected function getStatement()
    {
        return $this->statement;
    }

    /**
     * Setter for the adapted statement.
     *
     * @param Statement $statement The adapted statement.
     */
    protected function setStatement(Statement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Set the state of this wrapper to 'empty'.
     */
    private function setToEmptyState()
    {
        /** The following properties change the value for an  empty result set */
        $this->EOF = true;
    }

}
