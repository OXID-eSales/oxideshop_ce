<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Smarty\Plugin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

final class FunctionOxEvalTest extends UnitTestCase
{
    private string $templatePath;
    private string $unparsedValue = '[{1|cat:2|cat:3}]';
    private array $contextData;
    private string $parsedValue = '123';
    private TemplateRendererInterface $renderer;
    private string $template = '[{oxeval var=$someObject->someProperty}]';
    private string $templateWithForceParam = '[{oxeval var=$someObject->someProperty force=1}]';

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareTestData();
    }

    protected function tearDown(): void
    {
        $this->clearTestData();
        parent::tearDown();
    }

    public function testRender(): void
    {
        $this->createTemplateFile($this->template);

        $renderedTemplate = $this->renderer->renderTemplate($this->templatePath, $this->contextData);

        $this->assertSame($this->parsedValue, $renderedTemplate);
    }

    public function testRenderWithConfigForLongDescriptionsOff(): void
    {
        $this->createTemplateFile($this->template);
        Registry::getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', 0);

        $renderedTemplate = $this->renderer->renderTemplate($this->templatePath, $this->contextData);

        $this->assertSame($this->unparsedValue, $renderedTemplate);
    }

    public function testRenderWithConfigForLongDescriptionsOffAndForceParam(): void
    {
        $this->createTemplateFile($this->templateWithForceParam);
        Registry::getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', 0);
        $this->contextData['force'] = true;

        $renderedTemplate = $this->renderer->renderTemplate($this->templatePath, $this->contextData);

        $this->assertSame($this->parsedValue, $renderedTemplate);
    }

    public function testRenderWithDeactivatedConfig(): void
    {
        $this->createTemplateFile($this->template);
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);

        $renderedTemplate = $this->renderer->renderTemplate($this->templatePath, $this->contextData);

        $this->assertSame($this->unparsedValue, $renderedTemplate);
    }

    public function testRenderWithDeactivatedConfigAndForceParam(): void
    {
        $this->createTemplateFile($this->templateWithForceParam);
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);

        $renderedTemplate = $this->renderer->renderTemplate($this->templatePath, $this->contextData);

        $this->assertSame($this->unparsedValue, $renderedTemplate);
    }

    private function prepareTestData(): void
    {
        $this->renderer = ContainerFactory::getInstance()->getContainer()->get(TemplateRendererInterface::class);
        $this->contextData = [
            'someObject' => (object) [
                'someProperty' => $this->unparsedValue,
                ],
        ];
    }

    private function clearTestData(): void
    {
        $this->removeTemplateFile();
    }

    private function createTemplateFile(string $contents): void
    {
        $this->templatePath = Path::join(
            $this->getTemplateDir(),
            uniqid('test-tpl-', true)
        );
        file_put_contents($this->templatePath, $contents);
    }

    private function getTemplateDir(): string
    {
        $templateDir = Registry::getUtilsView()->getTemplateDirs(false)[0];
        /** @var Filesystem $filesystem */
        $filesystem = ContainerFactory::getInstance()->getContainer()->get('oxid_esales.symfony.file_system');
        if (!$filesystem->exists($templateDir)) {
            $filesystem->mkdir($templateDir);
        }
        return $templateDir;
    }

    private function removeTemplateFile(): void
    {
        if (is_file($this->templatePath)) {
            unlink($this->templatePath);
        }
    }
}
