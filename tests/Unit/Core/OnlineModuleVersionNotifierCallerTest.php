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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use \oxOnlineModuleVersionNotifierCaller;
use \oxSimpleXml;
use \stdClass;
use \oxTestModules;

/**
 * Class Unit_Core_oxOnlineModuleVersionNotifierCallerTest
 *
 * @covers oxOnlineModuleVersionNotifierCaller
 * @covers oxOnlineCaller
 */
class OnlineModuleVersionNotifierCallerTest extends \OxidTestCase
{
    public function testGetWebServiceUrl()
    {
        $this->stubExceptionToNotWriteToLog();

        /** @var oxCurl $oCurl */
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        /** @var OnlineServerEmailBuilder $oEmailBuilder */
        $oEmailBuilder = $this->getMock(OnlineServerEmailBuilder::class);
        $oNotifier = new oxOnlineModuleVersionNotifierCaller($oCurl, $oEmailBuilder, new oxSimpleXml());
        $oNotifier->call($this->_getRequest());

        $this->assertSame('https://omvn.oxid-esales.com/check.php', $oCurl->getUrl());
    }

    public function testDoRequestAndCheckDocumentName()
    {
        $this->stubExceptionToNotWriteToLog();

        $this->getConfig()->setConfigParam('sClusterId', 'generated_unique_cluster_id');

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute', 'setParameters'));
        $oCurl->expects($this->once())->method('execute');
        $oCurl->expects($this->once())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $this->_getExpectedXml())));
        /** @var oxCurl $oCurl */

        /** @var OnlineServerEmailBuilder $oEmailBuilder */
        $oEmailBuilder = $this->getMock(OnlineServerEmailBuilder::class);

        $oNotifier = new oxOnlineModuleVersionNotifierCaller($oCurl, $oEmailBuilder, new oxSimpleXml());
        $oNotifier->doRequest($this->_getRequest());
    }

    /**
     * Prepare request xml
     *
     * @return string
     */
    protected function _getExpectedXml()
    {
        $sXml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
        $sXml .= '<omvnRequest>';
        $sXml .= '<pVersion>1.0</pVersion>';
        $sXml .= '<modules>';
        $sXml .= '<module><id>modId</id><version>modVersion</version><activeInShops><activeInShop>myshop.com</activeInShop></activeInShops></module>';
        $sXml .= '</modules>';
        $sXml .= '<clusterId>generated_unique_cluster_id</clusterId>';
        $sXml .= '<edition>CE</edition>';
        $sXml .= '<version>5.6.5</version>';
        $sXml .= '<shopUrl>myshop.com</shopUrl>';
        $sXml .= '<productId>eShop</productId>';
        $sXml .= '</omvnRequest>' . PHP_EOL;

        return $sXml;
    }

    /**
     * Prepare request object
     *
     * @return oxOnlineModulesNotifierRequest
     */
    protected function _getRequest()
    {
        $oRequest = oxNew('oxOnlineModulesNotifierRequest');
        $oRequest->edition = 'CE';
        $oRequest->version = '5.6.5';
        $oRequest->shopUrl = 'myshop.com';
        $oRequest->pVersion = '1.0';
        $oRequest->productId = 'eShop';

        $modules = new stdClass();
        $modules->module = array();
        $module = new stdClass();
        $module->id = 'modId';
        $module->version = 'modVersion';
        $module->activeInShops = new stdClass();
        $module->activeInShops->activeInShop = array('myshop.com');
        $modules->module[] = $module;

        $oRequest->modules = $modules;

        return $oRequest;
    }
}
