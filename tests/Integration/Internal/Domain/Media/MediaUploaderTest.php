<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploader;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ImageHandlerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class MediaUploaderTest extends TestCase
{
    public function testUploadReturnsMediaPath(): void
    {
        $fileName = 'file.jpg';
        $filePath = tempnam(sys_get_temp_dir(), 'upl_');
        file_put_contents($filePath, 'some content');

        $uploadedFile = new UploadedFile(
            $filePath,
            $fileName,
            'image/jpeg',
            null,
            true
        );

        $imageHandler = $this->createMock(ImageHandlerInterface::class);
        $imageHandler
            ->expects($this->once())
            ->method('upload')
            ->with($filePath, 'media/' . $fileName);

        $uploader = new MediaUploader($imageHandler);

        $target = new MediaPath('media/' . $fileName);
        $result = $uploader->uploadTo($uploadedFile, $target);

        $this->assertSame('media/' . $fileName, (string)$result);
    }
}
