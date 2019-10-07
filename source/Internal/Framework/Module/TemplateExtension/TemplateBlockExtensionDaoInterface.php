<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension;

interface TemplateBlockExtensionDaoInterface
{
    /**
     * @param TemplateBlockExtension $templateBlockExtension
     */
    public function add(TemplateBlockExtension $templateBlockExtension);

    /**
     * @param string $name
     * @param int    $shopId
     * @return array
     */
    public function getExtensions(string $name, int $shopId): array;

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deleteExtensions(string $moduleId, int $shopId);
}
