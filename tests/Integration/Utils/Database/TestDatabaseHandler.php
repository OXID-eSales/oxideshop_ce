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
        self::configureTestConfig();
       // self::setupTestConfigInc();
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

    public static function setupTestConfigInc()
    {
        if (is_link(self::getConfigFile()) && readlink(self::getConfigFile()) === self::getTestConfigFile()) {
            // Allready set up, probably from an aborted suite. Do nothing.
            return;
        }
        if (file_exists(self::getConfigFile())) {
            rename(self::getConfigFile(), self::getConfigBackupFile());
        }
        symlink(self::getTestConfigFile(), self::getConfigFile());
    }

    public static function cleanupTestConfigInc()
    {
        if (is_link(self::getConfigFile()) && readlink(self::getConfigFile()) === self::getTestConfigFile()) {
            // Allready set up, probably from an aborted suite. Do nothing.
            unlink(self::getConfigFile());
        }
        if (file_exists(self::getConfigBackupFile())) {
            rename(self::getConfigBackupFile(), self::getConfigFile());
        }
        self::reset();
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
        if (file_exists(self::getConfigFile())) {
            $configInc = file_get_contents(self::getConfigFile());
        } else {
            $configInc = file_get_contents(self::getConfigDistFile());
        }
        if (getenv('TEST_DB_NAME') === false) {
            $testDb = 'oxidtest';
        } else {
            $testDb = getenv('TEST_DB_NAME');
        }

       /* $configInc = preg_replace('/this->dbHost\s+=\s+.*?;/', 'this->dbHost = \'localhost\';', $configInc);
        $configInc = preg_replace('/this->dbPort\s+=\s+.*?;/', 'this->dbPort = 3306;', $configInc);
        $configInc = preg_replace('/this->dbName\s+=\s+.*?;/', 'this->dbName = \'' . $testDb . '\';', $configInc);
        $configInc = preg_replace('/this->dbUser\s+=\s+.*?;/', 'this->dbUser = \'oxid\';', $configInc);
        $configInc = preg_replace('/this->dbPwd\s+=\s+.*?;/', 'this->dbPwd = \'oxid\';', $configInc);
*/
        // In case of an unconfigured config.inc.php we also set other necessary variables
        $configInc = preg_replace(
            '/this->sShopDir\s+=\s+\'<sShopDir>\'.*?;/',
            'this->sShopDir = \'' . self::getShopDir() . '\';',
            $configInc
        );
        $configInc = preg_replace(
            '/this->sCompileDir\s+=\s+\'<sCompileDir>\';/',
            'this->sCompileDir = \'' . self::getCompileDir() . '\';',
            $configInc
        );
        $shopUrl = getenv('OXID_SHOP_URL') ? getenv('OXID_SHOP_URL') : 'http://localhost';
        $configInc = preg_replace(
            '/this->sShopURL\s+=\s+\'<sShopURL>\';/',
            'this->sShopURL = \'' . $shopUrl . '\';',
            $configInc
        );

        file_put_contents(self::getTestConfigFile(), $configInc);
        // Reconfigure Registry
        $configFile = new ConfigFile(self::getTestConfigFile());
        Registry::set(ConfigFile::class, $configFile);
    }

    private static function getConfigFile(): string
    {
        return Path::join(OX_BASE_PATH, 'config.inc.php');
    }

    private static function getConfigBackupFile(): string
    {
        return Path::join(OX_BASE_PATH, 'config.inc.php.bak');
    }

    private static function getConfigDistFile(): string
    {
        return Path::join(OX_BASE_PATH, 'config.inc.php.dist');
    }

    private static function getTestConfigFile(): string
    {
        return Path::join(OX_BASE_PATH, 'config.inc.php.test');
    }

    private static function getShopDir(): string
    {
        return OX_BASE_PATH;
    }

    private static function getCompileDir(): string
    {
        return Path::join(OX_BASE_PATH, 'tmp');
    }
}
