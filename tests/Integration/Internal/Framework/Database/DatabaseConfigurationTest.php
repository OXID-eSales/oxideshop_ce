<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\EnvTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class DatabaseConfigurationTest extends IntegrationTestCase
{
    use EnvTrait;
    use ContainerTrait;

    public function testGetParameters(): void
    {
        $driver = 'mysql';
        $user = uniqid('user-', true);
        $pass = uniqid('secret-', true);
        $host = uniqid('host-', true);
        $port = 1234;
        $name = uniqid('db-', true);
        $encoding = 'utf8mb4';
        $driverOptions = 'SET @@SESSION.sql_mode=\"\"';
        $optionsString = sprintf(
            'charset=%s&driverOptions[1002]="%s"',
            $encoding,
            $driverOptions,
        );
        $url = sprintf(
            '%s://%s:%s@%s:%d/%s?%s',
            $driver,
            $user,
            $pass,
            $host,
            $port,
            $name,
            $optionsString,
        );

        $this->loadEnvFixture(__DIR__, ["OXID_DB_URL=$url"]);

        $this->assertEquals($host, $this->getParameter('oxid_esales.db.host'));
        $this->assertEquals($name, $this->getParameter('oxid_esales.db.name'));
        $this->assertEquals($user, $this->getParameter('oxid_esales.db.user'));
        $this->assertEquals($pass, $this->getParameter('oxid_esales.db.pass'));
        $this->assertIsInt($this->getParameter('oxid_esales.db.port'));
        $this->assertEquals($port, $this->getParameter('oxid_esales.db.port'));
        parse_str($this->getParameter('oxid_esales.db.options'), $parsedOptions);
        $this->assertEquals($encoding, $parsedOptions['charset']);
        $this->assertEquals(stripslashes($driverOptions), $parsedOptions['driverOptions'][1002]);
    }
}
