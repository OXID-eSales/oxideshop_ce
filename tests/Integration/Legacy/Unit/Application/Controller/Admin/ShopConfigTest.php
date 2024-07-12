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
class ShopConfigTest extends \PHPUnit\Framework\TestCase
{
    protected function setup(): void
    {
        $this->setAdminMode(true);

        parent::setUp();
    }

    /**
     * Shop_Config::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertSame('shop_config', $oView->render());
    }

    public function testSaveConfVars(): void
    {
        $this->setAdminMode(true);
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("confbools", ["varnamebool" => true]);
        $this->setRequestParameter("confstrs", ["varnamestr" => "string"]);
        $this->setRequestParameter("confarrs", ["varnamearr" => "a\nb\nc"]);
        $this->setRequestParameter("confaarrs", ["varnameaarr" => "a => b\nc => d"]);
        $this->setRequestParameter("confselects", ["varnamesel" => "a"]);

        $aTasks[] = "getConfig";
        $aTasks[] = "resetContentCache";
        $aTasks[] = "getModuleForConfigVars";

        $oConfig = $this->getMock(Config::class, ["saveShopConfVar"]);
        $oConfig
            ->method('saveShopConfVar')
            ->withConsecutive(
                ['bool', 'varnamebool', true, 'testId', 'theme:mytheme'],
                ['str', 'varnamestr', 'string', 'testId', 'theme:mytheme'],
                ['arr', 'varnamearr', ["a", "b", "c"], 'testId', 'theme:mytheme'],
                ['aarr', 'varnameaarr', ["a" => "b", "c" => "d"], 'testId', 'theme:mytheme'],
                ['select', 'varnamesel', "a", 'testId', 'theme:mytheme'],
            );

        $oView = $this->getMock(ShopConfiguration::class, $aTasks, [], '', false);
        $oView->expects($this->once())->method('resetContentCache');
        $oView->expects($this->atLeastOnce())->method('getModuleForConfigVars')
            ->willReturn('theme:mytheme');

        $oView->saveConfVars();
    }

    public function testGetModuleForConfigVars()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertSame('', $oTest->getModuleForConfigVars());
    }

    /**
     * Shop_Config::Save() test case
     */
    public function testSave()
    {
        $oView = $this->getMock(ShopConfiguration::class, ["saveConfVars"]);
        $oView->expects($this->once())->method('saveConfVars');
        $oView->save();
    }

    /**
     * Shop_Config::ArrayToMultiline() test case
     */
    public function testArrayToMultiline()
    {
        // defining parameters
        $aInput = ["a", "b", "c"];

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertSame("a\nb\nc", $oView->arrayToMultiline($aInput));
    }

    /**
     * Shop_Config::MultilineToArray() test case
     */
    public function testMultilineToArray()
    {
        // defining parameters
        $sMultiline = "a\nb\n\nc";

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertSame([0 => "a", 1 => "b", 3 => "c"], $oView->multilineToArray($sMultiline));
    }

    /**
     * Shop_Config::AarrayToMultiline() test case
     */
    public function testAarrayToMultiline()
    {
        // defining parameters
        $aInput = ["a" => "b", "c" => "d"];

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertSame("a => b\nc => d", $oView->aarrayToMultiline($aInput));
    }

    /**
     * Shop_Config::MultilineToAarray() test case
     */
    public function testMultilineToAarray()
    {
        // defining parameters
        $sMultiline = "a => b\nc => d";

        // testing..
        $oView = oxNew('Shop_Config');
        $this->assertSame(["a" => "b", "c" => "d"], $oView->multilineToAarray($sMultiline));
    }

    /**
     * _parseConstraint test
     */
    public function testParseConstraint()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertSame('', $oTest->parseConstraint('sometype', 'asdd'));
        $this->assertSame('', $oTest->parseConstraint('bool', 'asdd'));
        $this->assertSame('', $oTest->parseConstraint('string', 'asdd'));
        $this->assertSame(['a', 'bc', 'd'], $oTest->parseConstraint('select', 'a|bc|d'));
    }

    /**
     * _serializeConstraint test
     */
    public function testSerializeConstraint()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertSame('', $oTest->serializeConstraint('sometype', 'asdd'));
        $this->assertSame('', $oTest->serializeConstraint('bool', 'asdd'));
        $this->assertSame('', $oTest->serializeConstraint('string', 'asdd'));
        $this->assertSame('a|bc|d', $oTest->serializeConstraint('select', ['a', 'bc', 'd']));
    }

    /**
     * _serializeConfVar test
     */
    public function testSerializeConfVar()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertSame('1.1', $oTest->serializeConfVar('str', 'iMinOrderPrice', '1,1'));
        $this->assertSame('2,2', $oTest->serializeConfVar('str', 'shouldNotChange', '2,2'));
    }

    /**
     * _unserializeConfVar test
     */
    public function testUnserializeConfVar()
    {
        $oTest = oxNew('Shop_Config');
        $this->assertSame('1.1', $oTest->unserializeConfVar('str', 'iMinOrderPrice', '1,1'));
        $this->assertSame('2,2', $oTest->unserializeConfVar('str', 'shouldNotChange', '2,2'));
    }


    /**
     * loadConfVars test
     */
    public function testLoadConfVars()
    {
        $oTest = oxNew('Shop_Config');
        $aDbConfig = $oTest->loadConfVars($this->getConfig()->getShopId(), '');

        $this->assertSame(
            ['vars', 'constraints', 'grouping'],
            array_keys($aDbConfig)
        );

        $iVarSum = array_sum(array_map(count(...), $aDbConfig['vars']));
        $this->assertGreaterThan(100, $iVarSum);
        $this->assertCount($iVarSum, $aDbConfig['constraints']);
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
