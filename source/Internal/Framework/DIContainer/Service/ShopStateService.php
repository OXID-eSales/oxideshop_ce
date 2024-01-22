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
#[\AllowDynamicProperties]
class ShopStateService implements ShopStateServiceInterface
{
    private $dbHost;
    private $dbPort;
    private $dbName;
    private $dbUser;
    private $dbPwd;

    public function __construct(
        private BasicContextInterface $basicContext,
        private string $anyUnifiedNamespace
    ) {
    }

    /**
     * @return bool
     */
    public function isLaunched(): bool
    {
        return $this->areUnifiedNamespacesGenerated()
               && $this->doesConfigFileExist()
               && $this->doesConfigTableExist();
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
     * @return bool
     */
    private function doesConfigTableExist(): bool
    {
        try {
            $connection = $this->getConnection();
            $connection->exec(
                'SELECT 1 FROM ' . $this->basicContext->getConfigTableName() . ' LIMIT 1'
            );
        } catch (\PDOException) {
            return false;
        }

        return true;
    }

    /**
     * @return \PDO
     */
    private function getConnection(): \PDO
    {
        include $this->basicContext->getConfigFilePath();

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s',
            $this->dbHost,
            $this->dbPort,
            $this->dbName
        );

        return new \PDO(
            $dsn,
            $this->dbUser,
            $this->dbPwd,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}
