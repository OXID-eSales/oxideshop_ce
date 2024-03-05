<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;
use oxException;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use PDO;
use stdClass;

/**
 * The doctrine implementation of our database.
 *
 * @deprecated since v6.5.0 (2019-09-24);
 *             Use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface
 */
class Database implements DatabaseInterface
{
    private const MYSQL_DUPLICATE_KEY_ERROR_CODE = 1062;

    protected $connectionParameters = [];

    /**
     * @var DriverConnection The database connection.
     */
    protected $connection = null;

    /**
     * @var int The current fetch mode.
     */
    protected $fetchMode = PDO::FETCH_NUM;

    /**
     * @var array Map strings used in the shop to Doctrine constants
     */
    protected $transactionIsolationLevelMap = [
        'READ UNCOMMITTED' => Connection::TRANSACTION_READ_UNCOMMITTED,
        'READ COMMITTED' => Connection::TRANSACTION_READ_COMMITTED,
        'REPEATABLE READ' => Connection::TRANSACTION_REPEATABLE_READ,
        'SERIALIZABLE' => Connection::TRANSACTION_SERIALIZABLE
    ];

    /**
     * @var array Map fetch modes used in the shop to doctrine constants
     */
    protected $fetchModeMap = [
        DatabaseInterface::FETCH_MODE_DEFAULT => PDO::FETCH_BOTH,
        DatabaseInterface::FETCH_MODE_NUM => PDO::FETCH_NUM,
        DatabaseInterface::FETCH_MODE_ASSOC => PDO::FETCH_ASSOC,
        DatabaseInterface::FETCH_MODE_BOTH => PDO::FETCH_BOTH
    ];

    public function setConnectionParameters(array $connectionParameters)
    {
        if (array_key_exists('default', $connectionParameters)) {
            $this->connectionParameters = $connectionParameters['default'];
        }
    }

    /**
     * @inheritDoc
     */
    public function connect()
    {
        try {
            $connection = ContainerFacade::get(ConnectionFactoryInterface::class)->create();
            $connection->connect();

            $this->setConnection($connection);

            $this->ensureConnectionIsEstablished($connection);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function forceMasterConnection()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
    }

    /**
     * @inheritdoc
     */
    public function forceSlaveConnection()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
    }

    /**
     * @inheritdoc
     */
    public function closeConnection()
    {
        $this->connection->close();
        gc_collect_cycles();
    }

    /**
     * @param Connection $connection
     */
    protected function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     * @param integer $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     */
    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $this->fetchModeMap[$fetchMode];

