<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class CurlTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test oxCurl::setUrl()
     * Test oxCurl::getUrl()
     */
    public function testGetUrl_urlSet_setReturned()
    {
        $sEndpointUrl = 'http://www.oxid-esales.com/index.php?anid=article';

        $oCurl = oxNew('oxCurl');
        $oCurl->setUrl($sEndpointUrl);

        $sUrlToCall = $oCurl->getUrl();

        $this->assertSame($sEndpointUrl, $sUrlToCall, 'Url should be same as provided from config.');
    }

    /**
     * Test oxCurl::getUrl()
     */
    public function testGetUrl_notSet_null()
    {
        $oCurl = oxNew('oxCurl');
        $this->assertNull($oCurl->getUrl());
    }

    /**
     * Test oxCurl::setQuery()
     */
    public function testSetQuery_set_get()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setQuery('param1=value1&param2=values2');

        $this->assertSame('param1=value1&param2=values2', $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getQuery()
     */
    public function testGetQuery_setParameter_getQueryFromParameters()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setParameters(['param1' => 'value1', 'param2' => 'values2']);

        $this->assertSame('param1=value1&param2=values2', $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getQuery()
     */
    public function testGetQuery_setParameterManyTimes_getQueryFromParameters()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setParameters(['param1' => 'value1', 'param2' => 'values2']);
        $this->assertSame('param1=value1&param2=values2', $oCurl->getQuery());

        $oCurl->setParameters(['param3' => 'value3', 'param4' => 'values4']);
        $this->assertSame('param3=value3&param4=values4', $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getQuery()
     */
    public function testGetQuery_setParameterNotUtf_getQueryFromParameters()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setParameters(['param1' => 'Jäger', 'param2' => 'values2']);

        $aPramsUtf = ['param1' => 'Jäger', 'param2' => 'values2'];

        $this->assertSame(http_build_query($aPramsUtf), $oCurl->getQuery());
    }

    /**
     * Test oxCurl::getParameters()
     */
    public function testGetParameters_default_null()
    {
        $oCurl = oxNew('oxCurl');
        $this->assertNull($oCurl->getParameters());
    }

    /**
     * Test oxCurl::getParameters()
     */
    public function testGetParameters_set_returnSet()
    {
        $aParameters = ['parameter' => 'value'];

        $oCurl = oxNew('oxCurl');
        $oCurl->setParameters($aParameters);
        $this->assertSame($aParameters, $oCurl->getParameters());
    }

    /**
     * Test oxCurl::setConnectionCharset()
     */
    public function testSetConnectionCharset_set_get()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setConnectionCharset('ISO-8859-1');

        $this->assertSame('ISO-8859-1', $oCurl->getConnectionCharset());
    }

    /**
     * Test oxCurl::getConnectionCharset()
     */
    public function testGetConnectionCharset_notSet_UTF()
    {
        $oCurl = oxNew('oxCurl');
        $this->assertSame('UTF-8', $oCurl->getConnectionCharset());
    }

    /**
     * Checks if function returns null when nothing is set.
     */
    public function testGetHost_notSet_null()
    {
        $oCurl = oxNew('oxCurl');
        $this->assertNull($oCurl->getHost(), 'Default value must be null.');
    }

    /**
     * Check if getter returns what is set in setter.
     */
    public function testGetHost_setHost_host()
    {
        $sHost = 'someHost';

        $oCurl = oxNew('oxCurl');
        $oCurl->setHost($sHost);

        $this->assertSame($sHost, $oCurl->getHost(), 'Check if getter returns what is set in setter.');
    }

    /**
     * Checks if returned header is correct when header was not set and host was set.
     */
    public function testGetHeader_headerNotSetAndHostSet_headerWithHost()
    {
        $sHost = 'someHost';
        $aExpectedHeader = ['POST /cgi-bin/webscr HTTP/1.1', 'Content-Type: application/x-www-form-urlencoded', 'Host: ' . $sHost, 'Connection: close'];
        $oCurl = oxNew('oxCurl');
        $oCurl->setHost($sHost);

        $this->assertSame($aExpectedHeader, $oCurl->getHeader(), 'Header must be formed from set host.');
    }

    /**
     * Checks if returned header is correct when header was not set and host was not set.
     */
    public function testGetHeader_headerNotSetAndHostNotSet_headerWithoutHost()
    {
        $aExpectedHeader = ['POST /cgi-bin/webscr HTTP/1.1', 'Content-Type: application/x-www-form-urlencoded', 'Connection: close'];
        $oCurl = oxNew('oxCurl');

        $this->assertSame($aExpectedHeader, $oCurl->getHeader(), 'Header must be without host as host not set.');
    }

    /**
     * Checks if returned header is correct when header was set and host was set.
     */
    public function testGetHeader_headerSetAndHostSet_headerFromSet()
    {
        $sHost = 'someHost';
        $aHeader = ['Test header'];
        $oCurl = oxNew('oxCurl');
        $oCurl->setHost($sHost);
        $oCurl->setHeader($aHeader);

        $this->assertSame($aHeader, $oCurl->getHeader(), 'Header must be same as set header.');
    }

    /**
     * Checks if returned header is correct when header was set and host was not set.
     */
    public function testGetHeader_headerSetAndHostNotSet_headerWithoutHost()
    {
        $aHeader = ['Test header'];
        $oCurl = oxNew('oxCurl');
        $oCurl->setHeader($aHeader);

        $this->assertSame($aHeader, $oCurl->getHeader(), 'Header must be same as set header.');
    }

    /**
     * Test oxCurl::setOption()
     * Test oxCurl::getOptions()
     */
    public function testSetOption()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setOption('CURLOPT_VERBOSE', 0);

        $aOptions = $oCurl->getOptions();
        $this->assertCount(2, $aOptions);
        $this->assertSame(0, $aOptions['CURLOPT_VERBOSE']);
    }

    /**
     * Test oxCurl::setOption()
     */
    public function testSetOption_NotCurlOption()
    {
        $this->expectException('oxException');

        $oCurl = oxNew('oxCurl');
        $oCurl->setOption('rParam1', "rValue1");
    }

    /**
     * Test oxCurl::setOption()
     */
    public function testSetOption_WrongOptionName()
    {
        $this->expectException('oxException');

        $oCurl = oxNew('oxCurl');
        $oCurl->setOption('CURLOPT_WRONG', "rValue1");
    }

    /**
     * Test oxCurl::setMethod()
     * Test oxCurl::getMethod()
     */
    public function testSetGetMethod()
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setMethod('POST');

        $this->assertSame('POST', $oCurl->getMethod());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_getResponseArray()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl", 'setOpt', 'close', 'getErrorNumber', 'getHeader', 'getUrl', 'getQuery', 'getOptions']);
        $oCurl->method('setOpt');
        $oCurl->expects($this->once())->method('getHeader');
        $oCurl->expects($this->once())->method('getUrl');
        $oCurl->expects($this->once())->method('getQuery');
        $oCurl->expects($this->once())->method('getOptions')->willReturn(['CURLOPT_VERBOSE' => 'rValue1']);
        $oCurl->expects($this->once())->method('executeCurl')->willReturn('rParam1=rValue1');
        $oCurl->expects($this->once())->method('getErrorNumber')->willReturn(false);
        $oCurl->expects($this->once())->method('close');

        $this->assertSame('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_noAdditionalOptionsSetForGET()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl", 'setOpt', 'close', 'getErrorNumber', 'getHeader', 'getUrl', 'getQuery', 'getOptions']);
        $oCurl->expects($this->exactly(1))->method('setOpt');
        $oCurl->expects($this->once())->method('getHeader');
        $oCurl->expects($this->once())->method('getUrl');
        $oCurl->expects($this->never())->method('getQuery');
        $oCurl->expects($this->once())->method('getOptions')->willReturn([]);
        $oCurl->expects($this->once())->method('executeCurl')->willReturn('rParam1=rValue1');
        $oCurl->expects($this->once())->method('getErrorNumber')->willReturn(false);
        $oCurl->expects($this->once())->method('close');
        $oCurl->setMethod('GET');

        $this->assertSame('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_noAdditionalOptionsSetForPOST()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl", 'setOpt', 'close', 'getErrorNumber', 'getHeader', 'getUrl', 'getQuery', 'getOptions']);
        $oCurl->expects($this->exactly(3))->method('setOpt');
        $oCurl->expects($this->once())->method('getHeader');
        $oCurl->expects($this->once())->method('getUrl');
        $oCurl->expects($this->once())->method('getQuery');
        $oCurl->expects($this->once())->method('getOptions')->willReturn([]);
        $oCurl->expects($this->once())->method('executeCurl')->willReturn('rParam1=rValue1');
        $oCurl->expects($this->once())->method('getErrorNumber')->willReturn(false);
        $oCurl->expects($this->once())->method('close');

        $this->assertSame('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::execute()
     */
    public function testExecute_curlError()
    {
        $this->expectException('oxException');
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl", 'setOptions', 'close', 'getErrorNumber']);

        $oCurl->method('setOptions');
        $oCurl->expects($this->once())->method('executeCurl')->willReturn('rParam1=rValue1');
        $oCurl->expects($this->once())->method('getErrorNumber')->willReturn(1);
        $oCurl->expects($this->once())->method('close');

        $this->assertSame('rParam1=rValue1', $oCurl->execute());
    }

    /**
     * Test oxCurl::getStatusCode()
     */
    public function testGetStatusCode()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl"]);

        $this->assertNull($oCurl->getStatusCode());

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
        $this->assertSame("http://www.google.com", $oCurl->getUrl());
    }

    /**
     * Test oxCurl::getUrl()
     */
    public function testGetUrl_WithMultiDimensionalArray()
    {
        $aParams = ['emptyparam' => null, 'param1'     => 'val1', 'param2'     => ['subparam1' => 'subval1', 'subparam2' => ['sub2param1' => 'subsubval1', 'emptyparam' => null]]];

        $oCurl = oxNew("oxCurl");
        $oCurl->setMethod('GET');
        $oCurl->setUrl("http://www.google.com");
        $oCurl->setParameters($aParams);
        $this->assertSame('http://www.google.com?param1=val1&param2%5Bsubparam1%5D=subval1&param2%5Bsubparam2%5D%5Bsub2param1%5D=subsubval1', $oCurl->getUrl());
    }

    public function testSimplePOSTCall()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl", 'setOpt', 'close', 'getErrorNumber']);
        $oCurl->expects($this->once())->method('executeCurl')->willReturn('rParam1=rValue1');
        $oCurl->expects($this->once())->method('getErrorNumber')->willReturn(false);
        $oCurl->expects($this->once())->method('close');
        $oCurl->expects($this->exactly(5))->method('setOpt');
        $oCurl->setMethod('POST');
        $oCurl->setUrl("http://www.google.com");
        $oCurl->setParameters(["param1" => "val1", "param2" => "val2"]);

        $this->assertSame("http://www.google.com", $oCurl->getUrl());
        $this->assertSame("param1=val1&param2=val2", $oCurl->getQuery());
        $this->assertSame('rParam1=rValue1', $oCurl->execute());
    }

    public function testSimpleGETCall()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ["executeCurl", 'setOpt', 'close', 'getErrorNumber']);
        $oCurl->expects($this->once())->method('executeCurl')->willReturn('rParam1=rValue1');
        $oCurl->expects($this->once())->method('getErrorNumber')->willReturn(false);
        $oCurl->expects($this->once())->method('close');
        $oCurl->expects($this->exactly(5))->method('setOpt');
        $oCurl->setMethod('GET');
        $oCurl->setOption('CURLOPT_HEADER', false);
        $oCurl->setOption('CURLOPT_NOBODY', false);
        $oCurl->setHeader(["Header"]);
        $oCurl->setUrl("http://www.google.com");
        $oCurl->setParameters(["param1" => "val1", "param2" => "val2"]);

        $this->assertSame("http://www.google.com?param1=val1&param2=val2", $oCurl->getUrl());
        $this->assertSame("param1=val1&param2=val2", $oCurl->getQuery());
        $this->assertCount(3, $oCurl->getOptions());
        $this->assertSame(["Header"], $oCurl->getHeader());
        $this->assertSame('rParam1=rValue1', $oCurl->execute());
    }
}
