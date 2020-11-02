<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class ScriptLogic
{
    public function add(string $script, bool $isDynamic = false): void
    {
        $register = oxNew(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::class);
        $register->addSnippet($script, $isDynamic);
    }

    public function include(string $file, int $priority = 3, bool $isDynamic = false): void
    {
        $register = oxNew(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::class);
        $register->addFile($file, $priority, $isDynamic);
    }

    public function render(string $widget, bool $forceRender = false, bool $isDynamic = false): string
    {
        $renderer = oxNew(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRenderer::class);

        return $renderer->render($widget, $forceRender, $isDynamic);
    }
}
