<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter;

use DOMDocument;
use DOMXPath;

class HtmlFilter implements HtmlFilterInterface
{
    public function __construct(private readonly HtmlRemoverInterface $htmlRemover)
    {
    }

    public function filter(string $html): string
    {
        $doc = new DOMDocument();
        $doc->loadHTML("<div>$html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($doc);
        foreach ($xpath->query('//script') as $node) {
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
