<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

//THIS FILE IS IN LATIN1 AND NOT IN UTF
//bellow is one O uml char
//�

class StrMbTest extends \OxidTestCase
{
    /** @var string */
    protected $_sStrNeedle = "�";

    /** @var string */
    protected $_sStrHaystack = "Design Bau - auf zu neuen H�hen. H� h�.";

    /** @var string */
    protected $_sStrUpperCase = "H� H�";

    /** @var string */
    protected $_sStrHtmlEntities = "H� H� <b>bold</b>&amp;";

    /** @var oxStrMb */
    protected $_oSubj = null;

    public function setup()
    {
        parent::setUp();
        $this->_oSubj = oxNew('oxStrMb');
    }

    public function testStrlen()
    {
        $this->assertEquals(
            1,
            $this->_oSubj->strlen($this->_2Utf($this->_sStrNeedle))
        );
    }

    public function testSubstr2Params()
    {
        $this->assertEquals(
            $this->_2Utf("H� h�."),
            $this->_oSubj->substr($this->_2Utf($this->_sStrHaystack), 33)
        );
    }

    public function testSubstr3Params()
    {
        $this->assertEquals(
            $this->_2Utf("Design Bau - auf zu neuen H�he"),
            $this->_oSubj->substr($this->_2Utf($this->_sStrHaystack), 0, 30)
        );
    }

    public function testStrpos2Params()
    {
        $this->assertEquals(
            26,
            $this->_oSubj->strpos($this->_2Utf($this->_sStrHaystack), $this->_2Utf("H�hen"))
        );
    }

    public function testStrpos3Params()
    {
        $this->assertEquals(
            33,
            $this->_oSubj->strpos($this->_2Utf($this->_sStrHaystack), $this->_2Utf("H�"), 27)
        );
    }

    public function testStrstr()
    {
        $this->assertEquals(
            $this->_2Utf("H�hen. H� h�."),
            $this->_oSubj->strstr($this->_2Utf($this->_sStrHaystack), $this->_2Utf("H�"))
        );
    }

    public function testStrstrEmptyString()
    {
        $this->assertFalse($this->_oSubj->strstr($this->_2Utf(""), $this->_2Utf(",")));
    }

    public function testStrtolower()
    {
        $this->assertEquals(
            $this->_2Utf("h� h�"),
            $this->_oSubj->strtolower($this->_2Utf($this->_sStrUpperCase))
        );
    }

    public function testStrtoupper()
    {
        $this->assertEquals(
            $this->_2Utf("H� H�"),
            $this->_oSubj->strtoupper($this->_2Utf("h� h�"))
        );
    }

    public function testHtmlspecialchars()
    {
        $this->assertEquals(
            $this->_2Utf("H� H� &lt;b&gt;bold&lt;/b&gt;&amp;amp;"),
            $this->_oSubj->htmlspecialchars($this->_2Utf($this->_sStrHtmlEntities))
        );
    }

    public function testHtmlentities()
    {
        $this->assertEquals(
            $this->_2Utf("H&Ouml; H&Ouml; &lt;b&gt;bold&lt;/b&gt;&amp;amp;"),
            $this->_oSubj->htmlentities($this->_2Utf($this->_sStrHtmlEntities))
        );
    }

    public function testHtmlEtityDecode()
    {
        $this->assertEquals(
            $this->_2Utf($this->_sStrHtmlEntities),
            $this->_oSubj->html_entity_decode($this->_2Utf("H&Ouml; H&Ouml; &lt;b&gt;bold&lt;/b&gt;&amp;amp;"))
        );
    }

    public function testPregSplit()
    {
        $this->assertEquals(
            array($this->_2Utf("H�"), $this->_2Utf("H�")),
            $this->_oSubj->preg_split('/ /', $this->_2Utf($this->_sStrUpperCase))
        );
    }

    public function testPregReplace()
    {
        $this->assertEquals(
            $this->_2Utf("H�_H�"),
            $this->_oSubj->preg_replace('/ /', '_', $this->_2Utf($this->_sStrUpperCase))
        );
    }

    public function testPregReplaceArray()
    {
        $this->assertEquals(
            $this->_2Utf("H�_H�"),
            $this->_oSubj->preg_replace(array('/ /', '|//+|'), '_', $this->_2Utf($this->_sStrUpperCase))
        );
    }


