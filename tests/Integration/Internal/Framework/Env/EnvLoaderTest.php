<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Env;

use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Tests\RequestTrait;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

final class EnvLoaderTest extends TestCase
{
    use RequestTrait;

    private string $envKey = 'OXID_ENV';

    public function setUp(): void
    {
        parent::setUp();
        $this->backupRequestData();
    }

    public function tearDown(): void
    {
        $this->restoreRequestData();
        parent::tearDown();
    }

    public function testApplicationEnvironmentIsDefined(): void
    {
        (new DotenvLoader(
            (new Facts())->getShopRootPath()
        ))
            ->loadEnvironmentVariables();
        $currentEnvironment = getenv($this->envKey);

        $this->assertNotEmpty($currentEnvironment);
        $this->assertIsString($currentEnvironment);
    }

    public function testLoaderUsesExpectedEnvironmentKey(): void
    {
        (new DotenvLoader(
            __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures'
        ))
            ->loadEnvironmentVariables();
        $currentEnvironment = getenv($this->envKey);

        $this->assertEquals('abcde', $currentEnvironment);
    }
}
