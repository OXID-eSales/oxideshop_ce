<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
