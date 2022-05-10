<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemaDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use PHPUnit\Framework\TestCase;

final class MetaDataSchemaDaoTest extends TestCase
{
    public function testGetWithUnsupportedSchemaVersionWillThrow(): void
    {
        $version = 'unsupported-version';
        $supportedMetadataSchemas = [
            '2.1' => []
        ];

        $this->expectException(UnsupportedMetaDataVersionException::class);

        (new MetaDataSchemaDao($supportedMetadataSchemas))->get($version);
    }

    public function testGetWillReturnExpectedDataObject(): void
    {
        $version = '2.1';
        $keys = ['key-1', 'key-2', 'section-1', 'key-3'];
        $sections = ['section-1' => ['section-key-1', 'section-key-2']];
        $supportedMetadataSchemas = [
            $version => [
                'key-1',
                'key-2',
                'section-1' => ['section-key-1', 'section-key-2'],
                'key-3',
            ]
        ];

        $dataObject = (new MetaDataSchemaDao($supportedMetadataSchemas))->get($version);

        $this->assertEquals($keys, $dataObject->getKeys());
        $this->assertEquals($sections, $dataObject->getSections());
        $this->assertTrue($dataObject->hasKey('key-3'));
        $this->assertFalse($dataObject->hasKey('key-4'));
        $this->assertTrue($dataObject->hasSectionKey('section-1', 'section-key-2'));
        $this->assertFalse($dataObject->hasSectionKey('section-1', 'section-key-3'));
    }
}
