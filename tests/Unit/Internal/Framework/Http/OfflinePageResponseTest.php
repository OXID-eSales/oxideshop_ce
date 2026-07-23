<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;

final class OfflinePageResponseTest extends TestCase
{
    public function testCarriesServerErrorStatusAndOfflinePageContent(): void
    {
        $response = new OfflinePageResponse();

        $offlinePage = (string) file_get_contents(
            Path::join(new BasicContext()->getSourcePath(), 'offline.html')
        );
        $this->assertSame($offlinePage, $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testCarriesTheGivenStatus(): void
    {
        $response = new OfflinePageResponse(Response::HTTP_SERVICE_UNAVAILABLE);

        $this->assertSame(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
    }
}
