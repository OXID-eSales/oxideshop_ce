<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event\Database\CompatibilityChecker;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class MysqlVersionDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGetVersion(): void
    {
        $mysqlVersionDao = $this->get('oxid_esales.framework.database.compatibility_checker.mysql_version_dao');
        $version = $mysqlVersionDao->getVersion();

        $this->assertTrue(
            $this->isVersion($version)
        );
    }

    private function isVersion(string $version): bool
    {
        return $this->hasNumbers($version);
    }

    private function hasNumbers(string $version): bool
    {
        return (bool)preg_match('~[0-9]+~', $version);
    }
}
