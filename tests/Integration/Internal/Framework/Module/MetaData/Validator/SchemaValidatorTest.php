<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Test\Integration\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidatorInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;

class SchemaValidatorTest extends TestCase
{
    use ContainerTrait;

    private MetaDataSchemaValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $container = (new TestContainerFactory())->create();
        $container->compile();
        $this->validator = $this->get(MetaDataSchemaValidatorInterface::class);
    }

    public function testValidateWithInvalidMetadataVersion(): void
    {
        $this->expectException(UnsupportedMetaDataVersionException::class);

        $this->validator->validate('some-path', 'wrong-version', []);
    }

    public function testValidateWithInvalidModuleDataKey(): void
    {
        $this->expectException(UnsupportedMetaDataKeyException::class);

        $this->validator->validate('some-path', '2.1', ['wrong-key-name' => 123]);
    }

    public function testValidateWithValidMetaData(): void
    {
        $this->validator->validate('some-path', '2.1', []);
    }
}
