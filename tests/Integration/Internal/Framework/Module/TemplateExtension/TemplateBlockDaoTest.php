<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\TemplateExtension;

use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class TemplateBlockDaoTest extends TestCase
{
    use ContainerTrait;

    public function testAddTemplateBlock()
    {
        $templateBlock = new TemplateBlockExtension();
        $templateBlock
            ->setName('testTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(1)
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setThemeId('testThemeId');

        $templateBlockDao = $this->getTemplateBlockDao();
        $templateBlockDao->add($templateBlock);

        $this->assertEquals(
            [$templateBlock],
            $templateBlockDao->getExtensions('testTemplateBlock', 1)
        );
    }

    public function testDeleteAllModuleTemplateBlocks()
    {
        $templateBlock = new TemplateBlockExtension();
        $templateBlock
            ->setName('testTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(1)
            ->setModuleId('testModuleId')
            ->setShopId(1);

        $templateBlock2 = new TemplateBlockExtension();
        $templateBlock2
            ->setName('testTemplateBlock2')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(1)
            ->setModuleId('testModuleId')
            ->setShopId(1);

        $templateBlockDao = $this->getTemplateBlockDao();
        $templateBlockDao->add($templateBlock);
        $templateBlockDao->add($templateBlock2);

        $templateBlockDao->deleteExtensions('testModuleId', 1);

        $this->assertEquals(
            [],
            $templateBlockDao->getExtensions('testTemplateBlock', 1)
        );
    }

    private function getTemplateBlockDao(): TemplateBlockExtensionDaoInterface
    {
        return $this->get(TemplateBlockExtensionDaoInterface::class);
    }
}
