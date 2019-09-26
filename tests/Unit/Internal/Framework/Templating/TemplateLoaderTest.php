<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\TemplateFileNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateFileLocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateLoader;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateNameResolverInterface;

class TemplateLoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testExists()
    {
        $name = 'test_template.tpl';
        $locator = $this->getFileLocatorMock($name);
        $nameResolver = $this->getTemplateNameResolverMock($name);
        $loader = new TemplateLoader($locator, $nameResolver);

        $this->assertTrue($loader->exists($name));
    }

    public function testIfTemplateDoNotExists()
    {
        $name = 'not_existing_template.tpl';
        $locator = $this->getFileLocatorMock(false);
        $nameResolver = $this->getTemplateNameResolverMock($name);
        $loader = new TemplateLoader($locator, $nameResolver);

        $this->assertFalse($loader->exists('not_existing_template.tpl'));
    }

    public function testGetContext()
    {
        $name = 'testSmartyTemplate.tpl';
        $context = "The new contents of the file";
        $templateDir = vfsStream::setup('testTemplateDir');
        $template = vfsStream::newFile($name)
            ->at($templateDir)
            ->setContent($context)
            ->url();

        $locator = $this->getFileLocatorMock($template);
        $nameResolver = $this->getTemplateNameResolverMock($name);
        $loader = new TemplateLoader($locator, $nameResolver);

        $this->assertSame($context, $loader->getContext($template));
    }

    public function testGetPath()
    {
        $name = 'testSmartyTemplate.tpl';
        $context = "The new contents of the file";
        $templateDir = vfsStream::setup('testTemplateDir');
        $template = vfsStream::newFile($name)
            ->at($templateDir)
            ->setContent($context)
            ->url();

        $locator = $this->getFileLocatorMock($template);
        $nameResolver = $this->getTemplateNameResolverMock($name);
        $loader = new TemplateLoader($locator, $nameResolver);

        $this->assertSame($template, $loader->getPath($template));
    }

    public function testGetPathIfTemplateDoNotExits()
    {
        $this->expectException(TemplateFileNotFoundException::class);
        $name = 'not_existing_template.tpl';
        $locator = $this->getFileLocatorMock(false);
        $nameResolver = $this->getTemplateNameResolverMock($name);
        $loader = new TemplateLoader($locator, $nameResolver);
        $loader->getPath($name);
    }

    /**
     * @param $path
     *
     * @return TemplateFileLocatorInterface
     */
    private function getFileLocatorMock($path)
    {
        $locator = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateFileLocatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $locator->expects($this->any())
            ->method('locate')
            ->will($this->returnValue($path));

        return $locator;
    }

    /**
     * @param $path
     *
     * @return TemplateNameResolverInterface
     */
    private function getTemplateNameResolverMock($name)
    {
        $locator = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateNameResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $locator->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($name));

        return $locator;
    }
}
