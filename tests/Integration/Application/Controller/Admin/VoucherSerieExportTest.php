<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieExport;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;

final class VoucherSerieExportTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $exportFile;

    public function setUp(): void
    {
        parent::setUp();

        Registry::getSession()->setVariable('sExportFileName', 'voucher-export-test');
        $this->exportFile = Path::join(
            $this->getParameter('oxid_esales.shop_source_directory'),
            'export',
            'voucher-export-test'
        );
    }

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->exportFile);

        parent::tearDown();
    }

    public function testDownloadReturnsExportFileAsAttachment(): void
    {
        (new Filesystem())->dumpFile($this->exportFile, "voucher-1\nvoucher-2\n");

        $response = oxNew(VoucherSerieExport::class)->download();

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $this->assertSame("voucher-1\nvoucher-2\n", $content);
        $this->assertSame('attachment; filename=vouchers.csv', $response->headers->get('Content-Disposition'));
    }

    public function testDownloadWithoutExportFileReturnsNotFound(): void
    {
        $this->assertSame(Response::HTTP_NOT_FOUND, oxNew(VoucherSerieExport::class)->download()->getStatusCode());
    }
}
