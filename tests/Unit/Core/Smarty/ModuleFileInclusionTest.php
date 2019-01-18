<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \testModuleInclusion_parent;
use \oxRegistry;

/**
 * test for situation:
 * module class is registered for oxnew but was not yet instantialized
 * module file inclusion makes autoload mod_parent by including the same module file
 * thus in the end module class is created twice resulting in php fatal error
 *
 * @group module
 */
class ModuleFileInclusionTest extends \OxidTestCase
{

    /**
     * test main scenario
     */
    public function testModuleInclusion()
    {
        $wrapper = $this->getVfsStreamWrapper();
        oxRegistry::get("oxConfigFile")->setVar("sShopDir", $wrapper->getRootPath());
        $wrapper->createStructure(array(
            'modules' => array(
                'testmoduleinclusion.php' => "<?php
                    class testmoduleinclusion extends testmoduleinclusion_parent {
                        public function sayHi() {
                            return \"Hi!\";
                        }
                    }"
            )
        ));

        \OxidEsales\Eshop\Core\Registry::getUtilsObject()->setModuleVar('aModules', array('oxarticle' => 'testmoduleinclusion'));

        $oTestMod = oxNew('testModuleInclusion');
        $this->assertEquals("Hi!", $oTestMod->sayHi());

        $oTestArt = oxNew('oxArticle');
        $this->assertEquals("Hi!", $oTestArt->sayHi());
    }
}
