<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class AssignAdvancedLogic
{
    /**
     * Formats arrays and range() for template.
     *
     * @return mixed
     */
    public function formatValue(string $value)
    {
        if (preg_match('/^\s*array\s*\(\s*(.*)\s*\)\s*$/s', $value, $match)) {
            eval('$value=array(' . str_replace("\n", '', $match[1]) . ');');
        } elseif (preg_match('/^\s*range\s*\(\s*(.*)\s*\)\s*$/s', $value, $match)) {
            eval('$value=range(' . str_replace("\n", '', $match[1]) . ');');
        }

        return $value;
    }
}
