<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

/**
 * @internal
 */
class ShopStateService implements ShopStateServiceInterface
{
    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    /**
     * @var string
     */
    private $anyUnifiedNamespace;

    private $dbHost;
    private $dbPort;
    private $dbName;
    private $dbUser;
    private $dbPwd;

    /**
     * @param BasicContextInterface $basicContext
     * @param string                $anyUnifiedNamespace
     */
    public function __construct(BasicContextInterface $basicContext, string $anyUnifiedNamespace)
    {
        $this->basicContext = $basicContext;
        $this->anyUnifiedNamespace = $anyUnifiedNamespace;
    }

    /**
     * @return bool
     */
    public function isLaunched(): bool
    {
        return $this->areUnifiedNamespacesGenerated()
               && $this->doesConfigFileExist()
               && $this->doesConfigTableExist([]);
    }

    /**
     * @param string $dbHost
     * @param int    $dbPort
     * @param string $dbUser
     * @param string $dbPwd
     * @param string $dbName
     *
     * @return bool
     */
    public function checkIfDbExistsAndNotEmpty(
        string $dbHost,
        int $dbPort,
        string $dbUser,
        string $dbPwd,
        string $dbName
    ): bool {
        $dbParams = [
            'dbHost' => $dbHost,
            'dbPort' => $dbPort,
            'dbUser' => $dbUser,
            'dbPwd'  => $dbPwd,
            'dbName' => $dbName
        ];

        return $this->doesConfigTableExist($dbParams);
    }

    /**
     * @return bool
     */
    private function areUnifiedNamespacesGenerated(): bool
    {
        return class_exists($this->anyUnifiedNamespace);
    }

    /**
     * @return bool
     */
    private function doesConfigFileExist(): bool
    {
        return file_exists($this->basicContext->getConfigFilePath());
    }

    /**
     * @param array $dbParams
     *
     * @return bool
     */
    private function doesConfigTableExist(array $dbParams): bool
    {
        try {
            $connection = $this->getConnection($dbParams);
            $connection->exec(
                'SELECT 1 FROM ' . $this->basicContext->getConfigTableName() . ' LIMIT 1'
            );
        } catch (\PDOException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param array $dbParams
     *
     * @return \PDO
     */
    private function getConnection(array $dbParams): \PDO
    {
        include $this->basicContext->getConfigFilePath();

        if (count($dbParams) > 0) {
            $this->dbHost = $dbParams['dbHost'];
            $this->dbPort = $dbParams['dbPort'];
            $this->dbUser = $dbParams['dbUser'];
            $this->dbPwd = $dbParams['dbPwd'];
            $this->dbName = $dbParams['dbName'];
        }

        $dsn = sprintf('mysql:host=%s;port=%s', $this->dbHost, $this->dbPort);

        $connection = new \PDO(
            $dsn,
            $this->dbUser,
            $this->dbPwd,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );

        $connection->exec("USE `{$this->dbName}`");

        return $connection;
    }
}
