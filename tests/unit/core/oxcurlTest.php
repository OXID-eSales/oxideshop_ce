<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxCurlTest extends OxidTestCase
{

    /**
     * Test oxCurl::setUrl()
     * Test oxCurl::getUrl()
     */
    public function testGetUrl_urlSet_setReturned()
    {
        $sEndpointUrl = 'http://www.oxid-esales.com/index.php?anid=article';

        $oCurl = new oxCurl();
        $oCurl->setUrl($sEndpointUrl);
        $sUrlToCall = $oCurl->getUrl();

        $this->assertEquals($sEndpointUrl, $sUrlToCall, 'Url should be same as provided from config.');
    }

    /**
     * Test oxCurl::getUrl()
     */
    public function testGetUrl_notSet_null()
    {
        $oCurl = new oxCurl();
        $this->assertNull($oCurl->getUrl());
    }

    /**
     * Test oxCurl::setQuery()
     */
    public function testSetQuery_set_get()
    {
        $oCurl = new oxCurl();
        $oCurl->setQuery('param1=value1&param2=values2');

        $this->assertEquals('param1=value1&param2=values2', $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getQuery()
     */
    public function testGetQuery_setParameter_getQueryFromParameters()
    {
        $oCurl = new oxCurl();
        $oCurl->setParameters(array('param1' => 'value1', 'param2' => 'values2'));

        $this->assertEquals('param1=value1&param2=values2', $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getQuery()
     */
    public function testGetQuery_setParameterManyTimes_getQueryFromParameters()
    {
        $oCurl = new oxCurl();
        $oCurl->setParameters(array('param1' => 'value1', 'param2' => 'values2'));
        $this->assertEquals('param1=value1&param2=values2', $oCurl->getQuery());

        $oCurl->setParameters(array('param3' => 'value3', 'param4' => 'values4'));
        $this->assertEquals('param3=value3&param4=values4', $oCurl->getQuery());

    }

    /**
     * Test oxCurl::getQuery()
     */
    public function testGetQuery_setParameterNotUtf_getQueryFromParameters()
    {
        $oCurl = new oxCurl();
        $oCurl->setParameters(array('param1' => 'Jäger', 'param2' => 'values2'));

        $aPramsUtf = array('param1' => 'Jäger', 'param2' => 'values2');

        $this->assertEquals(http_build_query($aPramsUtf), $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getParameters()
     */
    public function testGetParameters_default_null()
    {
        $oCurl = new oxCurl();
        $this->assertNull($oCurl->getParameters());
    }

    /**
     * Test oxCurl::getParameters()
     */
    public function testGetParameters_set_returnSet()
    {
        $aParameters = array('parameter' => 'value');

        $oCurl = new oxCurl();
        $oCurl->setParameters($aParameters);
        $this->assertEquals($aParameters, $oCurl->getParameters());
    }

    /**
     * Test oxCurl::setConnectionCharset()
     */
    public function testSetConnectionCharset_set_get()
    {
        $oCurl = new oxCurl();
        $oCurl->setConnectionCharset('ISO-8859-1');

        $this->assertEquals('ISO-8859-1', $oCurl->getConnectionCharset());
    }

    /**
     * Test oxCurl::getConnectionCharset()
     */
    public function testGetConnectionCharset_notSet_UTF()
    {
        $oCurl = new oxCurl();
        $this->assertEquals('UTF-8', $oCurl->getConnectionCharset());
    }

    /**
     * Checks if function returns null when nothing is set.
     */
    public function testGetHost_notSet_null()
    {
        $oCurl = new oxCurl();
        $this->assertNull($oCurl->getHost(), 'Default value must be null.');
    }

    /**
     * Check if getter returns what is set in setter.
     */
    public function testGetHost_setHost_host()
    {
        $sHost = 'someHost';

        $oCurl = new oxCurl();
        $oCurl->setHost($sHost);

        $this->assertEquals($sHost, $oCurl->getHost(), 'Check if getter returns what is set in setter.');
    }

    /**
     * Checks if returned header is correct when header was not set and host was set.
     */
    public function testGetHeader_headerNotSetAndHostSet_headerWithHost()
    {
        $sHost = 'someHost';
        $aExpectedHeader = array(
            'POST /cgi-bin/webscr HTTP/1.1',
            'Content-Type: application/x-www-form-urlencoded',
            'Host: ' . $sHost,
            'Connection: close'
        );
        $oCurl = new oxCurl();
        $oCurl->setHost($sHost);

        $this->assertEquals($aExpectedHeader, $oCurl->getHeader(), 'Header must be formed from set host.');
    }

    /**
     * Checks if returned header is correct when header was not set and host was not set.
     */
    public function testGetHeader_headerNotSetAndHostNotSet_headerWithoutHost()
    {
        $aExpectedHeader = array(
            'POST /cgi-bin/webscr HTTP/1.1',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: close'
        );
        $oCurl = new oxCurl();

        $this->assertEquals($aExpectedHeader, $oCurl->getHeader(), 'Header must be without host as host not set.');
    }

    /**
     * Checks if returned header is correct when header was set and host was set.
     */
    public function testGetHeader_headerSetAndHostSet_headerFromSet()
    {
        $sHost = 'someHost';
        $aHeader = array('Test header');
        $oCurl = new oxCurl();
        $oCurl->setHost($sHost);
        $oCurl->setHeader($aHeader);

        $this->assertEquals($aHeader, $oCurl->getHeader(), 'Header must be same as set header.');
    }

    /**
     * Checks if returned header is correct when header was set and host was not set.
     */
    public function testGetHeader_headerSetAndHostNotSet_headerWithoutHost()
    {
        $aHeader = array('Test header');
        $oCurl = new oxCurl();
        $oCurl->setHeader($aHeader);

        $this->assertEquals($aHeader, $oCurl->getHeader(), 'Header must be same as set header.');
    }

    /**
     * Test oxCurl::setOption()
     * Test oxCurl::getOptions()
     */
    public function testSetOption()
    {
        $oCurl = new oxCurl();
        $oCurl->setOption('CURLOPT_VERBOSE', 0);
        $aOptions = $oCurl->getOptions();
        $this->assertEquals(2, count($aOptions));
        $this->assertEquals(0, $aOptions['CURLOPT_VERBOSE']);
    }

    /**
     * Test oxCurl::setOption()
     */
    public function testSetOption_NotCurlOption()
    {
        $this->setExpectedException('oxException');

        $oCurl = new oxCurl();
        $oCurl->setOption('rParam1', "rValue1");
    }

    /**
     * Test oxCurl::setOption()
     */
    public function testSetOption_WrongOptionName()
    {
        $this->setExpectedException('oxException');

        $oCurl = new oxCurl();
        $oCurl->setOption('CURLOPT_WRONG', "rValue1");
    }

    /**
     * Test oxCurl::setMethod()
     * Test oxCurl::getMethod()
     */
    public function testSetGetMethod()
    {
        $oCurl = new oxCurl();
        $oCurl->setMethod('POST');

        $this->assertEquals('POST', $oCurl->getMethod());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_getResponseArray()
    {
        $oCurl = $this->getMock('oxCurl', array("_execute", '_setOpt', '_close', '_getErrorNumber', 'getHeader', 'getUrl', 'getQuery', 'getOptions'));
        $oCurl->expects($this->any())->method('_setOpt');
        $oCurl->expects($this->once())->method('getHeader');
        $oCurl->expects($this->once())->method('getUrl');
        $oCurl->expects($this->once())->method('getQuery');
        $oCurl->expects($this->once())->method('getOptions')->will($this->returnValue(array('CURLOPT_VERBOSE' => 'rValue1')));
        $oCurl->expects($this->once())->method('_execute')->will($this->returnValue('rParam1=rValue1'));
        $oCurl->expects($this->once())->method('_getErrorNumber')->will($this->returnValue(false));
        $oCurl->expects($this->once())->method('_close');

        $this->assertEquals('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_noAdditionalOptionsSetForGET()
    {
        $oCurl = $this->getMock('oxCurl', array("_execute", '_setOpt', '_close', '_getErrorNumber', 'getHeader', 'getUrl', 'getQuery', 'getOptions'));
        $oCurl->expects($this->exactly(1))->method('_setOpt');
        $oCurl->expects($this->once())->method('getHeader');
        $oCurl->expects($this->once())->method('getUrl');
        $oCurl->expects($this->never())->method('getQuery');
        $oCurl->expects($this->once())->method('getOptions')->will($this->returnValue(array()));
        $oCurl->expects($this->once())->method('_execute')->will($this->returnValue('rParam1=rValue1'));
        $oCurl->expects($this->once())->method('_getErrorNumber')->will($this->returnValue(false));
        $oCurl->expects($this->once())->method('_close');
        $oCurl->setMethod('GET');

        $this->assertEquals('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_noAdditionalOptionsSetForPOST()
    {
        $oCurl = $this->getMock('oxCurl', array("_execute", '_setOpt', '_close', '_getErrorNumber', 'getHeader', 'getUrl', 'getQuery', 'getOptions'));
        $oCurl->expects($this->exactly(3))->method('_setOpt');
        $oCurl->expects($this->once())->method('getHeader');
        $oCurl->expects($this->once())->method('getUrl');
        $oCurl->expects($this->once())->method('getQuery');
        $oCurl->expects($this->once())->method('getOptions')->will($this->returnValue(array()));
        $oCurl->expects($this->once())->method('_execute')->will($this->returnValue('rParam1=rValue1'));
        $oCurl->expects($this->once())->method('_getErrorNumber')->will($this->returnValue(false));
        $oCurl->expects($this->once())->method('_close');

        $this->assertEquals('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_curlError()
    {
        $this->setExpectedException('oxException');
        $oCurl = $this->getMock('oxCurl', array("_execute", '_setOptions', '_close', '_getErrorNumber'));

        $oCurl->expects($this->any())->method('_setOptions');
        $oCurl->expects($this->once())->method('_execute')->will($this->returnValue('rParam1=rValue1'));
        $oCurl->expects($this->once())->method('_getErrorNumber')->will($this->returnValue(1));
        $oCurl->expects($this->once())->method('_close');

        $this->assertEquals('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::getStatusCode()
     */
    public function testGetStatusCode()
    {
        $oCurl = $this->getMock('oxCurl', array("_execute"));

        $this->assertSame(null, $oCurl->getStatusCode());

        $oCurl->execute();

        $this->assertSame(0, $oCurl->getStatusCode());
    }

    /**
     * Test oxCurl::getUrl()
     */
    public function testGetWithoutParameters()
    {
        $oCurl = oxNew("oxCurl");
        $oCurl->setMethod('GET');
        $oCurl->setUrl("http://www.google.com");
        $this->assertEquals("http://www.google.com", $oCurl->getUrl());
    }

    /**
     * Test oxCurl::getUrl()
     */
    public function testGetUrl_WithMultiDimensionalArray()
    {
        $aParams = array(
            'emptyparam' => null,
            'param1'     => 'val1',
            'param2'     => array(
                'subparam1' => 'subval1',
                'subparam2' => array(
                    'sub2param1' => 'subsubval1',
                    'emptyparam' => null
                ),
            ),
        );

        $oCurl = oxNew("oxCurl");
        $oCurl->setMethod('GET');
        $oCurl->setUrl("http://www.google.com");
        $oCurl->setParameters($aParams);
        $this->assertEquals('http://www.google.com?param1=val1&param2%5Bsubparam1%5D=subval1&param2%5Bsubparam2%5D%5Bsub2param1%5D=subsubval1', $oCurl->getUrl());
    }

    public function testSimplePOSTCall()
    {
        $oCurl = $this->getMock('oxCurl', array("_execute", '_setOpt', '_close', '_getErrorNumber'));
        $oCurl->expects($this->once())->method('_execute')->will($this->returnValue('rParam1=rValue1'));
        $oCurl->expects($this->once())->method('_getErrorNumber')->will($this->returnValue(false));
        $oCurl->expects($this->once())->method('_close');
        $oCurl->expects($this->exactly(5))->method('_setOpt');
        $oCurl->setMethod('POST');
        $oCurl->setUrl("http://www.google.com");
        $oCurl->setParameters(array("param1" => "val1", "param2" => "val2"));

        $this->assertEquals("http://www.google.com", $oCurl->getUrl());
        $this->assertEquals("param1=val1&param2=val2", $oCurl->getQuery());
        $this->assertEquals('rParam1=rValue1', $oCurl->execute());
    }

    public function testSimpleGETCall()
    {
        $oCurl = $this->getMock('oxCurl', array("_execute", '_setOpt', '_close', '_getErrorNumber'));
        $oCurl->expects($this->once())->method('_execute')->will($this->returnValue('rParam1=rValue1'));
        $oCurl->expects($this->once())->method('_getErrorNumber')->will($this->returnValue(false));
        $oCurl->expects($this->once())->method('_close');
        $oCurl->expects($this->exactly(5))->method('_setOpt');
        $oCurl->setMethod('GET');
        $oCurl->setOption('CURLOPT_HEADER', false);
        $oCurl->setOption('CURLOPT_NOBODY', false);
        $oCurl->setHeader(array("Header"));
        $oCurl->setUrl("http://www.google.com");
        $oCurl->setParameters(array("param1" => "val1", "param2" => "val2"));

        $this->assertEquals("http://www.google.com?param1=val1&param2=val2", $oCurl->getUrl());
        $this->assertEquals("param1=val1&param2=val2", $oCurl->getQuery());
        $this->assertEquals(3, count($oCurl->getOptions()));
        $this->assertEquals(array("Header"), $oCurl->getHeader());
        $this->assertEquals('rParam1=rValue1', $oCurl->execute());
    }
}