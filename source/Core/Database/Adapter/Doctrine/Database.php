<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine;

use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\TransactionIsolationLevel;
use InvalidArgumentException;
use oxException;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use PDOException;
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

    protected $connection = null;

    /**
     * @var array Map strings used in the shop to Doctrine constants
     */
    protected $transactionIsolationLevelMap = [
        'READ UNCOMMITTED' => TransactionIsolationLevel::READ_UNCOMMITTED,
        'READ COMMITTED' => TransactionIsolationLevel::READ_COMMITTED,
        'REPEATABLE READ' => TransactionIsolationLevel::REPEATABLE_READ,
        'SERIALIZABLE' => TransactionIsolationLevel::SERIALIZABLE
    ];

    public function setConnectionParameters(array $connectionParameters)
    {
        if (array_key_exists('default', $connectionParameters)) {
            $this->connectionParameters = $connectionParameters['default'];
        }
    }

    public function connect()
    {
        try {
            $connection = ContainerFacade::get(ConnectionFactoryInterface::class)->create();
            $this->setConnection($connection);
            $this->ensureConnectionIsEstablished($connection);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    public function forceMasterConnection()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
    }

    public function forceSlaveConnection()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
    }

    public function closeConnection()
    {
        $this->connection->close();
        gc_collect_cycles();
    }

    protected function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function getOne($query, $parameters = [])
    {
        if ($this->doesStatementProduceOutput($query)) {
            try {
                return $this->getConnection()->fetchOne($query, $parameters);
            } catch (DBALException | PDOException $exception) {
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

    public function getRow($query, $parameters = [])
    {
        try {
            $resultSet = $this->select($query, $parameters);
            $result = $resultSet->fields;
        } catch (DatabaseErrorException $exception) {
            $this->logException($exception);
            $result = [];
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->logException($exception);
            $result = [];
        }

        if ($result === false) {
            $result = [];
        }

        return $result;
    }

    public function quoteIdentifier($string)
    {
        $string = trim(str_replace('`', '', $string));
        try {
            $result = $this->getConnection()->quoteIdentifier($string);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    public function quote($value)
    {
        if (!is_scalar($value)) {
            return false;
        }

        try {
            return $this->getConnection()->quote((string) $value);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    public function quoteArray($array)
    {
        return array_map(function ($item) {
            return $this->quote($item);
        }, $array);
    }

    public function startTransaction()
    {
        try {
            $this->getConnection()->beginTransaction();
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    public function commitTransaction()
    {
        try {
            $this->getConnection()->commit();
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    public function rollbackTransaction()
    {
        try {
            $this->getConnection()->rollBack();
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    public function setTransactionIsolationLevel($level)
    {
        $level = strtoupper($level);

        if (!array_key_exists($level, $this->transactionIsolationLevelMap)) {
            throw new InvalidArgumentException('Transaction isolation level is invalid');
        }

        return $this->getConnection()->setTransactionIsolation($this->transactionIsolationLevelMap[$level]);
    }

    public function execute($query, $parameters = [])
    {
        return $this->executeUpdate($query, $parameters);
    }

    public function select($query, $parameters = [])
    {
        $this->checkIfSqlIsReadOnly($query);
        try {
            $parameters = $this->ensureParametersWithIntegerKeysStartWithOne($parameters);

            $statement = $this->getConnection()->prepare($this->checkForMultipleQueries($query, $parameters));
            foreach ($parameters as $key => $value) {
                $statement->bindValue($key, $value);
            }

            return new ResultSet($statement);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
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
        $queries = preg_split(
            '~(\"[^\\\\"]*\"|' . "\'[^\\\\']*\'|\'.+\'|`[^\\`]*`)(*SKIP)(*F)|(?<=;)(?![ ]*$)~",
            $query
        );
        if (count($queries) > 1) {
            Registry::getLogger()->error('More than one query within one statement', [$query]);
        }

        return $queries[0];
    }

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

    public function getCol($query, $parameters = [])
    {
        $this->checkIfSqlIsReadOnly($query);
        $result = [];

        try {
            $result = $this->getConnection()->fetchFirstColumn($query, $parameters);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    public function executeUpdate($query, $parameters = [], $types = [])
    {
        try {
            return $this->getConnection()->executeStatement($query, $parameters, $types);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

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

    protected function convertException(\Exception $exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $exceptionClass = DatabaseErrorException::class;

        switch (true) {
            case $exception instanceof DBALException\ConnectionException:
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
            case is_a($exception->getPrevious(), '\Exception')
                && in_array($exception->getPrevious()->getCode(), ['2003']):
                $exceptionClass = DatabaseConnectionException::class;
                break;
            case $exception instanceof DBALException:
                /**
                 * Doctrine passes the message and the code of the PDO Exception, which would break backward
                 * compatibility as it uses SQLSTATE error code (string),
                 * but the shop used to the (My)SQL errors (integer)
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
                 * The shop uses the (My)SQL errors (integer) in the error code,
                 * but $pdoException uses SQLSTATE error code (string)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately in some cases we can access PDOException and recover the original SQL error.
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

    protected function handleException(StandardException $exception)
    {
        throw $exception;
    }

    protected function logException(\Exception $exception)
    {
        /** The exception has to be converted into an instance of oxException in order to be logged like this */
        $exception = $this->convertException($exception);
        Registry::getLogger()->error($exception->getMessage(), [$exception]);
    }

    public function getAll($query, $parameters = [])
    {
        try {
            $result = $this->getConnection()->fetchAllAssociative($query, $parameters);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        if ($this->doesStatementProduceOutput($query)) {
            return $result;
        }

        Registry::getLogger()
            ->warning(
                'Given statement does not produce an output',
                [debug_backtrace()]
            );

        return [];
    }

    public function getLastInsertId()
    {
        try {
            $lastInsertId = $this->getConnection()->lastInsertId();
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $lastInsertId;
    }

    public function metaColumns($table)
    {
        $databaseName = $this->getConnection()->getDatabase();
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
            $columns = $this->getConnection()->fetchAllAssociative($query);
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

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

    public function isRollbackOnly()
    {
        try {
            $isRollbackOnly = $this->connection->isRollbackOnly();
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $isRollbackOnly;
    }

    public function isTransactionActive()
    {
        try {
            $isTransactionActive = $this->connection->isTransactionActive();
        } catch (DBALException | PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $isTransactionActive;
    }

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

        return $column[$keyMap[$key]];
    }

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
             * @todo: If the assigned type is one of the following and maxLength is -1, then,
             * if applicable the default max length ot that type should be assigned.
             */
        }

        return [(int)$maxLength, (int)$scale];
    }

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

    protected function ensureConnectionIsEstablished($connection)
    {
        if (!$this->isConnectionEstablished($connection)) {
            $message = $this->createConnectionErrorMessage($connection);

            throw new ConnectionException($message);
        }
    }

    protected function isConnectionEstablished($connection)
    {
        try {
            $connection->getServerVersion();
        } catch (DBALException) {
            return false;
        }

        return true;
    }

    protected function createConnectionErrorMessage($connection)
    {
        $params = $connection->getParams();
        return sprintf(
            "Could not connect to the database. Please check your database status and configuration. " .
            "driver: '%s', host: '%s'",
            $params['driver'] ?? '',
            $params['host'] ?? ''
        );
    }

    private function convertErrorCode($code)
    {
        return $code === self::MYSQL_DUPLICATE_KEY_ERROR_CODE
            ? self::DUPLICATE_KEY_ERROR_CODE
            : $code;
    }

    /**
     * Doctrine's DBAL requires that arrays with integer keys for positional
     * parameters must start from index 1. This method checks if the provided
     * parameter array keys are integers and if the lowest index is 0. If so,
     * it shifts all keys to begin from 1. Associative arrays are left untouched.
     */
    private function ensureParametersWithIntegerKeysStartWithOne(array $parameters): array
    {
        if (array_key_exists(0, $parameters)) {
            array_unshift($parameters, '');
            unset($parameters[0]);
        }

        return $parameters;
    }
}
