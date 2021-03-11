<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\FileSystem\FileGenerator;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\CsvFileGenerator;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CsvFileGeneratorTest
 */
class CsvFileGeneratorTest extends TestCase
{
    use ContainerTrait;

    private $filename = __DIR__ . DIRECTORY_SEPARATOR . 'test.csv';

    /** @var Filesystem  */
    private $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->get('oxid_esales.symfony.file_system');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->filesystem->remove($this->filename);
    }

    public function testGenerate(): void
    {
        $csvGenerator = new CsvFileGenerator();

        $this->filesystem->touch($this->filename);

        $csvGenerator->generate($this->filename, [
            0 => [
                "Salutation" => "MR",
                "Name"       => "John"
            ]
        ]);

        $this->assertEquals("Salutation,Name\nMR,John\n", file_get_contents($this->filename));
    }
}
