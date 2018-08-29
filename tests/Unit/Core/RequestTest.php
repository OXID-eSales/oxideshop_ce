<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\EshopCommunity\Core\Request;
use stdClass;

class RequestTest extends \OxidTestCase
{
    public function testGetRequestEscapedParameter()
    {
        $request = oxNew(Request::class);
        $_POST['postKey'] = '&test';

        $this->assertSame('&amp;test', $request->getRequestEscapedParameter('postKey'));
    }

    public function testGetRequestEscapedParameterWhenParameterNotFound()
    {
        $request = oxNew(Request::class);

        $this->assertSame(null, $request->getRequestEscapedParameter('notExistingPostKey'));
    }

    public function testGetRequestEscapedParameterWhenParameterNotFoundAndDefaultValueIsProvided()
    {
        $request = oxNew(Request::class);

        $this->assertSame('defaultValue', $request->getRequestEscapedParameter('notExistingPostKey', 'defaultValue'));
    }

    public function testGetRequestRawParameterFromPost()
    {
        $request = oxNew(Request::class);
        $_POST['postKey'] = 'testValue';

        $this->assertSame('testValue', $request->getRequestParameter('postKey'));
    }

    public function testGetRequestRawParameterFromGet()
    {
        $request = oxNew(Request::class);
        $_GET['getKey'] = 'testValue';

        $this->assertSame('testValue', $request->getRequestParameter('getKey'));
    }

    public function testGetRequestRawParameterWhenRequestParametersNotFound()
    {
        $request = oxNew(Request::class);

        $this->assertSame('defaultValue', $request->getRequestParameter('nonExisting', 'defaultValue'));
    }

    /**
     * Testing request uri getter
     */
    public function testGetRequestUrl()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = 'test.php?param1=value1&param2=value2';

        $request = oxNew(Request::class);
        $this->assertEquals('index.php?param1=value1&amp;param2=value2', $request->getRequestUrl());
    }

    /**
     * Testing request uri getter
     */
    public function testGetRequestUrlEmptyParams()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = '/shop/';

        $request = oxNew(Request::class);
        $this->assertEquals('', $request->getRequestUrl());
    }

    /**
     * Testing request uri getter
     */
    public function testGetRequestUrlSubfolder()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['SCRIPT_URI'] = '/shop/?cl=details';

        $request = oxNew(Request::class);
        $this->assertEquals('index.php?cl=details', $request->getRequestUrl());
    }

    /**
     * Testing request removing sid from link
     */
    public function testGetRequestUrl_removingSID()
    {
        $request = oxNew(Request::class);
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = 'test.php?param1=value1&sid=zzz&sysid=vvv&param2=ttt';
        $this->assertEquals('index.php?param1=value1&amp;sysid=vvv&amp;param2=ttt', $request->getRequestUrl());

        $_SERVER['REQUEST_URI'] = 'test.php?sid=zzz&param1=value1&sysid=vvv&param2=ttt';
        $this->assertEquals('index.php?param1=value1&amp;sysid=vvv&amp;param2=ttt', $request->getRequestUrl());

        $_SERVER['REQUEST_URI'] = 'test.php?param1=value1&sysid=vvv&param2=ttt&sid=zzz';
        $this->assertEquals('index.php?param1=value1&amp;sysid=vvv&amp;param2=ttt', $request->getRequestUrl());
    }

    /**
     * Testing input processor. Checking 3 cases - passing object, array, string.
     *
     */
    public function testCheckParamSpecialChars()
    {
        $oVar = new stdClass();
        $oVar->xxx = 'yyy';
        $aVar = array('&\\o<x>i"\'d' . chr(0));
        $sVar = '&\\o<x>i"\'d' . chr(0);
        $request = oxNew(Request::class);

        // object must came back the same
        $this->assertEquals($oVar, $request->checkParamSpecialChars($oVar));

        // array items comes fixed
        $this->assertEquals(array("&amp;&#092;o&lt;x&gt;i&quot;&#039;d"), $request->checkParamSpecialChars($aVar));

        // string comes fixed
        $this->assertEquals('&amp;&#092;o&lt;x&gt;i&quot;&#039;d', $request->checkParamSpecialChars($sVar));
    }

    /**
     * Testing input processor. Checking array, if few values must not be checked.
     *
     */
    public function testCheckParamSpecialCharsForArray()
    {
        $values = array('first' => 'first char &', 'second' => 'second char &', 'third' => 'third char &');
        $aRaw = array('first', 'third');

        $result = oxNew(Request::class)->checkParamSpecialChars($values, $aRaw);

        // object must came back the same
        $this->assertEquals($values['first'], $result['first']);
        $this->assertEquals('second char &amp;', $result['second']);
        $this->assertEquals($values['third'], $result['third']);
    }

    /**
     * Test if checkParamSpecialChars also can fix arrays
     *
     */
    public function testCheckParamSpecialCharsAlsoFixesArrayKeys()
    {
        $test = array(
            array(
                'data'   => array('asd&' => 'a%&'),
                'result' => array('asd&amp;' => 'a%&amp;'),
            ),
            array(
                'data'   => 'asd&',
                'result' => 'asd&amp;',
            )
        );
        $request = oxNew(Request::class);
        foreach ($test as $check) {
            $this->assertEquals($check['result'], $request->checkParamSpecialChars($check['data']));
        }
    }

    /**
     * @return array
     */
    public function providerCheckParamSpecialChars_newLineExist_newLineChanged()
    {
        return array(
            array("\r", '&#13;'),
            array("\n", '&#10;'),
            array("\r\n", '&#13;&#10;'),
            array("\n\r", '&#10;&#13;'),
        );
    }

    /**
     * @dataProvider providerCheckParamSpecialChars_newLineExist_newLineChanged
     */
    public function testCheckParamSpecialChars_newLineExist_newLineChanged($sNewLineCharacter, $sEscapedNewLineCharacter)
    {
        $oVar = new stdClass();
        $oVar->xxx = "text" . $sNewLineCharacter;
        $aVar = array("text" . $sNewLineCharacter);
        $sVar = "text" . $sNewLineCharacter;

        $request = oxNew(Request::class);
        // object must came back the same
        $this->assertEquals($oVar, $request->checkParamSpecialChars($oVar));

        // array items comes fixed
        $this->assertEquals(array("text" . $sEscapedNewLineCharacter), $request->checkParamSpecialChars($aVar));

        // string comes fixed
        $this->assertEquals("text" . $sEscapedNewLineCharacter, $request->checkParamSpecialChars($sVar));
    }
}
