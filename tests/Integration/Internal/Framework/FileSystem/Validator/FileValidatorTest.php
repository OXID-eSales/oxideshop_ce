<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\FileSystem\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator\FileValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\Validator\ImageValidationException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class FileValidatorTest extends TestCase
{
    use ContainerTrait;

    public function testImageValid(): void
    {
        $this->assertTrue($this->isValidImage('image.png'));
    }

    public function testImageWrongExtension(): void
    {
        $this->assertFalse($this->isValidImage('fake_image.php'));
    }

    public function testImageWrongType(): void
    {
        $this->assertFalse($this->isValidImage('fake_image.png'));
    }

    public function testImageNotExist(): void
    {
        $this->expectException(ImageValidationException::class);

        $this->isValidImage('no_image_path');
    }

    private function isValidImage(string $fileName): bool
    {
        return $this
            ->get(FileValidatorInterface::class)
            ->validateImage(Path::join(__DIR__, 'Fixtures', $fileName));
    }
}
