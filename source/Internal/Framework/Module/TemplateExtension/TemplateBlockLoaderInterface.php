<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension;

interface TemplateBlockLoaderInterface
{
    /**
     * @param string $templatePath
     * @param string $moduleId
     * @return string
     * @throws TemplateBlockNotFoundException
     */
    public function getContent(string $templatePath, string $moduleId): string;
}
