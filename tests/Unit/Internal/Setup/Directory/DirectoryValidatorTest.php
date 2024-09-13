<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Directory;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\DirectoryValidator;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\NoPermissionDirectoryException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;

final class DirectoryValidatorTest extends TestCase
{
    private vfsStreamDirectory $dir;

    protected function setUp(): void
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

        $this->dir = vfsStream::setup('root', 0777, $structure);

        parent::setUp();
    }

    public function testNoPermissionDirectories(): void
    {
        $this->dir->getChild('test-folder/out/pictures/promo')->chmod(0555);

        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context->method('getSourcePath')->willReturn($this->dir->getChild('test-folder')->url());
        $directoryValidator = new DirectoryValidator($context);

        $this->expectException(NoPermissionDirectoryException::class);

        $directoryValidator->validateDirectory(
            $this->dir->getChild('test-folder/tmp')->url()
        );
    }
}
