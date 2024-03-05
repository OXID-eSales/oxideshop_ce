<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use PHPUnit\Framework\TestCase;

/**
 * Class MetaDataSchemataProviderTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Dao
 */
final class MetaDataSchemataProviderTest extends TestCase
{
    private array $metaDataSchemata;
    private $schemaVersion20;
    private $schemaVersion21;

    public function testGetMetaDataSchemata(): void
    {
        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);

        $actualSchemata = $metaDataSchemata->getMetaDataSchemata();

        $this->assertEquals($this->metaDataSchemata, $actualSchemata);
    }

    public function testGetMetadataSchemaForVersion(): void
    {
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);
        $actualSchema20 = $metaDataSchema->getMetaDataSchemaForVersion('2.0');
        $actualSchema21 = $metaDataSchema->getMetaDataSchemaForVersion('2.1');

        $this->assertEquals($this->schemaVersion20, $actualSchema20);
        $this->assertEquals($this->schemaVersion21, $actualSchema21);
    }

    public function testGetFlippedMetadataSchemaForVersionThrowsExceptionOnUnsupportedVersion(): void
    {
        $this->expectException(UnsupportedMetaDataVersionException::class);
        $unsupportedVersion = '0.0';
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);

        $this->expectException(UnsupportedMetaDataVersionException::class);
        $metaDataSchema->getFlippedMetaDataSchemaForVersion($unsupportedVersion);
    }

    public function testGetFlippedMetadataSchemaForVersion(): void
    {
        $expectedSchema20 = [
            '20only'    => 0,
            'subSchema' => [
                'subKey1' => 0,
                'subKey2' => 1
            ],
        ];
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);

        $actualSchema20 = $metaDataSchema->getFlippedMetaDataSchemaForVersion('2.0');

        $this->assertSame($expectedSchema20, $actualSchema20);
    }

    public function testGetMetadataSchemaForVersionThrowsExceptionOnUnsupportedVersion(): void
    {
        $this->expectException(UnsupportedMetaDataVersionException::class);
        $unsupportedVersion = '0.0';
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);

        $this->expectException(UnsupportedMetaDataVersionException::class);
        $metaDataSchema->getMetaDataSchemaForVersion($unsupportedVersion);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->schemaVersion20 = [
            '20only',
            'subSchema' =>
                ['subKey1',
                 'subKey2',
                ],
        ];
        $this->schemaVersion21 = [
            '21only',
            'subSchema' =>
                ['subKey1',
                 'subKey2',
                ],
        ];
        $this->metaDataSchemata = [
            '2.0' => $this->schemaVersion20,
            '2.1' => $this->schemaVersion21,
        ];
    }
}
