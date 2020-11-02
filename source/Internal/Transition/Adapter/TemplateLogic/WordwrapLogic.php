<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Str;

class WordwrapLogic
{
    public function wordwrap(string $string, int $length = 80, string $wrapper = "\n", bool $cut = false): string
    {
        return Str::getStr()->wordwrap($string, $length, $wrapper, $cut);
    }
}
