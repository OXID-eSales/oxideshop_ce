<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;
use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

class TestDatabaseHandler
{
    /** @var Connection $connection */
    private static $connection = null;

    public static function createDatabase()
    {
        $facts = new Facts();
        exec(
            $facts->getCommunityEditionRootPath() .
            '/bin/oe-console oe:database:reset' .
            ' --db-host=' . $facts->getDatabaseHost() .
            ' --db-port=' . $facts->getDatabasePort() .
            ' --db-name=' . $facts->getDatabaseName() .
            ' --db-user=' . $facts->getDatabaseUserName() .
            ' --db-password=' . $facts->getDatabasePassword() .
            ' --force'
        );
    }

    public static function init()
    {
        if (! is_null(self::$connection)) {
            throw new \Exception("Test database already initialized.");
        }
        $connectionProvider = new ConnectionProvider();
        self::$connection = $connectionProvider->get();

        FixtureLoader::init(self::$connection);
        $fixtureLoader = FixtureLoader::getInstance();
        $fixtureLoader->loadFixtures([Path::join(__DIR__, 'basic_fixtures.yaml')]);

        try {
            self::$connection->executeQuery("SELECT 1 FROM oxv_oxarticles");
        } catch (\Exception $e) {
            (new ViewsGenerator())->generate();
        }
    }

    public static function get(): Connection
    {
        if (is_null(self::$connection)) {
            throw new \Exception("Test database is not initalized");
        }
        return self::$connection;
    }

    public static function createDump($pathData, $pathDump)
    {
        $facts = new Facts();
            $mysql_config = self::getMysqlConfigPath();
           // exec('mysql --defaults-file='.$mysql_config.' --default-character-set=utf8 '.$facts->getDatabaseName().' < '.$pathData);
            exec('mysqldump --defaults-file='.$mysql_config.' --default-character-set=utf8 '.$facts->getDatabaseName().' > '.$pathDump);

    }
/*
    public static function reset()
    {
        $fixtureLoader = FixtureLoader::getInstance();
        $fixtureLoader->reset();
    }
*/
    private static function getConfigFile(): string
    {
        return Path::join(OX_BASE_PATH, 'config.inc.php');
    }

    private static function getMysqlConfigPath()
    {
        $facts = new Facts();
        $configFile = new ConfigFile(self::getConfigFile());

        $generator = new \OxidEsales\EshopCommunity\Tests\Utils\Database\DatabaseDefaultsFileGenerator($configFile);

        return $generator->generate();
    }
}
