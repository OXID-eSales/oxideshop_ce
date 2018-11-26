<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\TemplateBlockModuleSettingHandler;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\EshopCommunity\Internal\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class TemplateBlockModuleSettingHandlerTest extends TestCase
{
    public function testCanHandleSetting()
    {
        $settingHandler = new TemplateBlockModuleSettingHandler($this->getTemplateBlockDaoMock());
        $moduleSetting = new ModuleSetting(ModuleSetting::TEMPLATE_BLOCKS, []);

        $this->assertTrue(
            $settingHandler->canHandle($moduleSetting)
        );
    }

    public function testCanNotHandleSetting()
    {
        $settingHandler = new TemplateBlockModuleSettingHandler($this->getTemplateBlockDaoMock());
        $moduleSetting = new ModuleSetting('anotherSetting', []);

        $this->assertFalse(
            $settingHandler->canHandle($moduleSetting)
        );
    }

    public function testHandlingOnModuleActivation()
    {
        $moduleSetting = new ModuleSetting(
            ModuleSetting::TEMPLATE_BLOCKS,
            [
                [
                    'block'     => 'testBlock',
                    'position'  => '3',
                    'theme'     => 'flow_theme',
                    'template'  => 'extendedTemplatePath',
                    'file'      => 'filePath',
                ],
            ]
        );

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
            $moduleSetting,
            'testModule',
            1
        );
    }

    public function testHandlingOnModuleDeactivation()
    {
        $moduleSetting = new ModuleSetting(ModuleSetting::TEMPLATE_BLOCKS, []);

        $templateBlockDao = $this->getTemplateBlockDaoMock();
        $templateBlockDao
            ->expects($this->once())
            ->method('deleteExtensions')
            ->with('testModule', 1);

        $settingHandler = new TemplateBlockModuleSettingHandler($templateBlockDao);
        $settingHandler->handleOnModuleDeactivation(
            $moduleSetting,
            'testModule',
            1
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongSettingModuleSettingHandlerException
     */
    public function testHandleWrongSettingOnModuleActivation()
    {
        $handler = new TemplateBlockModuleSettingHandler($this->getTemplateBlockDaoMock());

        $handler->handleOnModuleActivation(
            new ModuleSetting('wrongSettingForThisHandler', []),
            'testModule',
            1
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongSettingModuleSettingHandlerException
     */
    public function testHandleWrongSettingOnModuleDeactivation()
    {
        $handler = new TemplateBlockModuleSettingHandler($this->getTemplateBlockDaoMock());

        $handler->handleOnModuleActivation(
            new ModuleSetting('wrongSettingForThisHandler', []),
            'testModule',
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
