<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Test\Integration\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\SchemaValidator;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;

class SchemaValidatorTest extends TestCase
{
    use ContainerTrait;

    private SchemaValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $container = (new TestContainerFactory())->create();
        $container->compile();
        $this->validator = new SchemaValidator(
            $this->get(MetaDataSchemaDaoInterface::class)
        );
    }

    public function testValidateWithInvalidMetadataVersion(): void
    {
        $metaData = [
            'metaDataVersion' => 'wrong-version',
            'moduleData' => [],
        ];

        $this->expectException(UnsupportedMetaDataVersionException::class);

        $this->validator->validate($metaData);
    }

    public function testValidateWithInvalidModuleDataKey(): void
    {
        $metaData = [
            'metaDataVersion' => '2.1',
            'moduleData' => [
                'wrong-key-name' => 123,
            ],
        ];

        $this->expectException(UnsupportedMetaDataKeyException::class);

        $this->validator->validate($metaData);
    }

    public function testValidateWithValidMetaData(): void
    {
        $metaData = [
            'metaDataVersion' => '2.1',
            'moduleData' => [],
        ];

        $this->validator->validate($metaData);
    }
}
