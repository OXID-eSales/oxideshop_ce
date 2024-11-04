<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Locator;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\EditionUserFileLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class EditionUserFileLocatorTest extends TestCase
{
    private vfsStreamDirectory $vfsStreamDirectory;

    #[DataProvider('dataProviderTestLocate')]
    public function testLocate(string $edition): void
    {
        $this->createModuleStructure($edition);
        $locator = new EditionUserFileLocator(
            $this->getAdminThemeMock(),
            $this->getContext($edition),
            new Filesystem()
        );

        $expectedPath = $this->vfsStreamDirectory->url() .
            '/testSourcePath' .
            $edition .
            '/Application/views/admin/user.xml';
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

    private function getAdminThemeMock(): AdminThemeBridgeInterface
    {
        $adminTheme = $this->getMockBuilder(AdminThemeBridgeInterface::class)->getMock();
        $adminTheme->method('getActiveTheme')->willReturn('admin');

        return $adminTheme;
    }

    private function getContext(string $edition): BasicContextStub
    {
        $context = new BasicContextStub();
        $context->setEdition(Edition::from($edition));
        $context->setCommunityEditionSourcePath($this->vfsStreamDirectory->url() . '/testSourcePathCE');
        $context->setProfessionalEditionSourcePath($this->vfsStreamDirectory->url() . '/testSourcePathPE');
        $context->setEnterpriseEditionSourcePath($this->vfsStreamDirectory->url() . '/testSourcePathEE');

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
                            'user.xml' => '*this is menu xml for test*'
                        ]
                    ]
                ]
            ]
        ];

        $this->vfsStreamDirectory = vfsStream::setup('root', null, $structure);
    }
}
