<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\FileSystem\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator\FileValidator;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator\FileValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator\ImageValidationException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class FileValidatorTest extends IntegrationTestCase
{
    private FileValidatorInterface $fileValidator;

    public function setUp(): void
    {
        parent::setUp();

        $mimeTypes = $this->get('oxid_esales.symfony.mime_types');
        $this->fileValidator = new FileValidator($mimeTypes);
    }

    public function testImageValid(): void
    {
        $this->assertTrue(
            $this->fileValidator->validateImage($this->getFilePath('image.png'))
        );
    }

    public function testImageWrongExtension(): void
    {
        $this->assertFalse(
            $this->fileValidator->validateImage($this->getFilePath('fake_image.php'))
        );
    }

    public function testImageWrongType(): void
    {
        $this->assertFalse(
            $this->fileValidator->validateImage($this->getFilePath('fake_image.png'))
        );
    }

    public function testImageNotExist(): void
    {
        $this->expectException(ImageValidationException::class);

        $this->fileValidator->validateImage($this->getFilePath('noimagepath'));
    }

    private function getFilePath(string $fileName): string
    {
        return Path::join(__DIR__, 'Fixtures/images', $fileName);
    }
}
