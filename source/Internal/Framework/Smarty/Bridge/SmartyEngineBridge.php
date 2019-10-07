<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge;

class SmartyEngineBridge implements SmartyEngineBridgeInterface
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
    public function renderFragment(\Smarty $engine, string $fragment, string $fragmentId, array $context = []): string
    {
        // save old tpl data
        $tplVars = $engine->_tpl_vars;
        $forceRecompile = $engine->force_compile;
        $engine->force_compile = true;
        foreach ($context as $key => $value) {
            $engine->assign($key, $value);
        }
        $engine->oxidcache = new \OxidEsales\Eshop\Core\Field($fragment, \OxidEsales\Eshop\Core\Field::T_RAW);
        $result = $engine->fetch($fragmentId);
        $engine->_tpl_vars = $tplVars;
        $engine->force_compile = $forceRecompile;
        return $result;
    }
}
