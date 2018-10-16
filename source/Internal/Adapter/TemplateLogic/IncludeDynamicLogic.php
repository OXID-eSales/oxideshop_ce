<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;

/**
 * Class IncludeDynamicLogic
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IncludeDynamicLogic
{

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function renderForCache(array $parameters)
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
    public function includeDynamicPrefix(array $parameters)
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
