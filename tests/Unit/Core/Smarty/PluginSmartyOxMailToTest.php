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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \Smarty;
use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxmailto.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/function.oxmailto.php';
}

class PluginSmartyOxMailToTest extends \OxidTestCase
{

    public function testSmartyFunctionOxMailTo()
    {
        $aParams = array();
        $aParams['encode'] = 'javascript';
        $aParams['address'] = 'admin@myoxideshop.com';
        $aParams['cc'] = 'cc@myoxideshop.com';
        $aParams['bcc'] = 'bcc@myoxideshop.com';
        $aParams['followupto'] = 'followupto@myoxideshop.com';
        $aParams['subject'] = 'subject';
        $aParams['newsgroups'] = 'newsgroups';
        $aParams['extra'] = 'extra';
        $aParams['text'] = 'text';

        $oSmarty = new Smarty();

        $sMailTo = "admin@myoxideshop.com?cc=cc@myoxideshop.com&bcc=bcc@myoxideshop.com&followupto=followupto@myoxideshop.com";
        $sMailTo .= "&subject=subject&newsgroups=newsgroups";

        $sString = 'document.write(\'<a href="mailto:' . $sMailTo . '" extra>text</a>\');';
        $sEncodedString = "%" . wordwrap(current(unpack("H*", $sString)), 2, "%", true);
        $sExpected = '<script type="text/javascript">eval(decodeURIComponent(\'' . $sEncodedString . '\'))</script>';

        $this->assertEquals($sExpected, smarty_function_oxmailto($aParams, $oSmarty));
    }
}
