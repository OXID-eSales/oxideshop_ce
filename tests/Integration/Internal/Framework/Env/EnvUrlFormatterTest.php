<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Env;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Tests\EnvTrait;
use PHPUnit\Framework\TestCase;

final class EnvUrlFormatterTest extends TestCase
{
    use EnvTrait;

    public function testWithPath(): void
    {
        $this->loadEnvFixture(__DIR__, ['OXID_ENV=abc']);

        $url = EnvUrlFormatter::toEnvUrl('/path/to/some/directory');

        $this->assertEquals('/path/to/some/directory.abc', $url);
    }

    public function testWithPathAndTrailingSlash(): void
    {
        $this->loadEnvFixture(__DIR__, ['OXID_ENV=abc']);

        $url = EnvUrlFormatter::toEnvUrl('/path/to/some/directory/');

        $this->assertEquals('/path/to/some/directory.abc', $url);
    }
}
