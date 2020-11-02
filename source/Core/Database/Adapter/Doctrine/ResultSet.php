<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine;

use Doctrine\DBAL\Driver\Statement;
use OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface;

/**
 * The doctrine statement wrapper, to support the old adodblite interface.
 *
 * @deprecated since v6.5.0 (2019-09-24); Use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface
 */
class ResultSet implements \IteratorAggregate, ResultSetInterface
{
    /**
     * @var array holds the retrieved fields of the resultSet row on the current cursor position
     */
    public $fields;

    /**
     * @var bool Did we reach the end of the results?
     */
    public $EOF;

    /**
     * @var Statement the doctrine adapted statement
     */
    protected $statement;

    /**
     * @var int the current cursor position
     */
    private $currentRow = 0;

    /**
     * DoctrineResultSet constructor.
     *
     * @param Statement $statement the statement we want to wrap in this class
     */
    public function __construct(Statement $statement)
    {
        $this->fields = [];
        $this->setStatement($statement);
        $this->EOF = false;
        $this->currentRow = 0;

        if (0 === $this->count()) {
            $this->setToEmptyState();
        }

        $this->fetchRow();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        $this->getStatement()->closeCursor();
        $this->fields = [];
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
     * Returns an array containing all of the result set rows.
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
     * @return int the number of columns
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
     * Returns fields array.
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
     * @return Statement the adapted statement
     */
    protected function getStatement()
    {
        return $this->statement;
    }

    /**
     * Setter for the adapted statement.
     *
     * @param Statement $statement the adapted statement
     */
    protected function setStatement(Statement $statement): void
    {
        $this->statement = $statement;
    }

    /**
     * Set the state of this wrapper to 'empty'.
     */
    protected function setToEmptyState(): void
    {
        /* The following properties change the value for an  empty result set */
        $this->EOF = true;
    }

    /**
     * Count elements of an object
     * This method is executed when using the count() function on an object implementing Countable.
     *
     *  @return int the number of rows retrieved by the current statement
     */
    public function count()
    {
        return $this->getStatement()->rowCount();
    }
}
