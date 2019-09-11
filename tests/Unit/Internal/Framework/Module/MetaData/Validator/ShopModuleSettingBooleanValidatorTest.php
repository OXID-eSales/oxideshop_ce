<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\SettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\ModuleSettingBooleanValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\ModuleSettingBooleanValidator
 */
class SettingBooleanValidatorTest extends TestCase
{
    public function validationPassWithDataProvider(): array
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
     * @dataProvider validationPassWithDataProvider
     *
     * @param $value
     *
     * @deprecated   since v6.4.0 (2019-06-10);This is not recommended values for use,
     *               only boolean values should be used.
     */
    public function testValidationPassWithBackwardsCompatibleValues($value)
    {
        $this->executeValidationForBoolSetting($value);
    }

    public function validationPassDataProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }

    /**
     * @param bool $value
     * @dataProvider validationPassDataProvider
     */
    public function testValidationPass(bool $value)
    {
        $this->executeValidationForBoolSetting($value);
    }

    public function validationFailsDataProvider()
    {
        return [
            ['any random value'],
            [''],
            [11],
        ];
    }

    /**
     * @param mixed $value
     * @dataProvider validationFailsDataProvider
     */
    public function testValidationFails($value)
    {
        $this->expectException(SettingNotValidException::class);
        $this->executeValidationForBoolSetting($value);
    }

    public function testWhenStringTypeProvided()
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

    public function testWhenNoTypeProvided()
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

    private function executeValidationForBoolSetting($value): void
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
