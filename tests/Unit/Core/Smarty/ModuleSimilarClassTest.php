<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use OxidEsales\EshopCommunity\Core\Exception\SystemComponentException;
use \testModuleSimilarName_parent;
use \oxRegistry;
use \oxTestModules;

/**
 * @group module
 * @package Unit\Core\Smarty
 */
class ModuleSimilarClassTest extends \OxidTestCase
{

    /**
     * test when overloading class in module with similar name as other module
     */
    public function testModuleSimilarName()
    {
        $wrapper = $this->getVfsStreamWrapper();
        oxRegistry::get("oxConfigFile")->setVar("sShopDir", $wrapper->getRootPath());
        $wrapper->createStructure(array(
            'modules' => array(
                'testmodulesimilarname.php' => "<?php
                    class testModuleSimilarName extends testModuleSimilarName_parent {
                        public function sayHi() {
                            return \"Hi!\";
                        }
                    }"
            )
        ));

        $extensions = array('oxbasketitem' => 'testmodulesimilarnameitem', 'oxbasket' => 'testmodulesimilarname');
        \OxidEsales\Eshop\Core\Registry::getUtilsObject()->setModuleVar('aModules', $extensions);

        $oTestMod = oxNew('oxBasket');
        $this->assertEquals("Hi!", $oTestMod->sayHi());
    }

    /**
     * test catching exception when calling not existent similar module
     */
    public function testModuleSimilarName_ClassNotExist()
    {
        $wrapper = $this->getVfsStreamWrapper();
        oxRegistry::get("oxConfigFile")->setVar("sShopDir", $wrapper->getRootPath());
        $wrapper->createStructure(array(
            'modules' => array(
                'testmodulesimilarname.php' => "<?php
                    class testModuleSimilarName extends testModuleSimilarName_parent {
                        public function sayHi() {
                            return \"Hi!\";
                        }
                    }"
            )
        ));

        /**
         * Real error handling on missing files is disabled for the tests, but when the shop tries to include that not
         * existing file we expect an error to be thrown
         */
        $this->expectException(\PHPUnit\Framework\Error\Warning::class);

        $extensions = array('oxbasketitem' => 'testmodulesimilar', 'oxbasket' => 'testmodulesimilarname');
        \OxidEsales\Eshop\Core\Registry::getUtilsObject()->setModuleVar('aModules', $extensions);

        oxNew('testmodulesimilar');
    }
}
