<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploaderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\MediaConstraintValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ProductMediaUploadProcessor implements ProductMediaUploadProcessorInterface
{
    public function __construct(
        private MediaConstraintValidatorInterface $mediaConstraintValidator,
        private MediaUploaderInterface $mediaUploader,
        private ProductMediaFactoryInterface $productMediaFactory,
    ) {
    }

    public function process(Id $productId, UploadedFile $uploadedFile): ProductMedia
    {
        $this->mediaConstraintValidator->validate($uploadedFile);

        return $this->productMediaFactory->create(
            $productId,
            $this->mediaUploader->upload($uploadedFile),
            new MediaType($uploadedFile->getClientMimeType())
        );
    }
}
