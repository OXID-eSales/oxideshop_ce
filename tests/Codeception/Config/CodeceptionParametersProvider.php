<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Config;

use OxidEsales\Codeception\Module\Database;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Path;

class CodeceptionParametersProvider
{
    private DatabaseConfiguration $dbConfig;

    public function getParameters(): array
    {
        $facts = new Facts();
        $this->loadEnvironmentVariables();

        $this->dbConfig = (new DatabaseConfiguration(getenv('OXID_DB_URL')));
        return [
            'SHOP_URL' => getenv('OXID_SHOP_BASE_URL') ?: $facts->getShopUrl(),
            'PROJECT_ROOT' => $this->getProjectRoot(),
            'VENDOR_PATH' => $facts->getVendorPath(),
            'DB_NAME' => $this->getDbName(),
            'DB_USERNAME' => $this->getDbUser(),
            'DB_PASSWORD' => $this->getDbPass(),
            'DB_HOST' => $this->getDbHost(),
            'DB_PORT' => $this->getDbPort(),
            'DUMP_PATH' => $this->getTestDataDumpFilePath(),
            'FIXTURES_PATH' => $this->getTestFixtureSqlFilePath(),
            'OUT_DIRECTORY_FIXTURES' => $this->getOutDirectoryFixturesPath(),
            'MYSQL_CONFIG_PATH' => $this->generateMysqlStarUpConfigurationFile(),
            'SELENIUM_SERVER_PORT' => getenv('SELENIUM_SERVER_PORT') ?: '4444',
            'SELENIUM_SERVER_HOST' => getenv('SELENIUM_SERVER_HOST') ?: 'selenium',
            'PHP_BIN' => (getenv('PHPBIN')) ?: 'php',
            'SCREEN_SHOT_URL' => getenv('CC_SCREEN_SHOTS_URL') ?: '',
            'BROWSER' => getenv('BROWSER_NAME') ?: 'chrome',
            'THEME_ID' => getenv('THEME_ID') ?: 'apex',
            'MAIL_HOST' => getenv('MAIL_HOST') ?: 'mailpit',
            'MAIL_WEB_PORT' => getenv('MAIL_WEB_PORT') ?: '8025',
        ];
    }

    private function getTestDataDumpFilePath(): string
    {
        return Path::join(
            $this->getShopTestPath(),
            '/Codeception/Support/_generated/shop-dump.sql'
        );
    }

    private function getTestFixtureSqlFilePath(): string
    {
        return Path::join(
            $this->getShopTestPath(),
            '/Codeception/Support/Data/dump.sql',
        );
    }

    private function getOutDirectoryFixturesPath(): string
    {
        return Path::join(
            $this->getShopTestPath(),
            '/Codeception/Support/Data/out',
        );
    }

    private function getShopSuitePath(): string
    {
        $testSuitePath = (string)getenv('TEST_SUITE');
        if ($testSuitePath === '' || $testSuitePath === '0') {
            $testSuitePath = Path::join($this->getProjectRoot(), 'tests');
        }
        return $testSuitePath;
    }

    private function getShopTestPath(): string
    {
        $facts = new Facts();
        return $facts->isEnterprise()
            ? $facts->getEnterpriseEditionRootPath() . '/Tests'
            : $this->getShopSuitePath();
    }

    private function generateMysqlStarUpConfigurationFile(): string
    {
        return Database::generateStartupOptionsFile(
            $this->getDbUser(),
            $this->getDbPass(),
            $this->getDbHost(),
            $this->getDbPort(),
        );
    }

    private function getDbName(): string
    {
        return getenv('DB_NAME') ?: $this->dbConfig->getName();
    }

    private function getDbUser(): string
    {
        return getenv('DB_USERNAME') ?: $this->dbConfig->getUser();
    }

    private function getDbPass(): string
    {
        return getenv('DB_PASSWORD') ?: $this->dbConfig->getPass();
    }

    private function getDbHost(): string
    {
        return getenv('DB_HOST') ?: $this->dbConfig->getHost();
    }

    private function getDbPort(): int
    {
        return (int) getenv('DB_PORT') ?: $this->dbConfig->getPort();
    }

    private function loadEnvironmentVariables(): void
    {
        (new DotenvLoader($this->getProjectRoot()))->loadEnvironmentVariables();
    }

    private function getProjectRoot(): string
    {
        return (new ProjectRootLocator())->getProjectRoot();
    }
}
