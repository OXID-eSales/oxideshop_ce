<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\TokenParser;

use OxidEsales\EshopCommunity\Internal\Twig\TokenParser\IfContentTokenParser;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Parser;
use Twig\Source;
use Twig\Token;

class IfContentTokenParserTest extends TestCase
{

    /** @var Environment */
    private $environment;

    /** @var Parser */
    private $parser;

    /** @var IfContentTokenParser */
    private $ifContentParser;

    /**
     * Set up
     */
    protected function setUp()
    {
        /** @var LoaderInterface $loader */
        $loader = $this->getMockBuilder('Twig_LoaderInterface')->getMock();
        $this->environment = new \Twig_Environment($loader, ['cache' => false]);

        $this->ifContentParser = new IfContentTokenParser();
        $this->environment->addTokenParser($this->ifContentParser);

        $this->parser = new Parser($this->environment);
    }

    /**
     * @covers IfContentTokenParser::getTag
     */
    public function testGetTag()
    {
        $this->assertEquals('ifcontent', $this->ifContentParser->getTag());
    }

    /**
     * @covers IfContentTokenParser::parse
     */
    public function testParse()
    {
        $source = "{% ifcontent ident \"oxsomething\" set myVar %}Lorem Ipsum{% endifcontent %}";

        $stream = $this->environment->tokenize(new Source($source, 'index'));
        $node = $this->parser->parse($stream);

        $this->assertTrue($node->hasNode('body'));

        $bodyNode = $node->getNode('body');

        $ifContentNode = $bodyNode->getIterator()[0];

        $this->assertTrue($ifContentNode->hasNode('body'));
        $this->assertTrue($ifContentNode->hasNode('variable'));
        $this->assertTrue($ifContentNode->hasNode('ident'));
    }

    /**
     * @covers IfContentTokenParser::decideBlockEnd
     */
    public function testDecideBlockEnd()
    {
        $token = new Token(Token::NAME_TYPE, 'foo', 1);
        $this->assertEquals(false, $this->ifContentParser->decideBlockEnd($token));

        $token = new Token(Token::NAME_TYPE, 'endifcontent', 1);
        $this->assertEquals(true, $this->ifContentParser->decideBlockEnd($token));
    }
}
