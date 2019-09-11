<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\SmartyPluginDirectoriesValidator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;

class SmartyPluginDirectoriesModuleSettingValidatorTest extends TestCase
{

    /** @var vfsStreamDirectory */
    private $vfsStreamDirectory = null;

    /** @var ModulePathResolverInterface */
    private $modulePathResolver = null;

    public function setUp()
    {
        parent::setUp();
        $this->modulePathResolver = $this->getMockBuilder(ModulePathResolverInterface::class)->getMock();
    }

    public function testValidate()
    {
        $this->createModuleStructure();

        $this->modulePathResolver
            ->method('getFullModulePathFromConfiguration')
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        $validator = new SmartyPluginDirectoriesValidator($this->modulePathResolver);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSmartyPluginDirectory(new SmartyPluginDirectory('smarty'));
        $moduleConfiguration->setId("smartyTestModule");

        $validator->validate($moduleConfiguration, 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException
     */
    public function testValidateThrowsExceptionIfNotExistingDirectoryConfigured()
    {
        $this->createModuleStructure();

        $this->modulePathResolver
            ->method('getFullModulePathFromConfiguration')
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        $validator = new SmartyPluginDirectoriesValidator($this->modulePathResolver);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSmartyPluginDirectory(new SmartyPluginDirectory('notExistingDirectory'));
        $moduleConfiguration->setId("smartyTestModule");

        $validator->validate($moduleConfiguration, 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotReadableException
     */
    public function testValidateThrowsExceptionIfNonReadableDirectoryConfigured()
    {
        $this->createModuleStructure();
        $this->changePermissionsOfSmartyPluginDirectoryToNonReadable();
        $this->assertSmartyPluginDirectoryIsNonReadable();

        $this->modulePathResolver
            ->method('getFullModulePathFromConfiguration')
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        $validator = new SmartyPluginDirectoriesValidator($this->modulePathResolver);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSmartyPluginDirectory(new SmartyPluginDirectory('smarty'));
        $moduleConfiguration->setId("smartyTestModule");

        $validator->validate($moduleConfiguration, 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSettingNotValidException
     */
    public function testValidateThrowsExceptionIfNotArrayConfigured()
    {
        $validator = new SmartyPluginDirectoriesValidator($this->modulePathResolver);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSmartyPluginDirectory(new SmartyPluginDirectory(''));
        $moduleConfiguration->setId("smartyTestModule");

        $validator->validate($moduleConfiguration, 1);
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
}
