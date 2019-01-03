<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ModulePackageInstaller;
use OxidEsales\ComposerPlugin\Utilities\VfsFileStructureOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Webmozart\PathUtil\Path;

class ModulePackageInstallerTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $vfsStreamDirectory = null;

    public function setUp()
    {
        parent::setUp();
        $this->setupVfsStreamWrapper();
    }

    public function testModuleNotInstalledByDefault()
    {
        $packageName = 'test-vendor/test-package';
        $installer = $this->getPackageInstaller($packageName);

        $this->assertFalse($installer->isInstalled($packageName));
    }

    public function testModuleIsInstalledIfAlreadyExistsInShop()
    {
        $this->setupVirtualPackageRoot('source/modules/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $packageName = 'test-vendor/test-package';
        $installer = $this->getPackageInstaller($packageName);

        $this->assertTrue($installer->isInstalled($packageName));
    }

    public function testModuleIsInstalledAfterInstallProcess()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $packageName = 'test-vendor/test-package';
        $installer = $this->getPackageInstaller($packageName);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertTrue($installer->isInstalled($packageName));
    }

    public function testModuleFilesAreCopiedAfterInstallProcess()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package');
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/metadata.php',
            'source/modules/test-vendor/test-package/metadata.php'
        );
    }

    public function testModuleFilesAreCopiedAfterInstallProcessWithSameSourceDirectory()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'source-directory' => ''
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/metadata.php',
            'source/modules/test-vendor/test-package/metadata.php'
        );
    }

    public function testModuleFilesAreCopiedAfterInstallProcessWithSameTargetDirectory()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'target-directory' => 'test-vendor/test-package'
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/metadata.php',
            'source/modules/test-vendor/test-package/metadata.php'
        );
    }

    public function testModuleFilesAreCopiedAfterInstallProcessWithSameSourceDirectoryAndSameTargetDirectory()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'source-directory' => '',
                'target-directory' => 'test-vendor/test-package'
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/metadata.php',
            'source/modules/test-vendor/test-package/metadata.php'
        );
    }

    public function testModuleFilesAreCopiedAfterInstallProcessWithCustomSourceDirectory()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package/custom-root', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'source-directory' => 'custom-root',
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/custom-root/metadata.php',
            'source/modules/test-vendor/test-package/metadata.php'
        );
    }

    public function testModuleFilesAreCopiedAfterInstallProcessWithCustomTargetDirectory()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'target-directory' => 'custom-vendor/custom-package',
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/metadata.php',
            'source/modules/custom-vendor/custom-package/metadata.php'
        );
    }

    public function testModuleFilesAreCopiedAfterInstallProcessWithCustomSourceDirectoryAndCustomTargetDirectory()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package/custom-root', [
            'metadata.php' => '<?php'
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'source-directory' => 'custom-root',
                'target-directory' => 'custom-vendor/custom-package',
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/custom-root/metadata.php',
            'source/modules/custom-vendor/custom-package/metadata.php'
        );
    }

    public function testBlacklistedFilesArePresentWhenNoBlacklistFilterIsDefined()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php',
            'module.php' => '<?php',
            'readme.txt' => 'readme',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0');
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/metadata.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/module.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/readme.txt');
    }

    public function testBlacklistedFilesArePresentWhenEmptyBlacklistFilterIsDefined()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php',
            'module.php' => '<?php',
            'readme.txt' => 'readme',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'blacklist-filter' => []
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/metadata.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/module.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/readme.txt');
    }

    public function testBlacklistedFilesArePresentWhenDifferentBlacklistFilterIsDefined()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php',
            'module.php' => '<?php',
            'readme.txt' => 'readme',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'blacklist-filter' => [
                    '**/*.pdf'
                ]
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/metadata.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/module.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/readme.txt');
    }

    public function testBlacklistedFilesAreSkippedWhenABlacklistFilterIsDefined()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php',
            'module.php' => '<?php',
            'readme.txt' => 'readme',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'blacklist-filter' => [
                    '**/*.txt'
                ]
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/metadata.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/module.php');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/readme.txt');
    }

    public function testVCSFilesAreSkippedWhenNoBlacklistFilterIsDefined()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php',
            '.git/HEAD' => 'HEAD',
            '.git/index' => 'index',
            '.git/objects/ff/fftest' => 'blob',
            '.gitignore' => 'git ignore',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0');
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/metadata.php');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.git/HEAD');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.git/index');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.git/objects/ff/fftest');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.gitignore');
    }

    public function testVCSFilesAreSkippedWhenABlacklistFilterIsDefined()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package', [
            'metadata.php' => '<?php',
            'module.php' => '<?php',
            'readme.txt' => 'readme',
            '.git/HEAD' => 'HEAD',
            '.git/index' => 'index',
            '.git/objects/ff/fftest' => 'blob',
            '.gitignore' => 'git ignore',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'blacklist-filter' => [
                    '**/*.txt'
                ]
            ]
        ]);
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/metadata.php');
        $this->assertVirtualFileExists('source/modules/test-vendor/test-package/module.php');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/readme.txt');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.git/HEAD');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.git/index');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.git/objects/ff/fftest');
        $this->assertVirtualFileNotExists('source/modules/test-vendor/test-package/.gitignore');
    }

    public function testComplexCase()
    {
        $this->setupVirtualPackageRoot('vendor/test-vendor/test-package/custom-root', [
            'metadata.php' => '<?php',
            'module.php' => '<?php',
            'readme.txt' => 'readme',
            'readme.pdf' => 'PDF',
            'documentation/readme.txt' => 'readme',
            'documentation/example.php' => '<?php',
            'model/model.php' => '<?php',
        ]);

        $installer = $this->getPackageInstaller('test-vendor/test-package', '1.0.0', [
            'oxideshop' => [
                'source-directory' => 'custom-root',
                'target-directory' => 'custom-out',
                'blacklist-filter' => [
                    '**/*.txt',
                    '**/*.pdf',
                    'documentation/**/*.*',
                ]
            ]
        ]);
        $packageName = 'vendor/test-vendor/test-package';
        $installer->install($this->getVirtualFileSystemRootPath($packageName));

        $this->assertTrue($installer->isInstalled($packageName));
        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/custom-root/metadata.php',
            'source/modules/custom-out/metadata.php'
        );
        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/custom-root/module.php',
            'source/modules/custom-out/module.php'
        );
        $this->assertVirtualFileEquals(
            'vendor/test-vendor/test-package/custom-root/model/model.php',
            'source/modules/custom-out/model/model.php'
        );
        $this->assertVirtualFileNotExists('source/modules/custom-out/readme.txt');
        $this->assertVirtualFileNotExists('source/modules/custom-out/readme.pdf');
        $this->assertVirtualFileNotExists('source/modules/custom-out/documentation');
        $this->assertVirtualFileNotExists('source/modules/custom-out/documentation/readme.txt');
        $this->assertVirtualFileNotExists('source/modules/custom-out/documentation/example.php');
    }

    /**
     * @return Package|MockObject
     */
    private function getPackage() : Package
    {
        /** @var Package $package */
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getExtra', 'getName'])
            ->getMock();
        $package->method('getExtra')->willReturn(["target-directory" => "testvendor/testmodule"]);
        $package->method('getName')->willReturn('testvendor/testmodule');

        return $package;
    }



    private function setupVfsStreamWrapper()
    {
        if (!$this->vfsStreamDirectory) {
            $this->vfsStreamDirectory = vfsStream::setup();
        }
    }

    private function setupModuleStructure()
    {
        $structure = [
            'vendor' => [
                'testvendor' => [
                    'testmodule' => [
                        'metadata.php' => ''
                    ]
                ]
            ],
            'source' => [
                'modules' => []
            ]
        ];
        vfsStream::create($structure, $this->vfsStreamDirectory);
    }

    /**
     * @param        $packageName
     * @param string $version
     * @param array  $extra
     *
     * @return ModulePackageInstaller
     */
    private function getPackageInstaller(string $packageName, string $version = '1.0.0', array $extra = [])
    {
        $package = new Package($packageName, $version, $version);
        $package->setExtra($extra);

        return new ModulePackageInstaller(
            new NullIO(),
            $this->getVirtualShopSourcePath(),
            $package
        );
    }

    /**
     * @return string
     */
    private function getVirtualShopSourcePath() : string
    {
        return Path::join(vfsStream::url('root'), 'source');
    }

    /**
     * @param $packagePath
     * @param $filesAndDirectoriesInsidePackage
     *
     * @return vfsStreamDirectory
     */
    private function setupVirtualPackageRoot($packagePath, $filesAndDirectoriesInsidePackage)
    {
        $updated = [];

        foreach ($filesAndDirectoriesInsidePackage as $path => $contents) {
            $updated[Path::join($packagePath, $path)] = $contents;
        }

        return vfsStream::create(VfsFileStructureOperator::nest($updated));
    }

    /**
     * @param string $expected
     * @param string $actual
     */
    protected function assertVirtualFileEquals($expected, $actual)
    {
        $this->assertFileEquals(
            $this->getVirtualFileSystemRootPath($expected),
            $this->getVirtualFileSystemRootPath($actual)
        );
    }

    protected function assertVirtualFileExists($path)
    {
        $this->assertFileExists($this->getVirtualFileSystemRootPath($path));
    }

    protected function assertVirtualFileNotExists($path)
    {
        $this->assertFileNotExists($this->getVirtualFileSystemRootPath($path));
    }

    protected function getVirtualFileSystemRootPath($suffix = '')
    {
        return Path::join(vfsStream::url('root'), $suffix);
    }
}