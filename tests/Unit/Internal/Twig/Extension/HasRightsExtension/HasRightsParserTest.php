<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension\HasRightsExtension;

use OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsExtension;
use OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsParser;
use PHPUnit\Framework\TestCase;
use Twig\Loader\ArrayLoader;

class HasRightsParserTest extends TestCase
{
    /**
     * @var HasRightsParser
     */
    private $hasRightsParser;

    protected function setUp()
    {
        $env = $this->getEnv();
        $parser = new \Twig_Parser($env);
        $this->hasRightsParser = new HasRightsParser();
        $this->hasRightsParser->setParser($parser);
        parent::setUp();
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsParser:getTag
     */
    public function testGetTag()
    {
        $this->assertEquals('hasrights', $this->hasRightsParser->getTag());
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsParser:decideMyTagFork
     */
    public function testDecideMyTagForkIncorrect()
    {
        $token = new \Twig_Token(\Twig_Token::TEXT_TYPE, 1, 1);
        $this->assertEquals(false, $this->hasRightsParser->decideMyTagFork($token));
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsParser:decideMyTagFork
     */
    public function testDecideMyTagForkCorrect()
    {
        $token = new \Twig_Token(5, 'endhasrights', 1);
        $this->assertEquals(true, $this->hasRightsParser->decideMyTagFork($token));
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsParser:parse
     */
    public function testParse()
    {
        /**
         * @var \Twig_LoaderInterface $loader
         */
        $loader = $this->getMockBuilder('Twig_LoaderInterface')->getMock();
        $env = new \Twig_Environment($loader, array('cache' => false, 'autoescape' => false));
        $env->addExtension(new HasRightsExtension());

        $stream = $env->parse($env->tokenize(new \Twig_Source('{% hasrights {\'id\' : \'1\'} %}{% endhasrights %}', 'index')));
        $stream->compile(new \Twig_Compiler($env));

        $tokens = $env->getTags();
        $extensions = $env->getExtensions();

        $this->assertTrue(isset($tokens['hasrights']));
        $this->assertTrue(isset($extensions['OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsExtension']));
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension\HasRightsParser:parse
     * @expectedException \Twig_Error_Syntax
     */
    public function testParseException()
    {
        /**
         * @var \Twig_LoaderInterface $loader
         */
        $loader = $this->getMockBuilder('Twig_LoaderInterface')->getMock();
        $env = new \Twig_Environment($loader, array('cache' => false, 'autoescape' => false));
        $env->addExtension(new HasRightsExtension());

        $stream = $env->parse($env->tokenize(new \Twig_Source('{% hasrights {\'id\' : \'1\'} %}{% foo %}', 'index')));
        $stream->compile(new \Twig_Compiler($env));

        $this->expectExceptionMessage('Twig_Error_Syntax : Unexpected "foo" tag (expecting closing tag for the "hasrights" tag defined near line 1) in "index" at line 1.');
    }

    /**
     * @return \Twig_Environment
     */
    private function getEnv()
    {
        $loader = new ArrayLoader(['tokens' => 'foo']);
        $env = new \Twig_Environment($loader, ['debug' => false, 'cache' => false]);
        if(!$env->hasExtension('hasrights')) {
            $env->addExtension(new HasRightsExtension());
            $env->addTokenParser(new HasRightsParser());
        }
        return $env;
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
