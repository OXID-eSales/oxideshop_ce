<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Http;

/**
 * Testing that the .htaccess rules are as expected.
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Http
 */
class HtAccessTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Assure, that we get a HTTP code 301 for redirected file extensions.
     */
    public function testHtAccessRewrite301ForRedirectedFileExtensions()
    {
        $response = $this->callCurl('/file.someExtension');

        $this->assertHttpCode($response, 'All files with a not defined extension are redirected with code "301 Moved Permanently"', '301 Moved Permanently');
    }

    /**
     * Assure, that we get the wished HTTP code for the OXID eShop folders.
     *
     * @dataProvider dataProviderTestHtAccessRewrite
     *
     * @param string $urlPart   The URL part to the file or directory we want to check now.
     * @param string $httpError The HTTP error we want to check now.
     */
    public function testHtAccessRewrite($urlPart, $httpError)
    {
        $response = $this->callCurl($urlPart);

        $this->assertHttpCode($response, "All files and directories, which should not be publicly available are redirected with code \"$httpError\"", $httpError);
    }

    /**
     * Data provider for the testHtAccess test.
     *
     * @return array The test cases to look at.
     */
    public function dataProviderTestHtAccessRewrite()
    {
        return [
            ['urlPart' => '/migration', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/migration/data', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/migration/data/Version20170718124421.php', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/migration/project_data', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/migration/project_data/', 'httpError' => '403 Forbidden'],

            ['urlPart' => '/admin', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/Application', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/bin', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/cache', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/Core', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/export', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/log', 'httpError' => '403 Forbidden'],
            ['urlPart' => '/modules', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/out', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/Setup', 'httpError' => '301 Moved Permanently'],
            ['urlPart' => '/tmp', 'httpError' => '403 Forbidden'],
        ];
    }

    /**
     * Assure, that we get no HTTP code 301 for not redirected file extensions.
     *
     * @dataProvider dataProviderTestHtAccessRewriteForFileExtensions
     *
     * @param string $fileExtension The extension of the file we want to check right now.
     * @param string $message       The message we want to show, if the cURL response is a 301.
     */
    public function testHtAccessRewrite301ForNotRedirectedFileExtensions($fileExtension, $message)
    {
        $response = $this->callCurl('/file.' . $fileExtension);

        $this->assertNoHttpCode301($message, $response);
    }

    public function dataProviderTestHtAccessRewriteForFileExtensions()
    {
        return [
            ['html', 'html files are not redirected with 301'],
            ['jpg', 'jpg files are not redirected with 301'],
            ['jpeg', 'jpeg files are not redirected with 301'],
            ['css', 'css files are not redirected with 301'],
            ['pdf', 'pdf files are not redirected with 301'],
            ['doc', 'doc files are not redirected with 301'],
            ['gif', 'gif files are not redirected with 301'],
            ['png', 'png files are not redirected with 301'],
            ['js', 'js files are not redirected with 301'],
            ['htc', 'htc files are not redirected with 301'],
            ['svg', 'svg files are not redirected with 301'],
            ['HTML', 'HTML FILES ARE NOT REDIRECTED WITH 301'],
            ['JPG', 'JPG FILES ARE NOT REDIRECTED WITH 301'],
            ['JPEG', 'JPEG FILES ARE NOT REDIRECTED WITH 301'],
            ['CSS', 'CSS FILES ARE NOT REDIRECTED WITH 301'],
            ['PDF', 'PDF FILES ARE NOT REDIRECTED WITH 301'],
            ['DOC', 'DOC FILES ARE NOT REDIRECTED WITH 301'],
            ['GIF', 'GIF FILES ARE NOT REDIRECTED WITH 301'],
            ['PNG', 'PNG FILES ARE NOT REDIRECTED WITH 301'],
            ['JS', 'JS FILES ARE NOT REDIRECTED WITH 301'],
            ['HTC', 'HTC FILES ARE NOT REDIRECTED WITH 301'],
            ['SVG', 'SVG FILES ARE NOT REDIRECTED WITH 301'],
        ];
    }

    /**
     * Call an OXID eShop file URL over the shell cURL command. Assure, that the cURL command didn't failed.
     *
     * @param string $fileUrlPart The URL part pointing to the file we want to get over cURL.
     *
     * @return string The response of the cURL call.
     */
    protected function callCurl($fileUrlPart)
    {
        $url = $this->getConfig()->getShopUrl(1) . $fileUrlPart;

        $oCurl = oxNew(\OxidEsales\Eshop\Core\Curl::class);
        $oCurl->setOption('CURLOPT_HEADER', true);
        $oCurl->setUrl($url);

        return $oCurl->execute();
    }

    /**
     * Assure, that the given cURL response is a wished HTTP code.
     *
     * @param string      $response  The response of the cURL call.
     * @param null|string $httpError The HTTP error we expect here.
     * @param string      $message   The message we show, if the given response is not the HTTP code.
     */
    protected function assertHttpCode($response, $message, $httpError)
    {
        $this->assertContains($httpError, $response, $message);
    }

    /**
     * Assure, that the given cURL response isn't a HTTP code 301.
     *
     * @param string $response The response of the cURL call.
     * @param string $message  The message we show, if the given response is a HTTP code 301.
     */
    protected function assertNoHttpCode301($response, $message)
    {
        $this->assertNotContains('301 Moved Permanently', $response, $message);
    }
}
