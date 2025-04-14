<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\MediaConstraintValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductMediaValidator implements MediaConstraintValidatorInterface
{
    public function __construct(
        private readonly iterable $validators
    ) {
    }

    public function validate(UploadedFile $uploadedFile): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($uploadedFile);
        }
    }
}
