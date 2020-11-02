<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension;

class TemplateBlockLoaderBridge implements TemplateBlockLoaderBridgeInterface
{
    /**
     * @var TemplateBlockLoaderInterface
     */
    private $templateBlockLoader;

    public function __construct(TemplateBlockLoaderInterface $templateBlockLoader)
    {
        $this->templateBlockLoader = $templateBlockLoader;
    }

    /**
     * @inheritDoc
     */
    public function getContent(string $templatePath, string $moduleId): string
    {
        return $this->templateBlockLoader->getContent($templatePath, $moduleId);
    }
}
