<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter;

use DOMDocument;

class HtmlFilter implements HtmlFilterInterface
{
    public function __construct(private readonly HtmlRemoverInterface $htmlRemover)
    {
    }

    public function filter(string $html): string
    {
        $doc = new DOMDocument();
        $doc->loadHTML("<div>$html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $list = $doc->getElementsByTagName('script');
        for ($i = $list->length - 1; $i >= 0; --$i) {
            $node = $list->item($i);
            $this->htmlRemover->remove($node);
        }

        return $this->getInnerHtml($doc);
    }

    private function getInnerHtml(DOMDocument $doc): string
    {
        $html = '';
        foreach ($doc->documentElement->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }

        return $html;
    }
}
