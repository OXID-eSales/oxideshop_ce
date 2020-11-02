<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class Template
{
    /**
     * @var string
     */
    private $templateKey;

    /**
     * @var string
     */
    private $templatePath;

    public function __construct(string $templateKey, string $templatePath)
    {
        $this->templateKey = $templateKey;
        $this->templatePath = $templatePath;
    }

    public function getTemplateKey(): string
    {
        return $this->templateKey;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}
