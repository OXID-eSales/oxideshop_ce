<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Validator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\SettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\ModuleSettingBooleanValidator;
use PHPUnit\Framework\TestCase;

#[CoversClass(ModuleSettingBooleanValidator::class)]
final class ShopModuleSettingBooleanValidatorTest extends TestCase
{
    public static function validationPassWithDataProvider(): array
    {
        return [
            ['true'],
            ['TRUE'],
            ['false'],
            [1],
            ['1'],
            ['0'],
            [0],
        ];
    }

    /**
     *
     * @param $value
     * @deprecated   since v6.4.0 (2019-06-10);This is not recommended values for use,
     *               only boolean values should be used.
     */
    #[DataProvider('validationPassWithDataProvider')]
    #[DoesNotPerformAssertions]
    public function testValidationPassWithBackwardsCompatibleValues(string|int $value): void
    {
        $this->executeValidationForBoolSetting($value);
    }

    public static function validationPassDataProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }

    #[DataProvider('validationPassDataProvider')]
    #[DoesNotPerformAssertions]
    public function testValidationPass(bool $value): void
    {
        $this->executeValidationForBoolSetting($value);
    }

    public static function validationFailsDataProvider(): array
    {
        return [
            ['any random value'],
            [''],
            [11],
        ];
    }

    /**
     * @param mixed $value
     */
    #[DataProvider('validationFailsDataProvider')]
    public function testValidationFails(string|int $value): void
    {
        $this->expectException(SettingNotValidException::class);
        $this->executeValidationForBoolSetting($value);
    }

    #[DoesNotPerformAssertions]
    public function testWhenStringTypeProvided(): void
    {
        $settings =
            [
                MetaDataProvider::METADATA_ID => 'test_id',
                MetaDataProvider::METADATA_SETTINGS => [
                    [
                    'type' => 'str', 'value' => 'String value'
                    ],
                ]
            ];
        $validator = new ModuleSettingBooleanValidator();

        $validator->validate($settings);
    }

    #[DoesNotPerformAssertions]
    public function testWhenNoTypeProvided(): void
    {
        $settings =
            [
                MetaDataProvider::METADATA_ID => 'test_id',
                MetaDataProvider::METADATA_SETTINGS => [
                    [
                        'value' => 'Any value'
                    ],
                ]
            ];
        $validator = new ModuleSettingBooleanValidator();

        $validator->validate($settings);
    }

    private function executeValidationForBoolSetting(string|int|bool $value): void
    {
        $settings =
            [
                MetaDataProvider::METADATA_ID => 'test_id',
                MetaDataProvider::METADATA_SETTINGS => [
                    [
                        'type' => 'bool', 'value' => $value
                    ],
                ]
            ];

        $validator = new ModuleSettingBooleanValidator();

        $validator->validate($settings);
    }
}
