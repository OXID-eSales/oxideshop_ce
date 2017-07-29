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

namespace OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine;

use Doctrine\DBAL\Driver\Statement;
use OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface;

/**
 * The doctrine statement wrapper, to support the old adodblite interface.
 *
 * @package OxidEsales\EshopCommunity\Core\Database\Adapter
 */
class ResultSet implements \IteratorAggregate, ResultSetInterface
{

    /**
     * @var array Holds the retrieved fields of the resultSet row on the current cursor position.
     */
    public $fields;

    /**
     * @var bool Did we reach the end of the results?
     */
    public $EOF;

    /**
     * @var Statement The doctrine adapted statement.
     */
    protected $statement;

    /**
     * @var int The current cursor position.
     */
    private $currentRow = 0;

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

        if ($this->count() == 0) {
            $this->setToEmptyState();
        }

        $this->fetchRow();
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $this->getStatement()->closeCursor();
        $this->fields = array();
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

        if (false === $this->fields) {
            $this->EOF = true;
        }

        return $this->fields;
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * @return array
     */
    public function fetchAll()
    {
        $this->close();
        $this->getStatement()->execute();

        return $this->getStatement()->fetchAll();
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
     * Returns an external iterator.
     *
     * @return Statement The Statment class implements Traversable
     */
    public function getIterator()
    {
        $this->close();
        $this->getStatement()->execute();

        return $this->getStatement();
    }

    /**
     * Returns fields array
     *
     * @return array containing the retrieved fields of the resultSet row
     */
    public function getFields()
    {
        return $this->fields;
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
    protected function setToEmptyState()
    {
        /** The following properties change the value for an  empty result set */
        $this->EOF = true;
    }

    /**
     * Count elements of an object
     * This method is executed when using the count() function on an object implementing Countable.
     *
     *  @return int The number of rows retrieved by the current statement.
     */
    public function count()
    {
        return $this->getStatement()->rowCount();
    }
}
