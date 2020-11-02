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

    public function __construct(BasicContextInterface $basicContext, string $anyUnifiedNamespace)
    {
        $this->basicContext = $basicContext;
        $this->anyUnifiedNamespace = $anyUnifiedNamespace;
    }

    public function isLaunched(): bool
    {
        return $this->areUnifiedNamespacesGenerated()
               && $this->doesConfigFileExist()
               && $this->doesConfigTableExist();
    }

    private function areUnifiedNamespacesGenerated(): bool
    {
        return class_exists($this->anyUnifiedNamespace);
    }

    private function doesConfigFileExist(): bool
    {
        return file_exists($this->basicContext->getConfigFilePath());
    }

    private function doesConfigTableExist(): bool
    {
        try {
            $connection = $this->getConnection();
            $connection->exec(
                'SELECT 1 FROM ' . $this->basicContext->getConfigTableName() . ' LIMIT 1'
            );
        } catch (\PDOException $exception) {
            return false;
        }

        return true;
    }

    private function getConnection(): \PDO
    {
        include $this->basicContext->getConfigFilePath();

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
