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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ) . '/unit/OxidTestCase.php';
require_once realpath( "." ) . '/unit/test_config.inc.php';
require_once getShopBasePath() . '/setup/oxsetup.php';

class Unit_Core_oxOnlineModuleVersionNotifierCallerTest extends OxidTestCase
{
    public function testGetWebServiceUrl()
    {
        $oCaller = $this->getMock('oxOnlineCaller', array(), array(),'',false);

        $oNotifier = new oxOnlineModuleVersionNotifierCaller($oCaller);
        $this->assertSame('https://omvn.oxid-esales.com/check.php', $oNotifier->getWebServiceUrl());
    }

    public function testDoRequest()
    {
        $oCaller = $this->getMock('oxOnlineCaller', array('call'), array(),'',false);
        $oCaller->expects($this->any())
            ->method('call')
            ->with($this->equalTo('https://omvn.oxid-esales.com/check.php'), $this->equalTo($this->_getExpectedXml()));

        $oNotifier = new oxOnlineModuleVersionNotifierCaller($oCaller);
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
        $sXml .= '<modules>';
        $sXml .= '<module><id>modId</id><version>modVersion</version><activeInShops><activeInShop>myshop.com</activeInShop></activeInShops></module>';
        $sXml .= '</modules>';
        $sXml .= '<edition>CE</edition>';
        $sXml .= '<version>5.6.5</version>';
        $sXml .= '<shopurl>myshop.com</shopurl>';
        $sXml .= '<pversion>1.0</pversion>';
        $sXml .= '<productid>eShop</productid>';
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
        $oRequest            = new oxOnlineModulesNotifierRequest();
        $oRequest->edition   = 'CE';
        $oRequest->version   = '5.6.5';
        $oRequest->shopUrl   = 'myshop.com';
        $oRequest->pVersion  = '1.0';
        $oRequest->productId = 'eShop';

        $modules                             = new stdClass();
        $modules->module                     = array();
        $module                              = new stdClass();
        $module->id                          = 'modId';
        $module->version                     = 'modVersion';
        $module->activeInShops               = new stdClass();
        $module->activeInShops->activeInShop = array('myshop.com');
        $modules->module[]                   = $module;

        $oRequest->modules = $modules;

        return $oRequest;
    }
}