<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension;

interface TemplateBlockExtensionDaoInterface
{
    public function add(TemplateBlockExtension $templateBlockExtension);

    public function getExtensions(string $name, int $shopId): array;

    public function deleteExtensions(string $moduleId, int $shopId);
}
