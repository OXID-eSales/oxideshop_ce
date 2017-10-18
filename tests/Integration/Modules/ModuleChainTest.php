<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Registry;

/**
 * Test for the module class chain.
 *
 * We found some bugs, so this test class is a regression test, if you have a better place for some of the tests or all,
 * feel free to move things.
 *
 * @group module
 */
class ModuleChainTest extends BaseModuleTestCase
{
    /**
     * @var Environment The helper object for the environment.
     */
    protected $environment = null;

    /**
     * Standard set up method. Calls parent first.
     */
    public function setUp()
    {
        parent::setUp();

        $configFile = Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class);
        $configFile->setVar('sShopDir', realpath(__DIR__ . '/TestData'));

        Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);

        $this->environment = new Environment();
    }

    /**
     * Standard tear down method. Calls parent last.
     */
    public function tearDown()
    {
        $this->environment->clean();

        parent::setUp();
    }
    /**
     * Test, that a deactivated module is not used in the module chain.
     */
    public function testModuleChainIsntUsedForDeactivatedModules()
    {
        // @todo: implement test case
    }

    /**
     * Test, that the module activation removes not existing classes from the module chain.
     *
     * Note: same problem might exist with files - maybe we should write a test for the files too.
     */
    public function testModuleActivationRemovesNotExistingChainClasses()
    {
        // @todo: implement test case
    }

    /**
     * Assert, that the class inheritance chain of the given object is formed as expected.
     *
     * @param array  $expectedChain The expected class inheritance chain.
     * @param object $object        The object for which we want to assure, that the class inheritance chain is as expected.
     */
    protected function assertClassChain($expectedChain, $object)
    {
        $this->assertEquals(
            $expectedChain,
            $this->getClassChain($object),
            'The class inheritance chain of the given object does not fit to the expected one!'
        );
    }

    /**
     * Get the class chain of the given object.
     *
     * @param object $instance The object we want to have the class chain for.
     *
     * @return array The class chain of the given object.
     */
    protected function getClassChain($instance)
    {
        $parents = array_keys(class_parents($instance));

        return array_merge([get_class($instance)], $parents);
    }
}
