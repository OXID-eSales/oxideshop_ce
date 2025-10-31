<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Html;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

interface HtmlSanitizerConfigFactoryInterface
{
    public function create(): HtmlSanitizerConfig;
}
