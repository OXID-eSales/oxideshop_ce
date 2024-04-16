<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Locator;

use PHPUnit\Framework\Attributes\DataProvider;
use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\EditionMenuFileLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class EditionMenuFileLocatorTest extends TestCase
{
    /** @var vfsStream */
    private $vfsStreamDirectory;

    #[DataProvider('dataProviderTestLocate')]
    public function testLocate(string $edition): void
    {
        $this->createModuleStructure($edition);
        $locator = new EditionMenuFileLocator(
            $this->getAdminThemeMock(),
            $this->getContext($edition),
            new Filesystem()
        );

        $expectedPath = $this->vfsStreamDirectory->url() .
            '/testSourcePath' .
            $edition .
            '/Application/views/admin/menu.xml';
        $this->assertSame([$expectedPath], $locator->locate());
    }

    public static function dataProviderTestLocate(): array
    {
        return [
            ['CE'],
            ['PE'],
            ['EE'],
        ];
    }

    /**
     * @return AdminThemeBridgeInterface
     */
    private function getAdminThemeMock()
    {
        $adminTheme = $this->getMockBuilder(AdminThemeBridgeInterface::class)->getMock();
        $adminTheme->method('getActiveTheme')->willReturn('admin');

        return $adminTheme;
    }

    private function getContext(string $edition): BasicContextStub
    {
        $context = new BasicContextStub();
        $context->setEdition($edition);
        $context->setSourcePath($this->vfsStreamDirectory->url() . '/testSourcePathCE');
        $context->setProfessionalEditionRootPath($this->vfsStreamDirectory->url() . '/testSourcePathPE');
        $context->setEnterpriseEditionRootPath($this->vfsStreamDirectory->url() . '/testSourcePathEE');

        return $context;
    }

    private function createModuleStructure(string $edition): void
    {
        $shopPath = 'testSourcePath' . $edition;
        $structure = [
            $shopPath => [
                'Application' => [
                    'views' => [
                        'admin' => [
                            'menu.xml' => '*this is menu xml for test*'
                        ]
                    ]
                ]
            ]
        ];
        $this->vfsStreamDirectory = vfsStream::setup('root', null, $structure);
    }
}
