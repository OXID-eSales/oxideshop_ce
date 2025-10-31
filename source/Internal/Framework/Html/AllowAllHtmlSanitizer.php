<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Html;

readonly class AllowAllHtmlSanitizer implements HtmlSanitizerInterface
{
    public function sanitize(string $html): string
    {
        return $html;
    }
}
