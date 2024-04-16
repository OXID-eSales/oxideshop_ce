<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Validator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\ModuleIdNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\ModuleIdValidator;
use PHPUnit\Framework\TestCase;

#[CoversClass(ModuleIdValidator::class)]
final class ModuleIdValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidateWhenValid(): void
    {
        $metaData = [
            MetaDataProvider::METADATA_ID => 'some_id'
        ];

        $validator = new ModuleIdValidator();
        $validator->validate($metaData);
    }

    public static function validateInvalidIdProvidedDataProvider(): array
    {
        return [
            [''],
            [null],
        ];
    }

    /**
     * @param mixed $moduleId
     */
    #[DataProvider('validateInvalidIdProvidedDataProvider')]
    public function testValidateWhenInvalidIdProvided(?string $moduleId): void
    {
        $this->expectException(ModuleIdNotValidException::class);
        $metaData = [
            MetaDataProvider::METADATA_ID => $moduleId
        ];

        $validator = new ModuleIdValidator();
        $validator->validate($metaData);
    }

    public function testValidateWhenIdNotProvided(): void
    {
        $this->expectException(ModuleIdNotValidException::class);
        $metaData = [];

        $validator = new ModuleIdValidator();
        $validator->validate($metaData);
    }
}
