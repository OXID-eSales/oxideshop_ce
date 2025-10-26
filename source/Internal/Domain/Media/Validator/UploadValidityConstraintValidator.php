<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception\UploadInvalidException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class UploadValidityConstraintValidator implements MediaConstraintValidatorInterface
{
    public function validate(UploadedFile $uploadedFile): void
    {
        if (!$uploadedFile->isValid()) {
            throw new UploadInvalidException(
                $uploadedFile->getError()
            );
        }
    }
}
