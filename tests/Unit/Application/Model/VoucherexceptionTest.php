<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class VoucherexceptionTest extends \OxidTestCase
{
    public function testSetGetVoucherNr()
    {
        $sVoucher = "a voucher nr.";
        $oTestObject = oxNew('oxVoucherException');
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\VoucherException', get_class($oTestObject));
        $oTestObject->setVoucherNr($sVoucher);
        $this->assertEquals($sVoucher, $oTestObject->getVoucherNr());
    }

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $sMsg = 'Erik was here..';
        $sVoucher = "a voucher nr.";
        $oTestObject = oxNew('oxVoucherException', $sMsg);
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\VoucherException', get_class($oTestObject));
        $oTestObject->setVoucherNr($sVoucher);
        $sStringOut = $oTestObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($sMsg, $sStringOut);
        $this->assertContains('VoucherException', $sStringOut);
        $this->assertContains($sVoucher, $sStringOut);
    }

    public function testGetValues()
    {
        $oTestObject = oxNew('oxVoucherException');
        $sVoucher = "a voucher nr.";
        $oTestObject->setVoucherNr($sVoucher);
        $aRes = $oTestObject->getValues();
        $this->assertArrayHasKey('voucherNr', $aRes);
        $this->assertTrue($sVoucher === $aRes['voucherNr']);
    }
}
