<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Install;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Common\CopyGlob\CopyGlobServiceInterface;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryExistentException;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\ModuleCopyService;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\OxidEshopPackageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\PackageServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
class ModuleCopyServiceTest extends TestCase
{
    public function testCopyDispatchesCallsToTheCopyGlobService()
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

        $finder = new Finder();

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

        $moduleCopyService = new ModuleCopyService($packageService, $context, $fileSystem);
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

        $context = $this->getContext();
        $packageService = $this->getPackageService($packageName, []);
        $copyGlobService = $this->getCopyGlobService();

        $this->expectException(\OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryExistentException::class);

        $moduleCopyService = new ModuleCopyService($packageService, $context, $copyGlobService);

        $this->expectException(\OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryExistentException::class);
        try {
            $moduleCopyService->copy('pathDoesNotMatterHere');
            $this->fail('Exception should be thrown when target directory is already present');
        } catch (DirectoryExistentException $exception) {
            $directory = $exception->getDirectoryAlreadyExistent();
            $this->assertEquals(vfsStream::url('root/source/modules/myvendor/mymodule'), $directory);
            throw $exception;
        }
    }

    /**
     * @param string $packageName
     * @param array  $extraParameters
     *
     * @return OxidEshopPackageFactoryInterface
     */
    private function getPackageService(string $packageName, array $extraParameters = []) : OxidEshopPackageFactoryInterface
    {
        $package = new OxidEshopPackage($packageName, $extraParameters);

        $packageService = $this->getMockBuilder(OxidEshopPackageFactoryInterface::class)->getMock();
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


    /**
     * @return BasicContextInterface
     */
    private function getCopyGlobService() : CopyGlobServiceInterface
    {
        $copyGlobServiceInterface = $this->getMockBuilder(CopyGlobServiceInterface::class)->getMock();
        return $copyGlobServiceInterface;
    }
}
