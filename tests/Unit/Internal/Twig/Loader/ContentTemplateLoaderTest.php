<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Loader;

use OxidEsales\EshopCommunity\Internal\Twig\Loader\ContentTemplateLoader;
use OxidEsales\EshopCommunity\Internal\Twig\TemplateLoaderNameParser;
use PHPUnit\Framework\TestCase;

/**
 * Class ContentTemplateLoaderTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class ContentTemplateLoaderTest extends TestCase
{

    /** @var ContentTemplateLoader */
    private $contentTemplateLoader;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->contentTemplateLoader = new ContentTemplateLoader(new TemplateLoaderNameParser());
    }

    public function testGetSourceContext()
    {

    }

    public function testExists()
    {

    }

    public function testIsFresh()
    {

    }

    public function testGetCacheKey()
    {

    }
}
