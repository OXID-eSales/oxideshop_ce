<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\HtmlFilter;

use DOMNode;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter\HtmlFilter;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter\HtmlRemoverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HtmlFilterTest extends TestCase
{
    #[Test]
    #[DataProvider('filterNoneProvider')]
    public function filterNone(string $html): void
    {
        $remover = $this->createMock(HtmlRemoverInterface::class);
        $remover
            ->expects($this->never())
            ->method('remove');
        $filter = new HtmlFilter($remover);

        $filter->filter($html);
    }

    public static function filterNoneProvider(): array
    {
        return [
            ['html' => ''],
            ['html' => '<div></div>'],
            ['html' => '<div><span>content</span><b>bold</b></div>'],
        ];
    }

    #[Test]
    #[DataProvider('filterOneProvider')]
    public function filterOne(string $html): void
    {
        $remover = $this->createMock(HtmlRemoverInterface::class);
        $remover
            ->expects($this->once())
            ->method('remove')
            ->with($this->callback(function (DOMNode $node) {
                return $node->nodeName == 'script' && $node->textContent == '//content';
            }));
        $filter = new HtmlFilter($remover);

        $filter->filter($html);
    }

    public static function filterOneProvider(): array
    {
        return [
            ['html' => '<div><script>//content</script></div>'],
            ['html' => '<div><span>content</span><script>//content</script></div>'],
            ['html' => '<div><span>content<script>//content</script></span><b>bold</b></div>'],
        ];
    }

    #[Test]
    public function filterOneClosedTag(): void
    {
        $remover = $this->createMock(HtmlRemoverInterface::class);
        $remover
            ->expects($this->once())
            ->method('remove')
            ->with($this->callback(function (DOMNode $node) {
                $isAttributeValid = count($node->attributes) == 1
                    && $node->attributes[0]->nodeName == 'src'
                    && $node->attributes[0]->nodeValue == 'app.js';
                return $node->nodeName == 'script' && $isAttributeValid;
            }));
        $filter = new HtmlFilter($remover);

        $filter->filter('<div><script src="app.js"/></div>');
    }

    #[Test]
    public function filterMore(): void
    {
        $remover = $this->createMock(HtmlRemoverInterface::class);
        $remover
            ->expects($this->exactly(2))
            ->method('remove')
            ->with($this->callback(function (DOMNode $node) {
                return $node->nodeName == 'script' && $node->textContent == '//content';
            }));
        $filter = new HtmlFilter($remover);

        $filter->filter('<div><script>//content</script><script>//content</script></div>');
    }
}
