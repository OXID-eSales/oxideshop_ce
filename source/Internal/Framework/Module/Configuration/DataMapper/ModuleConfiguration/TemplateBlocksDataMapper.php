<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;

class TemplateBlocksDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'templateBlocks';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasTemplateBlocks()) {
            $data[self::MAPPING_KEY] = $this->getTemplateBlocks($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->setTemplateBlocks($moduleConfiguration, $data[self::MAPPING_KEY]);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @return array
     */
    private function getTemplateBlocks(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getTemplateBlocks() as $key => $templateBlock) {
            $data[$key] = [
                TemplateBlocksMappingKeys::SHOP_TEMPLATE_PATH => $templateBlock->getShopTemplatePath(),
                TemplateBlocksMappingKeys::BLOCK_NAME => $templateBlock->getBlockName(),
                TemplateBlocksMappingKeys::MODULE_TEMPLATE_PATH => $templateBlock->getModuleTemplatePath(),
            ];
            if ($templateBlock->getPosition() !== 0) {
                $data[$key][TemplateBlocksMappingKeys::POSITION] = $templateBlock->getPosition();
            }
            if ($templateBlock->getTheme() !== '') {
                $data[$key][TemplateBlocksMappingKeys::THEME] = $templateBlock->getTheme();
            }
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array $templateBlocks
     */
    private function setTemplateBlocks(ModuleConfiguration $moduleConfiguration, array $templateBlocks): void
    {
        foreach ($templateBlocks as $templateBlockData) {
            $templateBlock = new TemplateBlock(
                $templateBlockData[TemplateBlocksMappingKeys::SHOP_TEMPLATE_PATH],
                $templateBlockData[TemplateBlocksMappingKeys::BLOCK_NAME],
                $templateBlockData[TemplateBlocksMappingKeys::MODULE_TEMPLATE_PATH]
            );
            if (isset($templateBlockData[TemplateBlocksMappingKeys::POSITION])) {
                $templateBlock->setPosition($templateBlockData[TemplateBlocksMappingKeys::POSITION]);
            }
            if (isset($templateBlockData[TemplateBlocksMappingKeys::THEME])) {
                $templateBlock->setTheme($templateBlockData[TemplateBlocksMappingKeys::THEME]);
            }
            $moduleConfiguration->addTemplateBlock($templateBlock);
        }
    }
}
