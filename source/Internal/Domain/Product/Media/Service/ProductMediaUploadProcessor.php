<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploaderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\MediaConstraintValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Factory\ProductMediaFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ProductMediaUploadProcessor implements ProductMediaUploadProcessorInterface
{
    public function __construct(
        private MediaConstraintValidatorInterface $mediaConstraintValidator,
        private MediaUploaderInterface $mediaUploader,
        private ProductMediaFactoryInterface $productMediaFactory,
        private ProductMediaPathResolverInterface $productMediaPathResolver,
    ) {
    }

    public function process(Id $productId, UploadedFile $uploadedFile): ProductMedia
    {
        $this->mediaConstraintValidator->validate($uploadedFile);

        $targetPath = $this->productMediaPathResolver->resolve(
            (string) $productId,
            $uploadedFile->getClientOriginalName()
        );

        $this->mediaUploader->uploadTo($uploadedFile, $targetPath);

        return $this->productMediaFactory->create(
            $productId,
            $targetPath,
            new MediaType($uploadedFile->getClientMimeType())
        );
    }
}