    public function testPregReplaceCallback()
    {
        $callBack = function ($matches) { return "_";};
        $this->assertEquals(
            $this->_2Utf("H�_H�"),
            $this->_oSubj->preg_replace_callback('/ /', $callBack, $this->_2Utf($this->_sStrUpperCase))
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
            $this->_oSubj->preg_match('/Bau/', $this->_2Utf($this->_sStrHaystack))
        );
        $aRes = array();
        $this->_oSubj->preg_match('/Bau/', $this->_2Utf($this->_sStrHaystack), $aRes);
        $this->assertEquals(array('Bau'), $aRes);
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

    public function testUcfirst()
    {
        $this->assertEquals($this->_2Utf('�l'), $this->_oSubj->ucfirst($this->_2Utf('�l')));
    }

    public function testWordwrapForUtf()
    {
        $this->assertEquals(
            $this->_2Utf("H�\nH�"),
            $this->_oSubj->wordwrap($this->_2Utf($this->_sStrUpperCase), 2)
        );
        $this->assertEquals(
            $this->_2Utf("H�\na\nH�\na"),
            $this->_oSubj->wordwrap($this->_2Utf("H�a H�a"), 2, "\n", true)
        );
        $this->assertEquals(
            $this->_2Utf("H�a\na\nH�a\na"),
            $this->_oSubj->wordwrap($this->_2Utf("H�aa H�aa"), 3, "\n", true)
        );
        $this->assertEquals(
            $this->_2Utf("H�a\naH�\naa"),
            $this->_oSubj->wordwrap($this->_2Utf("H�aaH�aa"), 3, "\n", true)
        );
        $this->assertEquals(
            $this->_2Utf("H�a\nH�a"),
            $this->_oSubj->wordwrap($this->_2Utf("H�a H�a"), 2, "\n")
        );
        $this->assertEquals(
            $this->_2Utf("H�a\nH�a\nH�a\nH� a�\n�"),
            $this->_oSubj->wordwrap($this->_2Utf("H�a H�a H�a H� a� �"), 5, "\n")
        );
        $this->assertEquals(
            $this->_2Utf("H�a\nH�a\nH�a\nH� a�\n�"),
            $this->_oSubj->wordwrap($this->_2Utf("H�a H�a H�a H� a� �"), 5, "\n"), true
        );
    }

    public function testWordwrapMimicsPhpInternalWW()
    {
        // check if it really acts same as intended
        $this->assertEquals(
            wordwrap('aaa aaa', 2, "\n", false),
            $this->_oSubj->wordwrap('aaa aaa', 2, "\n", false)
        );
        $this->assertEquals(
            wordwrap('aaa aaa', 2, "\n", true),
            $this->_oSubj->wordwrap('aaa aaa', 2, "\n", true)
        );

        $this->assertEquals(
            wordwrap('aaa aaa a', 5, "\n", false),
            $this->_oSubj->wordwrap('aaa aaa a', 5, "\n", false)
        );
        $this->assertEquals(
            wordwrap('aaa aaa', 5, "\n", true),
            $this->_oSubj->wordwrap('aaa aaa', 5, "\n", true)
        );
        $this->assertEquals(
            wordwrap('   aaa    aaa', 2, "\n", false),
            $this->_oSubj->wordwrap('   aaa    aaa', 2, "\n", false)
        );
        $this->assertEquals(
            wordwrap('   aaa    aaa', 2, "\n", true),
            $this->_oSubj->wordwrap('   aaa    aaa', 2, "\n", true)
        );
        $this->assertEquals(
            wordwrap('   aaa    aaa', 5, "\n", false),
            $this->_oSubj->wordwrap('   aaa    aaa', 5, "\n", false)
        );
        $this->assertEquals(
            wordwrap('   aaa    aaa', 5, "\n", true),
            $this->_oSubj->wordwrap('   aaa    aaa', 5, "\n", true)
        );

        // very important:
        $this->assertEquals(
            wordwrap("laba diena, kjabsdjhb hb bhb bhbhh\n as esu sarunas tekste.\n o chia buvo new line. arvydas.\naaa    aaa", 10, "\n", true),
            $this->_oSubj->wordwrap("laba diena, kjabsdjhb hb bhb bhbhh\n as esu sarunas tekste.\n o chia buvo new line. arvydas.\naaa    aaa", 10, "\n", true)
        );
    }

    public function testRecodeEntities()
    {
        $this->assertEquals(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig;', $this->_oSubj->recodeEntities($this->_2Utf(' � � � � � � �'), true));
        $this->assertEquals($this->_2Utf(' � � � � � � � &amp;'), $this->_oSubj->recodeEntities(' &auml; &ouml; &uuml; &Auml; &Ouml; &Uuml; &szlig; &', false, array('&amp;'), array('&')));
    }

    public function testHasSpecialChars()
    {
        $this->assertEquals(1, $this->_oSubj->hasSpecialChars($this->_2Utf(' � � � � � � �')));
        $this->assertEquals(0, $this->_oSubj->hasSpecialChars('aaaa'));
    }

    public function testCleanStr()
    {
        $this->assertEquals(" \" " . '\'' . " : ! ?            ", $this->_oSubj->cleanStr(" \" " . '\'' . " : ! ? \n \r \t \xc2\x95 \xc2\xa0 ;"));
    }

    public function testCleanStrLeavesDots()
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
