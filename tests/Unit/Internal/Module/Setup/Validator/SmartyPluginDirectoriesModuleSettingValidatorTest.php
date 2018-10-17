<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\SmartyPluginDirectoriesModuleSettingValidator;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class SmartyPluginDirectoriesModuleSettingValidatorTest extends TestCase
{

    /** @var vfsStreamDirectory */
    private $vfsStreamDirectory = null;

    /** @var ShopAdapterInterface */
    private $shopAdapter = null;

    public function setUp()
    {
        parent::setUp();
        $this->shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
    }

    public function testCanValidate()
    {
        $validator = new SmartyPluginDirectoriesModuleSettingValidator(
            $this->shopAdapter
        );

        $smartyPluginDirectoriesModuleSetting = new ModuleSetting(ModuleSetting::SMARTY_PLUGIN_DIRECTORIES, []);

        $this->assertTrue(
            $validator->canValidate($smartyPluginDirectoriesModuleSetting)
        );
    }

    public function testCanNotValidate()
    {
        $validator = new SmartyPluginDirectoriesModuleSettingValidator(
            $this->shopAdapter
        );

        $invalidModuleSetting = new ModuleSetting('invalidKey', []);

        $this->assertFalse(
            $validator->canValidate($invalidModuleSetting)
        );
    }

    public function testValidate()
    {
        $this->createModuleStructure();

        $this->shopAdapter
            ->method('getModuleFullPath')
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        $validator = new SmartyPluginDirectoriesModuleSettingValidator($this->shopAdapter);

        $smartyPluginDirectoriesModuleSetting = new ModuleSetting(
            ModuleSetting::SMARTY_PLUGIN_DIRECTORIES,
            ['smarty']
        );
        $validator->validate($smartyPluginDirectoriesModuleSetting, 'smartyTestModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotExistentException
     */
    public function testValidateThrowsExceptionIfNotExistingDirectoryConfigured()
    {
        $this->createModuleStructure();

        $this->shopAdapter
            ->method('getModuleFullPath')
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        $validator = new SmartyPluginDirectoriesModuleSettingValidator($this->shopAdapter);

        $smartyPluginDirectoriesModuleSetting = new ModuleSetting(
            ModuleSetting::SMARTY_PLUGIN_DIRECTORIES,
            ['notExistingDirectory']
        );
        $validator->validate($smartyPluginDirectoriesModuleSetting, 'smartyTestModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotReadableException
     */
    public function testValidateThrowsExceptionIfNonReadableDirectoryConfigured()
    {
        $this->createModuleStructure();
        $this->changePermissionsOfSmartyPluginDirectoryToNonReadable();
        $this->assertSmartyPluginDirectoryIsNonReadable();

        $this->shopAdapter
            ->method('getModuleFullPath')
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        $validator = new SmartyPluginDirectoriesModuleSettingValidator($this->shopAdapter);

        $smartyPluginDirectoriesModuleSetting = new ModuleSetting(
            ModuleSetting::SMARTY_PLUGIN_DIRECTORIES,
            ['smarty']
        );
        $validator->validate($smartyPluginDirectoriesModuleSetting, 'smartyTestModule', 1);
    }

    private function changePermissionsOfSmartyPluginDirectoryToNonReadable()
    {
        $this->vfsStreamDirectory
            ->getChild('modules')
            ->getChild('smartyTestModule')
            ->getChild('smarty')
            ->chmod(0000);
    }

    private function assertSmartyPluginDirectoryIsNonReadable()
    {
        $this->assertFalse(
            $this->vfsStreamDirectory
                ->getChild('modules')
                ->getChild('smartyTestModule')
                ->getChild('smarty')
                ->isReadable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup())
        );
    }

    /**
     * @expectedException OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSetupValidationException
     */
    public function testValidateThrowsExceptionIfNotAbleToValidateSetting()
    {
        $validator = new SmartyPluginDirectoriesModuleSettingValidator($this->shopAdapter);

        $smartyPluginDirectoriesModuleSetting = new ModuleSetting(
            'SettingWhichIsNotAbleToBeValidated',
            ['directory']
        );
        $validator->validate($smartyPluginDirectoriesModuleSetting, 'smartyTestModule', 1);
    }

    /**
     * @expectedException OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException
     */
    public function testValidateThrowsExceptionIfNotArrayConfigured()
    {
        $validator = new SmartyPluginDirectoriesModuleSettingValidator($this->shopAdapter);

        $smartyPluginDirectoriesModuleSetting = new ModuleSetting(
            ModuleSetting::SMARTY_PLUGIN_DIRECTORIES,
            ''
        );
        $validator->validate($smartyPluginDirectoriesModuleSetting, 'smartyTestModule', 1);
    }

    private function createModuleStructure()
    {
        $structure = [
            'modules' => [
                'smartyTestModule' => [
                    'smarty' => [
                        'smartyPlugin.php' => '*this is test smarty plugin*'
                    ]
                ]
            ]
        ];

        if (!$this->vfsStreamDirectory) {
            $this->vfsStreamDirectory = vfsStream::setup('root', null, $structure);
        }
    }
}
