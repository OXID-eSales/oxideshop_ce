<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidator;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\SchemaValidator;
use PHPUnit\Framework\TestCase;

final class SchemaValidatorTest extends TestCase
{
    private array $metaDataSchema = [
        '2.1' => [
            'key-in-schema-1',
            'section-in-schema-1' =>
                [
                    'sub-key-in-schema-1',
                    'sub-key-in-schema-2',
                ],
            'extend' => [],
            'templates' => [],
        ],
    ];

    public function testValidateWithMinimalValidStructure(): void
    {
        $metadata = [
            'metaDataVersion' => '2.1',
            'moduleData' => [],
        ];

        $this->getValidator()->validate($metadata);
    }

    public function testValidateThrowsExceptionOnUnsupportedMetaDataVersion(): void
    {
        $metaData = [
            'metaDataVersion' => '1.2',
            'moduleData' => [],
        ];

        $this->expectException(UnsupportedMetaDataVersionException::class);

        $this->getValidator()->validate($metaData);
    }

    public function testValidateUnsupportedMetaDataKey(): void
    {
        $metaData = [
            'metaDataVersion' => '2.1',
            'moduleData' => [
                'unsupported-key' => [],
            ],
        ];

        $this->expectException(UnsupportedMetaDataKeyException::class);

        $this->getValidator()->validate($metaData);
    }

    public function testValidateWithUnsupportedMetaDataSubKey(): void
    {
        $metadata = [
            'metaDataVersion' => '2.1',
            'moduleData' => [
                'key-in-schema-1'   => 'value',
                'section-in-schema-1' => [
                    [
                        'sub-key-in-schema-1' => 'value1',
                        'sub-key-in-schema-2' => 'value1',
                    ],
                    [
                        'sub-key1'        => 'value2',
                        'unsupported-sub-key' => 'value2',
                    ],
                ],
            ],
        ];

        $this->expectException(UnsupportedMetaDataKeyException::class);

        $this->getValidator()->validate($metadata);
    }

    public function testValidateWithSectionExcludedFromValidation(): void
    {
        $sectionExcludedFromValidation = MetaDataProvider::METADATA_EXTEND;
        $metadata = [
            'metaDataVersion' => '2.1',
            'moduleData' => [
                'key-in-schema-1' => 'value',
                'section-in-schema-1' => [
                    [
                        'sub-key-in-schema-1' => 'value1',
                        'sub-key-in-schema-2' => 'value1',
                    ],
                ],
                $sectionExcludedFromValidation => [
                    'some-key' => 'value1',
                    'some-key-2' => ['some-sub-key-1' => 'some-value'],
                ],
            ],
        ];

        $this->getValidator()->validate($metadata);
    }

    public function testValidateWithKeyInWrongCaseWillFailValidation(): void
    {
        $this->expectException(UnsupportedMetaDataKeyException::class);

        $metadata = [
            'metaDataVersion' => '2.1',
            'moduleData' => [
                'KEY-IN-SCHEMA-1' => 'value',
            ],
        ];

        $this->getValidator()->validate($metadata);
    }

    public function testValidateWithNonScalarValueWillThrowException(): void
    {
        $unsupportedData = new \stdClass();
        $metadata = [
            'metaDataVersion' => '2.1',
            'moduleData' => [
                'key-in-schema-1' => $unsupportedData,
            ],
        ];

        $this->expectException(UnsupportedMetaDataValueTypeException::class);

        $this->getValidator()->validate($metadata);
    }

    private function getValidator(): SchemaValidator
    {
        return new SchemaValidator(
            new MetaDataSchemaValidator(
                new MetaDataSchemataProvider(
                    $this->metaDataSchema
                )
            )
        );
    }
}
