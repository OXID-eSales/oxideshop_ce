<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." )."/../source/core/oxebl/ebldataparser.php";
if (!class_exists('EBLException')) {
    require_once realpath( "." )."/../source/core/oxebl/eblexception.php";
}

class ebldataparser_test extends EBLDataParser {}

/**
 * Testing oxefiportlet class.
 */
class Unit_ebldataparserTest extends OxidTestCase
{
    /**
     * EFI data parser instance
     *
     * @var EBLDataParser
     */
    protected $_oParserProxy;

    /**
     * Setup test
     *
     * @see OxidTestCase::setUp()
     */
    protected function setUp()
    {
        $oRet = parent::setUp();
        $this->_oParserProxy = $this->getProxyClass('ebldataparser_test');
        return $oRet;
    }

    /**
     * EFI data parser test case - test ebldataparser::_getXMLObject()
     *
     * @return null
     */
    public function testGetXMLObject()
    {
        $this->_oParserProxy->setNonPublicVar('_oXML', 'testVal');
        $this->assertSame('testVal', $this->_oParserProxy->UNITgetXMLObject());
    }

    /**
     * EFI data parser test case - test ebldataparser::_setXMLObject() - invalid param passed.
     *
     * @return null
     */
    public function testSetXMLObject_invalidparam()
    {
        $this->setExpectedException('PHPUnit_Framework_Error', 'instance of SimpleXMLElement');
        $this->_oParserProxy->UNITsetXMLObject('testValSet');
        $this->fail('Input parameter was not validated.');
    }

    /**
     * EFI data parser test case - test ebldataparser::_setXMLObject() - pass.
     *
     * @return null
     */
    public function testSetXMLObject_pass()
    {
        $oXML = new SimpleXMLElement('<test>test</test>');
        $this->_oParserProxy->UNITsetXMLObject($oXML);
        $this->assertSame($oXML, $this->_oParserProxy->getNonPublicVar('_oXML'));
    }

    /**
     * EFI data parser test case - test ebldataparser::_parseString()
     *
     * @return null
     */
    public function testParseString()
    {
        $aProxyMock = $this->getMock(
            get_class($this->_oParserProxy),
            array('_getXMLObject')
        );

        $sXML = '<rootNode><tests><param1>val1</param1><param2>val2</param2><param3>val3</param3></tests></rootNode>';
        $oXML = simplexml_load_string($sXML);

        $aProxyMock->expects( $this->once() )->method('_getXMLObject')->will( $this->returnValue($oXML) );
        $this->assertSame('val2', $aProxyMock->UNITparseString('/rootNode/tests/param2'));
    }

    /**
     * EFI data parser test case - test ebldataparser::_parseArray()
     *
     * @return null
     */
    public function testParseArray()
    {
        $aProxyMock = $this->getMock(
            get_class($this->_oParserProxy),
            array('_getXMLObject')
        );

        $sXML = '<rootNode><tests><param1>val1</param1><param2>val2</param2><param3>val3</param3></tests><other><param1>otherVal</param1></other></rootNode>';
        $oXML = simplexml_load_string($sXML);
        $aExpVal = array('val1', 'val2', 'val3');

        $aProxyMock->expects( $this->once() )->method('_getXMLObject')->will( $this->returnValue($oXML) );
        $this->assertSame($aExpVal, $aProxyMock->UNITparseArray('/rootNode/tests'));
    }

    /**
     * EFI data parser test case - test ebldataparser::_parseAArray()
     *
     * @return null
     */
    public function testParseAArray()
    {
        $aProxyMock = $this->getMock(
            get_class($this->_oParserProxy),
            array('_getXMLObject')
        );

        $sXML = '<rootNode><tests><param1>val1</param1><param2>val2</param2><param3>val3</param3></tests></rootNode>';
        $oXML = simplexml_load_string($sXML);
        $aExpVal = array('param1' => 'val1', 'param2' => 'val2', 'param3' => 'val3');

        $aProxyMock->expects( $this->once() )->method('_getXMLObject')->will( $this->returnValue($oXML) );
        $this->assertSame($aExpVal, $aProxyMock->UNITparseAArray('/rootNode/tests'));
    }

    /**
     * EFI data parser test case - test ebldataparser::_getParameter() - invalid param passed, not array.
     *
     * @return null
     */
    public function testGetParameter_invalidparam_notarray()
    {
        $this->setExpectedException('EBLException', 'should be array');
        $this->_oParserProxy->UNITgetParameter('testValSet');
        $this->fail('Input parameter should be validated if it\'s array.');
    }

    /**
     * EFI data parser test case - test ebldataparser::_getParameter() - invalid param passed, missing type.
     *
     * @return null
     */
    public function testGetParameter_invalidparam_missingtype()
    {
        $this->setExpectedException('EBLException', 'should be set array fields [type, xpath]');

        $aParams = array('xpath' => 'some XPath');
        $this->_oParserProxy->UNITgetParameter($aParams);
        $this->fail('Input parameter should be validated, type param is mandatory.');
    }

