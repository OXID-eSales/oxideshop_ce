<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Html;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface as SymfonyHtmlSanitizerInterface;

readonly class HtmlSanitizer implements HtmlSanitizerInterface
{
    public function __construct(private SymfonyHtmlSanitizerInterface $sanitizer)
    {
    }

    public function sanitize(string $html): string
    {
        return $this->sanitizer->sanitize($html);
    }
}
