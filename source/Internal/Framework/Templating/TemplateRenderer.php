<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private readonly TemplateEngineInterface $templateEngine,
        private readonly ContextInterface $context,
        private readonly string $filenameExtension
    ) {
    }

    public function renderTemplate(string $template, array $context = []): string
    {
        return $this
            ->getTemplateEngine()
            ->render(
                $this->appendDefaultFilenameExtension($template),
                $context
            );
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
        return $this
            ->getTemplateEngine()
            ->exists(
                $this->appendDefaultFilenameExtension($name)
            );
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
