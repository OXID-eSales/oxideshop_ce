<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\ModuleIdNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\ModuleIdValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\ModuleIdValidator
 */
class ModuleIdValidatorTest extends TestCase
{

    public function testValidateWhenValid(): void
    {
        $metaData = [
            MetaDataProvider::METADATA_ID => 'some_id'
        ];

        $validator = new ModuleIdValidator();
        $validator->validate($metaData);
    }

    public function validateInvalidIdProvidedDataProvider(): array
    {
        return [
            [''],
            [null],
        ];
    }

    /**
     * @param mixed $moduleId
     * @dataProvider validateInvalidIdProvidedDataProvider
     */
    public function testValidateWhenInvalidIdProvided($moduleId): void
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
