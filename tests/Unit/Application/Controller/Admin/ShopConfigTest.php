<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Shop_Config class
 */
class ShopConfigTest extends \OxidTestCase
{
    public function setUp()
    {
        $this->setAdminMode(true);

        return parent::setUp();
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
        $this->assertEquals('shop_config.tpl', $oView->render());
    }

    /**
     * Shop_Config::SaveConfVars() test case
     *
     * @return null
     */
    public function testSaveConfVars()
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
        $aTasks[] = "_getModuleForConfigVars";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("saveShopConfVar"));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')
            ->with(
                $this->equalTo("bool"),
                $this->equalTo("varnamebool"),
                $this->equalTo(true),
                $this->equalTo("testId"),
                $this->equalTo('theme:mytheme')
            );
        $oConfig->expects($this->at(1))->method('saveShopConfVar')
            ->with(
                $this->equalTo("str"),
                $this->equalTo("varnamestr"),
                $this->equalTo("string"),
                $this->equalTo("testId"),
                $this->equalTo('theme:mytheme')
            );
        $oConfig->expects($this->at(2))->method('saveShopConfVar')
            ->with(
                $this->equalTo("arr"),
                $this->equalTo("varnamearr"),
                $this->equalTo(array("a", "b", "c")),
                $this->equalTo("testId"),
                $this->equalTo('theme:mytheme')
            );
        $oConfig->expects($this->at(3))->method('saveShopConfVar')
            ->with(
                $this->equalTo("aarr"),
                $this->equalTo("varnameaarr"),
                $this->equalTo(array("a" => "b", "c" => "d")),
                $this->equalTo("testId"),
                $this->equalTo('theme:mytheme')
            );
        $oConfig->expects($this->at(4))->method('saveShopConfVar')
            ->with(
                $this->equalTo("select"),
                $this->equalTo("varnamesel"),
                $this->equalTo("a"),
                $this->equalTo("testId"),
                $this->equalTo('theme:mytheme')
            );

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration::class, $aTasks, array(), '', false);
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('resetContentCache');
        $oView->expects($this->atLeastOnce())->method('_getModuleForConfigVars')
            ->will($this->returnValue('theme:mytheme'));

        $oView->saveConfVars();
    }

    public function testGetModuleForConfigVars()
    {
        $sCl = oxTestModules::publicize('Shop_Config', '_getModuleForConfigVars');
        $oTest = new $sCl;
        $this->assertEquals('', $oTest->p_getModuleForConfigVars());
    }

    /**
     * Shop_Config::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration::class, array("saveConfVars"));
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
        $this->assertEquals("a\nb\nc", $oView->UNITarrayToMultiline($aInput));
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
        $this->assertEquals(array(0 => "a", 1 => "b", 3 => "c"), $oView->UNITmultilineToArray($sMultiline));
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
        $this->assertEquals("a => b\nc => d", $oView->UNITaarrayToMultiline($aInput));
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
        $this->assertEquals(array("a" => "b", "c" => "d"), $oView->UNITmultilineToAarray($sMultiline));
    }

    /**
     * _parseConstraint test
     *
     * @return null
     */
    public function testParseConstraint()
    {
        $sCl = oxTestModules::publicize('Shop_Config', '_parseConstraint');
        $oTest = new $sCl;
        $this->assertEquals('', $oTest->p_parseConstraint('sometype', 'asdd'));
        $this->assertEquals('', $oTest->p_parseConstraint('bool', 'asdd'));
        $this->assertEquals('', $oTest->p_parseConstraint('string', 'asdd'));
        $this->assertEquals(array('a', 'bc', 'd'), $oTest->p_parseConstraint('select', 'a|bc|d'));
    }

    /**
     * _serializeConstraint test
     *
     * @return null
     */
    public function testSerializeConstraint()
    {
        $sCl = oxTestModules::publicize('Shop_Config', '_serializeConstraint');
        $oTest = new $sCl;
        $this->assertEquals('', $oTest->p_serializeConstraint('sometype', 'asdd'));
        $this->assertEquals('', $oTest->p_serializeConstraint('bool', 'asdd'));
        $this->assertEquals('', $oTest->p_serializeConstraint('string', 'asdd'));
        $this->assertEquals('a|bc|d', $oTest->p_serializeConstraint('select', array('a', 'bc', 'd')));
    }

    /**
     * _serializeConfVar test
     *
     * @return null
     */
    public function testSerializeConfVar()
    {
        $sCl = oxTestModules::publicize('Shop_Config', '_serializeConfVar');
        $oTest = new $sCl;
        $this->assertEquals('1.1', $oTest->p_serializeConfVar('str', 'iMinOrderPrice', '1,1'));
        $this->assertEquals('2,2', $oTest->p_serializeConfVar('str', 'shouldNotChange', '2,2'));
    }

    /**
     * _unserializeConfVar test
     *
     * @return null
     */
    public function testUnserializeConfVar()
    {
        $sCl = oxTestModules::publicize('Shop_Config', '_unserializeConfVar');
        $oTest = new $sCl;
        $this->assertEquals('1.1', $oTest->p_unserializeConfVar('str', 'iMinOrderPrice', '1,1'));
        $this->assertEquals('2,2', $oTest->p_unserializeConfVar('str', 'shouldNotChange', '2,2'));
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