        try {
            $this->getConnection()->setFetchMode($this->fetchMode);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function getOne($query, $parameters = [])
    {
        if ($this->doesStatementProduceOutput($query)) {
            try {
                return $this->getConnection()->fetchColumn($query, $parameters);
            } catch (DBALException $exception) {
                $exception = $this->convertException($exception);
                $this->handleException($exception);
            } catch (PDOException $exception) {
                $exception = $this->convertException($exception);
                $this->handleException($exception);
            }
        } else {
            Registry::getLogger()->warning(
                'Given statement does not produce output and was not executed',
                [debug_backtrace()]
            );
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRow($query, $parameters = [])
    {
        try {
            $resultSet = $this->select($query, $parameters);
            $result = $resultSet->fields;
        } catch (DatabaseErrorException $exception) {
            /** Only log exception, do not re-throw here, as legacy code expects this behavior */
            $this->logException($exception);
            $result = [];
        } catch (PDOException $exception) {
            /** Only log exception, do not re-throw here, as legacy code expects this behavior */
            $exception = $this->convertException($exception);
            $this->logException($exception);
            $result = [];
        }

        if (false == $result) {
            $result = [];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function quoteIdentifier($string)
    {
        $identifierQuoteCharacter = $this->getConnection()->getDatabasePlatform()->getIdentifierQuoteCharacter();

        if (!$identifierQuoteCharacter) {
            $identifierQuoteCharacter = '`';
        }

        $string = trim(str_replace($identifierQuoteCharacter, '', $string));
        try {
            $result = $this->getConnection()->quoteIdentifier($string);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function quote($value)
    {
        try {
            $result = $this->getConnection()->quote($value);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function quoteArray($array)
    {
        $result = [];

        foreach ($array as $key => $item) {
            $result[$key] = $this->quote($item);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function startTransaction()
    {
        try {
            $this->getConnection()->beginTransaction();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction()
    {
        try {
            $this->getConnection()->commit();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function rollbackTransaction()
    {
        try {
            $this->getConnection()->rollBack();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @param string $level The transaction isolation level
     *
     * @return bool|int
     * @throws InvalidArgumentException|DatabaseErrorException
     *
     * @see Doctrine::transactionIsolationLevelMap
     *
     */
    public function setTransactionIsolationLevel($level)
    {
        $level = strtoupper($level);

        if (!array_key_exists($level, $this->transactionIsolationLevelMap)) {
            throw new InvalidArgumentException('Transaction isolation level is invalid');
        }

        return $this->getConnection()->setTransactionIsolation($this->transactionIsolationLevelMap[$level]);
    }

    /**
     * @inheritDoc
     *
     */
    public function execute($query, $parameters = [])
    {
        return $this->executeUpdate($query, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function select($query, $parameters = [])
    {
        $result = null;

        $this->checkIfSqlIsReadOnly($query);
        try {
            /**
             * Be aware that Connection::executeQuery is a method specifically for READ operations only.
             * This is especially important in master-slave Connection
             */
            /** @var Statement $statement Statement is prepared and executed by executeQuery() */
            $statement = $this->getConnection()->executeQuery(
                $this->checkForMultipleQueries($query, $parameters),
                $parameters
            );

            $result = new \OxidEsales\Eshop\Core\Database\Adapter\Doctrine\ResultSet($statement);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function checkIfSqlIsReadOnly($query): void
    {
        $check = ltrim($query, " \t\n\r\0\x0B(");
        if (!(stripos($check, 'select') === 0 || stripos($check, 'show') === 0)) {
            throw new InvalidArgumentException("Function is only for read operations select or show");
        }
    }

    private function checkForMultipleQueries($query, $parameters): string
    {
        if ($parameters !== [] || strrpos($query, ';', -1) === false) {
            return $query;
        }
        $queries = preg_split('~(\"[^\\\\"]*\"|' . "\'[^\\\\']*\'|\'.+\'|`[^\\`]*`)(*SKIP)(*F)|(?<=;)(?![ ]*$)~", $query);
        if (count($queries) > 1) {
            Registry::getLogger()->error('More than one query within one statement', [$query]);
        }

        return $queries[0];
    }

    /**
     * @inheritDoc
     */
    public function selectLimit($query, $rowCount = -1, $offset = 0, $parameters = [])
    {
        /**
         * Parameter validation.
         * At the moment there will be no InvalidArgumentException thrown on non numeric values as this may break
         * too many things.
         */
        if (!is_numeric($rowCount) || !is_numeric($offset)) {
            trigger_error(
                'Parameters rowCount and offset have to be numeric in DatabaseInterface::selectLimit(). ' .
                'Please fix your code as this error may trigger an exception in future versions of OXID eShop.',
                E_USER_DEPRECATED
            );
        }

        if (0 > $offset) {
            throw new InvalidArgumentException('Argument $offset must not be smaller than zero.');
        }

        /**
         * Cast the parameters limit and offset to integer in in order to avoid SQL injection.
         */
        $rowCount = (int)$rowCount;
        $offset = (int)$offset;
        $limitClause = '';

        if ($rowCount >= 0 && $offset >= 0) {
            $limitClause = "LIMIT $rowCount OFFSET $offset";
        }

        return $this->select($query . " $limitClause ", $parameters);
    }

    /**
     * @inheritDoc
     */
    public function getCol($query, $parameters = [])
    {
        $this->checkIfSqlIsReadOnly($query);
        $result = [];

        try {
            $rows = $this->getConnection()->fetchAll($query, $parameters);
            foreach ($rows as $row) {
                // cause there is no doctrine equivalent, we take this little detour and restructure the result
                $columnNames = array_keys($row);
                $columnName = $columnNames[0];

                $result[] = $row[$columnName];
            }
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * Execute non read statements like INSERT, UPDATE, DELETE and return the number of rows affected by the statement.
     * This method has to be used EXCLUSIVELY for non read statements.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements to prevent SQL injection vulnerability.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string $query The SQL query.
     * @param array $parameters The query parameters.
     * @param array $types The parameter types.
     *
     * @return integer The number of affected rows.
     * @throws DatabaseErrorException
     *
     */
    public function executeUpdate($query, $parameters = [], $types = [])
    {
        $affectedRows = 0;

        try {
            $affectedRows = $this->getConnection()->executeUpdate($query, $parameters, $types);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $affectedRows;
    }

    /**
     * Get the database connection.
     *
     * @return DriverConnection $oConnection The database connection we want to use.
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @deprecated
     * @internal
     */
    public function getPublicConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $query
     *
     * @return bool
     */
    private function doesStatementProduceOutput($query)
    {
        return in_array(
            $this->getFirstCommandInStatement($query),
            [
                'SELECT',
                'EXECUTE',
                'GET',
                'SHOW',
                'CHECKSUM',
                'DESCRIBE',
                'EXPLAIN',
                'HELP',
            ]
        );
    }

    /**
     * Convert a given native Doctrine exception into an OxidEsales exception.
     * Note: This method is MySQL specific, as the MySQL error codes instead of SQLSTATE are used.
     *
     * @param \Exception $exception Doctrine exception to be converted
     *
     * @return StandardException Exception converted into an instance of StandardException
     */
    protected function convertException(\Exception $exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $exceptionClass = DatabaseErrorException::class;

        switch (true) {
            case $exception instanceof Exception\ConnectionException:
                // ConnectionException will be mapped to DatabaseConnectionException::class
            case $exception instanceof ConnectionException:
                /**
                 * Doctrine does not recognise "SQLSTATE[HY000] [2003] Can't connect to MySQL server on 'mysql.example'"
                 * as a connection error, as the error code 2003 is simply not treated in
                 * Doctrine\DBAL\Driver\AbstractMySQLDriver::convertException.
                 * We fix this here.
                 */
                // ConnectionException will be mapped to DatabaseConnectionException::class
                // no break
            case is_a($exception->getPrevious(), '\Exception') && in_array($exception->getPrevious()->getCode(), ['2003']):
                $exceptionClass = DatabaseConnectionException::class;
                break;
            case $exception instanceof DBALException:
                /**
                 * Doctrine passes the message and the code of the PDO Exception, which would break backward
                 * compatibility as it uses SQLSTATE error code (string), but the shop used to the (My)SQL errors (integer)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately we can access PDOException and recover the original SQL error code and message.
                 */
                /** @var $pdoException PDOException */
                $pdoException = $exception->getPrevious();

                if ($pdoException instanceof PDOException) {
                    $code = $this->convertErrorCode($pdoException->errorInfo[1]);
                    $message = $pdoException->errorInfo[2];
                }

                break;
            case $exception instanceof PDOException:
                /**
                 * The shop uses the (My)SQL errors (integer) in the error code, but $pdoException uses SQLSTATE error code (string)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately in some cases we can access PDOException and recover the original SQL error code and message.
                 */
                $code = $this->convertErrorCode($exception->errorInfo[1]);
                $message = $exception->errorInfo[2];

                /** In case the original code (int) cannot be recovered, code is set to 0 */
                if (!is_integer($code)) {
                    $code = 0;
                }

                break;
        }

        /** @var oxException $convertedException */
        $convertedException = new $exceptionClass($message, $code, $exception);

        return $convertedException;
    }

    /**
     * Handle a given exception. The standard behavior at the moment is to throw the exception passed in the parameter.
     * A second exception handling including logging will be done by the ShopControl class.
     *
     * @param StandardException $exception
     *
     * @throws StandardException
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    protected function handleException(StandardException $exception)
    {
        throw $exception;
    }

    /**
     * Log a given Exception the log file using the standard eShop logging mechanism.
     * Use this function whenever a exception is caught and not re-thrown.
     *
     * @param \Exception $exception
     */
    protected function logException(\Exception $exception)
    {
        /** The exception has to be converted into an instance of oxException in order to be logged like this */
        $exception = $this->convertException($exception);
        Registry::getLogger()->error($exception->getMessage(), [$exception]);
    }

    /**
     * @inheritDoc
     */
    public function getAll($query, $parameters = [])
    {
        $result = [];
        $statement = null;
        try {
            $statement = $this->getConnection()->executeQuery($query, $parameters);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        if ($this->doesStatementProduceOutput($query)) {
            $result = $statement->fetchAll();
        } else {
            Registry::getLogger()->warning('Given statement does not produce output and was not executed', [debug_backtrace()]);
        }

        return $result;
    }

    /**
     * Return string representing the row ID of the last row that was inserted into
     * the database.
     * Returns 0 for tables without autoincrement field.
     *
     * @return string|int Row ID
     */
    public function getLastInsertId()
    {
        try {
            $lastInsertId = $this->getConnection()->lastInsertId();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $lastInsertId;
    }

    /**
     * @inheritDoc
     */
    public function metaColumns($table)
    {
        $connection = $this->getConnection();
        $databaseName = $connection->getDatabase();
        $query = "SELECT
              COLUMN_NAME AS `Field`,
              COLUMN_TYPE AS `Type`,
              IS_NULLABLE AS `Null`,
              COLUMN_KEY AS `Key`,
              COLUMN_DEFAULT AS `Default`,
              EXTRA AS `Extra`,
              COLUMN_COMMENT AS `Comment`,
              CHARACTER_SET_NAME AS `CharacterSet`,
              COLLATION_NAME AS `Collation`
            FROM information_schema.COLUMNS
            WHERE
              TABLE_SCHEMA = '$databaseName'
              AND
              TABLE_NAME = '$table'
            ORDER BY ORDINAL_POSITION ASC";

        try {
            $columns = $connection->executeQuery($query)->fetchAll();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        /** Depending on the fetch mode we may find numeric or string key in the array $rawColumns */
        $result = [];

        foreach ($columns as $column) {
            $type = $this->getMetaColumnValueByKey($column, 'Type');
            $field = $this->getMetaColumnValueByKey($column, 'Field');
            $null = $this->getMetaColumnValueByKey($column, 'Null');
            $key = $this->getMetaColumnValueByKey($column, 'Key');
            $default = $this->getMetaColumnValueByKey($column, 'Default');
            $extra = $this->getMetaColumnValueByKey($column, 'Extra');
            $comment = $this->getMetaColumnValueByKey($column, 'Comment');
            $characterSet = $this->getMetaColumnValueByKey($column, 'CharacterSet');
            $collation = $this->getMetaColumnValueByKey($column, 'Collation');

            if ($default !== null) {
                // MariaDB puts quotes around default values:
                $default = trim($default, "'");
            }

            $typeInformation = explode('(', $type);
            $typeName = trim($typeInformation[0]);

            $item = new stdClass();
            $item->name = $field;
            $item->type = $typeName;
            $item->not_null = ('no' === strtolower($null));
            $item->primary_key = (strtolower($key) == 'pri');
            $item->auto_increment = strtolower($extra) == 'auto_increment';
            $item->binary = (false !== strpos(strtolower($type), 'blob'));
            $item->unsigned = (false !== strpos(strtolower($type), 'unsigned'));
            $item->has_default = ((is_null($default)) || ($default === '')) ? false : true;
            if ($item->has_default) {
                $item->default_value = $default;
            }

            /**
             * These variables were set only when there was a value in the previous implementation with ADOdb Lite.
             * We do it the same way here for compatibility.
             */
            list($max_length, $scale) = $this->getColumnMaxLengthAndScale($column, $item->type);
            if (-1 !== $max_length) {
                $item->max_length = (string)$max_length;
            } else {
                $item->max_length = $max_length;
            }
            if (-1 !== $scale) {
                $item->scale = (string)$scale;
            } else {
                $item->scale = null;
            }

            /** Unset has_default and default_value for binary types */
            if ($item->binary) {
                unset($item->has_default, $item->default_value);
            }

            /** Additional properties not found in ADODB lite */
            $item->comment = $comment;
            $item->characterSet = $characterSet;
            $item->collation = $collation;

            /**
             * ADODB lite properties not implemented
             *
             * @todo: implement the enums property for SET and ENUM fields
             */
            // $item->enums

            if (array_key_exists('Field', $column)) {
                $result[$item->name] = $item;
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isRollbackOnly()
    {
        try {
            $isRollbackOnly = $this->connection->isRollbackOnly();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $isRollbackOnly;
    }

    /**
     * @inheritDoc
     */
    public function isTransactionActive()
    {
        try {
            $isTransactionActive = $this->connection->isTransactionActive();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $isTransactionActive;
    }

    /**
     * @param array $column The meta column, where the value has to be fetched.
     * @param string $key The key to fetch.
     *
     * @return mixed
     */
    protected function getMetaColumnValueByKey(array $column, $key)
    {
        if (array_key_exists('Field', $column)) {
            $keyMap = [
                'Field' => 'Field',
                'Type' => 'Type',
                'Null' => 'Null',
                'Key' => 'Key',
                'Default' => 'Default',
                'Extra' => 'Extra',
                'Comment' => 'Comment',
                'CharacterSet' => 'CharacterSet',
                'Collation' => 'Collation',
            ];
        } else {
            $keyMap = [
                'Field' => 0,
                'Type' => 1,
                'Null' => 2,
                'Key' => 3,
                'Default' => 4,
                'Extra' => 5,
                'Comment' => 6,
                'CharacterSet' => 7,
                'Collation' => 8,
            ];
        }

        $result = $column[$keyMap[$key]];

        return $result;
    }


    /**
     * Get the maximal length of a given column of a given type.
     *
     * @param array $column The meta column for which the may length has to be found.
     * @param string $assignedType The type of the column.
     *
     * @return int[] The maximal length and the scale (in case of DECIMAL type).
     *               Both variables are -1 in case of no value can be found.
     */
    protected function getColumnMaxLengthAndScale(array $column, $assignedType)
    {
        /** @var int $maxLength The max length of a field. For floating point type or fixed point type fields the precision of the field */
        $maxLength = -1;
        /** @var int $scale The scale of floating point type or fixed point type fields */
        $scale = -1;

        /** @var string $mySqlType E.g. "CHAR(4)" or "DECIMAL(5,2)" or "tinyint(1) unsigned" */
        $mySqlType = $this->getMetaColumnValueByKey($column, 'Type');
        /** Get the maximum display width for the type */

        /** Match Precision an scale E.g DECIMAL(5,2) */
        if (preg_match("/^(.+)\((\d+),(\d+)/", $mySqlType, $matches)) {
            if (is_numeric($matches[2])) {
                $maxLength = $matches[2];
            }
            if (is_numeric($matches[3])) {
                $scale = $matches[3];
            }
            /** Match max length E.g CHAR(4) */
        } elseif (preg_match("/^(.+)\((\d+)/", $mySqlType, $matches)) {
            if (is_numeric($matches[2])) {
                $maxLength = $matches[2];
            }
            /**
             * Match List type E.g. SET('A', 'B', 'CDE)
             * In this case the length will be the string length of the longest element
             */
        } elseif (preg_match("/^(enum|set)\((.*)\)$/i", strtolower($mySqlType), $matches)) {
            if ($matches[2]) {
                $pieces = explode(",", $matches[2]);
                /** The array values contain 2 quotes, so we have to subtract 2 from the strlen */
                $maxLength = max(array_map("strlen", $pieces)) - 2;
                if ($maxLength <= 0) {
                    $maxLength = 1;
                }
            }
        }

        /** Numeric types, which may have a maximum length */
        $integerTypes = ['INTEGER', 'INT', 'SMALLINT', 'TINYINT', 'MEDIUMINT', 'BIGINT'];
        $fixedPointTypes = ['DECIMAL', 'NUMERIC'];
        $floatingPointTypes = ['FLOAT', 'DOUBLE'];

        /** Text types, which may have a maximum length */
        $textTypes = ['CHAR', 'VARCHAR'];

        /** Date types, which may have a maximum length */
        $dateTypes = ['YEAR'];

        $assignedType = strtoupper($assignedType);
        if (
            (in_array($assignedType, $integerTypes) ||
                in_array($assignedType, $fixedPointTypes) ||
                in_array($assignedType, $floatingPointTypes) ||
                in_array($assignedType, $textTypes) ||
                in_array($assignedType, $dateTypes)) && -1 == $maxLength
        ) {
            /**
             * @todo: If the assigned type is one of the following and maxLength is -1, then, if applicable the default max length ot that type should be assigned.
             */
        }

        return [(int)$maxLength, (int)$scale];
    }

    /**
     * @param string $query The query to extract the command from
     *
     * @return string
     */
    protected function getFirstCommandInStatement($query)
    {
        $singleLineQuery = str_replace(["\r", "\n"], ' ', $query);
        $sqlComments = '@(([\'"]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms';
        $uncommentedQuery = preg_replace($sqlComments, '$1', $singleLineQuery);

        return strtoupper(
            trim(
                explode(' ', trim($uncommentedQuery))[0]
            )
        );
    }

    /**
     * @param Connection $connection
     *
     * @throws \Exception If we are not connected correctly to the database.
     */
    protected function ensureConnectionIsEstablished($connection)
    {
        if (!$this->isConnectionEstablished($connection)) {
            $message = $this->createConnectionErrorMessage($connection);

            throw new ConnectionException($message);
        }
    }

    /**
     * @param Connection $connection
     *
     * @return bool
     */
    protected function isConnectionEstablished($connection)
    {
        return $connection->isConnected();
    }

    /**
     * @param Connection $connection The connection.
     *
     * @return string
     */
    protected function createConnectionErrorMessage($connection)
    {
        return sprintf(
            "Not connected to database. dsn: %s://****:****@%s:%s/%s",
            $connection->getDriver()->getName(),
            $connection->getHost(),
            $connection->getPort(),
            $connection->getDatabase()
        );
    }

    /**
     * @param int $code
     *
     * @return string
     */
    private function convertErrorCode($code)
    {
        return $code === self::MYSQL_DUPLICATE_KEY_ERROR_CODE
            ? self::DUPLICATE_KEY_ERROR_CODE
            : $code;
    }
}
