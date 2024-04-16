<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Validator;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use stdClass;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidator;
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
        ],
    ];

    #[DoesNotPerformAssertions]
    public function testValidateWithMinimalValidStructure(): void
    {
        $this->getValidator()->validate('', '2.1', []);
    }

    public function testValidateThrowsExceptionOnUnsupportedMetaDataVersion(): void
    {
        $this->expectException(UnsupportedMetaDataVersionException::class);

        $this->getValidator()->validate('', '1.2', []);
    }

    public function testValidateUnsupportedMetaDataKey(): void
    {
        $metaData = [
            'moduleData' => [
                'unsupported-key' => [],
            ],
        ];

        $this->expectException(UnsupportedMetaDataKeyException::class);

        $this->getValidator()->validate('', '2.1', $metaData);
    }

    public function testValidateWithUnsupportedMetaDataSubKey(): void
    {
        $metadata = [
            'key-in-schema-1' => 'value',
            'section-in-schema-1' => [
                [
                    'sub-key-in-schema-1' => 'value1',
                    'sub-key-in-schema-2' => 'value1',
                ],
                [
                    'sub-key1' => 'value2',
                    'unsupported-sub-key' => 'value2',
                ],
            ],
        ];

        $this->expectException(UnsupportedMetaDataKeyException::class);

        $this->getValidator()->validate('', '2.1', $metadata);
    }

    #[DoesNotPerformAssertions]
    public function testValidateWithSectionExcludedFromValidation(): void
    {
        $sectionExcludedFromValidation = MetaDataProvider::METADATA_EXTEND;
        $metadata = [
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
        ];

        $this->getValidator()->validate('', '2.1', $metadata);
    }

    public function testValidateWithKeyInWrongCaseWillFailValidation(): void
    {
        $this->expectException(UnsupportedMetaDataKeyException::class);

        $metadata = [
            'KEY-IN-SCHEMA-1' => 'value',
        ];

        $this->getValidator()->validate('', '2.1', $metadata);
    }

    public function testValidateWithNonScalarValueWillThrowException(): void
    {
        $unsupportedData = new stdClass();
        $metadata = [
            'key-in-schema-1' => $unsupportedData,
        ];

        $this->expectException(UnsupportedMetaDataValueTypeException::class);

        $this->getValidator()->validate('', '2.1', $metadata);
    }

    private function getValidator(): MetaDataSchemaValidator
    {
        return new MetaDataSchemaValidator(
            new MetaDataSchemataProvider(
                $this->metaDataSchema
            )
        );
    }
}
