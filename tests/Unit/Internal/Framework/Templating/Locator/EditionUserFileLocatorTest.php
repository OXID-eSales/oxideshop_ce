<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\Templating\Locator;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\EditionUserFileLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use Symfony\Component\Filesystem\Filesystem;

class EditionUserFileLocatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var vfsStream */
    private $vfsStreamDirectory;

    /**
     * @dataProvider dataProviderTestLocate
     */
    public function testLocate($edition)
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

    public function dataProviderTestLocate(): array
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

    /**
     * @param string $edition
     *
     * @return BasicContextStub
     */
    private function getContext(string $edition)
    {
        $context = new BasicContextStub();
        $context->setEdition($edition);
        $context->setCommunityEditionSourcePath($this->vfsStreamDirectory->url() . '/testSourcePathCE');
        $context->setProfessionalEditionRootPath($this->vfsStreamDirectory->url() . '/testSourcePathPE');
        $context->setEnterpriseEditionRootPath($this->vfsStreamDirectory->url() . '/testSourcePathEE');

        return $context;
    }

    private function createModuleStructure($edition)
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
