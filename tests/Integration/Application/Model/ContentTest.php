<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContentTest extends IntegrationTestCase
{
    #[Test]
    #[RunInSeparateProcess]
    #[DataProvider('contentProvider')]
    public function filterContentOn(string $template, string $expectedTemplate): void
    {
        $this->setupContainer();

        $content = $this->prepareContent($template);
        $this->assertEquals($expectedTemplate, $content->getFieldData('oxcontent'));
    }

    #[Test]
    #[DataProvider('contentProvider')]
    public function filterContentOff(string $template, string $expectedTemplate): void
    {
        $content = $this->prepareContent($template);
        $this->assertEquals($template, $content->getFieldData('oxcontent'));
    }

    /**
     * @deprecated Use methods from ContainerTrait in shop 8.0
     */
    private function setupContainer(): void
    {
        $container = (new TestContainerFactory())->create();
        $container->setParameter('oxid_esales.templating.filter_content_tags', true);
        $container->compile();

        $this->attachContainerToContainerFactory($container);
    }

    /**
     * @deprecated Use methods from ContainerTrait in shop 8.0
     */
    private function attachContainerToContainerFactory(ContainerBuilder $container): void
    {
        $reflectionClass = new ReflectionClass(ContainerFactory::getInstance());
        $reflectionProperty = $reflectionClass->getProperty('symfonyContainer');
        $reflectionProperty->setValue(ContainerFactory::getInstance(), $container);
    }

    private function prepareContent(string $template): Content
    {
        $content = oxNew(Content::class);
        $content->setId('id1');
        $content->assign([
            'oxloadid' => 'id1',
            'oxcontent' => $template,
        ]);
        $content->save();

        $content = oxNew(Content::class);
        $content->load('id1');
        return $content;
    }

    public static function contentProvider(): array
    {
        return [
            [
                'template' => '<p>par 1</p>',
                'expectedTemplate' => '<p>par 1</p>',
            ],
            [
                'template' => '<p>par 1</p><script src="app.js"/>',
                'expectedTemplate' => '<p>par 1</p>',
            ],
            [
                'template' => '<h1>h1</h1><script>//inner1</script><h2>h2</h2><script>//inner2</script><h3>h3</h3>',
                'expectedTemplate' => '<h1>h1</h1><h2>h2</h2><h3>h3</h3>',
            ],
        ];
    }
}
