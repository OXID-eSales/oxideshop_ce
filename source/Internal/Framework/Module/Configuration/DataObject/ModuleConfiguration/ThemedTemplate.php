<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class ThemedTemplate extends Template
{
    private string $templateTheme;

    public function __construct(string $templateKey, string $templatePath, string $templateTheme)
    {
        parent::__construct($templateKey, $templatePath);
        $this->templateTheme = $templateTheme;
    }

    public function getTemplateTheme(): string
    {
        return $this->templateTheme;
    }
}
