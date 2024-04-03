<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Config;

use OxidEsales\Codeception\Module\Database\DatabaseDefaultsFileGenerator;
use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Path;

class CodeceptionParametersProvider
{
    public function getParameters(): array
    {
        $facts = new Facts();
        return [
            'SHOP_URL' => getenv('SHOP_URL') ?: $facts->getShopUrl(),
            'SHOP_SOURCE_PATH' => getenv('SHOP_SOURCE_PATH') ?: $facts->getSourcePath(),
            'VENDOR_PATH' => $facts->getVendorPath(),
            'DB_NAME' => getenv('DB_NAME') ?: $facts->getDatabaseName(),
            'DB_USERNAME' => getenv('DB_USERNAME') ?: $facts->getDatabaseUserName(),
            'DB_PASSWORD' => getenv('DB_PASSWORD') ?: $facts->getDatabasePassword(),
            'DB_HOST' => getenv('DB_HOST') ?: $facts->getDatabaseHost(),
            'DB_PORT' => getenv('DB_PORT') ?: $facts->getDatabasePort(),
            'DUMP_PATH' => $this->getTestDataDumpFilePath(),
            'FIXTURES_PATH' => $this->getTestFixtureSqlFilePath(),
            'OUT_DIRECTORY_FIXTURES' => $this->getOutDirectoryFixturesPath(),
            'MYSQL_CONFIG_PATH' => $this->getMysqlConfigPath(),
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

    private function getShopSuitePath(Facts $facts): string
    {
        $testSuitePath = (string)getenv('TEST_SUITE');
        if ($testSuitePath === '' || $testSuitePath === '0') {
            $testSuitePath = $facts->getShopRootPath() . '/tests';
        }
        return $testSuitePath;
    }

    private function getShopTestPath(): string
    {
        $facts = new Facts();
        return $facts->isEnterprise()
            ? $facts->getEnterpriseEditionRootPath() . '/Tests'
            : $this->getShopSuitePath($facts);
    }

    private function getMysqlConfigPath(): string
    {
        return (
        new DatabaseDefaultsFileGenerator(
            new ConfigFile()
        )
        )
            ->generate();
    }
}
