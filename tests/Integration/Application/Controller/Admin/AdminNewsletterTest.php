<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminNewsletter;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class AdminNewsletterTest extends IntegrationTestCase
{
    public function testExportReturnsRecipientsAsCsvDownload(): void
    {
        $response = oxNew(AdminNewsletter::class)->export();

        ob_start();
        $response->sendContent();
        $csv = (string) ob_get_clean();

        $this->assertSame('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame(
            'attachment; filename=Export_user_recipient_status_' . date('Y-m-d') . '.csv',
            $response->headers->get('Content-Disposition')
        );
        $this->assertStringStartsWith('Salutation,', $csv);
    }
}
