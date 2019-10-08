<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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

    /**
     * @param string $templateKey
     * @param string $templatePath
     */
    public function __construct(string $templateKey, string $templatePath)
    {
        $this->templateKey = $templateKey;
        $this->templatePath = $templatePath;
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
