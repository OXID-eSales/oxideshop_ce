<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaPathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ImageHandlerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class MediaUploader implements MediaUploaderInterface
{
    public function __construct(
        private ProductMediaPathResolverInterface $mediaPathResolver,
        private ImageHandlerInterface $imageHandler,
    ) {
    }

    public function upload(UploadedFile $uploadedFile): MediaPath
    {
        $mediaPath = new MediaPath(
            $this->mediaPathResolver->getRelativePath($uploadedFile->getClientOriginalName())
        );
        $this->imageHandler->upload(
            $uploadedFile->getPathname(),
            (string)$mediaPath
        );

        return $mediaPath;
    }
}
