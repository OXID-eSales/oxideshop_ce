<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Validator;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use stdClass;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidator;
use PHPUnit\Framework\TestCase;

final class MetaDataSchemaValidatorTest extends TestCase
{
    private array $metaDataSchemata;
    private $metaDataSchemaVersion20;
    private $metaDataSchemaVersion21;

    public function testValidateThrowsExceptionOnUnsupportedMetaDataVersion(): void
    {
        $this->expectException(UnsupportedMetaDataVersionException::class);
        $metaDataToValidate = [];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $this->expectException(UnsupportedMetaDataVersionException::class);
        $validator->validate('path/to/metadata.php', '1.2', $metaDataToValidate);
    }

    public function testValidateUnsupportedMetaDataKey(): void
    {
        $this->expectException(UnsupportedMetaDataKeyException::class);

        $metaDataToValidate = [
            'somePluginDirectories' => [],
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    /**
     * This test covers metaData sections like 'blocks' or 'settings', which have their own well defined subKeys
     */
    public function testValidateUnsupportedMetaDataSubKey(): void
    {
        $this->expectException(UnsupportedMetaDataKeyException::class);

        $metaDataToValidate = [
            '20only'   => 'value',
            'section1' => [
                [
                    'subkey1' => 'value1',
                    'subkey2' => 'value1',
                ],
                [
                    'subkey1'        => 'value2',
                    'unsupportedKey' => 'value2',
                ],
            ]
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    /**
     * This test covers metaData sections like 'extend', or 'templates', which have their custom subKeys
     */
    #[DoesNotPerformAssertions]
    public function testExcludedSectionItemValidation(): void
    {
        $metaDataToValidate = [
            '20only'                                             => 'value',
            'section1'                                           => [
                [
                    'subKey1' => 'value1',
                    'subKey2' => 'value1',
                ],
                [
                    'subKey1' => 'value2',
                    'subKey2' => 'value2',
                ],
            ],
            MetaDataProvider::METADATA_EXTEND                    => [
                'excludedsubkey1' => 'value2',
                'excludedsubkey2' => 'value2',
            ]
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    public function testValidateIsCaseSensitive(): void
    {
        $this->expectException(UnsupportedMetaDataKeyException::class);

        $metaDataToValidate = [
            '20ONLY'   => 'value', // This UPPERCASE key will not validate
            'section1' => [
                [
                    'subkey1' => 'value1',
                    'subkey2' => 'value1',
                ],
                [
                    'subkey1' => 'value2',
                    'subkey2' => 'value2',
                ],
            ]
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    public function testValidateThrowsExceptionOnUnsupportedMetaDataValueType(): void
    {
        $this->expectException(UnsupportedMetaDataValueTypeException::class);
        $metaDataToValidate = [
            '20only' => new stdClass(),
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $this->expectException(UnsupportedMetaDataValueTypeException::class);
        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    #[DoesNotPerformAssertions]
    public function testValidateThrowsNoExceptionOnIncompleteFirstLevel(): void
    {
        $metaDataToValidate = [
            // missing '20only'        => 'value',
            'section1' => [
                [
                    'subKey1' => 'value1',
                    'subKey2' => 'value1'
                ],
            ]
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    #[DoesNotPerformAssertions]
    public function testValidateThrowsNoExceptionOnIncompleteSecondLevel(): void
    {
        $metaDataToValidate = [
            '20only'   => 'value',
            'section1' => [
                [
                    // missing 'subKey1' => 'value1',
                    'subKey2' => 'value1'
                ],
            ]
        ];

        $metaDataSchemata = new MetaDataSchemataProvider($this->metaDataSchemata);
        $validator = new MetaDataSchemaValidator($metaDataSchemata);

        $validator->validate('path/to/metadata.php', '2.0', $metaDataToValidate);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->metaDataSchemaVersion20 = [
            '20only',
            'section1' =>
                ['subKey1',
                 'subKey2',
                ],
            'extend',
        ];
        $this->metaDataSchemaVersion21 = [
            '21only',
            'section1' =>
                ['subKey1',
                 'subKey2',
                ],
            'extend',
        ];
        $this->metaDataSchemata = [
            '2.0' => $this->metaDataSchemaVersion20,
            '2.1' => $this->metaDataSchemaVersion21,
        ];
    }
}
