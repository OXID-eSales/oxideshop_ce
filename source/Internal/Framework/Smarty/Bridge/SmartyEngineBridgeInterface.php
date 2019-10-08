<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge;

interface SmartyEngineBridgeInterface
{
    /**
     * Renders a fragment of the template.
     *
     * @param \Smarty $engine
     * @param string  $fragment   The template fragment to render
     * @param string  $fragmentId The Id of the fragment
     * @param array   $context    An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderFragment(\Smarty $engine, string $fragment, string $fragmentId, array $context = []): string;
}
