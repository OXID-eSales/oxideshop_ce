<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Install;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryExistentException;
use OxidEsales\EshopCommunity\Internal\Common\FileSystem\FinderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleFilesInstaller;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\Dao\OxidEshopPackageDaoInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
class ModuleFilesInstallerTest extends TestCase
{
    public function testCopyDispatchesCallsToTheFileSystemService()
    {
        $packageName = 'myvendor/mymodule';
        $packagePath = '/var/www/vendor/myvendor/mymodule';
        $extra = [
            "oxideshop" => [
                "blacklist-filter" => [
                    "documentation/**/*.*",
                    "CHANGELOG.md",
                    "composer.json",
                    "CONTRIBUTING.md",
                    "README.md"
                ],
                "target-directory" => "myvendor/myinstallationpath",
                "source-directory" => "src/mymodule"
            ]
        ];

        vfsStream::setup();
        $context = $this->getContext();
        $packageService = $this->getPackageService($packageName, $extra);

        $finder = $this->getMockBuilder(Finder::class)->getMock();
        $finder
            ->expects($this->once())
            ->method('in')
            ->with('/var/www/vendor/myvendor/mymodule/src/mymodule')
            ->willReturn($finder);

        $finder
            ->expects($this->once())
            ->method('notName')
            ->with([
                'documentation/**/*.*',
                'CHANGELOG.md',
                'composer.json',
                'CONTRIBUTING.md',
                'README.md'
            ]);

        $finderFactory = $this->getMockBuilder(FinderFactoryInterface::class)->getMock();
        $finderFactory->method('create')->willReturn($finder);

        $fileSystem = $this->getMockBuilder(Filesystem::class)->getMock();
        $fileSystem
            ->expects($this->once())
            ->method('mirror')
            ->with(
                $packagePath . DIRECTORY_SEPARATOR . $extra['oxideshop']['source-directory'],
                $context->getModulesPath() . DIRECTORY_SEPARATOR . $extra['oxideshop']['target-directory'],
                $finder,
                ['override' => true]
            );

        $moduleCopyService = new ModuleFilesInstaller($packageService, $context, $fileSystem, $finderFactory);
        $moduleCopyService->copy($packagePath);
    }

    public function testCopyThrowsExceptionIfTargetDirectoryAlreadyPresent()
    {
        $structure = [
            'source' => [
                'modules' => [
                    'myvendor' => [
                        'mymodule' => [
                            'metadata.php' => ''
                        ]
                    ]
                ]
            ]
        ];
        vfsStream::setup('root', null, $structure);

        $packageName = 'myvendor/mymodule';

        $this->expectException(DirectoryExistentException::class);

        $moduleCopyService = new ModuleFilesInstaller(
            $packageService = $this->getPackageService($packageName, []),
            $this->getContext(),
            $this->getMockBuilder(Filesystem::class)->getMock(),
            $this->getMockBuilder(FinderFactoryInterface::class)->getMock()
        );

        $moduleCopyService->copy('pathDoesNotMatterHere');
    }

    /**
     * @param string $packageName
     * @param array  $extraParameters
     *
     * @return OxidEshopPackageDaoInterface
     */
    private function getPackageService(string $packageName, array $extraParameters = []): OxidEshopPackageDaoInterface
    {
        $package = new OxidEshopPackage($packageName, $extraParameters);

        $packageService = $this->getMockBuilder(OxidEshopPackageDaoInterface::class)->getMock();
        $packageService->method('getPackage')->willReturn($package);

        return $packageService;
    }

    /**
     * @return BasicContextInterface
     */
    private function getContext() : BasicContextInterface
    {
        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context->method('getModulesPath')->willReturn(vfsStream::url('root/source/modules'));
        return $context;
    }
}
