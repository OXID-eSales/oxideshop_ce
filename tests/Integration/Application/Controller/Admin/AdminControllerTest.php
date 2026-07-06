<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

final class AdminControllerTest extends TestCase
{
    private array $getBackup;

    public function setUp(): void
    {
        parent::setUp();
        $this->getBackup = $_GET;
    }

    public function tearDown(): void
    {
        $_GET = $this->getBackup;
        parent::tearDown();
    }

    public function testInitWithoutAuthorizationInRedirectLoopSignalsForbiddenResponse(): void
    {
        $_GET['redirected'] = '1';

        try {
            oxNew(AdminController::class)->init();
        } catch (ResponseReady $responseReady) {
            $this->assertSame(
                Response::HTTP_FORBIDDEN,
                $responseReady->getResponse()->getStatusCode()
            );

            return;
        }

        $this->fail('Expected a ResponseReady signal stopping the unauthorized request');
    }
}
