<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Registry;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

class FixtureLoader
{
    private static $instance = null;

    /** @var Connection */
    private $connection;

    private $fixtureTables = [];

    private function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function loadFixtures(array $fixtureFiles)
    {
        foreach ($fixtureFiles as $fixture) {
            $this->loadFixture($fixture);
        }
    }

    public function cleanupTable($table)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->delete($table)->execute();
    }

    public function cleanupFixtureTables()
    {
        foreach ($this->fixtureTables as $table) {
            $this->cleanupTable($table);
        }
    }

    private function loadFixture(string $fileName)
    {
        $definitions = Yaml::parseFile($fileName);
        foreach ($definitions as $table => $rows) {
            $this->prepareTable($table);
            foreach ($rows as $row) {
                $this->insertRow($table, $row);
            }
        }
    }

    private function insertRow($table, $row)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert($table);
        $valuesArray = [];
        foreach ($row as $column => $value) {
            $valuesArray[$column] = ":$column";
            if ($value === 'UUID') {
                $value = Registry::getUtilsObject()->generateUId();
            }
            $queryBuilder->setParameter(":$column", $value);
        }
        $queryBuilder->values($valuesArray)->execute();
    }

    private function prepareTable(string $table)
    {
        // We want to clean up a table only once so
        // that several fixtures may add data to the same table
        if (array_search($table, $this->fixtureTables) !== false) {
            return;
        }
        $this->cleanupTable($table);
        $this->fixtureTables[] = $table;
    }

    public function reset()
    {
        $this->fixtureTables = [];
    }

    public function loadBasicFixtures()
    {
        $this->loadFixtures([Path::join(__DIR__, 'basic_fixtures.yaml')]);
    }

    public static function init(Connection $connection): void
    {
        self::$instance = new FixtureLoader($connection);
    }

    public static function getInstance(): FixtureLoader
    {
        if (is_null(self::$instance)){
            throw new \Exception("Fixture loader not initialized. Call init before calling getInstance().\n");
        }
        return self::$instance;
    }
}