<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Directory;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\DirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use PHPUnit\Framework\TestCase;

final class DirectoryServiceTest extends TestCase
{

    public function testNonExistentDirectories(): void
    {
        $directoryService = $this->getDirectoryService('/test-folder');

        $this->expectException(DirectoryException::class);
        $directoryService->checkDirectoriesExistent();
    }

    public function testNoPermissionDirectories(): void
    {
        $structure = [
            'test-folder' => [
                'out' => [
                    'pictures' => [
                        'promo' => [],
                        'master' => [],
                        'generated' => [],
                        'media' => []
                    ],
                    'media' => [],
                ],
                'log' => [],
                'tmp' => []
            ],
            'var' => []
        ];

        $root = vfsStream::setup('root');
        vfsStream::create($structure, $root);

        $directoryService = $this->getDirectoryService('vfs://root/test-folder');
        $directoryService->checkDirectoriesPermission();

        chmod('vfs://root/test-folder/out/pictures/promo', 0111);

        $directoryService = $this->getDirectoryService('vfs://root/test-folder');
        $this->expectException(DirectoryException::class);
        $directoryService->checkDirectoriesPermission();
    }

    /**
     * @param string $basePath
     *
     * @return DirectoryService
     */
    private function getDirectoryService(string $basePath): DirectoryService
    {
        $context = $this->getMockBuilder(BasicContext::class)
            ->setMethods(['getSourcePath'])
            ->getMock();

        $context->method('getSourcePath')->willReturn($basePath);

        return new DirectoryService($context);
    }
}
