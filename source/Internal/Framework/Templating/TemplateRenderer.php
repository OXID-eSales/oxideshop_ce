<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

use AllowDynamicProperties;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

#[AllowDynamicProperties]
class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private TemplateEngineInterface $templateEngine,
        private ContextInterface $context,
        private readonly ?string $filenameExtension = null
    ) {
    }

    public function renderTemplate(string $template, array $context = []): string
    {
        if ($this->filenameExtension) {
            $template = $this->appendDefaultFilenameExtension($template);
        }
        return $this->getTemplateEngine()->render($template, $context);
    }

    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        if ($this->doNotRenderForDemoShop()) {
            return $fragment;
        }
        return $this->getTemplateEngine()->renderFragment($fragment, $fragmentId, $context);
    }

    public function getTemplateEngine(): TemplateEngineInterface
    {
        return $this->templateEngine;
    }

    public function exists(string $name): bool
    {
        if ($this->filenameExtension) {
            $name = $this->appendDefaultFilenameExtension($name);
        }
        return $this->getTemplateEngine()->exists($name);
    }

    private function doNotRenderForDemoShop(): bool
    {
        return $this->context->isShopInDemoMode();
    }

    private function appendDefaultFilenameExtension(string $templateName): string
    {
        return str_ends_with($templateName, $this->filenameExtension) ?
            $templateName :
            "$templateName.$this->filenameExtension";
    }
}
