<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;


class AssignAdvancedLogic
{

    /**
     * Formats arrays and range() for template
     *
     * @param $value
     * @return mixed
     */
    public function formatValue($value)
    {
        if(preg_match('/^\s*array\s*\(\s*(.*)\s*\)\s*$/s', $value, $match)) {
            eval('$value=array(' . str_replace("\n", "", $match[1]) . ');');
        } else if(preg_match('/^\s*range\s*\(\s*(.*)\s*\)\s*$/s', $value, $match)) {
            eval('$value=range(' . str_replace("\n", "", $match[1]) . ');');
        }
        return $value;
    }

}