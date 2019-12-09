<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker;

class MysqlDatabaseChecker implements DatabaseCheckerInterface
{
    /** @var DatabaseVersionDaoInterface */
    private $databaseVersionDao;

    /** @param DatabaseVersionDaoInterface $databaseVersionDao */
    public function __construct(DatabaseVersionDaoInterface $databaseVersionDao)
    {
        $this->databaseVersionDao = $databaseVersionDao;
    }

    /** @return bool */
    public function isDatabaseCompatible(): bool
    {
        return version_compare(
            $this->databaseVersionDao->getVersion(),
            '5.6',
            '>='
        );
    }

    /** @return string[] - Array of untranslated notice strings */
    public function getCompatibilityNotices(): array
    {
        $notices = [];
        if ($this->isVersion56()) {
            $notices[] = 'ERROR_MYSQL_56_NOT_RECOMMENDED';
        }
        return $notices;
    }

    /** @return bool */
    private function isVersion56(): bool
    {
        return version_compare($this->databaseVersionDao->getVersion(), '5.6') === 0;
    }
}
