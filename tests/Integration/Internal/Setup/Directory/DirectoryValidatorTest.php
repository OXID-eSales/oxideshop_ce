<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Directory;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\DirectoryValidatorException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryValidator;
use PHPUnit\Framework\TestCase;

final class DirectoryValidatorTest extends TestCase
{
    private const SHOP_SOURCE_PATH = 'vfs://root/test-folder';
    private const SHOP_COMPILE_PATH = 'vfs://root/test-folder/tmp';

    protected function setUp(): void
    {
        $this->createDirectoryStructure();

        parent::setUp();
    }

    private function createDirectoryStructure(): void
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
    }

    public function testDirectoriesExistentAndPermission(): void
    {
        $directoryValidator = new DirectoryValidator();
        $directoryValidator->validateDirectory(self::SHOP_SOURCE_PATH, self::SHOP_COMPILE_PATH);
    }

    public function testNonExistentDirectories(): void
    {
        $shopSourcePath  = '/test-folder';

        $directoryValidator = new DirectoryValidator();

        $this->expectException(DirectoryValidatorException::class);
        $directoryValidator->validateDirectory($shopSourcePath, self::SHOP_COMPILE_PATH);
    }

    public function testNoPermissionDirectories(): void
    {
        $directoryValidator = new DirectoryValidator();

        $directoryValidator->validateDirectory(self::SHOP_SOURCE_PATH, self::SHOP_COMPILE_PATH);

        chmod('vfs://root/test-folder/out/pictures/promo', 0111);

        $this->expectException(DirectoryValidatorException::class);
        $directoryValidator->validateDirectory(self::SHOP_SOURCE_PATH, self::SHOP_COMPILE_PATH);
    }
}
