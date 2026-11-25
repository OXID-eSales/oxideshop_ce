<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponseFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;

final class OfflinePageResponseFactoryTest extends TestCase
{
    private string $sourceDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sourceDirectory = Path::join(sys_get_temp_dir(), uniqid('offline-page-', true));
        (new Filesystem())->mkdir($this->sourceDirectory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->sourceDirectory);

        parent::tearDown();
    }

    public function testCreateReadsOfflinePage(): void
    {
        (new Filesystem())->dumpFile(Path::join($this->sourceDirectory, 'offline.html'), '<html>offline</html>');

        $response = $this->createFactory()->create();

        $this->assertSame('<html>offline</html>', $response->getContent());
    }

    public function testCreateAnswersWithServerError(): void
    {
        $response = $this->createFactory()->create();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testCreateWithoutOfflinePageReturnsEmptyContent(): void
    {
        $response = $this->createFactory()->create();

        $this->assertSame('', $response->getContent());
    }

    private function createFactory(): OfflinePageResponseFactory
    {
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getSourcePath')->willReturn($this->sourceDirectory);

        return new OfflinePageResponseFactory($context);
    }
}
