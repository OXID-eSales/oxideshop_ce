<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseChecker;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\TestCase;

final class DatabaseCheckerTest extends TestCase
{
    use ContainerTrait;

    public function testCanCreateDatabaseWithExistingDatabaseWillThrow(): void
    {
        $configFile = new ConfigFile();
        $existingDatabaseName = $configFile->getVar('dbName');

        $this->expectException(DatabaseExistsAndNotEmptyException::class);

        (new DatabaseChecker($this->get(BasicContextInterface::class)))
            ->canCreateDatabase(
                $configFile->getVar('dbHost'),
                (int) $configFile->getVar('dbPort'),
                $configFile->getVar('dbUser'),
                $configFile->getVar('dbPwd'),
                $existingDatabaseName
            );
    }

    #[DoesNotPerformAssertions]
    public function testCanCreateDatabaseWithNewDatabaseName(): void
    {
        $configFile = new ConfigFile();
        $nonExistingDatabaseName = uniqid('some-string-', true);

        (new DatabaseChecker($this->get(BasicContextInterface::class)))
            ->canCreateDatabase(
                $configFile->getVar('dbHost'),
                (int) $configFile->getVar('dbPort'),
                $configFile->getVar('dbUser'),
                $configFile->getVar('dbPwd'),
                $nonExistingDatabaseName
            );
    }
}
