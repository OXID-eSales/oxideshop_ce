<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter;

use DOMNode;

interface HtmlRemoverInterface
{
    public function remove(DOMNode $node): void;
}
