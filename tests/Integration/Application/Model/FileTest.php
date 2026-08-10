<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\File;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class FileTest extends IntegrationTestCase
{
    private Filesystem $filesystem;
    private string $downloadsDir;
    private string $siblingDir;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $testRoot = Path::join((new ProjectRootLocator())->getProjectRoot(), 'var', 'file-traversal-test');
        $this->downloadsDir = Path::join($testRoot, 'downloads');
        $this->siblingDir = Path::join($testRoot, 'downloads-marketing-export');

        $this->filesystem->mkdir(Path::join($this->downloadsDir, 'uploads'));
        $this->filesystem->mkdir($this->siblingDir);

        Registry::getConfig()->getConfigParam('sDownloadsDir');
        Registry::getConfig()->setConfigParam('sDownloadsDir', $this->downloadsDir);
    }

    public function testLegitimateManuallyPlacedFileIsCorrectlyRecognized(): void
    {
        $priceList = Path::join($this->downloadsDir, 'uploads', 'price-list.pdf');
        $this->filesystem->dumpFile($priceList, '%PDF-1.4');

        $file = $this->createManuallyReferencedFile('price-list.pdf');

        $this->assertTrue($file->exist());
        $this->assertTrue($file->isUnderDownloadFolder());
    }

    public function testGetStoreLocationBuildsAnEscapingPathFromAnUnvalidatedFilename(): void
    {
        $file = $this->createManuallyReferencedFile('../../downloads-marketing-export/leads.csv');

        $resolvedStoreLocation = Path::canonicalize($file->getStoreLocation());

        $this->assertSame(Path::join($this->siblingDir, 'leads.csv'), $resolvedStoreLocation);
    }

    public function testIsUnderDownloadFolderIncorrectlyAcceptsATraversalIntoASiblingDirectory(): void
    {
        $leads = Path::join($this->siblingDir, 'leads.csv');
        $this->filesystem->dumpFile($leads, 'name,email');

        $file = $this->createManuallyReferencedFile('../../downloads-marketing-export/leads.csv');

        $this->assertTrue($file->isUnderDownloadFolder());
    }

    public function testResolvedStoreLocationContentComesFromTheSiblingDirectory(): void
    {
        $leads = Path::join($this->siblingDir, 'leads.csv');
        $this->filesystem->dumpFile($leads, 'name,email' . PHP_EOL . 'jane,jane@example.com');

        $file = $this->createManuallyReferencedFile('../../downloads-marketing-export/leads.csv');

        $this->assertTrue($file->exist());
        $this->assertTrue($file->isUnderDownloadFolder());
        $this->assertSame(file_get_contents($leads), file_get_contents($file->getStoreLocation()));
    }

    public function testTheMediaPathTraversalCheckWouldHaveRejectedThisFilenameOutright(): void
    {
        $filename = '../../downloads-marketing-export/leads.csv';

        $this->assertMatchesRegularExpression('~(^|[\\\\/])\.\.([\\\\/]|$)~', $filename);
    }

    private function createManuallyReferencedFile(string $filename): File
    {
        $file = oxNew(File::class);
        $file->assign([
            'oxfiles__oxfilename' => $filename,
            'oxfiles__oxstorehash' => '',
        ]);

        return $file;
    }
}
