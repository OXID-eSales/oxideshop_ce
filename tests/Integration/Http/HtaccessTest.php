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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Integration\Http;


class HtaccessTest extends \OxidTestCase
{
    public function testHtaccessRewrite301ForRedirectedFileExtensions()
    {
        $shopUrl = $this->getConfig()->getShopUrl(1);
        $command = 'curl -I -s ' . $shopUrl . '/file.someExtension' ;
        $response = shell_exec($command);

        $this->assertNotNull($response, 'This command failed to execute: ' . $command);
        $this->assertContains('301 Moved Permanently', $response, 'All files with a not defined extension are redirected with code "301 Moved Permanently" ' . $command);

    }

    /**
     * @dataProvider dataProviderTestHtaccessRewriteForFileExtensions
     *
     * @param $fileExtension
     * @param $message
     */
    public function testHtaccessRewrite301ForNotRedirectedFileExtensions($fileExtension, $message)
    {
        $shopUrl = $this->getConfig()->getShopUrl(1);
        $command = 'curl -I -s ' . $shopUrl . '/file.' . $fileExtension;
        $response = shell_exec($command);

        $this->assertNotNull($response, 'This command failed to execute: ' . $command);
        $this->assertNotContains('301 Moved Permanently', $response, $message);

    }

    public function dataProviderTestHtaccessRewriteForFileExtensions()
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
}
