<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Html;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

readonly class HtmlSanitizerConfigFactory implements HtmlSanitizerConfigFactoryInterface
{
    public function create(): HtmlSanitizerConfig
    {
        return (new HtmlSanitizerConfig())
            ->allowStaticElements()
            ->allowRelativeMedias()
            ->allowRelativeLinks()
            ->allowMediaSchemes(['http', 'https']);
    }
}
