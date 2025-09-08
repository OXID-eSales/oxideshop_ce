<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Html;

interface HtmlSanitizerInterface
{
    public function sanitize(string $html): string;
}
