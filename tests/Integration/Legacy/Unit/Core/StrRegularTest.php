<?php

/**
 * Copyright � OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

//THIS FILE IS IN LATIN1 AND NOT IN UTF
//bellow is one O uml char
//�


//Warning: StrRegular functions should not be ever called with utf string params,
//we test the behaviour here which actually is expected with given params, but wrong.

class StrRegularTest extends \PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $_sStrNeedle = "�";

    /** @var string */
    protected $_sStrHaystack = "Design Bau - auf zu neuen H�hen. H� h�.";

    /** @var string */
    protected $_sStrUpperCase = "H� H�";

    /** @var string */
    protected $_sStrHtmlEntities = "H� H� <b>bold</b>&amp;";

    /** @var oxStrRegular */
    protected $_oSubj;

    protected function setup(): void
    {
        parent::setUp();

        $this->_oSubj = oxNew('oxStrRegular');
    }

    public function testSameMethodsAsStrMb()
    {
        $aStrMbMethods = get_class_methods('oxStrMb');
        $aStrRegulraMethods = get_class_methods('oxStrRegular');

        $this->assertEquals($aStrRegulraMethods, $aStrMbMethods);
    }

    public function testStrlen()
    {
        $this->assertSame(
            2,
            $this->_oSubj->strlen($this->_2Utf($this->_sStrNeedle))
        );
    }

    public function testSubstr2Params()
    {
        $this->assertEquals(
            $this->_2Utf(" H� h�."),
            $this->_oSubj->substr($this->_2Utf($this->_sStrHaystack), 33)
        );
    }

    public function testSubstr3Params()
    {
        $this->assertEquals(
            $this->_2Utf("Design Bau - auf zu neuen H�h"),
            $this->_oSubj->substr($this->_2Utf($this->_sStrHaystack), 0, 30)
        );
    }

    public function testStrpos2Params()
    {
        $this->assertSame(
            26,
            $this->_oSubj->strpos($this->_sStrHaystack, "H�hen")
        );
    }

    /**
     * oxStrMb::preg_match_all() test case
     */
    public function testPregMatchAll()
    {
        $aRez = [['a', 'b', 'c']];
        $this->assertSame(3, $this->_oSubj->preg_match_all("/[^\-]+/", $this->_2Utf("a-b-c"), $aMatches));
        $this->assertSame($aRez, $aMatches);
    }

    public function testStrpos3Params()
    {
        $this->assertSame(
            33,
            $this->_oSubj->strpos($this->_sStrHaystack, "H�", 27)
        );
    }

    public function testStrstr()
    {
        $this->assertEquals(
            $this->_2Utf("H�hen. H� h�."),
            $this->_oSubj->strstr($this->_2Utf($this->_sStrHaystack), $this->_2Utf("H�"))
        );
    }

    public function testStrtolower()
    {
        $this->assertSame(
            "h� h�",
            $this->_oSubj->strtolower($this->_sStrUpperCase)
        );
    }

    public function testStrtoupper()
    {
        $this->assertSame(
            "H� H�",
            $this->_oSubj->strtoupper("h� h�")
        );
    }

    public function testHtmlspecialchars()
    {
        $this->assertSame(
            "H� H� &lt;b&gt;bold&lt;/b&gt;&amp;amp;",
            $this->_oSubj->htmlspecialchars($this->_sStrHtmlEntities)
        );
    }

    public function testHtmlentities()
    {
        $this->assertSame(
            "H&Ouml; H&Ouml; &lt;b&gt;bold&lt;/b&gt;&amp;amp;",
            $this->_oSubj->htmlentities($this->_sStrHtmlEntities)
        );
    }

    public function testHtmlEntityDecode()
    {
        $this->assertSame(
            $this->_sStrHtmlEntities,
            $this->_oSubj->html_entity_decode("H&Ouml; H&Ouml; &lt;b&gt;bold&lt;/b&gt;&amp;amp;")
        );
    }

    public function testPregSplit()
    {
        $this->assertSame(
            ["H�", "H�"],
            $this->_oSubj->preg_split('/ /', $this->_sStrUpperCase)
        );
    }

    public function testPregReplace()
    {
        $this->assertSame(
            "H�_H�",
            $this->_oSubj->preg_replace('/ /', '_', $this->_sStrUpperCase)
        );
    }

    public function testPregReplaceArray()
    {
        $this->assertSame(
            "H�_H�",
            $this->_oSubj->preg_replace(['/ /', '|//+|'], '_', $this->_sStrUpperCase)
        );
    }

    public function testPregReplaceCallback()
    {
        $callBack = fn($matches) => "_";
        $this->assertSame(
            "H�_H�",
            $this->_oSubj->preg_replace_callback('/ /', $callBack, $this->_sStrUpperCase)
        );
    }

    public function testPregReplaceCallbackArray()
    {
        $callBack = fn($matches) => "_";
        $this->assertEquals(
            $this->_2Utf("H�_H�"),
            $this->_oSubj->preg_replace_callback(['/ /', '|//+|'], $callBack, $this->_2Utf($this->_sStrUpperCase))
        );
    }

    public function testPregMatch()
    {
        $this->assertSame(
            1,
            $this->_oSubj->preg_match('/Bau/', $this->_sStrHaystack)
        );
        $aRes = [];
        $this->_oSubj->preg_match('/Bau/', $this->_sStrHaystack, $aRes);
        $this->assertSame(['Bau'], $aRes);
    }

    public function testUcfirst()
    {
        // with umlaut's doesn't work
        $this->assertSame('�l', $this->_oSubj->ucfirst('�l'));
    }

    public function testWordwrap()
    {
        $this->assertSame(
            "H�\nH�",
            $this->_oSubj->wordwrap($this->_sStrUpperCase, 2)
        );
        $this->assertSame(
            "H�\na\nH�\na",
            $this->_oSubj->wordwrap("H�a H�a", 2, "\n", true)
        );
        $this->assertSame(
            "H�a\na\nH�a\na",
            $this->_oSubj->wordwrap("H�aa H�aa", 3, "\n", true)
        );
        $this->assertSame(
            "H�a\nH�a",
            $this->_oSubj->wordwrap("H�a H�a", 2, "\n")
        );
    }

    public function testRecodeEntities()
    {
        $this->assertSame(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig;', $this->_oSubj->recodeEntities(' � � � � � � �', true));
        $this->assertSame(' � � � � � � � &amp;', $this->_oSubj->recodeEntities(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', false, ['&amp;'], ['&']));
    }

    public function testHasSpecialChars()
    {
        $this->assertSame(1, $this->_oSubj->hasSpecialChars(' � � � � � � �'));
        $this->assertSame(0, $this->_oSubj->hasSpecialChars('aaaa'));
    }

    public function testCleanStr()
    {
        $this->assertSame(' " \' : ! ?            ', $this->_oSubj->cleanStr(' " \'' . " : ! ? \n \r \t \x95 \xa0 ;"));
    }

    public function testCleanStrLeavsDots()
    {
        $this->assertSame('.  ', $this->_oSubj->cleanStr(". ;"));
    }

    public function testJsonEncode()
    {
        $this->assertSame('[". ;","asdasd",{"asd":"asdasd","0":"asda"}]', $this->_oSubj->jsonEncode([". ;", 'asdasd', ['asd' => 'asdasd', 'asda']]));
        $this->assertSame('[". ;","asdasd",{"asd":"as\n\t\\\\d\\\\a\\\\\'\"[]{sd","0":"asda"}]', $this->_oSubj->jsonEncode([". ;", 'asdasd', ['asd' => "as\n\t\\d\a\'\"[]{sd", 'asda']]));
    }

    public function testStripTags()
    {
        $this->assertSame('without styling definition.', $this->_oSubj->strip_tags('<div>without</div> <style type="text/css">p {color:blue;}</style>styling definition.'));
        $this->assertSame('with <style type="text/css">p {color:blue;}</style>styling definition.', $this->_oSubj->strip_tags('<div>with</div> <style type="text/css">p {color:blue;}</style>styling definition.', '<style>'));
    }
}
