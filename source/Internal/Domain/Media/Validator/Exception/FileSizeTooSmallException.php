<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FileSizeLogic;

class FileSizeTooSmallException extends MediaValidationException
{
    public function __construct(private readonly int $actualBytes, private readonly int $minKb)
    {
        parent::__construct('File too small: ' . $actualBytes . ' bytes, min ' . $minKb . ' KB');
    }

    public function getActualBytes(): int
    {
        return $this->actualBytes;
    }

    public function getMinKb(): int
    {
        return $this->minKb;
    }

    public function getActualFormatted(): string
    {
        return (new FileSizeLogic())->getFileSize($this->actualBytes);
    }

    public function getMinFormatted(): string
    {
        return (new FileSizeLogic())->getFileSize($this->minKb * 1024);
    }
}
