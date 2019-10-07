<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class IncludeDynamicLogic
{
    /**
     * @param array $parameters
     *
     * @return string
     */
    public function renderForCache(array $parameters): string
    {
        $content = "<oxid_dynamic>";

        foreach ($parameters as $key => $value) {
            $content .= " $key='" . base64_encode($value) . "'";
        }

        $content .= "</oxid_dynamic>";

        return $content;
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function includeDynamicPrefix(array $parameters): array
    {
        $prefix = "_";
        if (array_key_exists('type', $parameters)) {
            $prefix .= $parameters['type'] . "_";
        }
        foreach ($parameters as $key => $value) {
            unset($parameters[$key]);
            if ($key != 'type' && $key != 'file') {
                $parameters[$prefix . $key] = $value;
            }
        }

        return $parameters;
    }
}
