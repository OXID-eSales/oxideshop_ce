<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\FileSystem\FileGenerator;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\CsvFileGenerator;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CsvFileGeneratorTest
 */
final class CsvFileGeneratorTest extends TestCase
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

    public function testGenerateIfDataExists(): void
    {
        $csvGenerator = new CsvFileGenerator();

        $this->filesystem->touch($this->filename);

        $csvGenerator->generate($this->filename, [
            ["Salutation", "Name"],
            ["MR", "John"]
        ]);

        $this->assertEquals("Salutation,Name\nMR,John\n", file_get_contents($this->filename));
    }

    public function testGenerateIfDataNotExists(): void
    {
        $csvGenerator = new CsvFileGenerator();

        $this->filesystem->touch($this->filename);

        $data = [
            [
                NewsletterRecipientsDataMapper::SALUTATION,
                NewsletterRecipientsDataMapper::FIRST_NAME,
                NewsletterRecipientsDataMapper::LAST_NAME,
                NewsletterRecipientsDataMapper::EMAIL,
                NewsletterRecipientsDataMapper::OPT_IN_STATE,
                NewsletterRecipientsDataMapper::COUNTRY,
                NewsletterRecipientsDataMapper::ASSIGNED_USER_GROUPS
            ]
        ];

        $csvGenerator->generate($this->filename, $data);
        $expected = "Salutation,Firstname,LastName,Email,\"Opt-In state\",Country,\"Assigned user groups\"\n";

        $this->assertEquals($expected, file_get_contents($this->filename));
    }
}
