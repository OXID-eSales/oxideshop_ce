<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;
use OxidEsales\Eshop\Core\Config;

/**
 * Tests for Shop_Config class
 */
class ShopConfigTest extends \OxidTestCase
{
    public function setup(): void
    {
        $this->setAdminMode(true);

        parent::setUp();
    }

    /**
     * Shop_Config::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertEquals('shop_config', $oView->render());
    }

    public function testSaveConfVars(): void
    {
        $this->setAdminMode(true);
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("confbools", array("varnamebool" => true));
        $this->setRequestParameter("confstrs", array("varnamestr" => "string"));
        $this->setRequestParameter("confarrs", array("varnamearr" => "a\nb\nc"));
        $this->setRequestParameter("confaarrs", array("varnameaarr" => "a => b\nc => d"));
        $this->setRequestParameter("confselects", array("varnamesel" => "a"));

        $aTasks[] = "getConfig";
        $aTasks[] = "resetContentCache";
        $aTasks[] = "getModuleForConfigVars";

        $oConfig = $this->getMock(Config::class, array("saveShopConfVar"));
        $oConfig
            ->method('saveShopConfVar')
            ->withConsecutive(
                ['bool', 'varnamebool', true, 'testId', 'theme:mytheme'],
                ['str', 'varnamestr', 'string', 'testId', 'theme:mytheme'],
                ['arr', 'varnamearr', ["a", "b", "c"], 'testId', 'theme:mytheme'],
                ['aarr', 'varnameaarr', ["a" => "b", "c" => "d"], 'testId', 'theme:mytheme'],
                ['select', 'varnamesel', "a", 'testId', 'theme:mytheme'],
            );

        $oView = $this->getMock(ShopConfiguration::class, $aTasks, array(), '', false);
        $oView->expects($this->once())->method('resetContentCache');
        $oView->expects($this->atLeastOnce())->method('getModuleForConfigVars')
            ->willReturn('theme:mytheme');

        $oView->saveConfVars();
    }

    public function testGetModuleForConfigVars()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertEquals('', $oTest->getModuleForConfigVars());
    }

    /**
     * Shop_Config::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        $oView = $this->getMock(ShopConfiguration::class, array("saveConfVars"));
        $oView->expects($this->once())->method('saveConfVars');
        $oView->save();
    }

    /**
     * Shop_Config::ArrayToMultiline() test case
     *
     * @return null
     */
    public function testArrayToMultiline()
    {
        // defining parameters
        $aInput = array("a", "b", "c");

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertEquals("a\nb\nc", $oView->arrayToMultiline($aInput));
    }

    /**
     * Shop_Config::MultilineToArray() test case
     *
     * @return null
     */
    public function testMultilineToArray()
    {
        // defining parameters
        $sMultiline = "a\nb\n\nc";

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertEquals(array(0 => "a", 1 => "b", 3 => "c"), $oView->multilineToArray($sMultiline));
    }

    /**
     * Shop_Config::AarrayToMultiline() test case
     *
     * @return null
     */
    public function testAarrayToMultiline()
    {
        // defining parameters
        $aInput = array("a" => "b", "c" => "d");

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertEquals("a => b\nc => d", $oView->aarrayToMultiline($aInput));
    }

    /**
     * Shop_Config::MultilineToAarray() test case
     *
     * @return null
     */
    public function testMultilineToAarray()
    {
        // defining parameters
        $sMultiline = "a => b\nc => d";

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertEquals(array("a" => "b", "c" => "d"), $oView->multilineToAarray($sMultiline));
    }

    /**
     * _parseConstraint test
     *
     * @return null
     */
    public function testParseConstraint()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertEquals('', $oTest->parseConstraint('sometype', 'asdd'));
        $this->assertEquals('', $oTest->parseConstraint('bool', 'asdd'));
        $this->assertEquals('', $oTest->parseConstraint('string', 'asdd'));
        $this->assertEquals(array('a', 'bc', 'd'), $oTest->parseConstraint('select', 'a|bc|d'));
    }

    /**
     * _serializeConstraint test
     *
     * @return null
     */
    public function testSerializeConstraint()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertEquals('', $oTest->serializeConstraint('sometype', 'asdd'));
        $this->assertEquals('', $oTest->serializeConstraint('bool', 'asdd'));
        $this->assertEquals('', $oTest->serializeConstraint('string', 'asdd'));
        $this->assertEquals('a|bc|d', $oTest->serializeConstraint('select', array('a', 'bc', 'd')));
    }

    /**
     * _serializeConfVar test
     *
     * @return null
     */
    public function testSerializeConfVar()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertEquals('1.1', $oTest->serializeConfVar('str', 'iMinOrderPrice', '1,1'));
        $this->assertEquals('2,2', $oTest->serializeConfVar('str', 'shouldNotChange', '2,2'));
    }

    /**
     * _unserializeConfVar test
     *
     * @return null
     */
    public function testUnserializeConfVar()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertEquals('1.1', $oTest->unserializeConfVar('str', 'iMinOrderPrice', '1,1'));
        $this->assertEquals('2,2', $oTest->unserializeConfVar('str', 'shouldNotChange', '2,2'));
    }


    /**
     * loadConfVars test
     *
     * @return null
     */
    public function testLoadConfVars()
    {
        $oTest = oxNew('Shop_Config');
        $aDbConfig = $oTest->loadConfVars($this->getConfig()->getShopId(), '');

        $this->assertEquals(
            array('vars', 'constraints', 'grouping'),
            array_keys($aDbConfig)
        );

        $iVarSum = array_sum(array_map('count', $aDbConfig['vars']));
        $this->assertGreaterThan(100, $iVarSum);
        $this->assertEquals($iVarSum, count($aDbConfig['constraints']));
    }

    public function testInformationSendingToOxidConfigurable()
    {
        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            $this->markTestSkipped('This test is for Community editions only.');
        }

        $shopConfig = oxNew('Shop_Config');

        $this->assertTrue($shopConfig->informationSendingToOxidConfigurable());
    }
}
