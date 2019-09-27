<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Extension\CacheResourcePlugin;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class CacheResourcePluginTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTemplate()
    {
        $smarty = $this->getMockBuilder(\Smarty::class)->getMock();
        $smarty->oxidcache = new \OxidEsales\Eshop\Core\Field('newValue', \OxidEsales\Eshop\Core\Field::T_RAW);
        $smarty->security = false;

        $resource = $this->getSmartyExtensionObject();

        $tplSource = 'initialValue';
        $this->assertTrue($resource::getTemplate('templateName', $tplSource, $smarty));
        $this->assertSame('newValue', $tplSource);
        $this->assertFalse($smarty->security);
    }

    public function testGetTemplateIfDemoShopIsActive()
    {
        $smarty = $this->getMockBuilder(\Smarty::class)->getMock();
        $smarty->oxidcache = new \OxidEsales\Eshop\Core\Field('newValue', \OxidEsales\Eshop\Core\Field::T_RAW);
        $smarty->security = false;

        $resource = $this->getSmartyExtensionObject(true);

        $tplSource = 'initialValue';
        $this->assertTrue($resource::getTemplate('templateName', $tplSource, $smarty));
        $this->assertSame('newValue', $tplSource);
        $this->assertTrue($smarty->security);
    }

    public function testGetTimestamp()
    {
        $smarty = $this->getMockBuilder(\Smarty::class)->getMock();

        $resource = $this->getSmartyExtensionObject();

        $time = 2;
        $this->assertTrue($resource::getTimestamp('templateName', $time, $smarty));
        $this->assertTrue(is_numeric($time));
        $this->assertTrue($time > 2);
    }

    public function testGetTimestampIfTimeCacheIsGiven()
    {
        $smarty = $this->getMockBuilder(\Smarty::class)->getMock();
        $smarty->oxidtimecache = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);

        $resource = $this->getSmartyExtensionObject();

        $time = 2;
        $this->assertTrue($resource::getTimestamp('templateName', $time, $smarty));
        $this->assertTrue(is_numeric($time));
        $this->assertEquals(1, $time);
    }

    public function testGetSecure()
    {
        $smarty = $this->getMockBuilder(\Smarty::class)->getMock();
        $resource = $this->getSmartyExtensionObject();
        $this->assertTrue($resource::getSecure("templateName", $smarty));
    }

    public function testGetTrusted()
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $resource = new CacheResourcePlugin($smartyContextMock);
        // we just need to test if this method exists
        $this->assertTrue(method_exists($resource, 'getTrusted'));
    }

    private function getSmartyExtensionObject($securityMode = false)
    {
        $smartyContextMock = $this
        ->getMockBuilder(SmartyContextInterface::class)
        ->getMock();

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        return new CacheResourcePlugin($smartyContextMock);
    }
}
