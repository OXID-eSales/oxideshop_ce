<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Utils\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;
use Webmozart\PathUtil\Path;

class TestDatabaseHandler
{
    /** @var Connection $connection */
    private static $connection = null;

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

    public static function reset()
    {
        $fixtureLoader = FixtureLoader::getInstance();
        $fixtureLoader->reset();
    }

    public static function configureTestConfig()
    {
        // Reconfigure Registry
        $configFile = new ConfigFile(self::getConfigFile());
        Registry::set(ConfigFile::class, $configFile);
    }

    private static function getConfigFile(): string
    {
        return Path::join(OX_BASE_PATH, 'config.inc.php');
    }
}
