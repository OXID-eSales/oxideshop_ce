<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Fixtures;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use Throwable;

readonly class FailingTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(private Throwable $templateError)
    {
    }

    public function renderTemplate(string $template, array $context = []): string
    {
        if ($template !== 'message/exception') {
            throw $this->templateError;
        }

        return 'exception page';
    }

    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        throw $this->templateError;
    }

    public function getTemplateEngine(): TemplateEngineInterface
    {
        throw $this->templateError;
    }

    public function exists(string $name): bool
    {
        return $name === 'message/exception';
    }
}
