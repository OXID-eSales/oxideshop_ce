<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataSchemataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class MetaDataSchemataProviderTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Service
 */
class MetaDataSchemataProviderTest extends TestCase
{
    private $metaDataSchemata;
    private $schemaVersion20;
    private $schemaVersion21;

    public function testGetMetaDataSchemata()
    {
        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);

        $actualSchemata = $metaDataSchemata->getMetaDataSchemata();

        $this->assertEquals($this->metaDataSchemata, $actualSchemata);
    }

    public function testGetMetadataSchemaForVersion()
    {
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);
        $actualSchema20 = $metaDataSchema->getMetaDataSchemaForVersion('2.0');
        $actualSchema21 = $metaDataSchema->getMetaDataSchemaForVersion('2.1');

        $this->assertEquals($this->schemaVersion20, $actualSchema20);
        $this->assertEquals($this->schemaVersion21, $actualSchema21);
    }

    /**
     * @expectedException  \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException
     */
    public function testGetFlippedMetadataSchemaForVersionThrowsExceptionOnUnsupportedVersion()
    {
        $unsupportedVersion = '0.0';
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);

        $metaDataSchema->getFlippedMetaDataSchemaForVersion($unsupportedVersion);
    }

    public function testGetFlippedMetadataSchemaForVersion()
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

    /**
     * @expectedException  \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException
     */
    public function testGetMetadataSchemaForVersionThrowsExceptionOnUnsupportedVersion()
    {
        $unsupportedVersion = '0.0';
        $metaDataSchema = new MetaDataSchemataProvider($this->metaDataSchemata);

        $metaDataSchema->getMetaDataSchemaForVersion($unsupportedVersion);
    }

    protected function setUp()
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
