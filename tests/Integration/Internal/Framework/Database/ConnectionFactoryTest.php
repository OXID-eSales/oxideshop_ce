<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database;

use Doctrine\DBAL\Driver\Connection;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    use ContainerTrait;

    private string $logFile = __DIR__ . DIRECTORY_SEPARATOR . 'some-log.log';

    public function tearDown(): void
    {
        parent::tearDown();
        unlink($this->logFile);
    }

    public function testSqlLoggerWritesExpectedValueToTheLog(): void
    {
        $this->injectContextMock();
        $id = Registry::getUtilsObject()->generateUId();
        $variableName = 'some-variable';
        $variableValue = uniqid('some-value-', true);
        $connection = $this->get(Connection::class);
        $connection->connect();
        $query = "INSERT INTO `oxconfig` (`OXID`, `OXVARNAME`, `OXVARVALUE`)  VALUES (?, ?, ?);";
        $statement = $connection->prepare($query);

        $statement->execute([
            $id,
            $variableName,
            $variableValue,
        ]);

        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString($id, $log);
        $this->assertStringContainsString($variableName, $log);
        $this->assertStringContainsString($variableValue, $log);
    }

    private function injectContextMock(): void
    {
        $this->container = (new TestContainerFactory())->create();
        $contextMock = $this->createConfiguredMock(
            ContextInterface::class,
            [
                'isAdmin' => true,
                'isEnabledAdminQueryLog' => true,
                'getAdminLogFilePath' => $this->logFile
            ]
        );
        $this->container->set(ContextInterface::class, $contextMock);
        $this->container->autowire(ContextInterface::class, Context::class);
        $this->container->compile();
    }
}
