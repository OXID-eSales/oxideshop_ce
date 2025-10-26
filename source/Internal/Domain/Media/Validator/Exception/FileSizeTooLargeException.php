<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Validator\Exception;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\FileSizeLogic;

class FileSizeTooLargeException extends MediaValidationException
{
    public function __construct(private readonly int $actualBytes, private readonly int $maxKb)
    {
        parent::__construct('File too large: ' . $actualBytes . ' bytes, max ' . $maxKb . ' KB');
    }

    public function getActualBytes(): int
    {
        return $this->actualBytes;
    }

    public function getMaxKb(): int
    {
        return $this->maxKb;
    }

    public function getActualFormatted(): string
    {
        return (new FileSizeLogic())->getFileSize($this->actualBytes);
    }

    public function getMaxFormatted(): string
    {
        return (new FileSizeLogic())->getFileSize($this->maxKb * 1024);
    }
}
