<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaUploaderInterface
{
    public function upload(UploadedFile $uploadedFile): MediaPath;
}