    /**
     * EFI data parser test case - test ebldataparser::_getParameter() - invalid param passed, missing xpath.
     *
     * @return null
     */
    public function testGetParameter_invalidparam_missingxpath()
    {
        $this->setExpectedException('EBLException', 'should be set array fields [type, xpath]');

        $aParams = array('type' => 'some type');
        $this->_oParserProxy->UNITgetParameter($aParams);
        $this->fail('Input parameter should be validated, xpath param is mandatory.');
    }

    /**
     * EFI data parser test case - test ebldataparser::_getParameter() - not defined custom parse type.
     *
     * @return null
     */
    public function testGetParameter_undefinedtype()
    {
        $this->setExpectedException('EBLException', 'Unknown parse method');

        $aParams = array('type' => 'some undefined type', 'xpath' => 'some XPath');
        $this->_oParserProxy->UNITgetParameter($aParams);
        $this->fail('Parsing type should be validated.');
    }

    /**
     * EFI data parser test case - test ebldataparser::_getParameter() - call parse method.
     *
     * @return null
     */
    public function testGetParameter_callparser()
    {
        $aProxyMock = $this->getMock(
            get_class($this->_oParserProxy),
            array('_parseCustomType')
        );

        $aProxyMock->expects( $this->once() )->method('_parseCustomType')->will( $this->returnValue('some response') );

        $aParams = array('type' => 'CustomType', 'xpath' => 'some XPath');
        $sResp = $aProxyMock->UNITgetParameter($aParams);
        $this->assertSame('some response', $sResp);
    }

    /**
     * EFI data parser test case - test ebldataparser::clearParseRules()
     *
     * @return null
     */
    public function testClearParseRules()
    {
        $this->_oParserProxy->setNonPublicVar('_aParseRules', 'someValue');
        $this->assertSame(true, $this->_oParserProxy->clearParseRules());
        $this->assertSame(array(), $this->_oParserProxy->getNonPublicVar('_aParseRules'));
    }

    /**
     * EFI data parser test case - test ebldataparser::delParseRule()
     *
     * @return null
     */
    public function testDelParseRule()
    {
        $aRules = array(
            'testKey' => array(
                'type' => 'String',
                'xpath' => 'some XPath'
               )
           );
        $this->_oParserProxy->setNonPublicVar('_aParseRules', $aRules);
        $this->assertSame(false, $this->_oParserProxy->delParseRule('non_existing_key'));
        $this->assertSame($aRules, $this->_oParserProxy->getNonPublicVar('_aParseRules'));

        $this->assertSame(true, $this->_oParserProxy->delParseRule('testKey'));
        $this->assertSame(array(), $this->_oParserProxy->getNonPublicVar('_aParseRules'));
    }

    /**
     * EFI data parser test case - test ebldataparser::setParseRule()
     *
     * @return null
     */
    public function testSetParseRule()
    {
        $aExpVal = array(
            'someKey' => array(
                'type' => 'someType',
                'xpath' => 'someXPath',
            ),
        );
        $this->_oParserProxy->setNonPublicVar('_aParseRules', array());
        $this->assertSame(true, $this->_oParserProxy->setParseRule('someKey', 'someType', 'someXPath'));
        $this->assertSame($aExpVal, $this->_oParserProxy->getNonPublicVar('_aParseRules'));
    }

    /**
     * EFI data parser test case - test ebldataparser::getParseRule()
     *
     * @return null
     */
    public function testGetParseRule()
    {
        $aExpResp = array(
            'type' => 'someType',
            'xpath' => 'someXPath',
        );
        $aSetVal = array(
            'someKey' => $aExpResp,
        );

        $this->_oParserProxy->setNonPublicVar('_aParseRules', $aSetVal);
        $this->assertSame(false, $this->_oParserProxy->getParseRule('someUndefinedKey'));
        $this->assertSame($aExpResp, $this->_oParserProxy->getParseRule('someKey'));
    }

    /**
     * EFI data parser test case - test ebldataparser::getAsocArray() - invalid param passed.
     *
     * @return null
     */
    public function testGetAsocArray_invalidparam()
    {
        $this->setExpectedException('PHPUnit_Framework_Error', 'instance of SimpleXMLElement');
        $this->_oParserProxy->getAsocArray('testValSet');
        $this->fail('Input parameter was not validated.');
    }

    /**
     * EFI data parser test case - test ebldataparser::getAsocArray() - pass.
     *
     * @return null
     */
    public function testGetAsocArray_pass()
    {
        $oXML = new SimpleXMLElement('<test>test</test>');
        $aProxyMock = $this->getMock(
            get_class($this->_oParserProxy),
            array('_setXMLObject', '_getParameter')
        );

        $aParseRules = array(
            'someKey' => array(
                'type' => 'someType',
                'xpath' => 'someXPath',
            ),
        );
        $aProxyMock->setNonPublicVar('_aParseRules', $aParseRules);

        $aProxyMock->expects( $this->once() )->method('_setXMLObject');
        $aProxyMock->expects( $this->atLeastOnce() )->method('_getParameter')->will( $this->returnValue('someVal') );

        $this->assertSame(array('someKey' => 'someVal'), $aProxyMock->getAsocArray($oXML));
    }
}
