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

class StrRegularTest extends \OxidTestCase
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
    protected $_oSubj = null;

    public function setup()
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
        $this->assertEquals(
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
        $this->assertEquals(
            26,
            $this->_oSubj->strpos($this->_sStrHaystack, "H�hen")
        );
    }

    /**
     * oxStrMb::preg_match_all() test case
     *
     * @return null
     */
    public function testPregMatchAll()
    {
        $aRez = array(array('a', 'b', 'c'));
        $this->assertEquals(3, $this->_oSubj->preg_match_all("/[^\-]+/", $this->_2Utf("a-b-c"), $aMatches));
        $this->assertEquals($aRez, $aMatches);
    }

    public function testStrpos3Params()
    {
        $this->assertEquals(
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
        $this->assertEquals(
            "h� h�",
            $this->_oSubj->strtolower($this->_sStrUpperCase)
        );
    }

    public function testStrtoupper()
    {
        $this->assertEquals(
            "H� H�",
            $this->_oSubj->strtoupper("h� h�")
        );
    }

    public function testHtmlspecialchars()
    {
        $this->assertEquals(
            "H� H� &lt;b&gt;bold&lt;/b&gt;&amp;amp;",
            $this->_oSubj->htmlspecialchars($this->_sStrHtmlEntities)
        );
    }

    public function testHtmlentities()
    {
        $this->assertEquals(
            "H&Ouml; H&Ouml; &lt;b&gt;bold&lt;/b&gt;&amp;amp;",
            $this->_oSubj->htmlentities($this->_sStrHtmlEntities)
        );
    }

    public function testHtmlEntityDecode()
    {
        $this->assertEquals(
            $this->_sStrHtmlEntities,
            $this->_oSubj->html_entity_decode("H&Ouml; H&Ouml; &lt;b&gt;bold&lt;/b&gt;&amp;amp;")
        );
    }

    public function testPregSplit()
    {
        $this->assertEquals(
            array("H�", "H�"),
            $this->_oSubj->preg_split('/ /', $this->_sStrUpperCase)
        );
    }

    public function testPregReplace()
    {
        $this->assertEquals(
            "H�_H�",
            $this->_oSubj->preg_replace('/ /', '_', $this->_sStrUpperCase)
        );
    }

    public function testPregReplaceArray()
    {
        $this->assertEquals(
            "H�_H�",
            $this->_oSubj->preg_replace(array('/ /', '|//+|'), '_', $this->_sStrUpperCase)
        );
    }

    public function testPregReplaceCallback()
    {
        $callBack = function ($matches) { return "_";};
        $this->assertEquals(
            "H�_H�",
            $this->_oSubj->preg_replace_callback('/ /', $callBack, $this->_sStrUpperCase)
        );
    }

    public function testPregReplaceCallbackArray()
    {
        $callBack = function ($matches) { return "_";};
        $this->assertEquals(
            $this->_2Utf("H�_H�"),
            $this->_oSubj->preg_replace_callback(array('/ /', '|//+|'), $callBack, $this->_2Utf($this->_sStrUpperCase))
        );
    }

    public function testPregMatch()
    {
        $this->assertEquals(
            1,
            $this->_oSubj->preg_match('/Bau/', $this->_sStrHaystack)
        );
        $aRes = array();
        $this->_oSubj->preg_match('/Bau/', $this->_sStrHaystack, $aRes);
        $this->assertEquals(array('Bau'), $aRes);
    }

    public function testUcfirst()
    {
        // with umlaut's doesn't work
        $this->assertEquals('�l', $this->_oSubj->ucfirst('�l'));
    }

    public function testWordwrap()
    {
        $this->assertEquals(
            "H�\nH�",
            $this->_oSubj->wordwrap($this->_sStrUpperCase, 2)
        );
        $this->assertEquals(
            "H�\na\nH�\na",
            $this->_oSubj->wordwrap("H�a H�a", 2, "\n", true)
        );
        $this->assertEquals(
            "H�a\na\nH�a\na",
            $this->_oSubj->wordwrap("H�aa H�aa", 3, "\n", true)
        );
        $this->assertEquals(
            "H�a\nH�a",
            $this->_oSubj->wordwrap("H�a H�a", 2, "\n")
        );
    }

    public function testRecodeEntities()
    {
        $this->assertEquals(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig;', $this->_oSubj->recodeEntities(' � � � � � � �', true));
        $this->assertEquals(' � � � � � � � &amp;', $this->_oSubj->recodeEntities(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', false, array('&amp;'), array('&')));
    }

    public function testHasSpecialChars()
    {
        $this->assertEquals(1, $this->_oSubj->hasSpecialChars(' � � � � � � �'));
        $this->assertEquals(0, $this->_oSubj->hasSpecialChars('aaaa'));
    }

    public function testCleanStr()
    {
        $this->assertEquals(" \" " . '\'' . " : ! ?            ", $this->_oSubj->cleanStr(" \" " . '\'' . " : ! ? \n \r \t \x95 \xa0 ;"));
    }

    public function testCleanStrLeavsDots()
    {
        $this->assertEquals('.  ', $this->_oSubj->cleanStr(". ;"));
    }

    public function testJsonEncode()
    {
        $this->assertEquals('[". ;","asdasd",{"asd":"asdasd","0":"asda"}]', $this->_oSubj->jsonEncode(array(". ;", 'asdasd', array('asd' => 'asdasd', 'asda'))));
        $this->assertEquals('[". ;","asdasd",{"asd":"as\n\t\\\\d\\\\a\\\\\'\"[]{sd","0":"asda"}]', $this->_oSubj->jsonEncode(array(". ;", 'asdasd', array('asd' => "as\n\t\\d\a\'\"[]{sd", 'asda'))));
    }

    public function testStripTags()
    {
        $this->assertEquals('without styling definition.', $this->_oSubj->strip_tags('<div>without</div> <style type="text/css">p {color:blue;}</style>styling definition.'));
        $this->assertEquals('with <style type="text/css">p {color:blue;}</style>styling definition.', $this->_oSubj->strip_tags('<div>with</div> <style type="text/css">p {color:blue;}</style>styling definition.', '<style>'));
    }
}
