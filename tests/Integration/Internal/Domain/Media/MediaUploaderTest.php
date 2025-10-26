<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Integration\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploader;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaPathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ImageHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class MediaUploaderTest extends TestCase
{
    use ProphecyTrait;

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

        $mediaPathResolver = $this->prophesize(ProductMediaPathResolverInterface::class);
        $resolvedPath = 'media/' . $fileName;
        $mediaPathResolver
            ->getRelativePath($fileName)
            ->willReturn($resolvedPath);
        $imageHandler = $this->prophesize(ImageHandlerInterface::class);
        $imageHandler
            ->upload($filePath, $resolvedPath)
            ->shouldBeCalledOnce();

        $uploader = new MediaUploader(
            $mediaPathResolver->reveal(),
            $imageHandler->reveal()
        );

        $result = $uploader->upload($uploadedFile);

        $this->assertSame($resolvedPath, (string)$result);
    }
}
