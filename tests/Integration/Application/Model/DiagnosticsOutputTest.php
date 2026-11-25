<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\EshopCommunity\Application\Model\DiagnosticsOutput;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

final class DiagnosticsOutputTest extends IntegrationTestCase
{
    public function testGetResultFileResponseDeliversCachedResultAsAttachment(): void
    {
        $diagnosticsOutput = new DiagnosticsOutput();
        $diagnosticsOutput->storeResult('some-content-123');

        $response = $diagnosticsOutput->getResultFileResponse();

        $this->assertSame('some-content-123', $response->getContent());
        $this->assertSame(
            'attachment; filename=diagnostic_tool_result.html',
            $response->headers->get('Content-Disposition')
        );
    }

    public function testGetResultFileResponseReturnsNotFoundWithoutStoredResult(): void
    {
        $diagnosticsOutput = new DiagnosticsOutput();
        $diagnosticsOutput->storeResult('');

        $response = $diagnosticsOutput->getResultFileResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
