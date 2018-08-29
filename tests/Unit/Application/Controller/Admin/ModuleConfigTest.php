<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Config class
 */
class ModuleConfigTest extends \OxidTestCase
{

    /**
     * Shop_Config::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Module_Config');
        $this->assertEquals('module_config.tpl', $oView->render());
    }


    public function testGetModuleForConfigVars()
    {
        $oView = $this->getProxyClass('Module_Config');
        $oView->setNonPublicVar("_sModuleId", "testModuleId");
        $this->assertEquals('module:testModuleId', $oView->UNITgetModuleForConfigVars());
    }

    public function testSaveConfVars()
    {
        $aStrRequest = array(
            'stringParameterName1' => '',
            'stringParameterName2' => 'stringParameterValue2',
        );
        $aPasswordRequest = array(
            'passwordParameterName1' => '',
            'passwordParameterName2' => 'passwordParameterValue',
        );

        $this->setRequestParameter('confstrs', $aStrRequest);
        $this->setRequestParameter('confpassword', $aPasswordRequest);

        $oModuleConfig = oxNew('Module_Config');
        $oModuleConfig->saveConfVars();

        $this->assertSame('', $this->getConfigParam('stringParameterName1'), 'First string parameter is not as expected.');
        $this->assertSame('stringParameterValue2', $this->getConfigParam('stringParameterName2'), 'Second string parameter is not as expected.');
        $this->assertSame('', $this->getConfigParam('passwordParameterName1'), 'First password parameter is not as expected.');
        $this->assertSame('passwordParameterValue', $this->getConfigParam('passwordParameterName2'), 'Second pasword parameter is not as expected.');
    }
}
