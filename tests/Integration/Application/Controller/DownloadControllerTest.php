<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\DownloadController;
use OxidEsales\Eshop\Application\Model\File;
use OxidEsales\Eshop\Application\Model\OrderFile;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadControllerTest extends IntegrationTestCase
{
    private const ORDER_FILE_ID = 'download-controller-test';
    private const FILE_NAME = 'manual.txt';
    private const FILE_CONTENT = 'complete file content';

    private string $downloadDirectory;
    private mixed $configuredDownloadDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $this->downloadDirectory = Path::join(sys_get_temp_dir(), uniqid('downloads-', true));
        (new Filesystem())->dumpFile(Path::join($this->downloadDirectory, 'uploads', self::FILE_NAME), self::FILE_CONTENT);
        $this->configuredDownloadDirectory = Registry::getConfig()->getConfigParam('sDownloadsDir');
        Registry::getConfig()->setConfigParam('sDownloadsDir', $this->downloadDirectory);
    }

    public function tearDown(): void
    {
        $_GET = [];
        Registry::getConfig()->setConfigParam('sDownloadsDir', $this->configuredDownloadDirectory);
        (new Filesystem())->remove($this->downloadDirectory);

        parent::tearDown();
    }

    public function testRenderDeliversCompleteFileForRangeRequest(): void
    {
        $this->givenOrderedFile();
        $_GET['sorderfileid'] = self::ORDER_FILE_ID;

        $response = oxNew(DownloadController::class)->render();
        $response->prepare(Request::create('/', server: ['HTTP_RANGE' => 'bytes=0-3']));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame((string) strlen(self::FILE_CONTENT), $response->headers->get('Content-Length'));
        $this->assertSame(self::FILE_CONTENT, $this->captureStreamedContent($response));
    }

    public function testRenderCountsDownload(): void
    {
        $this->givenOrderedFile();
        $_GET['sorderfileid'] = self::ORDER_FILE_ID;

        oxNew(DownloadController::class)->render();

        $orderFile = oxNew(OrderFile::class);
        $orderFile->load(self::ORDER_FILE_ID);
        $this->assertSame(1, (int) $orderFile->oxorderfiles__oxdownloadcount->value);
    }

    public function testRenderRedirectsUnknownDownloadLinkToAccountDownloads(): void
    {
        $_GET['sorderfileid'] = 'unknown-order-file';

        $this->expectException(RedirectException::class);

        oxNew(DownloadController::class)->render();
    }

    private function givenOrderedFile(): void
    {
        $file = oxNew(File::class);
        $file->setId('download-controller-test-file');
        $file->oxfiles__oxfilename = new Field(self::FILE_NAME);
        $file->save();

        $orderFile = oxNew(OrderFile::class);
        $orderFile->setId(self::ORDER_FILE_ID);
        $orderFile->oxorderfiles__oxfileid = new Field($file->getId());
        $orderFile->oxorderfiles__oxfilename = new Field(self::FILE_NAME);
        $orderFile->oxorderfiles__oxlinkexpirationtime = new Field(24);
        $orderFile->oxorderfiles__oxvaliduntil = new Field(date('Y-m-d H:i:s', strtotime('+1 day')));
        $orderFile->save();
    }

    private function captureStreamedContent(Response $response): string
    {
        ob_start();
        $response->sendContent();

        return (string) ob_get_clean();
    }
}
