<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class Template
{
    public function __construct(
        private string $templateKey,
        private string $templatePath
    ) {
    }

    /**
     * @return string
     */
    public function getTemplateKey(): string
    {
        return $this->templateKey;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}
