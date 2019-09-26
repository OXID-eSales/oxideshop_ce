<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\TemplateBlockModuleSettingHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class TemplateBlockModuleSettingHandlerTest extends TestCase
{
    public function testHandlingOnModuleActivation()
    {
        $templateBlock = new TemplateBlock(
            'extendedTemplatePath',
            'testBlock',
            'filePath'
        );
        $templateBlock->setTheme('flow_theme');
        $templateBlock->setPosition(3);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModule')
            ->addTemplateBlock($templateBlock);

        $templateBlockExtension = new TemplateBlockExtension();
        $templateBlockExtension
            ->setShopId(1)
            ->setModuleId('testModule')
            ->setName('testBlock')
            ->setThemeId('flow_theme')
            ->setPosition(3)
            ->setExtendedBlockTemplatePath('extendedTemplatePath')
            ->setFilePath('filePath');

        $templateBlockDao = $this->getTemplateBlockDaoMock();
        $templateBlockDao
            ->expects($this->once())
            ->method('add')
            ->with($templateBlockExtension);

        $settingHandler = new TemplateBlockModuleSettingHandler($templateBlockDao);
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );
    }

    public function testHandlingOnModuleDeactivation()
    {
        $templateBlock = new TemplateBlock('', '', '');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModule')
            ->addTemplateBlock($templateBlock);

        $templateBlockDao = $this->getTemplateBlockDaoMock();
        $templateBlockDao
            ->expects($this->once())
            ->method('deleteExtensions')
            ->with('testModule', 1);

        $settingHandler = new TemplateBlockModuleSettingHandler($templateBlockDao);
        $settingHandler->handleOnModuleDeactivation(
            $moduleConfiguration,
            1
        );
    }

    private function getTemplateBlockDaoMock(): TemplateBlockExtensionDaoInterface
    {
        return $this
            ->getMockBuilder(TemplateBlockExtensionDaoInterface::class)
            ->getMock();
    }
}
