<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\UtilsFile;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

final class UtilsFileTest extends IntegrationTestCase
{
    use ContainerTrait;

    #[DoesNotPerformAssertions]
    public function testProcessFileWithDefaultConfiguration(): void
    {
        $filename = 'some_file.jpg';
        $_FILES[$filename]['name'] = $filename;
        $_FILES[$filename]['tmp_name'] = uniqid('some-file-', true);

        (new UtilsFile())->processFile($filename, '');
    }

    public function testProcessFileWithDefaultConfigurationAndDisallowedFileExtension(): void
    {
        $filename = 'some_file.exe';
        $_FILES[$filename]['name'] = $filename;
        $_FILES[$filename]['tmp_name'] = uniqid('some-file-', true);

        $this->expectException(StandardException::class);
        $this->expectExceptionMessage('EXCEPTION_NOTALLOWEDTYPE');

        (new UtilsFile())->processFile($filename, '');
    }

    #[DoesNotPerformAssertions]
    public function testProcessFileWithModifiedConfig(): void
    {
        $filename = 'some_file.exe';
        $_FILES[$filename]['name'] = $filename;
        $_FILES[$filename]['tmp_name'] = uniqid('some-file-', true);
        $this->setAllowedFileExtensions(['exe']);

        (new UtilsFile())->processFile($filename, '');
    }

    public function testProcessFileWithModifiedConfigAndDisallowedFileExtension(): void
    {
        $filename = 'some_file.jpg';
        $_FILES[$filename]['name'] = $filename;
        $_FILES[$filename]['tmp_name'] = uniqid('some-file-', true);
        $this->setAllowedFileExtensions([]);

        $this->expectException(StandardException::class);
        $this->expectExceptionMessage('EXCEPTION_NOTALLOWEDTYPE');

        (new UtilsFile())->processFile($filename, '');
    }

    private function setAllowedFileExtensions(array $parameter): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_allowed_uploaded_types', $parameter);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();
    }
}
