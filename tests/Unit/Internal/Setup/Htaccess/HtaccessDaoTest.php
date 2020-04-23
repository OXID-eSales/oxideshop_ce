<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Htaccess;

use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessAccessException;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessDao;
use PHPUnit\Framework\TestCase;

class HtaccessDaoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        copy($this->getHtaccessTplPath(), $this->getTestFilePath());
    }

    protected function tearDown(): void
    {
        unlink($this->getTestFilePath());
        parent::tearDown();
    }

    public function testSetRewriteBaseWithNonExistingFileWillThrow(): void
    {
        $this->expectException(HtaccessAccessException::class);

        (new HtaccessDao(''))->setRewriteBase('some-string');
    }

    public function testSetRewriteBaseWithCorrectFileWillUpdateFile(): void
    {
        $rewriteBase = '/some-string';
        $this->assertStringNotContainsString($rewriteBase, file_get_contents($this->getTestFilePath()));

        (new HtaccessDao($this->getTestFilePath()))->setRewriteBase($rewriteBase);

        $this->assertStringContainsString("RewriteBase $rewriteBase", file_get_contents($this->getTestFilePath()));
    }

    /** @return string */
    private function getTestFilePath(): string
    {
        return $this->getHtaccessTplPath() . '-test';
    }

    private function getHtaccessTplPath(): string
    {
        return __DIR__ . '/testData/.htaccess';
    }
}
